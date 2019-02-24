<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\CouponCodeUnavailableException;

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
    // 检查优惠券的有效性
    public function checkAvailable($orderAmount = null)
    {
        if (!$this->enabled) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        if ($this->total - $this->used <= 0) {
            throw new CouponCodeUnavailableException('该优惠券已被兑完');
        }

        if ($this->not_before && $this->not_before->gt(Carbon::now())) {
            throw new CouponCodeUnavailableException('该优惠券现在还不能使用');
        }

        if ($this->not_after && $this->not_after->lt(Carbon::now())) {
            throw new CouponCodeUnavailableException('该优惠券已过期');
        }

        // 订单金额不为空 并且 订单金额 小于 最低金额
        if (!is_null($orderAmount) && $orderAmount < $this->min_amount) {
            throw new CouponCodeUnavailableException('订单金额不满足该优惠券最低金额');
        }
    }
    // 获取 券后 订单的价格
    public function getAdjustedPrice($orderAmount)
    {
        // 固定金额
        if ($this->type === self::TYPE_FIXED) {
            // 为了保证系统健壮性，我们需要订单金额最少为 0.01 元
            return max(0.01, $orderAmount - $this->value);
        }

        return number_format($orderAmount * (100 - $this->value) / 100, 2, '.', '');
    }
    // 优惠券用量变更
    public function changeUsed($increase = true)
    {
        // 传入 true 代表新增用量，否则是减少用量
        if ($increase) {
            // 与检查 SKU 库存类似，这里需要检查当前用量是否已经超过总量
            return $this->newQuery()->where('id', $this->id)->where('used', '<', $this->total)->increment('used');
        } else {
            return $this->decrement('used');
        }
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
