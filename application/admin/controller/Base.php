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

namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    // 分页变量
    protected $page = 0;
    protected $limit = 10;

    /**
     * 构造方法
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->page = input('page', 0);
        $this->limit = input('limit', 10);
    }

    /**
     * 返回 table 所需格式
     * @param array $list
     * @return mixed
     */
    protected function toTable($list = [])
    {
        if (is_object($list)) {
            $list = $list->toArray();
        }

        $data = [
            'code' => 0,
            'msg' => 'ok',
            'count' => $list['total'] ?: count($list)
        ];

        return json(array_merge($data, $list));
    }
}
