<?php

namespace App\Models\Plaza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlazaUser extends Model
{
    use SoftDeletes;

    protected $table = 'plaza_users';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'is_platform_admin',
        'status',
        'verification_token',
        'reset_token',
        'token_expires_at',
        'failed_login_attempts',
        'lockout_until',
        'last_login_at',
    ];

    protected $casts = [
        'is_platform_admin' => 'boolean',
        'failed_login_attempts' => 'integer',
        'token_expires_at' => 'datetime',
        'lockout_until' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function ownedStores(): HasMany
    {
        return $this->hasMany(PlazaStore::class, 'owner_id');
    }

    public function createdTeams(): HasMany
    {
        return $this->hasMany(PlazaTeam::class, 'created_by');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(PlazaMembership::class, 'user_id');
    }

    public function invitedMemberships(): HasMany
    {
        return $this->hasMany(PlazaMembership::class, 'invited_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PlazaAuthAudit::class, 'user_id');
    }
}

