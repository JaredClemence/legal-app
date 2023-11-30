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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->char('paypal_id',36);
            $table->char('status', 21)->default("PENDING");
            $table->char('intent', 20)->default("CAPTURE");
            $table->json('paypal_order_obj');
            $table->json('payment_source')->nullable();
            $table->json('purchase_units')->nullable();
            $table->json('payments')->nullable();
            $table->json('payer')->nullable();
            $table->json('links')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
