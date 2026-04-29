<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orderItems', function (Blueprint $table) {
            $table->bigIncrements('orderItemID');
            $table->unsignedBigInteger('orderID');
            $table->foreign('orderID')
                  ->references('orderID')->on('orders')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('productID');
            $table->foreign('productID')
                  ->references('productID')->on('products')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->unsignedInteger('quantity');
            $table->decimal('unitPrice', 10, 2);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orderItems');
    }
};
