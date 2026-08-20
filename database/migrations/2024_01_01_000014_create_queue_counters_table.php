<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('queue_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->date('counter_date');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();

            $table->unique(['branch_id', 'counter_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_counters');
    }
};
