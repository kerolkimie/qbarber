<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueCounter extends Model
{
    protected $fillable = ['branch_id', 'counter_date', 'last_number'];

    protected $casts = ['counter_date' => 'date'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Dapatkan nombor giliran seterusnya untuk cawangan pada hari ini (atomic-safe guna lockForUpdate).
     */
    public static function nextNumber(int $branchId): int
    {
        return \DB::transaction(function () use ($branchId) {
            $counter = static::where('branch_id', $branchId)
                ->whereDate('counter_date', today())
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = static::create([
                    'branch_id' => $branchId,
                    'counter_date' => today(),
                    'last_number' => 0,
                ]);
            }

            $counter->increment('last_number');

            return $counter->last_number;
        });
    }
}
