<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Models\User;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $department = $request->user()?->department_code;

        $query = Asset::query()
            ->with(['category', 'custodian'])
            ->withCount('assignments')
            ->orderByDesc('updated_at');

        if ($department) {
            $query->whereHas('category', fn ($q) => $q->where('department_code', $department));
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->integer('asset_category_id')) {
            $query->where('asset_category_id', $categoryId);
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('asset_code', 'like', "%$search%")
                    ->orWhere('brand', 'like', "%$search%")
                    ->orWhere('model', 'like', "%$search%")
                    ->orWhere('serial_number', 'like', "%$search%");
            });
        }

        $perPage = (int) $request->integer('per_page', 20);

        return AssetResource::collection($query->paginate($perPage));
    }

    public function store(Request $request): AssetResource
    {
        $data = $this->validateData($request);

        $asset = Asset::create($data);

        return AssetResource::make($asset->load(['category', 'custodian']));
    }

    public function show(Asset $asset): AssetResource
    {
        return AssetResource::make(
            $asset->load(['category', 'custodian', 'assignments.assignedTo', 'assignments.assignedBy'])
        );
    }

    public function showByCode(Request $request, string $code): AssetResource
    {
        $department = $request->user()?->department_code;

        $query = Asset::query()
            ->where('asset_code', $code)
            ->with(['category', 'custodian', 'assignments.assignedTo', 'assignments.assignedBy']);

        if ($department) {
            $query->whereHas('category', fn ($q) => $q->where('department_code', $department));
        }

        $asset = $query->firstOrFail();

        return AssetResource::make($asset);
    }

    public function barcode(Request $request, Asset $asset)
    {
        $department = $request->user()?->department_code;
        if ($department && $asset->category?->department_code !== $department) {
            abort(403);
        }

        $value = $asset->asset_code ?: (string) $asset->id;
        $generator = new BarcodeGeneratorPNG();
        $image = $generator->getBarcode($value, $generator::TYPE_CODE_128);
        $filename = 'asset-' . $value . '.png';

        return response($image, 200)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    public function update(Request $request, Asset $asset): AssetResource
    {
        $data = $this->validateData($request, $asset);

        $asset->update($data);

        return AssetResource::make($asset->fresh()->load(['category', 'custodian']));
    }

    public function destroy(Asset $asset): JsonResponse
    {
        $asset->delete();

        return response()->json(status: 204);
    }

    private function validateData(Request $request, ?Asset $asset = null): array
    {
        $assetId = $asset?->id;

        $rules = [
            'asset_code' => [
                $assetId ? 'sometimes' : 'required',
                'string',
                'max:100',
                Rule::unique('assets', 'asset_code')->ignore($assetId),
            ],
            'name' => [$assetId ? 'sometimes' : 'required', 'string', 'max:255'],
            'asset_category_id' => [$assetId ? 'sometimes' : 'required', 'exists:asset_categories,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'processor_name' => ['nullable', 'string', 'max:255'],
            'ram_capacity' => ['nullable', 'string', 'max:255'],
            'storage_type' => ['nullable', 'string', 'max:255'],
            'storage_brand' => ['nullable', 'string', 'max:255'],
            'storage_capacity' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expiry' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:50'],
            'condition_notes' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'current_custodian_id' => ['nullable', 'exists:users,id'],
            'custodian_name' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'asset_photo' => ['sometimes', 'file', 'image', 'max:5120'],
            'remove_asset_photo' => ['sometimes', 'boolean'],
        ];

        $validated = $request->validate($rules);

        if (array_key_exists('department', $validated)) {
            $validated['department_name'] = filled($validated['department'])
                ? $validated['department']
                : null;
            unset($validated['department']);
        }

        if (array_key_exists('custodian_name', $validated)) {
            $validated['current_custodian_name'] = filled($validated['custodian_name'])
                ? $validated['custodian_name']
                : null;
            unset($validated['custodian_name']);
        }

        if (!empty($validated['current_custodian_id'])) {
            $user = User::find($validated['current_custodian_id']);
            if ($user) {
                $validated['current_custodian_name'] = $user->name;
                $validated['department_name'] = $validated['department_name'] ?? $user->department_code;
            }
        }

        if ($request->hasFile('asset_photo')) {
            $path = $request->file('asset_photo')->store('asset-photos', 'public');

            if ($asset && $asset->asset_photo_path) {
                Storage::disk('public')->delete($asset->asset_photo_path);
            }

            $validated['asset_photo_path'] = $path;
        } elseif ($request->boolean('remove_asset_photo') && $asset && $asset->asset_photo_path) {
            Storage::disk('public')->delete($asset->asset_photo_path);
            $validated['asset_photo_path'] = null;
        }

        unset($validated['asset_photo'], $validated['remove_asset_photo']);

        if ($assetId === null) {
            $validated['status'] = $validated['status'] ?? 'available';
        }

        return $validated;
    }
}

