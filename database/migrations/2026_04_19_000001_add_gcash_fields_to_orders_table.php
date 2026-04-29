<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('gcashName', 100)->nullable()->after('paymentMethod');
            $table->string('gcashNumber', 20)->nullable()->after('gcashName');
            $table->string('referenceNumber', 30)->nullable()->after('gcashNumber');
            $table->decimal('amountPaid', 10, 2)->nullable()->after('referenceNumber');
            $table->string('paymentStatus', 20)->nullable()->after('amountPaid');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['gcashName', 'gcashNumber', 'referenceNumber', 'amountPaid', 'paymentStatus']);
        });
    }
};
