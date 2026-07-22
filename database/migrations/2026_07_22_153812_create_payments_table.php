<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_order_id')->unique();
            $table->string('pidx')->nullable()->index();
            $table->string('table_number');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('amount_paisa');
            $table->string('status')->default('pending');
            $table->string('gateway')->default('khalti');
            $table->string('source');
            $table->json('order_ids');
            $table->string('transaction_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_url')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
