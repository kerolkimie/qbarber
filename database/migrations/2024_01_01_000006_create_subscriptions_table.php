<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained();
            $table->foreignId('plan_id')->constrained('subscription_plans');
            $table->foreignId('agent_id')->nullable()->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('amount_paid', 10, 2);
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
