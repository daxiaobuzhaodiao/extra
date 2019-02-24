<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

class HomeController extends Controller
{

    public function index(Faker $faker)
    {
        // dd($faker->name());      // 返回带有空格的 英文名字
        // dd($faker->words());     // 返回三个单词组成的一个数组
        // dd(Str::random(10));        // 返回大小写字母和数字组成的随机字符串
    }
}
