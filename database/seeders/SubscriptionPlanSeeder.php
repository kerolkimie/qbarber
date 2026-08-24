<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'price' => 70,
                'points_included' => 0,
                'duration_days' => 30,
                'max_branches' => 1,
                'max_barbers' => 5,
                'is_per_branch_limit' => true,
                'features' => "Sesuai untuk kedai tunggal\nQR code tanpa had\nSkrin paparan giliran masa nyata\nSokongan emel",
            ],
            [
                'name' => 'Pro',
                'price' => 120,
                'points_included' => 0,
                'duration_days' => 30,
                'max_branches' => 2,
                'max_barbers' => 5,
                'is_per_branch_limit' => true,
                'features' => "Sesuai untuk 2 lokasi kecil\nSemua ciri Basic\nLaporan & analitik lanjutan\nSokongan keutamaan",
            ],
            [
                'name' => 'Premium',
                'price' => 180,
                'points_included' => 0,
                'duration_days' => 30,
                'max_branches' => 3,
                'max_barbers' => 5,
                'is_per_branch_limit' => true,
                'features' => "Untuk rangkaian barbershop berkembang\nSemua ciri Pro\nAkses API\nAkaun pengurus khusus",
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                array_merge($plan, ['status' => 'active'])
            );
        }

        try {
            SubscriptionPlan::whereIn('name', ['Starter', 'Standard'])->delete();
        } catch (\Throwable $e) {
            // Ada subscription sedia ada rujuk pakej lama ni — biarkan sahaja, tak kritikal.
        }
    }
}
