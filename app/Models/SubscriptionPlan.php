<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'price', 'points_included', 'duration_days', 'max_branches', 'max_barbers', 'is_per_branch_limit', 'features', 'status',
    ];

    protected $casts = [
        'is_per_branch_limit' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}
