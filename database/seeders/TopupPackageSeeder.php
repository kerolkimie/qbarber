<?php

namespace Database\Seeders;

use App\Models\TopupPackage;
use Illuminate\Database\Seeder;

class TopupPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['points' => 20, 'price' => 20],
            ['points' => 50, 'price' => 45],
            ['points' => 100, 'price' => 85],
        ];

        foreach ($packages as $package) {
            TopupPackage::firstOrCreate(
                ['points' => $package['points']],
                array_merge($package, ['status' => 'active'])
            );
        }
    }
}
