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
        Schema::create('invoices_other_expenses', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->index();
            $table->integer('account_title_id')->index();
            $table->boolean('has_child')->default(0);
            $table->decimal('amount', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_other_expenses');
    }
};
