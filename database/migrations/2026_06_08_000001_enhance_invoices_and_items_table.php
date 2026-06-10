<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('salesperson')->nullable()->after('customer_id');
            $table->text('remarks')->nullable()->after('salesperson');
            $table->unsignedInteger('item_count')->default(0)->after('subtotal');
            $table->decimal('gross_total', 10, 2)->default(0)->after('item_count');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->default(0)->after('rate');
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_percent');
            $table->string('product_name')->nullable()->after('stock_batch_id');
            $table->string('manufacturer_name')->nullable()->after('product_name');
            $table->string('batch_number')->nullable()->after('manufacturer_name');
            $table->date('expiry_date')->nullable()->after('batch_number');
            $table->string('pack_size')->nullable()->after('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['salesperson', 'remarks', 'item_count', 'gross_total']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn([
                'amount', 'discount_value', 'product_name', 'manufacturer_name',
                'batch_number', 'expiry_date', 'pack_size',
            ]);
        });
    }
};
