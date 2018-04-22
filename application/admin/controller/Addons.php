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

use think\Db;
use think\Container;
use think\facade\Env;

class Addons extends Base
{
    /**
     * 插件管理
     * @return mixed
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $q = input('q', '');
            $map = [];
            if ($q) {
                $map[] = ['name|title', 'like', "%{$q}%"];
            }
            $list = model('Addons')->where($map)->paginate($this->limit);
            return $this->toTable($list);
        }
        return $this->fetch();
    }

    /**
     * 预览插件
     */
    public function preview()
    {
        $data = $this->request->post();
        $tpl = model('Addons')->data($data)->preview();
        if (false === $tpl) {
            $this->error(model('Addons')->getError() ?: '预览失败');
        }
        return $this->success($tpl);
    }

    /**
     * 快速创建插件
     * @return mixed
     */
    public function create()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = model('Addons')->data($data)->build();
            if (false !== $ret) {
                $this->success('创建成功', url('index#uninstalled'));
            }
            $this->error('创建失败');
        }
        $this->assign('hooks', model('Hooks')->field('name')->order('id asc')->select());
        cookie('__forward__', $this->request->referer());
        return $this->fetch();
    }

    /**
     * 未安装插件列表
     * @return mixed
     */
    public function uninstalled()
    {
        if ($this->request->isAjax()) {
            $addons = [];
            $addons_path = Env::get('addons_path');

            // 获取已存在的插件
            $addons_arr = model('Addons')->column('id,name,title', 'name');

            //扫描插件文件夹
            $files = scandir($addons_path);
            $id = 1;
            foreach ($files as $file) {
                // 跳过已安装的插件
                if (isset($addons_arr[strtolower($file)])) {
                    continue;
                }
                // 处理未安装的插件
                if ($file != '.' && $file != '..' && is_dir($addons_path . $file)) {
                    if ($object = $this->getInstance($file)) {
                        $addons[] = array_merge(['id' => $id], $object->getInfo(), ['setting' => $object->getConfig()]);
                        $id++;
                    }
                }
            }

            $list = ['data' => $addons];
            return $this->toTable($list);
        }
    }

    /**
     * 安装插件
     */
    public function install()
    {
        $name = input('name', '');
        if (!$name) {
            $this->error('参数错误');
        }
        if ($object = $this->getInstance($name)) {
            $data = $object->getInfo();
            $data['setting'] = $object->getConfig();
            if (model('Addons')->where(['name' => $data['name']])->count()) {
                $this->error('当前插件已存在');
            }
            // 读取插件目录及钩子列表
            $base = get_class_methods("\\think\\Addons");
            // 读取出所有公共方法
            $methods = (array)get_class_methods($object);
            // 跟插件基类方法做比对，得到差异结果
            $hooks = array_diff($methods, $base);
            // 查询钩子信息
            if (!empty($hooks)) {
                $hooks = model('Hooks')->where('name', 'in', $hooks)->select();
                $hooklist = [];
                foreach ($hooks as $hook) {
                    $addons = explode(',', $hook['addons']);
                    array_push($addons, $name);
                    $addons = array_filter(array_unique($addons));
                    $hooklist[] = [
                        'id' => $hook['id'],
                        'addons' => implode(',', $addons)
                    ];
                }
            }
            Db::startTrans();
            try {
                model('Addons')->allowField('name,title,author,version,description,is_admin,is_index,setting')->save($data);
                if (isset($hooklist) && !empty($hooklist)) {
                    model('Hooks')->saveAll($hooklist);
                }
                if (false !== $object->install()) {
                    Db::commit();
                }
            } catch (\Exception $e) {
                Db::rollback();
                $this->error('安装异常');
            }
            $this->success('安装成功');
        }
        $this->error('安装失败');
    }

    /**
     * 卸载插件
     */
    public function uninstall()
    {
        $name = input('name', '');
        if (!$name) {
            $this->error('参数错误');
        }
        $info = model('Addons')->where(['name' => $name])->find();
        if ($info && $object = $this->getInstance($name)) {
            // 获取所有相关钩子
            $hooks = model('Hooks')->where('find_in_set(:name, addons)', ['name' => $name])->select();
            $hooklist = [];
            foreach ($hooks as $hook) {
                $addons = explode(',', $hook['addons']);
                $addons = array_diff($addons, [$name]);
                $addons = array_filter(array_unique($addons));
                $hooklist[] = [
                    'id' => $hook['id'],
                    'addons' => implode(',', $addons)
                ];
            }
            // 开启事务
            Db::startTrans();
            try {
                // 删除插件
                $info->delete();
                // 删除钩子
                if (!empty($hooklist)) {
                    model('Hooks')->saveAll($hooklist);
                }
                if (false !== $object->uninstall()) {
                    Db::commit();
                }
            } catch (\Exception $e) {
                Db::rollback();
                $this->error('卸载异常');
            }
            $this->success('卸载成功');
        }
        $this->error('卸载失败');
    }

    /**
     * 删除插件
     */
    public function delete()
    {
        $name = input('name', '');
        $addons_path = Env::get('addons_path');
        if ($name) {
            rm_dirs($addons_path . $name);
            $this->success('删除成功');
        }
        $this->error('参数错误');
    }

    /**
     * 获取插件实例
     * @param $file
     * @return bool|object
     */
    protected function getInstance($file)
    {
        $class = "\\addons\\{$file}\\" . ucfirst($file);
        if (class_exists($class)) {
            return Container::get($class);
        }
        return false;
    }
}
