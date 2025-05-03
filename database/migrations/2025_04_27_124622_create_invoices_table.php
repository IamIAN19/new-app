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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); 
            $table->string('tin')->index(); // index
            $table->string('supplier');
            $table->string('address');
            $table->decimal('vat_tax_amount', 12, 2)->nullable();
            $table->integer('vat_tax_percentage')->nullable();
            $table->decimal('vat_exempt', 12, 2)->nullable();
            $table->decimal('vat_zero_rated', 12, 2)->nullable();
    
            $table->unsignedBigInteger('sales_category_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('company_id')->index();
    
            $table->timestamps();
            $table->decimal('total_amount', 12, 2);
    
            // Foreign key constraints
            $table->foreign('sales_category_id')->references('id')->on('sales_categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
