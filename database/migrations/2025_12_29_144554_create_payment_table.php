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
        Schema::create('payment', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('c_id');
            $table->foreign('c_id')->references('id')->on('customers')->onDelete('cascade');     

            $table->unsignedBigInteger('so_id')->nullable();
            $table->foreign('so_id')->references('id')->on('sales_orders')->onDelete('cascade');     

            
            $table->date('payment_date');

            $table->decimal('amount', 12, 2);

            $table->unsignedBigInteger('payment_mode_id');
            $table->foreign('payment_mode_id')->references('id')->on('payment_mode_master')->onDelete('cascade');     


            $table->string('reference_no')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
