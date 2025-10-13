<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class AssetDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $roles = [
            'HR_ADMIN' => 'HR Admin',
            'IT_ADMIN' => 'IT Admin',
        ];

        $roleModels = collect($roles)->mapWithKeys(function (string $name, string $code) use ($now) {
            $role = Role::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'updated_at' => $now, 'created_at' => $now]
            );

            return [$code => $role];
        });

        $users = [
            [
                'name' => 'Alice HR',
                'email' => 'alice.hr@example.com',
                'department_code' => 'HR',
                'role_code' => 'HR_ADMIN',
            ],
            [
                'name' => 'Ian IT',
                'email' => 'ian.it@example.com',
                'department_code' => 'IT',
                'role_code' => 'IT_ADMIN',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 's.johnson@example.com',
                'department_code' => 'IT',
                'role_code' => null,
            ],
        ];

        $userModels = collect($users)->map(function (array $data) use ($roleModels) {
            $role = $data['role_code'] ? $roleModels[$data['role_code']] : null;

            return User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role_id' => $role?->id,
                    'department_code' => $data['department_code'],
                ]
            );
        })->keyBy('email');

        $categories = [
            [
                'name' => 'Laptops',
                'department_code' => 'IT',
                'description' => 'Laptop, Ultrabook, MacBook',
            ],
            [
                'name' => 'Desktop PCs',
                'department_code' => 'IT',
                'description' => 'Workstation & desktop unit',
            ],
            [
                'name' => 'CCTV Systems',
                'department_code' => 'IT',
                'description' => 'Kamera pengawas dan aksesoris monitoring',
            ],
            [
                'name' => 'Peripherals',
                'department_code' => 'IT',
                'description' => 'Perangkat pendukung seperti keyboard, mouse, dll.',
            ],
            [
                'name' => 'RAM Modules',
                'department_code' => 'IT',
                'description' => 'Memory modules untuk perangkat IT',
            ],
            [
                'name' => 'Processors',
                'department_code' => 'IT',
                'description' => 'CPU dan unit pemrosesan lainnya',
            ],
            [
                'name' => 'Monitors',
                'department_code' => 'IT',
                'description' => 'Monitor LCD/LED untuk workstation dan ruang meeting',
            ],
        ];

        $categoryModels = collect($categories)->map(function (array $data) {
            return AssetCategory::updateOrCreate(
                Arr::only($data, ['name', 'department_code']),
                $data
            );
        })->keyBy('name');

        $assets = [
            [
                'asset_code' => 'LT-DEV-001',
                'name' => 'Dell Latitude 5520',
                'asset_category_id' => $categoryModels['Laptops']->id,
                'brand' => 'Dell',
                'model' => 'Latitude 5520',
                'serial_number' => 'DL2024001',
                'purchase_date' => now()->subMonths(8)->toDateString(),
                'warranty_expiry' => now()->addMonths(6)->toDateString(),
                'purchase_price' => 14500000,
                'status' => 'assigned',
                'condition_notes' => 'Digunakan tim development',
                'location' => 'HQ - Lantai 3',
                'current_custodian_id' => $userModels['ian.it@example.com']->id,
            ],
            [
                'asset_code' => 'PC-FIN-004',
                'name' => 'HP EliteDesk 800',
                'asset_category_id' => $categoryModels['Desktop PCs']->id,
                'brand' => 'HP',
                'model' => 'EliteDesk 800 G6',
                'serial_number' => 'HP2025004',
                'purchase_date' => now()->subMonths(12)->toDateString(),
                'warranty_expiry' => now()->addMonths(2)->toDateString(),
                'purchase_price' => 21000000,
                'status' => 'maintenance',
                'condition_notes' => 'Menunggu pergantian PSU',
                'location' => 'Finance Office',
                'current_custodian_id' => $userModels['alice.hr@example.com']->id,
            ],
            [
                'asset_code' => 'MN-MTG-007',
                'name' => 'Dell UltraSharp 27"',
                'asset_category_id' => $categoryModels['Monitors']->id,
                'brand' => 'Dell',
                'model' => 'U2722DE',
                'serial_number' => 'DL-MON-8821',
                'purchase_date' => now()->subMonths(4)->toDateString(),
                'warranty_expiry' => now()->addMonths(20)->toDateString(),
                'purchase_price' => 7800000,
                'status' => 'available',
                'condition_notes' => 'Cadangan untuk ruang meeting',
                'location' => 'Gudang IT - Rak Monitor',
                'current_custodian_id' => null,
            ],
        ];

        $assetModels = collect($assets)->map(function (array $data) {
            return Asset::updateOrCreate(
                ['asset_code' => $data['asset_code']],
                $data
            );
        })->keyBy('asset_code');

        AssetAssignment::updateOrCreate(
            [
                'asset_id' => $assetModels['LT-DEV-001']->id,
                'assigned_at' => now()->subDays(10),
            ],
            [
                'assigned_to_user_id' => $userModels['ian.it@example.com']->id,
                'assigned_by_user_id' => $userModels['ian.it@example.com']->id,
                'department_code' => 'IT',
                'expected_return_at' => now()->addMonths(6),
                'notes' => 'Penyerahan perdana untuk dev team',
            ]
        );

        AssetAssignment::updateOrCreate(
            [
                'asset_id' => $assetModels['PC-FIN-004']->id,
                'assigned_at' => now()->subDays(40),
            ],
            [
                'assigned_to_user_id' => $userModels['alice.hr@example.com']->id,
                'assigned_by_user_id' => $userModels['ian.it@example.com']->id,
                'department_code' => 'HR',
                'notes' => 'Dipakai sementara oleh HR untuk training',
            ]
        );
    }
}
