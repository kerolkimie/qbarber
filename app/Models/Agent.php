<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = ['user_id', 'agent_code', 'commission_percent', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owners()
    {
        return $this->hasMany(Owner::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
}
