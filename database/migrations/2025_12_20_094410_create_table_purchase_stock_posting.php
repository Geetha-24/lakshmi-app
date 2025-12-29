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
        Schema::create('purchase_stock_posting', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('po_detail_id');
           $table->foreign('po_detail_id')->references('id')->on('po_detail')->onDelete('cascade');
            $table->string('batch_code')->nullable();
            $table->integer('quantity');
            $table->integer('posted_qty');
            $table->integer('remaining_qty');
            $table->date('posting_date');
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
        Schema::dropIfExists('table_purchase_stock_posting');
    }
};
