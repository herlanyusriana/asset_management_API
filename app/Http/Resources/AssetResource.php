<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AssetResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_code' => $this->asset_code,
            'name' => $this->name,
            'brand' => $this->brand,
            'model' => $this->model,
            'processor_name' => $this->processor_name,
            'ram_capacity' => $this->ram_capacity,
            'storage_type' => $this->storage_type,
            'storage_brand' => $this->storage_brand,
            'storage_capacity' => $this->storage_capacity,
            'serial_number' => $this->serial_number,
            'department' => $this->department_name ?? $this->category?->department_code,
            'department_name' => $this->department_name,
            'purchase_date' => optional($this->purchase_date)?->toDateString(),
            'warranty_expiry' => optional($this->warranty_expiry)?->toDateString(),
            'purchase_price' => $this->purchase_price,
            'status' => $this->status,
            'condition_notes' => $this->condition_notes,
            'location' => $this->location,
            'asset_photo_path' => $this->asset_photo_path,
            'asset_photo_url' => $this->asset_photo_path
                ? Storage::disk('public')->url($this->asset_photo_path)
                : null,
            'custodian_name' => $this->current_custodian_name ?? $this->custodian?->name,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'department_code' => $this->category->department_code,
            ]),
            'custodian' => $this->whenLoaded('custodian', fn () => [
                'id' => $this->custodian->id,
                'name' => $this->custodian->name,
                'email' => $this->custodian->email,
                'department_code' => $this->custodian->department_code,
            ]),
            'assignments_count' => $this->when(isset($this->assignments_count), $this->assignments_count),
            'assignments' => AssetAssignmentResource::collection($this->whenLoaded('assignments')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
