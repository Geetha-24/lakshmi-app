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
        Schema::create('sales_return_batch_allocation', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sales_return_item_id');
            $table->foreign('sales_return_item_id')->references('id')->on('sales_return_items')->onDelete('cascade');

            $table->unsignedBigInteger('pb_id');
            $table->foreign('pb_id')->references('id')->on('purchase_batches')->onDelete('cascade');

            $table->decimal('qty_in', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_batch_allocation');
    }
};
