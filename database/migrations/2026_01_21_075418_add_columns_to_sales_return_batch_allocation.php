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
        Schema::table('sales_return_batch_allocation', function (Blueprint $table) {

            $table->unsignedBigInteger('sr_id')->after('id');
            $table->foreign('sr_id')->references('id')->on('sales_returns')->onDelete('cascade');        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_return_batch_allocation', function (Blueprint $table) {
            //
        });
    }
};
