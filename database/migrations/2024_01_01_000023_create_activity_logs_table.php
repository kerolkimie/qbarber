<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // email_sent, subscription_selected, point_topup, commission_paid, account_activated_manual
            $table->string('type');
            $table->text('description');
            $table->string('subject_type')->nullable(); // model class rekod berkaitan (cth: App\Models\Subscription)
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('causer_type')->nullable(); // siapa buat tindakan (biasanya App\Models\User)
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->json('properties')->nullable(); // data tambahan (jumlah, emel, dll)
            $table->timestamps();

            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
