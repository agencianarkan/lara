<?php

namespace App\Models\Plaza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlazaTeam extends Model
{
    use SoftDeletes;

    protected $table = 'plaza_teams';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'store_id',
        'name',
        'description',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function store(): BelongsTo
    {
        return $this->belongsTo(PlazaStore::class, 'store_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(PlazaUser::class, 'created_by');
    }

    public function membershipTeams(): HasMany
    {
        return $this->hasMany(PlazaMembershipTeam::class, 'team_id');
    }
}

