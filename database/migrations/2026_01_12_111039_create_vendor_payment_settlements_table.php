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
        Schema::create('vendor_payment_settlements', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('vendor_payment_id');
            $table->foreign('vendor_payment_id')->references('id')->on('vendor_payments')->onDelete('cascade');

            $table->unsignedBigInteger('po_id');
            $table->foreign('po_id')->references('id')->on('purchase_order')->onDelete('cascade');

            $table->decimal('settled_amount', 15, 2);
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payment_settlements');
    }
};
