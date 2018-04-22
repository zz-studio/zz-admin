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

namespace app\common\model;

use think\facade\Env;

class Addons extends Base
{
    // 数据完成配置
    protected $auto = ['update_time'];
    protected $insert = ['install_time', 'status' => 1];
    protected $createTime = 'install_time';
    protected $append = ['status_text'];
    protected $type = [
        'setting' => 'json'
    ];

    /**
     * status_text 获取器
     * @return mixed|string
     */
    protected function getStatusTextAttr()
    {
        $text = '';
        $status = $this->getData('status');
        switch ($status) {
            case 1:
                $text = '正常';
                break;
            case 0:
                $text = '禁用';
        }
        return $text;
    }

    /**
     * 预览插件
     * @param array $data
     * @return bool|string
     */
    public function preview($data = [])
    {
        $data = array_merge($this->getData(), $data);
        $data['status'] = 1;
        $data['hooks'] = isset($data['hooks']) ? $data['hooks'] : [];

        $hook = '';
        foreach ($data['hooks'] as $value) {
            $hook .= <<<str
    // 实现的 {$value} 钩子方法
    public function {$value}(\$param){
    
    }

str;
        }
        $classname = ucfirst($data['name']);
        $namespace = 'addons\\' . $data['name'];
        $tpl = <<<str
<?php

namespace {$namespace};
use think\Addons;

/**
 * {$data['title']}插件
 * @author {$data['author']}
 */
class {$classname} extends Addons
{
    public \$info = array(
        'name'=>'{$data['name']}',
        'title'=>'{$data['title']}',
        'description'=>'{$data['description']}',
        'status'=>{$data['status']},
        'author'=>'{$data['author']}',
        'version'=>'{$data['version']}'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

{$hook}
}
str;
        return $tpl;
    }


    public function build($data = [])
    {
        $data = array_merge($this->getData(), $data);

        $addonFile = $this->preview ();
        $addons_path = Env::get('addons_path');

        // 创建目录结构
        $files = array ();
        $addon_dir = "$addons_path{$data['name']}/";
        $files [] = $addon_dir;
        $addon_name = ucfirst($data['name']).".php";

        // 如果有前后台入口
        if (isset($data['is_admin']) || isset($data['is_index'])) {
            $files[] = "{$addon_dir}controller/";
            $files[] = "{$addon_dir}model/";
        }

        foreach ($files as $dir) {
            if (!mk_dirs($dir)) {
                $this->error = '插件' . $data['name'] . '目录存在';
                return false;
            }
        }

        // 写文件
        file_put_contents( "{$addon_dir}{$addon_name}", $addonFile);

        // 如果有配置文件
        if (isset($data['is_config'] ) && $data['is_config'] == 1) {
            $config = <<<str
// 插件配置
return [
	'title'     => [//配置在表单中的键名 ,这个会是config[title]
		'title' => '显示标题:',//表单的文字
		'type'  => 'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value' => '系统信息',			 //表单的默认值
	],
	'display'   => [
		'title' => '是否显示:',
		'type'  => 'radio',
		'options'   => [
			'1' => '显示',
			'0' => '不显示'
		],
		'value' => '1'
	]
];
str;
            file_put_contents($addon_dir . 'config.php', $config);
        }

        // 如果存在后台
        if (isset($data['is_admin']) && $data['is_admin'] == 1) {
            $adminController = <<<str
namespace addons\demo\controller;

use think\addons\Controller;

class Admin extends Controller
{
    // 索引入口
    public function index()
    {
        return 'hello addons admin';
    }
}
str;
            file_put_contents("{$addon_dir}controller/admin.php", $adminController);
        }

        // 如果存在前台
        if (isset($data['is_index']) && $data['is_index'] == 1) {
            $indexController = <<<str
namespace addons\demo\controller;

use think\addons\Controller;

class Index extends Controller
{
    // 索引入口
    public function index()
    {
        return 'hello addons index';
    }
}

str;
            file_put_contents("{$addon_dir}controller/index.php", $indexController);
        }

        return true;
    }
}