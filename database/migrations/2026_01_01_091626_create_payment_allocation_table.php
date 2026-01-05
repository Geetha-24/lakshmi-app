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
        Schema::create('payment_allocation', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('payment_id');
             $table->foreign('payment_id')->references('id')->on('payment')->onDelete('cascade'); 

             $table->unsignedBigInteger('so_id');
             $table->foreign('so_id')->references('id')->on('sales_orders')->onDelete('cascade'); 

           
            $table->decimal('allocated_amount', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_allocation');
    }
};
