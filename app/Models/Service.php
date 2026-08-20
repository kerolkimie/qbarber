<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['branch_id', 'name', 'price', 'duration_minutes', 'status'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function queueTickets()
    {
        return $this->hasMany(QueueTicket::class);
    }
}
