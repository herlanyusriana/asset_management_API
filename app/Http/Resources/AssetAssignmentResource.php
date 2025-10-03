<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetAssignmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset' => $this->whenLoaded('asset', fn () => [
                'id' => $this->asset->id,
                'name' => $this->asset->name,
                'asset_code' => $this->asset->asset_code,
            ]),
            'assigned_to' => $this->whenLoaded('assignedTo', fn () => [
                'id' => $this->assignedTo->id,
                'name' => $this->assignedTo->name,
                'email' => $this->assignedTo->email,
            ]),
            'assigned_by' => $this->whenLoaded('assignedBy', fn () => [
                'id' => $this->assignedBy->id,
                'name' => $this->assignedBy->name,
                'email' => $this->assignedBy->email,
            ]),
            'department_code' => $this->department_code,
            'assigned_at' => $this->assigned_at?->toIso8601String(),
            'expected_return_at' => $this->expected_return_at?->toIso8601String(),
            'returned_at' => $this->returned_at?->toIso8601String(),
            'condition_on_return' => $this->condition_on_return,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
