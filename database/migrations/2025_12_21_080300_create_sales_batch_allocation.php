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
        Schema::create('sales_batch_allocation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('so_id');
            $table->foreign('so_id')->references('id')->on('sales_orders')->onDelete('cascade'); 

            $table->unsignedBigInteger('so_detail_id');
            $table->foreign('so_detail_id')->references('id')->on('so_detail')->onDelete('cascade');    

            $table->unsignedBigInteger('pb_id');
            $table->foreign('pb_id')->references('id')->on('purchase_batches')->onDelete('cascade');    

            $table->integer('qty');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('profit', 12, 2);
            $table->integer('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_batch_allocation');
    }
};
