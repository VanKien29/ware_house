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
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('storage_locations')->nullOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->restrictOnDelete();
            $table->decimal('quantity_on_hand', 14, 3)->default(0);
            $table->decimal('quantity_reserved', 14, 3)->default(0);
            $table->decimal('reorder_point', 14, 3)->nullable();
            $table->decimal('max_stock', 14, 3)->nullable();
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamps();

            $table->unique(['warehouse_id', 'location_id', 'product_variant_id'], 'stock_levels_unique_position');
            $table->index('product_variant_id');
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('storage_locations')->nullOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->restrictOnDelete();
            $table->enum('movement_type', [
                'opening_balance',
                'purchase_receipt',
                'sales_issue',
                'transfer_out',
                'transfer_in',
                'adjustment',
                'stock_count',
                'return_in',
                'return_out',
            ]);
            $table->enum('reference_type', [
                'purchase_order',
                'goods_receipt',
                'sales_order',
                'stock_transfer',
                'stock_adjustment',
                'inventory_count',
                'manual',
            ])->default('manual');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('quantity_change', 14, 3);
            $table->decimal('quantity_after', 14, 3);
            $table->decimal('unit_cost', 14, 2)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['warehouse_id', 'product_variant_id', 'created_at'], 'stock_movements_stock_time_index');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_levels');
    }
};
