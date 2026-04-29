<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shippingAddresses', function (Blueprint $table) {
            $table->bigIncrements('shippingAddressID');
            $table->unsignedBigInteger('userID');
            $table->foreign('userID')
                ->references('userID')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('fullName', 160);
            $table->string('phone', 20);

            $table->string('addressLine', 255);
            $table->string('city', 100);
            $table->string('province', 100);
            $table->string('country', 100)->default('Philippines');
            $table->string('postal', 20)->nullable();
            $table->string('landmark', 160)->nullable();

            $table->string('label', 30)->default('Home'); // Home / Office / Other
            $table->boolean('isDefault')->default(false);

            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->index(['userID', 'isDefault']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shippingAddresses');
    }
};

