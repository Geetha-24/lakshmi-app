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
            $table->decimal('line_profit',12,2)->default(0);
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->decimal('total_profit', 12, 2)->default(0);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
