<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InternalException extends Exception
{
    protected $msgForUser;
    /**
     * 500 Internal Server Error    
     * 不需要让用户知道具体错误信息，所以自定义错误信息返回给用户
     * @param 1 系统错误信息
     * @param 2 自定义错误信息，显示给用户看的
     * @param 3 错误码
     */
    
    function __construct(string $message = "", string $msgForUser = '系统内部错误', int $code = 500)
    {
        parent::__construct($message, $code);
        $this->msgForUser = $msgForUser;
    }

    public function render(Request $request)
    {
        if($request->expectsJson()){
            return response()->json(['msg' => $this->msgForUser], $this->code);
        }

        return view('pages.error', ['msg' => $this->msgForUser], $this->code);
    }
}
