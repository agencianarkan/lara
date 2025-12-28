<?php

namespace App\Models\Plaza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlazaStore extends Model
{
    use SoftDeletes;

    protected $table = 'plaza_stores';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'name',
        'domain_url',
        'platform_type',
        'connection_config',
        'plaza_api_key',
        'owner_id',
        'logo_url',
    ];

    protected $casts = [
        'connection_config' => 'array',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function owner(): BelongsTo
    {
        return $this->belongsTo(PlazaUser::class, 'owner_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(PlazaTeam::class, 'store_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(PlazaMembership::class, 'store_id');
    }
}

