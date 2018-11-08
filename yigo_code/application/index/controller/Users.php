<?php

namespace app\index\controller;

use app\index\model\Userdata;
use think\Controller;
use app\index\model\Index_users as userModel;
use think\Db;
use think\Session;

class Users extends Controller
{
    public function login()
    {
        //登录接口
        if (request()->isPost()) {
            $data = input('post.');
            $user = new userModel();
//            if (captcha_check($data['code'])) {//判断验证码是否正确
            $res1 = $user->where('username', '=', $data['username'])->find();//查询是否是账号登录
            if ($res1) {//查询结果$res1不为空时执行
                if ($res1['password'] == ($data['password'])) {
                    Session::set('username', $res1['username']);
                    Session::set('uid', $res1['uid']);
                    return $this->success('登录成功', '/index/index/index');
                } else {
                    return $this->error('密码错误');
                }
            } else {//查询结果$res1为空时执行
                $res2 = $user->where('userphone', '=', $data['username'])->find();//查询是否是手机登录
                if ($res2) {
                    if ($res2['password'] == ($data['password'])) {
                        Session::set('username', $res2['username']);
                        Session::set('uid', $res2['uid']);
                        return $this->success('登录成功', '/index/index/index');
                    } else {
                        return $this->error('密码错误');
                    }
                } else {
                    return $this->error('用户名不存在');
                }
            }
//            } else {
//                return $this->error('验证码错误');
//            }
        }

        return $this->fetch();
    }

    /**
     * 用户注册
     */
    public function register()
    {
        if (request()->isPost()) {
            //注册前先判断数据库中账号是否存在，可以用validate类去验证
            
            $data = input('post.');
            $User = new userModel();
            $data['create_time'] = date('Y-m-d H:i:s');
            $res = $User -> allowField(true) -> save($data);
            // 写入数据库并返回新增数据的自增ID
            $uid = $User->getLastInsID();
            // 注册后自动添加用户默认信息
            $usersData = [
                'uid' => $uid,
                'thumb'=>'/iconUpload/20180801/defaultIcon.JPG',
                'nickname' => rand(),
                'birthday' => '',
                'create_time' => date('Y-m-d H:i:s'),
            ];
            Userdata::create($usersData);
            if ($res !== false) {
                $this->success('注册成功', '/index/users/login');
            } else {
                $this->error('注册失败');
            }
            return;
        }
        return $this->fetch();
    }

    /**
     * 退出登录
     */
    public function logout()
    {//清空session
        Session::clear();
        return $this->success('退出成功', '/index/index/index');
    }
}
