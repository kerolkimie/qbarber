<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('barbers', function (Blueprint $table) {
            // 'commission' = ikut peratus komisen cawangan (sedia ada).
            // 'chair_rental' = bayar sewa kerusi tetap, simpan 100% hasil servis sendiri.
            $table->string('payment_type')->default('commission')->after('current_state');
            $table->decimal('rental_amount', 10, 2)->nullable()->after('payment_type');
            $table->string('rental_period')->nullable()->after('rental_amount'); // daily, weekly, monthly
        });
    }

    public function down(): void
    {
        Schema::table('barbers', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'rental_amount', 'rental_period']);
        });
    }
};
