<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueGroup extends Model
{
    protected $fillable = ['branch_id', 'pax', 'customer_name', 'customer_phone'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function tickets()
    {
        return $this->hasMany(QueueTicket::class);
    }
}
