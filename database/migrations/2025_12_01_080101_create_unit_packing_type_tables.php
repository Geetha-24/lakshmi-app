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
        Schema::create('units_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });

         Schema::create('packing_types_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units_table');
        Schema::dropIfExists('packing_types_table');

    }
};
