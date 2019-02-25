<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;

class CouponCodesController extends Controller
{
    // 检查优惠券是否有效
    public function show($code, Request $request)
    {
        if (!$record = CouponCode::where('code', $code)->first()) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        $record->checkAvailable($request->user());
        
        return response()->json(['record' => $record]);
    }

}
