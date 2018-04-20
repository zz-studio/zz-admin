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

class Config extends Base
{
    // 数据完成配置
    protected $auto = ['name'];
    protected $insert = ['status' => 1];

    /**
     * 分组名称
     * @param $value
     * @param array $data
     * @return string
     */
    protected function getGroupTextAttr($value, $data = [])
    {
        $group = (array)setting('config_group_list');
        return isset($group[$data['group']]) ? $group[$data['group']] : '无';
    }

    /**
     * 类型名称
     * @param $value
     * @param array $data
     * @return string
     */
    protected function getTypeTextAttr($value, $data = [])
    {
        $type = (array)setting('config_type_list');
        return isset($type[$data['type']]) ? $type[$data['type']] : '无';
    }

    /**
     * 自动处理name字段
     * @param $value
     * @return string
     */
    protected function setNameAttr($value)
    {
        return strtolower($value);
    }

    /**
     * 获取配置列表
     * @return array 配置数组
     * @author Byron Sampson <xiaobo.sun@f2eer.net>
     */
    public function lists()
    {
        $map = ['status' => 1];
        $data = $this->where($map)->field('type, name, value')->select();

        $config = [];
        if (is_null($data)) {
            return $config;
        }
        foreach ($data as $value) {
            $config[$value['name']] = $this->parse($value['type'], $value['value']);
        }
        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param  integer $type 配置类型
     * @param  string $value 配置值
     * @author Byron Sampson <xiaobo.sun@f2eer.net>
     * @return array
     */
    private function parse($type, $value)
    {
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = [];
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }

}