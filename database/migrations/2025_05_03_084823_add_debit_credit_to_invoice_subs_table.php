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
        Schema::table('invoice_subs', function (Blueprint $table) {
            $table->decimal('debit', 12,2)->nullable();
            $table->decimal('credit', 12,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_subs', function (Blueprint $table) {
            $table->dropColumn(['debit', 'credit']);
        });
    }
};
