<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Agregar campos necesarios para MercadoPago
            $table->decimal('total_amount', 10, 2)->nullable()->after('notes');
            $table->string('payment_preference_id')->nullable()->after('total_amount');
            $table->string('payment_id')->nullable()->after('payment_preference_id');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending')->after('payment_id');
            $table->json('payment_details')->nullable()->after('payment_status');
            $table->string('payment_method')->nullable()->after('payment_details');
            $table->timestamp('payment_completed_at')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'total_amount',
                'payment_preference_id',
                'payment_id',
                'payment_status',
                'payment_details',
                'payment_method',
                'payment_completed_at'
            ]);
        });
    }
};