<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('barber_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barber_id')->constrained();
            $table->date('shift_date');
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->timestamps();

            $table->unique(['barber_id', 'shift_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barber_shifts');
    }
};
