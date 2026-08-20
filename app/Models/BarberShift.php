<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarberShift extends Model
{
    protected $fillable = ['barber_id', 'shift_date', 'clock_in', 'clock_out'];

    protected $casts = [
        'shift_date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}
