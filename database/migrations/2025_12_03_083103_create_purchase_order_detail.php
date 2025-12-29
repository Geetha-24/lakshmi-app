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
        Schema::create('po_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id');
            $table->foreign('po_id')->references('id')->on('purchase_order')->onDelete('cascade');
            $table->unsignedBigInteger('pv_id');
            $table->foreign('pv_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->integer('qunatity');
            $table->double('unit_price');
            $table->double('total_amount');
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
        Schema::dropIfExists('purchase_order_detail');
    }
};
