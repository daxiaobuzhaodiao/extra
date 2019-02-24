<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponCode extends Model
{
    // 像 order 模型一样，用常量的方法表示 coupon 的类型
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED => '固定金额',
        self::TYPE_PERCENT => '比例'
    ];
    
    protected $fillable = [
        'name', 'code', 'type', 'value', 'total', 'used', 'min_amount', 'not_before', 'not_after', 'enabled'
    ];
    
    protected $casts = [
        'enabled' => 'boolean'
    ];
    // 这两个字段转换成 carbon 对象
    protected $dates = [
        'not_before', 'not_after'
    ];
    
    // 优惠券码生成
    public static function createAvailableCode($length = 16)
    {
        do{
            $code = strtoupper(Str::random($length));
        }while(self::query()->where('code', $code)->exists());
        return $code;
    }

    // 加一个临时字段   获取这个 description 这个属性的时候会返回 下边方法返回的值  下面的属性和方法是成对出现的
    protected $appends = ['description', 'usag'];
    public function getDescriptionAttribute()
    {
        $str = '';
        if($this->min_amount > 0) {
            $str = '满'.str_replace('.00', '', $this->min_amount);
        }
        if($this->type === self::TYPE_PERCENT) {
            return $str.'优惠'.str_replace('.00', '', $this->value).'%';
        }
        return $str.'减'.str_replace('.00', '', $this->value);
    }

    public function getUsagAttribute()
    {
        return $this->used.' / '.$this->total;
    }
}
