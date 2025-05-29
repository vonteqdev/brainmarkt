<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // For future relationships
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'name',
        'website',
        'industry',
        'contact_person',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the agency that owns the client.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    // Example for future relationships:
    // public function feedSources(): HasMany
    // {
    //     return $this->hasMany(FeedSource::class);
    // }
}
