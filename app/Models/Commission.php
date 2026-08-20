<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = ['agent_id', 'subscription_id', 'amount', 'percent', 'status', 'paid_at'];

    protected $casts = ['paid_at' => 'datetime'];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
