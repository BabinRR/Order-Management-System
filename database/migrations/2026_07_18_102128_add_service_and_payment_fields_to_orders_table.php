<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('service_status')->default('pending')->after('status');
            $table->string('payment_status')->default('unpaid')->after('service_status');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->foreignId('served_by')->nullable()->after('worker_id')->constrained('users')->nullOnDelete();
            $table->timestamp('served_at')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('served_at');
        });

        DB::table('orders')->orderBy('id')->chunkById(100, function ($orders): void {
            foreach ($orders as $order) {
                $service = match ($order->status) {
                    'Preparing' => 'preparing',
                    'Served' => 'served',
                    'Completed' => 'served',
                    default => 'pending',
                };

                $payment = $order->status === 'Completed' ? 'paid' : 'unpaid';

                DB::table('orders')->where('id', $order->id)->update([
                    'service_status' => $service,
                    'payment_status' => $payment,
                    'payment_method' => $payment === 'paid' ? 'cash' : null,
                    'served_at' => $service === 'served' ? $order->updated_at : null,
                    'paid_at' => $payment === 'paid' ? $order->updated_at : null,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('served_by');
            $table->dropColumn([
                'service_status',
                'payment_status',
                'payment_method',
                'served_at',
                'paid_at',
            ]);
        });
    }
};
