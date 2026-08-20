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
                'price' => 70000000,
                'points_included' => 0,
                'duration_days' => 30,
                'max_branches' => 1,
                'max_barbers' => 5,
                'is_per_branch_limit' => true,
                'features' => "Sesuai untuk kedai yang baru buka\nNo angka giliran tanpa had\nTidak boleh pertukaran tukang gunting antara cawangan",
            ],
            [
                'name' => 'Pro',
                'price' => 130,
                'points_included' => 0,
                'duration_days' => 30,
                'max_branches' => 2,
                'max_barbers' => 5,
                'is_per_branch_limit' => true,
                'features' => "Sesuai untuk 2 lokasi yang berlainan\nNo angka giliran tanpa had\nBoleh pertukaran tukang gunting antara cawangan",
            ],
            [
                'name' => 'Premium',
                'price' => 250,
                'points_included' => 0,
                'duration_days' => 30,
                'max_branches' => 4,
                'max_barbers' => 5,
                'is_per_branch_limit' => true,
                'features' => "Sesuai untuk 1-4 lokasi yang berlainan\nNo angka giliran tanpa had\nBoleh pertukaran tukang gunting antara cawangan",
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
m