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
        Schema::create('customer_ledgers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('c_id');
            $table->foreign('c_id')->references('id')->on('customers')->onDelete('cascade'); 

            $table->date('date');

            $table->enum('type', ['DEBIT', 'CREDIT']);

            $table->string('reference_type'); // sales_order, payment

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->decimal('amount', 12, 2);

            $table->decimal('balance_after', 12, 2);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_ledgers');
    }
};
