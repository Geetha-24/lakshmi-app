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
        Schema::table('so_detail', function (Blueprint $table) {
                $table->decimal('returned_qty', 10, 2)->default(0)->after('quantity')->nullable();

        });
        
        Schema::table('purchase_batches', function (Blueprint $table) {
                $table->decimal('returned_qty', 10, 2)->default(0)->after('sold_qty')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('so_detail', function (Blueprint $table) {
            //
        });
    }
};
