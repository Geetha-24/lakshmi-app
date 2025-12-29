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
        Schema::create('so_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('so_id');
            $table->foreign('so_id')->references('id')->on('sales_orders')->onDelete('cascade');            
            $table->unsignedBigInteger('pv_id');
            $table->foreign('pv_id')->references('id')->on('product_variants')->onDelete('cascade');            
            $table->decimal('quantity', 12, 2);
            $table->decimal('fixed_selling_price',12,2)->nullable()->default(0);
            $table->decimal('sold_price', 12, 2);
            $table->decimal('line_total', 12, 2)->nullable()->default(0);
            $table->integer('status')->default('0');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('so_detail');
    }
};
