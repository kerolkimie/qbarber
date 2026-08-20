<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Branch extends Model
{
    protected $fillable = ['owner_id', 'name', 'address', 'phone', 'qr_token', 'commission_percent', 'status'];

    protected static function booted()
    {
        static::creating(function (Branch $branch) {
            if (empty($branch->qr_token)) {
                $branch->qr_token = (string) Str::uuid();
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function barbers()
    {
        return $this->hasMany(Barber::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function queueGroups()
    {
        return $this->hasMany(QueueGroup::class);
    }

    public function queueTickets()
    {
        return $this->hasMany(QueueTicket::class);
    }
}
