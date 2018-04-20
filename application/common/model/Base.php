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

class Base extends \think\Model
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    /**
     * list_order设置器
     * @param int $val
     * @return int|mixed
     */
    protected function setListOrderAttr($val = 0)
    {
        if (!$val) {
            $val = $this->max($this->getPk()) + 1;
        }
        return $val;
    }

    /**
     * 排序列表
     * @param array $list
     * @param string $pk 主键
     * @param string $sort 排序字段
     * @return array|false|\think\Collection
     * @throws \Exception
     */
    public function listSort($list = [], $pk = 'id', $sort = 'list_order')
    {
        $pk = empty($pk) ? $this->getPk() : $pk;

        $dataSet = [];
        foreach ($list as $id => $order) {
            $dataSet[] = [$pk => $id, $sort => $order];
        }

        return $this->saveAll($dataSet);
    }

    /**
     * 快速排序
     * @param int $id
     * @param string $exp
     * @return bool
     * @throws \Exception
     */
    public function sort($id = 0, $exp = '=')
    {
        if ($exp == '>=') {
            $order = 'asc';
        } else {
            $order = 'desc';
        }
        $list_order = $this->where(['id'=>$id])->value('list_order');
        $list = $this->where('list_order', $exp, $list_order)->limit(2)->order('list_order', $order)->column('id,list_order');
        if (count($list) < 2) {
            return false;
        }

        $first = array_shift($list);
        $last = array_shift($list);

        $this->saveAll([
            ['id' => $first['id'], 'list_order' => $last['list_order']],
            ['id' => $last['id'], 'list_order' => $first['list_order']]
        ]);

        return true;
    }
}