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
        Schema::create('product', function (Blueprint $table) {
            $table->increments('product_id');
            $table->string('name');
            $table->float('price')->nullable();
            $table->string('permalink')->nullable();
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();
           
            $table->foreign('parent_id')->references('product_id')->on('product');
            $table->foreign('category_id')->references('category_id')->on('product_category');
            $table->foreign('created_by')->references('user_id')->on('users');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
}
