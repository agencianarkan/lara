<?php

namespace App\Models\Plaza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlazaCapability extends Model
{
    protected $table = 'plaza_capabilities';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'module',
        'slug',
        'label',
    ];

    public function roleDefinitions(): HasMany
    {
        return $this->hasMany(PlazaRoleDefinition::class, 'capability_id');
    }

    public function customOverrides(): HasMany
    {
        return $this->hasMany(PlazaCustomOverride::class, 'capability_id');
    }
}

