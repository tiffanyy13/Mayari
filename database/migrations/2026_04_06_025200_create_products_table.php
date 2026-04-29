<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('productID');
            $table->unsignedBigInteger('categoryID');
            $table->foreign('categoryID')
                  ->references('categoryID')->on('categories')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->string('pName', 150);
            $table->text('descript')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('image', 255)->default('default.png');
            $table->tinyInteger('isArchived')->default(0);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
