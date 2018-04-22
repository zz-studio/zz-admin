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

class Hooks extends Base
{
    /**
     *  钩子管理
     * @return mixed
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $q = input('q', '');
            $map = [];
            if ($q) {
                $map[] = ['name|description', 'like', "%{$q}%"];
            }
            $list = model('Hooks')->where($map)->paginate($this->limit);
            return $this->toTable($list);
        }
        return $this->fetch();
    }

    /**
     * 快速更新某个字段
     */
    public function field()
    {
        $id = input('post.id', 0);
        $field = input('post.column', '');
        $value = input('post.value', '');
        if (!$id || !$field) {
            $this->error('参数错误');
        }

        $info = model('Hooks')->where(['id'=>$id])->find();
        if ($info) {
            $ret = $info->data($field, $value)->save();
            if (false !== $ret) {
                $this->success('更新完成');
            }
        }
        $this->error('操作失败');
    }

    /**
     * 添加钩子
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = model('Hooks')->allowField('name,description')->save($data);
            if (false == $ret) {
                $this->error('创建失败');
            }
            $this->success('创建成功', cookie('__forward__'));
        }
        cookie('__forward__', $this->request->referer());
        return $this->fetch();
    }

    /**
     * 编辑钩子
     * @return mixed
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = model('Hooks')->allowField('name,description')->save($data, ['id'=>$data['id']]);
            if (false == $ret) {
                $this->error('保存失败');
            }
            $this->success('保存成功', cookie('__forward__'));
        }
        $id = input('get.id/d', 0);
        $this->assign('info', model('Hooks')->get($id));
        cookie('__forward__', $this->request->referer());
        return $this->fetch();
    }

    /**
     * 删除数据，支持批量
     */
    public function delete()
    {
        $ids = explode(',', input('ids', ''));
        if (empty($ids)) {
            $this->error('参数错误');
        }
        $ret = model('Hooks')->where('id', 'in', $ids)->delete();
        if ($ret) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }
}
