<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1 0001
 * Time: 下午 10:35
 */

namespace app\admin\model;

use think\Model;

class AdminUsers extends Model
{
    //会员添加 查看是否有这个用户
    public function memberadd($data)
    {
        //根据控制器层传递过来参数去查询数据库，
        $users = $this->where('username', $data['username'])->find();
//        dump($users['uid']);die;
        if ($users) {
            //对比数据，判断是否登录成功
            if ($users['password'] == $data['password']) {
                return $users['uid'];   //验证成功，返回该用户ID
            } else {
                return 2;   //用户名密码输入不正确返回  2
            }
        } else {
            return 0;     //没有这个用户  返回  零
        }
    }
}