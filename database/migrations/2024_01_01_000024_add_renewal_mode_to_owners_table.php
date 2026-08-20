<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            // 'online' = perbaharui pakej lalui ToyyibPay (payment gateway sebenar).
            // 'offline' = admin/owner boleh terus topup tanpa payment gateway
            // (cth: owner bayar cash/bank transfer terus kepada kedai/admin).
            $table->string('renewal_mode')->default('online')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn('renewal_mode');
        });
    }
};
