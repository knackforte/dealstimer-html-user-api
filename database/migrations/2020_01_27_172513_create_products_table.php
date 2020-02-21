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
            $table->float('price')->nullable();
            $table->string('permalink')->nullable();
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();
           
            $table->foreign('parent_id')->references('id')->on('products');
            $table->foreign('category_id')->references('id')->on('product_category');
            $table->foreign('created_by')->references('id')->on('users');
            
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
