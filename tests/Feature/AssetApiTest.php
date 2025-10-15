<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AssetApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create([
            'department_code' => 'IT',
        ]);

        Sanctum::actingAs($user);

        return $user;
    }

    private function createCategory(): AssetCategory
    {
        return AssetCategory::create([
            'name' => 'Test Category',
            'department_code' => 'IT',
            'description' => 'QA category',
        ]);
    }

    public function test_store_asset_persists_hardware_spec_fields(): void
    {
        $this->actingAsUser();
        $category = $this->createCategory();

        $payload = [
            'asset_code' => 'QA-STORE-001',
            'name' => 'QA Laptop',
            'asset_category_id' => $category->id,
            'serial_number' => 'SN-STORE-001',
            'brand' => 'Lenovo',
            'model' => 'ThinkPad QA',
            'processor_name' => 'Intel Core i7-1260P',
            'ram_capacity' => '16 GB',
            'storage_type' => 'SSD NVMe',
            'storage_brand' => 'Samsung',
            'storage_capacity' => '512 GB',
            'purchase_date' => Carbon::now()->toDateString(),
            'warranty_expiry' => Carbon::now()->addYear()->toDateString(),
            'purchase_price' => 17500000,
            'status' => 'available',
            'location' => 'QA Lab',
            'department' => 'IT',
        ];

        $response = $this->postJson('/api/assets', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.processor_name', 'Intel Core i7-1260P')
            ->assertJsonPath('data.ram_capacity', '16 GB')
            ->assertJsonPath('data.storage_type', 'SSD NVMe');

        $this->assertDatabaseHas('assets', [
            'asset_code' => 'QA-STORE-001',
            'processor_name' => 'Intel Core i7-1260P',
            'ram_capacity' => '16 GB',
            'storage_type' => 'SSD NVMe',
            'storage_brand' => 'Samsung',
            'storage_capacity' => '512 GB',
        ]);
    }

    public function test_update_asset_persists_hardware_spec_fields(): void
    {
        $user = $this->actingAsUser();
        $category = $this->createCategory();

        $asset = Asset::create([
            'asset_code' => 'QA-UPD-001',
            'name' => 'QA Workstation',
            'asset_category_id' => $category->id,
            'department_name' => 'IT',
            'brand' => 'HP',
            'model' => 'ZBook',
            'serial_number' => 'SN-UPD-001',
            'status' => 'available',
        ]);

        $payload = [
            'asset_code' => $asset->asset_code,
            'name' => $asset->name,
            'asset_category_id' => $asset->asset_category_id,
            'serial_number' => $asset->serial_number,
            'brand' => 'HP',
            'model' => 'ZBook Fury',
            'processor_name' => 'AMD Ryzen 9 7940HS',
            'ram_capacity' => '32 GB',
            'storage_type' => 'SSD NVMe',
            'storage_brand' => 'WD Black',
            'storage_capacity' => '1 TB',
            'status' => 'assigned',
            'department' => 'Finance',
            'custodian_name' => $user->name,
            'current_custodian_id' => $user->id,
            'location' => 'Finance Desk',
        ];

        $response = $this->putJson("/api/assets/{$asset->id}", $payload);

        $response
            ->assertOk()
            ->assertJsonPath('data.processor_name', 'AMD Ryzen 9 7940HS')
            ->assertJsonPath('data.ram_capacity', '32 GB')
            ->assertJsonPath('data.storage_capacity', '1 TB')
            ->assertJsonPath('data.department', 'Finance')
            ->assertJsonPath('data.location', 'Finance Desk');

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'processor_name' => 'AMD Ryzen 9 7940HS',
            'ram_capacity' => '32 GB',
            'storage_type' => 'SSD NVMe',
            'storage_brand' => 'WD Black',
            'storage_capacity' => '1 TB',
            'current_custodian_name' => $user->name,
            'department_name' => 'Finance',
            'location' => 'Finance Desk',
        ]);
    }

    public function test_delete_asset_cascades_assignments(): void
    {
        $user = $this->actingAsUser();
        $category = $this->createCategory();

        $asset = Asset::create([
            'asset_code' => 'QA-DEL-001',
            'name' => 'QA Spare Device',
            'asset_category_id' => $category->id,
            'department_name' => 'IT',
            'status' => 'available',
        ]);

        $assignment = AssetAssignment::create([
            'asset_id' => $asset->id,
            'assigned_to_user_id' => $user->id,
            'assigned_by_user_id' => $user->id,
            'department_code' => 'IT',
            'assigned_at' => Carbon::now(),
            'notes' => 'QA assignment',
        ]);

        $this->deleteJson("/api/assets/{$asset->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
        $this->assertDatabaseMissing('asset_assignments', ['id' => $assignment->id]);
    }
}
