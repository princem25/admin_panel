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
    Schema::connection('analytics')->create('order_analytics', function (Blueprint $table) {
        $table->id();
        $table->integer('total_orders');
        $table->decimal('total_revenue', 10, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('analytics')->dropIfExists('order_analytics');
    }
};
