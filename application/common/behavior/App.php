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
use think\facade\Cache;
use think\facade\Env;
use app\common\model\Config AS Setting;

class App
{
    /**
     * 行为初始化
     * @param $params
     */
    public function run($params)
    {
        // 当前版本
        define('SYS_VERSION', '1.0.0 beta');

        // 读取数据库中的配置
        $config = Env::get('app_debug') ? [] : Cache::pull('DB_CONFIG_DATA');
        // 如果未发现配置缓存则从数据库获取
        if (empty($config)) {
            $Setting = new Setting;
            $config = $Setting->lists();
            Cache::set('DB_CONFIG_DATA', $config);
        }

        // 系统创始人user_id
        $config['founder'] = 1;

        Config::set($config, 'cfg'); //添加配置
    }
}