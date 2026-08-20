<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueTicket extends Model
{
    protected $fillable = [
        'queue_group_id', 'branch_id', 'service_id', 'barber_id', 'preferred_barber_id', 'ticket_number',
        'status', 'estimated_minutes', 'called_at', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function queueGroup()
    {
        return $this->belongsTo(QueueGroup::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function preferredBarber()
    {
        return $this->belongsTo(Barber::class, 'preferred_barber_id');
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'in_progress']);
    }
}
