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
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sr_id');
            $table->foreign('sr_id')->references('id')->on('sales_returns')->onDelete('cascade'); 
            
            $table->unsignedBigInteger('so_detail_id');
            $table->foreign('so_detail_id')->references('id')->on('so_detail')->onDelete('cascade'); 

            $table->unsignedBigInteger('pv_id');
            $table->foreign('pv_id')->references('id')->on('product_variants')->onDelete('cascade'); 

            $table->decimal('return_qty', 10, 2);
            $table->decimal('rate', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
    }
};
