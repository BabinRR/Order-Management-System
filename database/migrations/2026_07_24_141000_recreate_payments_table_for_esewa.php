<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payments');

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_uuid')->unique();
            $table->string('table_number');
            $table->unsignedInteger('amount');
            $table->string('status')->default('pending');
            $table->string('gateway')->default('esewa');
            $table->string('source');
            $table->json('order_ids');
            $table->string('transaction_code')->nullable();
            $table->string('ref_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
