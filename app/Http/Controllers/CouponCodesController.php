<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use App\Exceptions\InvalidRequestException;

class CouponCodesController extends Controller
{
    // 检查优惠券是否有效
    public function show($code)
    {
        if (!$record = CouponCode::where('code', $code)->first()) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        $record->checkAvailable();
        
        return response()->json(['record' => $record]);
    }

}
