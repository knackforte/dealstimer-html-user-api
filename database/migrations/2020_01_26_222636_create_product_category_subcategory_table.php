<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategorySubcategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_category_subcategory', function (Blueprint $table) {
            $table->unsignedInteger('product_category');
            $table->unsignedInteger('product_subcategory');
            $table->foreign('product_category')->references('id')->on('product_category');
            $table->foreign('product_subcategory')->references('id')->on('product_subcategory');
            $table->primary(array('product_category', 'product_subcategory'),'product_category_subcategory_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_category_subcategory');
    }
}
