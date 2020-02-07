<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_product', function (Blueprint $table) {
            $table->increments('id');
            $table->string('picture');
            $table->string('permalink');
            $table->float('price');
            $table->boolean('in_stock');
            $table->unsignedInteger('quantity');
            $table->string('color_hex');
            $table->string('color_display_name');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('vendor_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('vendor_id')->references('id')->on('users');
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
        Schema::dropIfExists('vendor_product');
    }
}
