<?php
/**
 * Created by PhpStorm.
 * User: zhoubairao
 * Date: 2018/7/31
 * Time: 下午9:08
 */

namespace app\admin\validate;


use think\Validate;

class Goods_off_add extends Validate
{
    protected $rule = [
        //验证规则定义
        'goods_off_name|折扣名称' => 'require|max:15',
        'goods_off_m|输入折扣率' => 'require|number',
        'start_time|选择开始时间' => 'require|date',
        'end_time|选择结束时间' => 'require|date',
    ];

    //返回用户
    protected $message = [
        'goods_off_name.require' => '商品折扣名称不可为空',
        'goods_off_name.max' => '商品折扣不可以大于5位',
        'goods_off_m.require' => '输入折扣率不可为空',
        'goods_off_m.number' => '商品折扣率为数字',
        'start_time.require' => '开始时间不可为空',
        'start_time.date' => '开始时间格式必须为日期',
        'end_time.require' => '结束日期不可为空',
        'end_time.date' => '结束时间必须为日期格式',
    ];

}