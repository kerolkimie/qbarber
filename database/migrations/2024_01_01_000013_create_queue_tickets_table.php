<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_group_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('barber_id')->nullable()->constrained();
            $table->unsignedInteger('ticket_number');
            $table->string('status')->default('waiting'); // waiting, in_progress, completed, cancelled
            $table->unsignedInteger('estimated_minutes')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
    }
};
