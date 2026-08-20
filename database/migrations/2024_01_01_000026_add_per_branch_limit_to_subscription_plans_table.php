<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            // true = had kerusi (max_barbers) terpakai SETIAP cawangan (cth: Premium 5/cawangan).
            // false = had kerusi terpakai JUMLAH KESELURUHAN merentasi semua cawangan (cth: Pro 5 total).
            $table->boolean('is_per_branch_limit')->default(false)->after('max_barbers');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('is_per_branch_limit');
        });
    }
};
