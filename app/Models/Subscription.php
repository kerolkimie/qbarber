<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'owner_id', 'plan_id', 'agent_id', 'start_date', 'end_date', 'amount_paid', 'status', 'toyyibpay_bill_code',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function commission()
    {
        return $this->hasOne(Commission::class);
    }
}
