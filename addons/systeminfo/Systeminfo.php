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
namespace addons\systeminfo;

use think\Db;
use think\Addons;

/**
 * 系统环境信息插件
 * @author byron sampson
 */
class Systeminfo extends Addons
{
    public $info = [
        'name' => 'systeminfo',
        'title' => '系统环境信息',
        'description' => '用于显示一些服务器的信息',
        'status' => 0,
        'author' => 'byron sampson',
        'version' => '0.1'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现的 adminIndex 钩子方法
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    public function adminIndex($param)
    {
        $config = $this->getConfig();
        $this->assign('addons_config', $config);
        $this->assign('system_info_mysql', Db::query("select version() as v;"));
        if ($config['display']) {
            return $this->fetch('widget');
        }
    }

}