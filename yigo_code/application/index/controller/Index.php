<?php

namespace app\index\controller;

use think\Controller;
use think\Db;


class Index extends Controller
{
    public function index()
    {
        $pic = Db::name('admin_adver')->select();
        $this->assign('pic',$pic);
//        dump($pic);die();
        return $this->fetch();//商城主页
    }

    public function contact_us()
    {
        return $this->fetch();//联系我们
    }

    public function sale()
    {
        return $this->fetch();//打折商品
    }

    public function hot()
    {
        return $this->fetch();//热卖商品
    }

    public function newitem()
    {
        return $this->fetch();//最新商品
    }

    public function myyg()
    {
        return $this->fetch();
    }

    public function collection()
    {
        return $this->fetch();//商品分类
    }

    public function all()
    {
        return $this->fetch();
    }

    public function product()
    {
        if (session('username')) {//用户登录之后才能自动添加足迹
            $fpmodel = new fpmodel();
            $searchnum = $fpmodel->where('uid', '=', "session('uid')")->count();//查询登录的用户足迹数量
            if ($searchnum = 10) {
                $del = $fpmodel->where('uid', '=', "session('uid')")->limit(1)->order('fpid', asc)->delete();
            }
            $data = ['uid' => "session('uid')", 'goods_id' => "input('goods_id')"];
            $res = $fpmodel->save($data);
            return;
        }
        return $this->fetch();//商品购买
    }
}
