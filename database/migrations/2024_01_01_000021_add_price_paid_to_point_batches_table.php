<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('point_batches', function (Blueprint $table) {
            // Simpan harga SEBENAR dibayar pada masa transaksi (rekod audit) —
            // supaya kekal tepat walaupun harga pakej ditukar admin kemudian.
            $table->decimal('price_paid', 10, 2)->nullable()->after('points_total');
            $table->foreignId('topup_package_id')->nullable()->after('price_paid')->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('point_batches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('topup_package_id');
            $table->dropColumn('price_paid');
        });
    }
};
