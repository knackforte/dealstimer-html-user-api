<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_coupon', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->float('coupon_perc')->nullable();
            $table->unsignedInteger('coupon_val')->nullable();
            $table->boolean('is_active');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_coupon');
    }
}
