<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('userID');
            $table->string('firstName', 100);
            $table->string('lastName', 100);
            $table->string('email', 255)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'customer'])->default('customer');
            $table->string('rememberToken', 100)->nullable();
            $table->timestamp('emailVerifiedAt')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('passwordResetTokens', function (Blueprint $table) {
            $table->string('email', 255)->primary();
            $table->string('token', 255);
            $table->timestamp('createdAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passwordResetTokens');
        Schema::dropIfExists('users');
    }
};
