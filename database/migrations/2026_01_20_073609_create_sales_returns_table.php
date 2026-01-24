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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('so_id');
            $table->foreign('so_id')->references('id')->on('sales_orders')->onDelete('cascade'); 

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); 

            $table->string('return_no')->unique();
            $table->date('return_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->enum('refund_type', ['CREDIT', 'CASH', 'BANK'])->default('CREDIT');
            $table->enum('status', ['DRAFT', 'CONFIRMED'])->default('DRAFT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
