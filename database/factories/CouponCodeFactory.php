<?php

use Faker\Generator as Faker;
use App\Models\CouponCode;


$factory->define(CouponCode::class, function (Faker $faker) {
    // 随机获取类型
    $type = $faker->randomElement(array_keys(CouponCode::$typeMap));
    // 根据类型制作折扣
    $value = $type === CouponCode::TYPE_FIXED ? random_int(1, 200) : random_int(1, 50);

    // 如果是固定金额，则最低金额必须要比固定金额高 0.01 元
    if($type === CouponCode::TYPE_FIXED) {
        $minAmount = $value + 0.01;
    }else{
        // 如果是 百分比 折扣，有 50% 的概率不需要最低订单金额  （部分券没有最低消费金额限制）
        if(random_int(0, 100) < 50) {
            $minAmount = 0;
        }else{
            $minAmount = random_int(100, 1000);
        }
    }
    // $faker->words() == $faker->words 返回三个元素的数组
    return [
        'name' => join(' ',$faker->words),
        'code' => CouponCode::createAvailableCode(),    // CouponCode 模型中定义了该方法
        'type' => $type,
        'value' => $value,
        'total' => 1000,
        'used' => 0,
        'min_amount' => $minAmount,
        'not_before' => null,
        'not_after' => null,
        'enabled' => true
    ];
});

