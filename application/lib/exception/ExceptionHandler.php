<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/24 0024
 * Time: 下午 1:51
 */

namespace app\lib\exception;


use think\exception\Handle;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    // 重写render方法
    public function render(\Exception $e){
        if( config('app_debug') ){
            return parent::render($e);
        } else {
            $this->code = 500;
            $this->msg = "服务器内部错误";
            $this->errorCode = 500;
        }

        $request = \think\Request::instance();

        $result = [
            'msg' => $this->msg,
            'code' => $this->errorCode,
            'request_url' => $request->url()
        ];

        return json($result, $this->code);
    }
}