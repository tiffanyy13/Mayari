<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('orderID');
            $table->unsignedBigInteger('userID');
            $table->foreign('userID')
                  ->references('userID')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->string('deliveryAdd', 255);
            $table->string('city', 100);
            $table->string('province', 100);
            $table->string('country', 100)->default('Philippines');
            $table->string('postal', 20);
            $table->string('contactNo', 20)->nullable();
            $table->enum('paymentMethod', ['cod', 'ecard']);
            $table->enum('status', [
                'Pending', 'Accepted', 'Shipped', 'Delivered', 'Canceled'
            ])->default('Pending');
            $table->decimal('deliveryFee', 8, 2)->default(6.99);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
