<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssetAssignmentResource;
use App\Models\Asset;
use App\Models\AssetAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class AssetAssignmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $department = $request->user()?->department_code;

        $query = AssetAssignment::query()
            ->with(['asset.category', 'assignedTo', 'assignedBy'])
            ->orderByDesc('assigned_at');

        if ($department) {
            $query->where('department_code', $department);
        }

        return AssetAssignmentResource::collection($query->paginate((int) $request->integer('per_page', 20)));
    }

    public function store(Request $request): AssetAssignmentResource
    {
        $data = $request->validate([
            'asset_id' => ['required', Rule::exists('assets', 'id')],
            'assigned_to_user_id' => ['nullable', Rule::exists('users', 'id')],
            'assigned_by_user_id' => ['required', Rule::exists('users', 'id')],
            'department_code' => ['required', 'string', 'max:50'],
            'assigned_at' => ['required', 'date'],
            'expected_return_at' => ['nullable', 'date', 'after_or_equal:assigned_at'],
            'notes' => ['nullable', 'string'],
        ]);

        $assignment = AssetAssignment::create($data);

        /** @var Asset $asset */
        $asset = Asset::findOrFail($data['asset_id']);
        $asset->update([
            'status' => 'assigned',
            'current_custodian_id' => $data['assigned_to_user_id'] ?? $asset->current_custodian_id,
        ]);

        return AssetAssignmentResource::make($assignment->load(['asset.category', 'assignedTo', 'assignedBy']));
    }

    public function update(Request $request, AssetAssignment $assetAssignment): AssetAssignmentResource
    {
        $data = $request->validate([
            'returned_at' => ['nullable', 'date'],
            'condition_on_return' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $assetAssignment->update($data);

        if (! empty($data['returned_at'])) {
            $assetAssignment->asset->update([
                'status' => 'available',
                'current_custodian_id' => null,
            ]);
        }

        return AssetAssignmentResource::make($assetAssignment->fresh()->load(['asset.category', 'assignedTo', 'assignedBy']));
    }

    public function destroy(AssetAssignment $assetAssignment): JsonResponse
    {
        $assetAssignment->delete();

        return response()->json(status: 204);
    }
}
