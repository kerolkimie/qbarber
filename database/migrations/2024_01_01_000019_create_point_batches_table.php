<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('point_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained();
            $table->foreignId('subscription_id')->nullable()->constrained();
            $table->string('source')->default('subscription'); // subscription, topup
            $table->integer('points_total');
            $table->integer('points_remaining');
            $table->date('granted_at');
            $table->date('expires_at'); // granted_at + 3 bulan — carry forward tapi ada had luput
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_batches');
    }
};
