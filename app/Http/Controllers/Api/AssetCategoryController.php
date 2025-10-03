<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssetCategoryResource;
use App\Models\AssetCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AssetCategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $department = $request->user()?->department_code;

        $query = AssetCategory::query()
            ->withCount('assets')
            ->orderBy('name');

        if ($department) {
            $query->where('department_code', $department);
        }

        return AssetCategoryResource::collection($query->get());
    }

    public function store(Request $request): AssetCategoryResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department_code' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        $category = AssetCategory::create($data);

        return AssetCategoryResource::make($category->loadCount('assets'));
    }

    public function show(AssetCategory $assetCategory): AssetCategoryResource
    {
        return AssetCategoryResource::make($assetCategory->loadCount('assets', 'assets')); // assets for nested listing
    }

    public function update(Request $request, AssetCategory $assetCategory): AssetCategoryResource
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'department_code' => ['sometimes', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        $assetCategory->update($data);

        return AssetCategoryResource::make($assetCategory->fresh()->loadCount('assets'));
    }

    public function destroy(AssetCategory $assetCategory): JsonResponse
    {
        $assetCategory->delete();

        return response()->json(status: 204);
    }
}
