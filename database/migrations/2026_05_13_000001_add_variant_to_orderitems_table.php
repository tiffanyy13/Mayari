<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orderitems', function (Blueprint $table) {
            $table->string('variant', 255)->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('orderitems', function (Blueprint $table) {
            $table->dropColumn('variant');
        });
    }
};
