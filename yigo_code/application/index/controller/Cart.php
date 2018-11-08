<?php
namespace app\index\controller;

use think\Db;

class Cart extends Base
{
    public function Cart()
    {
        if (request() -> isPost()) {
            dump(input('post.'));
            die();
        }
//        dump(session('uid'));
        // 当前用户的购物车
        $userCart = Db::name('goods_cart')
            -> where('uid', session('uid'))
            -> select();
        foreach ($userCart as $k => $v) {
            $goods = Db::name('goods')
                -> where('goods_id', $v['goods_id'])
                -> find();
            foreach ($goods as $i => $j) {
                // 判断是否含有这些字符串, 有的话就将其在数组中删除,
                // 这里不能使用 array_key_exists() 进行判断, 因为 $i 是一个字符串, 而不是一个数组
                if ($i == 'goods_id' || $i == 'create_time' || $i == 'update_time' || $i == 'delete_time') {
                    unset($goods[$i]);
                // 将分类全部遍历出来,组成一个字符串,添加到初始数组中
                } else if ($i == 'goods_classify_id') {
                    // 找到 goods_classify_id ,将其对应的值, 去数据库中查询, 并接收
                    $goods_classify = Db::name('goods_classify')
                        -> where('cid', $j)
                        -> find();
                    // 遍历新得到的数组, 获得 goods_classify_id 对应的名称
                    foreach ($goods_classify as $g => $h) {
                        if ($g == 'c_name') {
                            $userCart[$k]['goods_classify'] = $h;
                        // 初始数组中 goods_classify_id 莫名丢失,所以重新赋予
                        } else if ($g == 'cid') {
                            $userCart[$k]['goods_classify_id'] = $h;
                        }
                    }
                } else {
                    $userCart[$k][$i] = $j;
                }
            }
        }
        $this -> assign('userCart', $userCart);
//        dump($userCart);
//        die();
        return $this->fetch();//购物车
    }

    public function delete_null($arr)
    {
        if(!empty($arr))
        {  //去掉数组键值为空
            foreach($arr as $k => $v)
            {
                if(empty($v))
                {
                    unset($arr[$k]);
                }
            }
            $allscorearray = $arr;
            return $allscorearray;
        }
    }

    // 提交订单页面
    public function pay()
    {
        //根据session 在数据库中查找此用户名
        $user = Db::name('index_users') -> where('username', session('username')) -> find();
        // 根据所查找到的用户名的uid 在 用户优惠卷关联表中查找用户共有多少张优惠卷
        $userCoupon = Db::name('index_userconfig') -> where('uid', $user['uid']) -> select();
        // 遍历数据检查优惠卷是否已经过期过着已被使用, 再将优惠卷的 id 存储在一个数组中
        // 时间戳转日期 date('Y-m-d H:i:s')
        // 系统当前时间戳
        $date = time();
        // 创建一个空数组,用来保存优惠卷 id
        $couponid = array();
        foreach ($userCoupon as $k => $v) {
            // 判断优惠卷类型是否是未使用
            if ($v['usercou_state'] == 0) {
                // 将数据库中时间转化为时间戳,与现在时间戳对比
                if (strtotime($v['usercou_Dtime']) > $date) {
                    // 将符合条件的优惠卷 id 提取出来
                    $couponid[] = $v['cou_id'];
                } else {
                    // 将已经过期的优惠卷状态改为已使用
                    Db::name('index_userconfig') -> where('usercou_id', $v['usercou_id']) -> update(['usercou_state' => 2]);
                }
            }
        }
        // 遍历拿到的优惠卷数组,并将优惠卷的信息保存在新数组里
        $coupon = array();
        foreach ($couponid as $k => $v) {
            $coupon[] = Db::name('index_coupon') -> where('cou_id', $v) -> find();
            // 将时间格式转化为 年月日, 去掉当天时间
            if ($coupon[$k]['cou_Ctime']) {
                // 前端通过 {$cou.cou_Ctime|date='Y-m-d',###} 可以将时间戳转化为日期
                $coupon[$k]['cou_Ctime'] = strtotime($coupon[$k]['cou_Ctime']);
                $coupon[$k]['cou_Dtime'] = strtotime($coupon[$k]['cou_Dtime']);
            }
        }
        $coupon = $this -> delete_null($coupon);
        $this -> assign('coupon', $coupon);
        return $this -> fetch();
    }
}

