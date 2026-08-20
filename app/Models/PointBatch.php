<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointBatch extends Model
{
    protected $fillable = [
        'owner_id', 'subscription_id', 'topup_package_id', 'source',
        'points_total', 'points_remaining', 'price_paid', 'granted_at', 'expires_at',
    ];

    protected $casts = [
        'granted_at' => 'date',
        'expires_at' => 'date',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function topupPackage()
    {
        return $this->belongsTo(TopupPackage::class);
    }
}
