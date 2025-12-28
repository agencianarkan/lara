<?php

namespace App\Models\Plaza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlazaRole extends Model
{
    protected $table = 'plaza_roles';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_customizable',
    ];

    protected $casts = [
        'is_customizable' => 'boolean',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(PlazaMembership::class, 'role_id');
    }

    public function roleDefinitions(): HasMany
    {
        return $this->hasMany(PlazaRoleDefinition::class, 'role_id');
    }
}

