<?php
/**
 * Created by PhpStorm.
 * User: zhoubairao
 * Date: 2018/7/31
 * Time: 下午9:08
 */

namespace app\admin\validate;


use think\Validate;

class AddCoupon extends Validate
{
    protected $rule = [
        //验证规则定义
        'cou_name|优惠券名称' => 'require|max:15',
        'cou_price|优惠卷价值' => 'require|number',
        'cou_rule|优惠券规则' => 'require|number',
        'cou_Ctime|优惠券可领取时间' => 'require',
        'cou_Dtime|优惠券结束时间' => 'require',
    ];

    //返回用户
    protected $message = [
        'cou_name.require' => '优惠券名称不可为空',
        'cou_name.max' => '优惠券名称不可以大于5位',
        'cou_price.require' => '优惠券价值不可为空',
        'cou_price.number' => '优惠券价值必须为数字',
        'cou_rule.require' => '优惠券满减规则不可为空',
        'cou_rule.number' => '优惠券满减规则必须为数字',
        'cou_Ctime.require' => '优惠券领取时间不可为空',
        'cou_Dtime.require' => '优惠券结束时间不可为空',
    ];

}