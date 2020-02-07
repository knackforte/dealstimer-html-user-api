<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTypeCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_type_category', function (Blueprint $table) {
            $table->unsignedInteger('product_type');
            $table->unsignedInteger('product_category');
            $table->foreign('product_type')->references('id')->on('product_type');
            $table->foreign('product_category')->references('id')->on('product_category');
            $table->primary(array('product_type', 'product_category'),'product_type_category_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_type_category');
    }
}
