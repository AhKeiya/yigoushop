<?php
/**
 * Created by PhpStorm.
 * User: 54222
 * Date: 2018/7/28
 * Time: 15:46
 */

namespace app\index\controller;

use app\index\model\Index_coupon;
use app\index\model\Index_userconfig;
use think\Db;
use think\Loader;
use think\Session;

class Coupon extends Base
{
    public function coupon()
    {
        if (request() -> isPost()) {
            // 接受参数
            $data = input('post.');
            $validate = Loader::validate('Coupon');
            if (!$validate -> check($data)) {
                dump($validate -> getError());
            }
            // 当前时间
            $datetime = date('Y-m-d H:i:s');
            // date add 用法 往时间里添加 年月日
            $date=date_create($datetime);
            // 添加15天
            date_add($date,date_interval_create_from_date_string("15 days"));
            $datetime = date_format($date,"Y-m-d H:i:s");
            // 当存在 session 用户名 和 id 时
            if (Session::has('username') && Session::has('uid')) {
                // 数据库查找
                $rel = Db::name('index_userconfig')
                    -> where('uid', session('uid'))
                    -> where('cou_id', $data['cou_id'])
                    -> find();
                if ($rel){
                    $this -> error('优惠卷领取失败');
                } else {
                    $s = new Index_userconfig([
                        'uid' => session('uid'),
                        'cou_id' => $data['cou_id'],
                        'usercou_Dtime' => $datetime,
                    ]);
                    $s -> save();
                    $this -> success('优惠卷领取成功', '/index/coupon/coupon');
                }
            }
        }
        // 此数组用于保存所有不是为领取状态的优惠卷, 未领取优惠卷状态为 4
        // 仅针对于当前登陆的用户
        $newcouponid = array();
        //根据session 在数据库中查找此用户名
        $user = Db::name('index_users') -> where('username', session('username')) -> find();
        // 根据所查找到的用户名的uid 在 用户优惠卷关联表中查找用户共有多少张优惠卷
        $userCoupon = Db::name('index_userconfig') -> where('uid', $user['uid']) -> paginate(12);
        // 遍历数据检查优惠卷是否已经过期过着已被使用, 再将优惠卷的 id 存储在一个数组中
        // 时间戳转日期 date('Y-m-d H:i:s')
        // 系统当前时间戳
        $date = time();
        // 创建一个空数组,用来保存未使用的优惠卷 id
        $couponid = array();
        // 存储已使用的优惠卷 id
        $usedcouponid = array();
        // 存储已过期的优惠卷 id
        $expiredcouponid = array();
        foreach ($userCoupon as $k => $v) {
            // 判断优惠卷类型是否是未使用
            if ($v['usercou_state'] == 0) {
                // 将数据库中时间转化为时间戳,与现在时间戳对比
                if (strtotime($v['usercou_Dtime']) > $date) {
                    // 将符合条件的优惠卷 id 提取出来
                    $couponid[] = $v['cou_id'];
                    $newcouponid[] = $v['cou_id'];
                } else {
                    // 将已经过期的优惠卷状态改为已使用
                    Db::name('index_userconfig') -> where('usercou_id', $v['usercou_id']) -> update(['usercou_state' => 2]);
                }
            } else if ($v['usercou_state'] == 1) {
                // 当优惠卷状态为已使用的时候
                $usedcouponid[] = $v['cou_id'];
                $newcouponid[] = $v['cou_id'];
            } else if ($v['usercou_state'] == 2) {
                // 优惠卷状态为已过期
                $expiredcouponid[] = $v['cou_id'];
                $newcouponid[] = $v['cou_id'];
            } else {
                $this -> error('对不起出错了~');
            }
        }
        // 遍历拿到的优惠卷数组,并将优惠卷的信息保存在新数组里
        $coupon = array();
        if ($couponid) {
            foreach ($couponid as $k => $v) {
                $coupon[] = Db::name('index_coupon') -> where('cou_id', $v) -> find();
                // 将时间格式转化为 年月日, 去掉当天时间
                if ($coupon[$k]['cou_Ctime']) {
                    // 前端通过 {$cou.cou_Ctime|date='Y-m-d',###} 可以将时间戳转化为日期
                    $coupon[$k]['cou_Ctime'] = strtotime($coupon[$k]['cou_Ctime']);
                    $coupon[$k]['cou_Dtime'] = strtotime($coupon[$k]['cou_Dtime']);
                }
            }
        }
        // 已使用优惠卷
        $usedcoupon = array();
        if ($usedcouponid) {
            foreach ($usedcouponid as $k => $v) {
                $usedcoupon[] = Db::name('index_coupon') -> where('cou_id', $v) -> find();
                if ($usedcoupon[$k]['cou_Ctime']) {
                    $usedcoupon[$k]['cou_Ctime'] = strtotime($usedcoupon[$k]['cou_Ctime']);
                    $usedcoupon[$k]['cou_Dtime'] = strtotime($usedcoupon[$k]['cou_Dtime']);
                }
            }
        }
        // 已过期优惠卷
        $expiredcoupon = array();
        if ($expiredcouponid) {
            foreach ($expiredcouponid as $k => $v) {
                $expiredcoupon[] = Db::name('index_coupon')->where('cou_id', $v)->find();
                if ($expiredcoupon[$k]['cou_Ctime']) {
                    $expiredcoupon[$k]['cou_Ctime'] = strtotime($expiredcoupon[$k]['cou_Ctime']);
                    $expiredcoupon[$k]['cou_Dtime'] = strtotime($expiredcoupon[$k]['cou_Dtime']);
                }
            }
        }
        // 遍历可领取的优惠卷 id . 将$newcouponid 与数据库优惠卷所有 id 进行对比
        $allcoupon = new Index_coupon();
        // 获取模型中所有的数据
        $allcoupontime = $allcoupon -> all();
        // 创建一个空数组,用来保存可领取的优惠卷的 id
        $newcoupon = array();
        // $newcouponid 是所有非可领取优惠卷的 id
        foreach ($allcoupontime as $k => $v) {
            // 首先判断在优惠卷数据库, Index_coupon 中的过期时间, 如果过期则将其软删除
            if (strtotime($v['cou_Dtime']) < $date) {
                Index_coupon::destroy($v['cou_id']);
            // 优惠卷在有效期内时, 再判断优惠卷数据库中是否有优惠卷不存在于非可领取优惠卷的数组中
            } else if (!in_array($v['cou_id'], $newcouponid)) {
                $newcoupon[] = $v['cou_id'];
            }
        }
        $avaicoupon = array();
        foreach ($newcoupon as $k => $v) {
            $avaicoupon[] = $allcoupon -> where('cou_id', $v) -> find();
            if ($avaicoupon[$k]['cou_Ctime']) {
                $avaicoupon[$k]['cou_Ctime'] = strtotime($avaicoupon[$k]['cou_Ctime']);
                $avaicoupon[$k]['cou_Dtime'] = strtotime($avaicoupon[$k]['cou_Dtime']);
            }
        }
        // 未使用的优惠卷信息
        $this->assign('coupon', $coupon);
        // 已使用的优惠卷信息
        $this->assign('usedcoupon', $usedcoupon);
        // 已过期的优惠卷信息
        $this->assign('expiredcoupon', $expiredcoupon);
        // 可领取的优惠卷信息
        $this->assign('avaicoupon', $avaicoupon);
        return $this -> fetch();
    }
}