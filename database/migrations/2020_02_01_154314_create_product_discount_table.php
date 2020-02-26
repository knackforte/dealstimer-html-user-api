<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_discount', function (Blueprint $table) {
            $table->increments('discount_id');
            $table->unsignedInteger('product_id');
            $table->float('discount_perc')->nullable();
            $table->unsignedInteger('discount_val')->nullable();
            $table->boolean('is_active');
            $table->foreign('product_id')->references('product_id')->on('product');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_discount');
    }
}
