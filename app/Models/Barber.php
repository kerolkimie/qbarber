<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    protected $fillable = [
        'branch_id', 'user_id', 'name', 'status', 'current_state', 'last_completed_at',
        'payment_type', 'rental_amount', 'rental_period',
    ];

    protected $casts = ['last_completed_at' => 'datetime'];

    public const PERIOD_LABELS = [
        'daily' => 'sehari',
        'weekly' => 'seminggu',
        'monthly' => 'sebulan',
    ];

    public function isChairRental(): bool
    {
        return $this->payment_type === 'chair_rental';
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function queueTickets()
    {
        return $this->hasMany(QueueTicket::class);
    }

    public function shifts()
    {
        return $this->hasMany(BarberShift::class);
    }

    public function todayShift()
    {
        return $this->shifts()->whereDate('shift_date', today())->first();
    }

    /**
     * Barber aktif yang paling lama rehat (belum ada tugasan / paling lama siapkan tugasan terakhir).
     * Guna ini untuk auto-assign tiket seterusnya.
     */
    public static function nextAvailable(int $branchId): ?self
    {
        return static::where('branch_id', $branchId)
            ->where('status', 'active')
            ->where('current_state', 'available')
            ->orderByRaw('last_completed_at IS NULL DESC') // barber yang belum pernah buat kerja diutamakan
            ->orderBy('last_completed_at', 'asc')
            ->first();
    }
}
