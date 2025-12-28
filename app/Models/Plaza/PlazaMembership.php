<?php

namespace App\Models\Plaza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlazaMembership extends Model
{
    use SoftDeletes;

    protected $table = 'plaza_memberships';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'store_id',
        'role_id',
        'is_custom_mode',
        'invited_by',
    ];

    protected $casts = [
        'is_custom_mode' => 'boolean',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(PlazaUser::class, 'user_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(PlazaStore::class, 'store_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(PlazaRole::class, 'role_id');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(PlazaUser::class, 'invited_by');
    }

    public function membershipTeams(): HasMany
    {
        return $this->hasMany(PlazaMembershipTeam::class, 'membership_id');
    }

    public function customOverrides(): HasMany
    {
        return $this->hasMany(PlazaCustomOverride::class, 'membership_id');
    }
}

