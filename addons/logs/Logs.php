<?php

namespace addons\logs;

use think\Addons;

/**
 * 历史日志插件
 * @author zz-admin
 */
class Logs extends Addons
{
    public $info = array(
        'name' => 'logs',
        'title' => '历史日志',
        'description' => '',
        'status' => 1,
        'author' => 'zz-admin',
        'version' => '0.1'
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    // 实现的 adminIndex 钩子方法
    public function adminIndex($param)
    {
        $this->assign('addons_info', $this->info);
        return $this->fetch('widget');
    }

}