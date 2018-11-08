<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25 0025
 * Time: 下午 5:56
 */

namespace app\admin\controller;

use think\Controller;
//权限组
class AuthGroup extends Controller
{
//    权限组列表
    public function lst()
    {
        return view();
    }

//     权限组添加
    public function add()
    {
        return view();
    }

//    权限组编辑
    public function edit()
    {
        return view();
    }

//    权限组删除
    public function del()
    {
        echo '删除成功';
    }
}