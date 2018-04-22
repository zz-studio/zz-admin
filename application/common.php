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
use think\Db;
use think\facade\Config;
use think\facade\Hook;
use think\facade\Request;

// 应用公共文件

// 闭包自动处理插件钩子业务
Hook::add('app_init', function () {
    // 获取开关
    $autoload = (bool)Config::get('addons.autoload', false);
    // 配置自动加载时直接返回
    if ($autoload) {
        return;
    }
    // 非正时表示后台接管插件业务
    // 当debug时不缓存配置
    $config = config('app_debug') ? [] : (array)cache('addons');
    if (empty($config)) {
        // 读取插件钩子列表
        $hooks = Db::name('Hooks')->field('name,addons')->select();
        foreach ($hooks as $hook) {
            $config['hooks'][$hook['name']] = explode(',', $hook['addons']);
        }
        cache('addons', $config);
    }
    config('addons', $config);
});

// 向 Request 对象注入 referer 方法
Request::hook('referer', function($request, $url = null){
    if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
        $url = $request->url(true) == $_SERVER["HTTP_REFERER"] ? cookie('__forward__') : $_SERVER["HTTP_REFERER"];
    } elseif (is_string($url) && '' !== $url) {
        $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : Container::get('url')->build($url);
    }
    return $url;
});

/**
 * 递归创建某目录
 * @param unknown $dirname
 * @return boolean
 */
function mk_dirs($dirname){
    if (!is_dir($dirname)) {
        if (!mk_dirs(dirname($dirname))) {
            return false;
        }
        if (!mkdir($dirname, 0777)) {
            return false;
        }
    }
    return true;
}

/**
 * 递归删除某目录
 * @param string $dirname
 * @return boolean
 */
function rm_dirs($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            rm_dirs($dirname . DIRECTORY_SEPARATOR . $entry);
        }
    }
    $dir->close();
    return @rmdir($dirname);
}