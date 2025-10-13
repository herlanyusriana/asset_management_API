<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'asset_code',
        'name',
        'asset_category_id',
        'department_name',
        'brand',
        'model',
        'processor_name',
        'ram_capacity',
        'storage_type',
        'storage_brand',
        'storage_capacity',
        'serial_number',
        'purchase_date',
        'warranty_expiry',
        'purchase_price',
        'status',
        'condition_notes',
        'location',
        'current_custodian_id',
        'current_custodian_name',
        'asset_photo_path',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function custodian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_custodian_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(AssetRequest::class);
    }
}
