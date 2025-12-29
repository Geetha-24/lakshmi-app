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
        Schema::create('margin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pv_id')->unique();
            $table->foreign('pv_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->decimal('profit_amount', 10, 2)->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('margin');
    }
};
