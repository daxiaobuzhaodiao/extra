<?php

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class UserAddressesSeeder extends Seeder
{
    public function run()
    {
        // ->each() 是 Collection 的一个方法，与 foreach 类似，
        // 循环集合中的每一个元素，将其作为参数传递给匿名函数，在这里集合里的元素都是 User 类型
        User::all()->each(function (User $user) {
            factory(UserAddress::class, random_int(1, 3))->create(['user_id' => $user->id]);
        });
    }
}