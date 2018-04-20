<?php
/**
 * +----------------------------------------------------------------------
 * | ZzStudio Admin Control [稚子网络工作室 - 后台管理模块]
 * +----------------------------------------------------------------------
 *  .--,       .--,             | FILE: Index.php
 * ( (  \.---./  ) )            | AUTHOR: byron sampson
 *  '.__/o   o\__.'             | EMAIL: xiaobo.sun@qq.com
 *     {=  ^  =}                | QQ: 150093589
 *      >  -  <                 | WECHAT: wx5ini99
 *     /       \                | DATETIME: 2018/4/19
 *    //       \\               |
 *   //|   .   |\\              |
 *   "'\       /'"_.-~^`'-.     |
 *      \  _  /--'         `    |
 *    ___)( )(___               |-----------------------------------------
 *   (((__) (__)))              | 高山仰止,景行行止.虽不能至,心向往之。
 * +----------------------------------------------------------------------
 * | Copyright (c) 2017 http://www.zzstudio.net All rights reserved.
 * +----------------------------------------------------------------------
 */

namespace app\http\exception;

use Exception;
use think\exception\HttpException;
use think\exception\ValidateException;

class Handle extends \think\exception\Handle
{
    /**
     * 系统异常处理接管
     * @param Exception $e
     * @return string|\think\Response|\think\response\Json
     */
    public function render(Exception $e)
    {
        // 参数验证错误
        if ($e instanceof ValidateException) {
            if (request()->isAjax()) {
                $result = [
                    'status' => 422,
                    'code' => 0,
                    'msg'  => $e->getMessage()
                ];
                return json($result);
            }
        }

        // 请求异常
        if ($e instanceof HttpException && request()->isAjax()) {
            $result = [
                'status' => $e->getStatusCode(),
                'code' => 0,
                'msg'  => $e->getMessage()
            ];
            return json($result);
        }

        // 其他错误交给系统处理
        return parent::render($e);
    }
}