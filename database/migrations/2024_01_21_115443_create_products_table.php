<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('tax_id')->constrained();
            $table->foreignId('special_tax_reason_id')->nullable()->constrained();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description');
            $table->string('type');
            $table->unsignedBigInteger('unit_type');
            $table->string('unit');
            $table->decimal('buying_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->boolean('is_buying_price_inclusive');
            $table->boolean('is_selling_price_inclusive');
            $table->string('sku');
            $table->string('barcode')->nullable();
            $table->boolean('is_sold');
            $table->boolean('is_bought');
            $table->boolean('track_quantity');
            $table->boolean('pos_product');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
