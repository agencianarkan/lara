<?php

namespace App\Models\Plaza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlazaRoleDefinition extends Model
{
    protected $table = 'plaza_role_definitions';

    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = [
        'role_id',
        'capability_id',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(PlazaRole::class, 'role_id');
    }

    public function capability(): BelongsTo
    {
        return $this->belongsTo(PlazaCapability::class, 'capability_id');
    }
}

