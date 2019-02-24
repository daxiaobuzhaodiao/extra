<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    protected $fillable = [
        'no', 'address', 'total_amount', 'remark', 'paid_at',
        'payment_method', 'payment_no', 'refund_status', 'refund_no',
        'closed', 'reviewed', 'ship_status', 'ship_data', 'extra'
    ];

    protected $casts = [
        'closed' => 'boolean',
        'reviewed' => 'boolean',
        'address' => 'json',
        'ship_data' => 'json',
        'extra' => 'json'
    ];

    protected $dates = ['paid_at'];

    // boot() 通常用来注册与这个模型 增 删 改相关事件的回调 效果与 observer 相同
    // 监听模型创建事件，在写入数据库之前触发，此时生成订单号
    protected static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            // 如果模型的订单号为空
            if(!$model->no) {
                // 创建订单号
                $model->no = static::findAvailableNo();
                // 如果创建失败，则终止创建订单
                if(!$model->no) {
                    return false;
                }
            }
        });
    }
    // 创建唯一订单号
    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for($i = 0; $i <10; $i++) {
            // 随机生成6位数字
            $no = $prefix.str_pad(random_int(0, 999999), 6, 0, STR_PAD_LEFT);
            // 判断是否已经存在
            if(!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        \Log::warning('find order no failed');
        return false;
    }

    // 关联 User 模型
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    // 关联 OrderItem 模型
    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItem');
    }
    // 关联 CouponCode 模型
    public function couponCode()
    {
        return $this->belongsTo('App\Models\CouponCode');
    }

    // 创建唯一退款单号
    public static function getAvailableRefundNo()
    {
        do{
            // Uuid类可以用来生成大概率不重复的字符串
            $no = Uuid::uuid4()->getHex();
        }while(self::query()->where('refund_no', $no)->exists());

        return $no;
    }
    
}
