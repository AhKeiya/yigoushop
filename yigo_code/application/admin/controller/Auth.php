<?php
/**
 * 权限管理
 */

namespace app\admin\controller;

use think\Controller;
//权限用户
class Auth extends Controller
{
//    用户列表
    public function lst()
    {
        return $this->fetch();
    }

//    用户添加add
    public function add()
    {
        return $this->fetch();
    }

//    用户添加add
    public function edit()
    {
        return $this->fetch();
    }

//    用户删除
    public function del()
    {
        echo '删除';
    }

}