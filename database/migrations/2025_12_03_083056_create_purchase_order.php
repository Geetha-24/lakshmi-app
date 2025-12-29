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
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->string('invoice_number');
            $table->dateTime('invoice_date');
            $table->double('gross_amount');
            $table->double('tax_amount')->nullable();
            $table->double('discount_amount')->nullable();
            $table->double('net_amount')->nullable();
            $table->double('paid_amount')->nullable();
            $table->double('due_amount')->nullable();
            $table->integer('payment_status');
            $table->integer('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
