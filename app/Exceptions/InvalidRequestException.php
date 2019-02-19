<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    function __construct(string $message = '', int $code = 400)
    {
        parent::__construct($message, $code);
    }

    // Laravel 5.5 之后支持在异常类中定义 render() 方法，该异常被触发时系统会调用 render() 方法来输出
    public function render(Request $request)
    {
        // 如果是 ajax 请求 则返回 json 格式错误信息
        if($request->expectsJson()) {
            return response()->json(['msg' => $this->message], $this->code);
        }
        // 跳转到自定义错误页面
        return view('pages.error', ['msg' => $this->message]);
    }
}
