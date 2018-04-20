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
namespace app\common\behavior;

use think\facade\Config;
use think\facade\Env;

class Init
{
    /**
     * 应用初始化行为
     */
    public function run()
    {
        $request = request();
        $module = $request->module();
        $controller = $request->controller();
        $action = $request->action();
        $root = $request->root();

        // 如果api模块则跳出自定义行为
        if (strtolower($module) == 'api') {
            return;
        }

        // debug时关闭模板编译缓存
        if (Env::get('app_debug')) {
            Config::set('template.tpl_cache', false);
        }

        // 模板相关配置
        $template['tpl_replace_string'] = [
            '__URL__' => $request->url(),
            '__ROOT__' => $root . "/",
            '__STATIC__' => $root . "/static", //"http://static.xxx.com",
            '__IMG__' => $root . "/static/${module}/images",
            '__CSS__' => $root . "/static/${module}/css",
            '__JS__' => $root . "/static/${module}/js",
        ];
        Config::set($template, 'template');
    }
}