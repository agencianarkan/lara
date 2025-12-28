<?php

namespace App\Models\Plaza;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlazaMembershipTeam extends Model
{
    protected $table = 'plaza_membership_teams';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'membership_id',
        'team_id',
        'is_team_leader',
    ];

    protected $casts = [
        'is_team_leader' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    const CREATED_AT = 'assigned_at';
    const UPDATED_AT = null;

    public function membership(): BelongsTo
    {
        return $this->belongsTo(PlazaMembership::class, 'membership_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(PlazaTeam::class, 'team_id');
    }
}

