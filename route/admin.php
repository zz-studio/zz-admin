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

// 系统钩子管理验证
Route::get('admin/hooks/add', 'admin/hooks/add');
Route::post('admin/hooks/add', 'admin/hooks/add')->validate('app\common\validate\Hooks', 'add');
Route::get('admin/hooks/edit', 'admin/hooks/edit')->validate(['id' => 'require|number']);
Route::post('admin/hooks/edit', 'admin/hooks/edit')->model('id', 'app\common\model\Hooks')->validate('app\common\validate\Hooks', 'edit');

// 系统插件验证
Route::post('admin/addons/preview', 'admin/addons/preview')->mergeExtraVars()->validate('app\common\validate\Addons');
Route::get('admin/addons/create', 'admin/addons/create');
Route::post('admin/addons/create', 'admin/addons/create')->validate('app\common\validate\Addons');