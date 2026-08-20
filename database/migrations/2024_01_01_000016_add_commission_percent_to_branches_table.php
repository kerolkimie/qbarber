<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Peratus komisen tukang gunting untuk cawangan ni. Cth: 40.00 = barber
            // dapat 40% dari harga servis yang dia siapkan, baki 60% untuk kedai.
            $table->decimal('commission_percent', 5, 2)->default(0)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('commission_percent');
        });
    }
};
