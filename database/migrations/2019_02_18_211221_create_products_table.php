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
            $table->string('title');            // 商品名称
            $table->text('description');        // 商品描述
            $table->string('image');            // 商品主图
            $table->boolean('on_sale')->default(true);  // 是否上架
            $table->float('rating')->default(5);    // 平均评分
            $table->unsignedInteger('sold_count')->default(0);  // 销量
            $table->unsignedInteger('review_count')->default(0);// 评价数量
            $table->decimal('price', 10, 2);    // 单品最低价格
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
