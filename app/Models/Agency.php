<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'contact_person_name',
        'contact_email',
        'phone_number',
        'address',
        'website',
        'logo_url',
        'timezone',
        'currency_preference',
        // 'subscription_plan_id',
        // 'subscription_status',
    ];

    /**
     * Get the users associated with the agency.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the clients managed by the agency.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
