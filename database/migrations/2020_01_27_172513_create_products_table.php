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
            $table->float('price');
            $table->boolean('in_stock');
            $table->unsignedInteger('type');
            $table->unsignedInteger('category');
            $table->unsignedInteger('sub_category');
            $table->string('permalink')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('created_by');
            $table->foreign('parent_id')->references('id')->on('products');
            $table->foreign('type')->references('id')->on('product_type');
            $table->foreign('category')->references('id')->on('product_category');
            $table->foreign('sub_category')->references('id')->on('product_subcategory');
            $table->foreign('created_by')->references('id')->on('users');
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
