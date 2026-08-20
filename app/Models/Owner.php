<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $fillable = ['user_id', 'agent_id', 'business_name', 'status', 'renewal_mode'];

    public function isOnlineRenewal(): bool
    {
        return $this->renewal_mode !== 'offline';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    /**
     * Subscription aktif DAN belum tamat tempoh (end_date >= hari ini).
     * Guna ni untuk kawal akses ciri (bukan setakat status='active' dalam DB,
     * sebab subscription lama mungkin tak sempat auto-expired lagi statusnya).
     */
    public function hasValidSubscription(): bool
    {
        $sub = $this->activeSubscription;

        return $sub !== null && ! $sub->end_date->isPast();
    }

    public function currentPlan(): ?SubscriptionPlan
    {
        return $this->activeSubscription?->plan;
    }

    /**
     * Had bilangan cawangan ikut pakej semasa. Tiada subscription aktif = 0
     * (elak owner tanpa pakej terus boleh tambah cawangan tanpa had).
     */
    public function branchLimit(): int
    {
        return $this->currentPlan()?->max_branches ?? 0;
    }

    public function canAddBranch(): bool
    {
        return $this->branches()->count() < $this->branchLimit();
    }

    /**
     * Had bilangan kerusi (tukang gunting). Kalau pakej "per cawangan"
     * (cth: Premium), had ni terpakai UNTUK SETIAP cawangan. Kalau tidak,
     * had ni terpakai sebagai JUMLAH KESELURUHAN merentasi semua cawangan.
     */
    public function chairLimit(): int
    {
        return $this->currentPlan()?->max_barbers ?? 0;
    }

    public function isPerBranchChairLimit(): bool
    {
        return (bool) $this->currentPlan()?->is_per_branch_limit;
    }

    /**
     * Boleh tambah tukang gunting baru pada cawangan ni ke?
     */
    public function canAddBarberToBranch(Branch $branch): bool
    {
        $limit = $this->chairLimit();

        if ($limit <= 0) {
            return false; // tiada pakej aktif langsung
        }

        if ($this->isPerBranchChairLimit()) {
            return $branch->barbers()->where('status', 'active')->count() < $limit;
        }

        // Had jumlah keseluruhan merentasi semua cawangan owner ni.
        $branchIds = $this->branches()->pluck('id');
        $totalActiveBarbers = Barber::whereIn('branch_id', $branchIds)->where('status', 'active')->count();

        return $totalActiveBarbers < $limit;
    }

    /**
     * Jumlah kerusi digunakan sekarang (untuk paparan "3/5 kerusi digunakan").
     */
    public function totalActiveBarbers(): int
    {
        $branchIds = $this->branches()->pluck('id');

        return Barber::whereIn('branch_id', $branchIds)->where('status', 'active')->count();
    }
}
