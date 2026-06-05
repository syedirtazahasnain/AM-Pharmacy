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
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->date('date')->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(18);
            $table->decimal('gst_value', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->text('warranty_notes')->nullable();
            $table->timestamps();
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
