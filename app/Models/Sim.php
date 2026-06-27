<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sim extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'category',
        'provider',
        'user_id',
        'partner_id',
        'status',
    ];

    /**
     * Get the user that is assigned to the SIM.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the partner that owns/assigned the SIM.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    /**
     * Get the requests associated with the SIM.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(SimRequest::class, 'sim_id');
    }
}
