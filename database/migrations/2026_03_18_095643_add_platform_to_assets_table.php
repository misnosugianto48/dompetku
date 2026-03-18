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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('platform')->nullable()->after('type'); // Bibit, Nanovest, etc.
            $table->decimal('quantity', 24, 8)->change();
            $table->decimal('purchase_price', 24, 4)->change();
            $table->decimal('current_price', 24, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('platform');
            $table->decimal('quantity', 15, 4)->change();
            $table->decimal('purchase_price', 15, 2)->change();
            $table->decimal('current_price', 15, 2)->change();
        });
    }
};
