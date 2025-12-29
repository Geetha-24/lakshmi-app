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
        Schema::create('customers', function (Blueprint $table) {
           $table->id();
            $table->string('name');
            $table->string('contactno');
            $table->string('whatsappno')->nullable();
            $table->string('location')->nullable();
            $table->string('mill_name')->nullable();
            $table->string('gst_number')->nullable();
            $table->integer('status');
            $table->integer('type')->nullable();  //0-wholeSale 1-Retail
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
