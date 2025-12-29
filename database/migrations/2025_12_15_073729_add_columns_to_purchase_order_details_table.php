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
            $table->integer('posted_qty')->default(0);
            $table->boolean('is_posted')->default(false);
            $table->timestamp('posted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_columns_to_purchase_order_details');
    }
};
