<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    protected $fillable = [
        'name',
        'department_code',
        'description',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
