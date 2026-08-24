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

    /**
     * Rekod subscription STATUS='active' PALING BARU dalam DB — guna ni untuk
     * paparan sejarah/rujukan umum SAHAJA. Untuk KAWALAN HAD/CIRI SEBENAR,
     * guna effectiveSubscription() sebaliknya (ambil kira jadual pakej akan datang).
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    /**
     * Subscription yang BENAR-BENAR berkuat kuasa HARI INI — boleh jadi rekod
     * 'active' (mula serta-merta) ATAU rekod 'scheduled' yang tarikh mula dia
     * dah sampai. Ni sumber KEBENARAN untuk kira had cawangan/kerusi.
     */
    public function effectiveSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->whereIn('status', ['active', 'scheduled'])
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->orderByDesc('start_date')
            ->first();
    }

    /**
     * Subscription yang DIJADUALKAN untuk mula pada masa hadapan (belum berkuat
     * kuasa lagi) — untuk paparan "Pakej X akan bermula pada [tarikh]".
     */
    public function upcomingSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'scheduled')
            ->where('start_date', '>', today())
            ->orderBy('start_date')
            ->first();
    }

    /**
     * Tarikh subscription BARU patut mula — hari ini kalau tiada subscription
     * lain yang masih meliputi masa hadapan, atau esok selepas subscription
     * yang sedang berkuat kuasa/berjadual tamat (elak bertindih).
     */
    public function nextSubscriptionStartDate(): \Carbon\Carbon
    {
        $latestEnd = $this->subscriptions()
            ->whereIn('status', ['active', 'scheduled'])
            ->where('end_date', '>=', today())
            ->max('end_date');

        return $latestEnd ? \Carbon\Carbon::parse($latestEnd)->addDay() : today()->copy();
    }

    /**
     * Pakej BARU ni upgrade (harga lebih tinggi) berbanding pakej semasa/
     * berjadual ke? Upgrade = kuatkuasa SERTA-MERTA. Downgrade/sama harga =
     * DIJADUALKAN bermula lepas pakej sedia ada tamat.
     */
    public function isUpgrade(SubscriptionPlan $newPlan): bool
    {
        $current = $this->effectiveSubscription()?->plan ?? $this->upcomingSubscription()?->plan;

        if (! $current) {
            return true; // tiada pakej sedia ada — anggap "upgrade" (terus kuatkuasa)
        }

        return $newPlan->price > $current->price;
    }

    public function hasValidSubscription(): bool
    {
        return $this->effectiveSubscription() !== null;
    }

    public function currentPlan(): ?SubscriptionPlan
    {
        return $this->effectiveSubscription()?->plan;
    }

    /**
     * Had bilangan cawangan ikut pakej BERKUAT KUASA HARI INI. Tiada
     * subscription aktif = 0 (elak owner tanpa pakej terus boleh tambah cawangan).
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
     * Cawangan ni "terkunci" (boleh lihat sahaja, tak boleh edit) sebab
     * melebihi had pakej semasa ke? Cawangan PALING AWAL dicipta (ikut had)
     * kekal boleh diurus; cawangan LEBIHAN (dicipta lepas had) dikunci.
     */
    public function isBranchLocked(Branch $branch): bool
    {
        $limit = $this->branchLimit();

        $rank = $this->branches()
            ->orderBy('created_at')
            ->pluck('id')
            ->search($branch->id);

        return $rank === false || $rank >= $limit;
    }

    /**
     * Had bilangan kerusi (tukang gunting) ikut pakej BERKUAT KUASA HARI INI.
     * Kalau pakej "per cawangan" (cth: Premium), had ni terpakai UNTUK SETIAP
     * cawangan. Kalau tidak, had ni jumlah KESELURUHAN merentasi semua cawangan.
     */
    public function chairLimit(): int
    {
        return $this->currentPlan()?->max_barbers ?? 0;
    }

    public function isPerBranchChairLimit(): bool
    {
        return (bool) $this->currentPlan()?->is_per_branch_limit;
    }

    public function canAddBarberToBranch(Branch $branch): bool
    {
        $limit = $this->chairLimit();

        if ($limit <= 0) {
            return false;
        }

        if ($this->isPerBranchChairLimit()) {
            return $branch->barbers()->where('status', 'active')->count() < $limit;
        }

        $branchIds = $this->branches()->pluck('id');
        $totalActiveBarbers = Barber::whereIn('branch_id', $branchIds)->where('status', 'active')->count();

        return $totalActiveBarbers < $limit;
    }

    public function totalActiveBarbers(): int
    {
        $branchIds = $this->branches()->pluck('id');

        return Barber::whereIn('branch_id', $branchIds)->where('status', 'active')->count();
    }

    public function isOverBranchLimit(): bool
    {
        return $this->branches()->count() > $this->branchLimit();
    }

    public function isOverChairLimit(): bool
    {
        $limit = $this->chairLimit();

        if ($limit <= 0) {
            return false;
        }

        if ($this->isPerBranchChairLimit()) {
            return $this->branches()->get()->contains(
                fn ($branch) => $branch->barbers()->where('status', 'active')->count() > $limit
            );
        }

        return $this->totalActiveBarbers() > $limit;
    }
}
