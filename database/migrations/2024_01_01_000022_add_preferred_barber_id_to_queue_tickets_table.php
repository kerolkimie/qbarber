<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('queue_tickets', function (Blueprint $table) {
            // Pilihan tukang gunting oleh pelanggan (optional). NULL = tiada pilihan,
            // sesiapa pun boleh panggil tiket ni.
            $table->foreignId('preferred_barber_id')->nullable()->after('barber_id')->constrained('barbers');
        });
    }

    public function down(): void
    {
        Schema::table('queue_tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('preferred_barber_id');
        });
    }
};
