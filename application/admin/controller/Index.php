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

class Index extends Base
{
    /**
     * 后台索引
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    public function main()
    {
        return $this->fetch();
    }

    /**
     * 系统菜单
     * @return string|\think\response\Json
     */
    public function navs()
    {
        $navs = [
            [
                "title" => "系统扩展",
                "icon" => "fa-cubes",
                "href" => url('extend/index'),
                "spread" => true,
                "children" => [
                    [
                        "title" => "插件管理",
                        "icon" => "fa-plug",
                        "href" => url('addons/index'),
                        "spread" => false
                    ],
                    [
                        "title" => "钩子管理",
                        "icon" => "fa-anchor",
                        "href" => url('hooks/index'),
                        "spread" => false
                    ]
                ]
            ]
        ];
        return json($navs);
    }
}
