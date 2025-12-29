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
        Schema::table('po_detail', function (Blueprint $table) {
            $table->integer('tax_percentage')->nullable()->default(0);
            $table->decimal('tax_amount',10,2)->nullable()->default(0);
            $table->decimal('inc_tax_total_amount',10,2)->nullable()->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            //
        });
    }
};
