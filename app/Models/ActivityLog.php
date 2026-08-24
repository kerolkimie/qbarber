<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'type', 'description', 'subject_type', 'subject_id',
        'causer_type', 'causer_id', 'properties',
    ];

    protected $casts = ['properties' => 'array'];

    public const TYPE_LABELS = [
        'email_sent' => 'Emel Dihantar',
        'subscription_selected' => 'Pemilihan Subscription',
        'point_topup' => 'Topup Point',
        'point_manual_grant' => 'Point Diberi Manual (Admin)',
        'commission_paid' => 'Komisen Dibayar',
        'account_activated_manual' => 'Akaun Diaktifkan (Manual)',
        'barber_transferred' => 'Tukang Gunting Dipindahkan',
        'trial_granted' => 'Tempoh Percubaan Diberi',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function causer()
    {
        return $this->morphTo();
    }

    /**
     * Rekod satu log aktiviti. $subject = model berkaitan (Subscription/PointBatch/
     * Commission/User), $properties = data tambahan (jumlah, emel, dll) untuk rujukan.
     */
    public static function record(string $type, string $description, $subject = null, array $properties = []): self
    {
        $causer = Auth::user();

        return static::create([
            'type' => $type,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer?->id,
            'properties' => $properties,
        ]);
    }
}
