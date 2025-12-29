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
        Schema::create('purchase_batches', function (Blueprint $table) {
           $table->id();

           $table->unsignedBigInteger('po_id');
           $table->foreign('po_id')->references('id')->on('purchase_order')->onDelete('cascade');

           $table->unsignedBigInteger('po_detail_id');
           $table->foreign('po_detail_id')->references('id')->on('po_detail')->onDelete('cascade');


            // Relations
            $table->unsignedBigInteger('pv_id');
            $table->foreign('pv_id')->references('id')->on('product_variants')->onDelete('cascade');

            $table->string('batch_code')->nullable()->unique();
            $table->integer('purchased_quantity');
            $table->integer('total_stock_in');
            $table->integer('sold_qty')->default(0);

            // Purchase values
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);

            $table->boolean('is_sold')->default(false);

            $table->date('purchase_date');
            $table->integer('status')->default(0);
            $table->softDeletes();

            // Audit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_batches');
    }
};
