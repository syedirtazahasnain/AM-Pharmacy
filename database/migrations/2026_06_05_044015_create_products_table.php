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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();  // e.g., "RESTAINE(ISOFURANE)100ML"
            $table->unsignedBigInteger('manufacturer_id')->nullable();
            $table->string('pack_size')->nullable();  // "1*1", "1X100"
            $table->decimal('mrp', 10, 2)->nullable();  // Maximum Retail Price
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('category')->nullable();  // Medicine, Surgical, Disposable
            $table->boolean('is_gst_applicable')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
