<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('name');
            $table->string('picture');
            $table->string('permalink');
            $table->float('price');
            $table->float('sale_price');
            $table->boolean('in_stock');
            $table->string('discount');
            $table->unsignedInteger('type');
            $table->unsignedInteger('category');
            $table->unsignedInteger('sub_category');
            $table->foreign('type')->references('id')->on('product_type');
            $table->foreign('category')->references('id')->on('product_category');
            $table->foreign('sub_category')->references('id')->on('product_subcategory');
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
