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
                $table->renameColumn('landed_cost_per_unit', 'SP_with_profit');
                $table->decimal('SP_without_profit', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po_detail', function (Blueprint $table) {
            //
        });
    }
};
