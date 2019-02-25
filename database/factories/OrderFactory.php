<?php

use App\Models\User;
use App\Models\Order;
use App\Models\CouponCode;
use Faker\Generator as Faker;

$factory->define(App\Models\Order::class, function (Faker $faker) {
    // 获取一个随机用户
    $user = User::query()->inRandomOrder()->first();
    // 随机取一个该用户的地址
    $address = $user->addresses()->inRandomOrder()->first();
    // 10% 概率将订单标记为退款
    $refund = random_int(10, 100) < 10;
    // 随机生成订单发货状态
    $ship = $faker->randomElement(array_keys(Order::$shipStatusMap));
    // 优惠券
    $coupon = null;
    // 30% 的概率使用了优惠券
    if(random_int(0, 10) < 3) {
        // 为了避免出现逻辑错误，我们只选择没有最低金额限制的优惠券
        $coupon = CouponCode::query()->where('min_amount', 0)->inRandomOrder()->first();
        // 更新用量
        $coupon->changeUsed();
    }

    return [
        'address'        => [
            'address'       => $address->full_address,
            'zip'           => $address->zip,
            'contact_name'  => $address->contact_name,
            'contact_phone' => $address->contact_phone,
        ],
        'total_amount'   => 0, // 暂时设置为 0  OrdersSeeder 文件中更新赋值
        'remark'         => $faker->sentence,
        'paid_at'        => $faker->dateTimeBetween('-30 days'), // 30天前到现在任意时间点
        'payment_method' => $faker->randomElement(['wechat', 'alipay']),
        'payment_no'     => $faker->uuid,
        'refund_status'  => $refund ? Order::REFUND_STATUS_SUCCESS : Order::REFUND_STATUS_PENDING,
        'refund_no'      => $refund ? Order::getAvailableRefundNo() : null,
        'closed'         => false,
        'reviewed'       => random_int(0, 10) > 2,
        'ship_status'    => $ship,
        'ship_data'      => $ship === Order::SHIP_STATUS_PENDING ? null : [
            'express_company' => $faker->company,
            'express_no'      => $faker->uuid,
        ],
        'extra'          => $refund ? ['refund_reason' => $faker->sentence] : [],
        'user_id'        => $user->id,
        'coupon_code_id' => $coupon ? $coupon->id : null,
    ];
});
