<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');         // 优惠券标题
            $table->string('code')->unique();         // 优惠券码 下单时使用
            $table->string('type');         // 优惠券类型，支持固定金额 和 百分比折扣
            $table->decimal('value');       // 折扣值，类型不同含义不同
            $table->unsignedInteger('total');// 全站可兑换数量
            $table->unsignedInteger('used')->default(0);// 当前已兑换数量
            $table->decimal('min_amount', 10, 2);  // 使用该优惠券的最低限额
            $table->dateTime('not_before')->nullable(); // 在此之前不可用
            $table->dateTime('not_after')->nullable();  // 在此之后不可用
            $table->boolean('enabled');     // 优惠券是否生效
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
        Schema::dropIfExists('coupon_codes');
    }
}
