<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssetRequestResource;
use App\Models\AssetRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class AssetRequestController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $department = $request->user()?->department_code;

        $query = AssetRequest::query()
            ->with(['category', 'asset', 'requester'])
            ->orderByDesc('requested_at');

        if ($department) {
            $query->where('department_code', $department);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return AssetRequestResource::collection($query->paginate((int) $request->integer('per_page', 20)));
    }

    public function store(Request $request): AssetRequestResource
    {
        $data = $request->validate([
            'asset_category_id' => ['required', Rule::exists('asset_categories', 'id')],
            'asset_id' => ['nullable', Rule::exists('assets', 'id')],
            'department_code' => ['required', 'string', 'max:50'],
            'reason' => ['nullable', 'string'],
        ]);

        $requesterId = optional($request->user())->id;

        if (! $requesterId) {
            $validatedRequester = $request->validate([
                'requester_user_id' => ['required', Rule::exists('users', 'id')],
            ]);

            $requesterId = $validatedRequester['requester_user_id'];
        }

        $payload = array_merge($data, [
            'requester_user_id' => $requesterId,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        $assetRequest = AssetRequest::create($payload);

        return AssetRequestResource::make($assetRequest->load(['category', 'asset', 'requester']));
    }

    public function show(AssetRequest $assetRequest): AssetRequestResource
    {
        return AssetRequestResource::make($assetRequest->load(['category', 'asset', 'requester']));
    }

    public function update(Request $request, AssetRequest $assetRequest): AssetRequestResource
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'fulfilled'])],
            'asset_id' => ['nullable', Rule::exists('assets', 'id')],
            'reason' => ['nullable', 'string'],
        ]);

        if (! empty($data['asset_id']) && $assetRequest->asset_id !== $data['asset_id']) {
            $assetRequest->asset_id = $data['asset_id'];
        }

        $assetRequest->status = $data['status'];
        $assetRequest->reason = $data['reason'] ?? $assetRequest->reason;
        $assetRequest->processed_at = now();
        $assetRequest->save();

        if ($assetRequest->asset && in_array($assetRequest->status, ['approved', 'fulfilled'], true)) {
            $assetRequest->asset->update([
                'status' => $assetRequest->status === 'fulfilled' ? 'assigned' : 'available',
            ]);
        }

        return AssetRequestResource::make($assetRequest->fresh()->load(['category', 'asset', 'requester']));
    }

    public function destroy(AssetRequest $assetRequest): JsonResponse
    {
        $assetRequest->delete();

        return response()->json(status: 204);
    }
}
