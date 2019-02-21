<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];

    // 声明以后 取出来是个 carbon 时间对象
    protected $dates = ['last_used_at']; 

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getFullAddressAttribute()
    {
        return ($this->province. ' ' .$this->city. ' ' .$this->district. ' ' .$this->address);
    }
}
