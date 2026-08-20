<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['subscription_id', 'amount', 'method', 'reference_no', 'status', 'paid_at'];

    protected $casts = ['paid_at' => 'datetime'];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
