<?php

namespace App\Models\Plaza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlazaCustomOverride extends Model
{
    protected $table = 'plaza_custom_overrides';

    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = [
        'membership_id',
        'capability_id',
        'is_granted',
    ];

    protected $casts = [
        'is_granted' => 'boolean',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(PlazaMembership::class, 'membership_id');
    }

    public function capability(): BelongsTo
    {
        return $this->belongsTo(PlazaCapability::class, 'capability_id');
    }
}

