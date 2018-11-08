<?php
/**
 * Created by PhpStorm.
 * User: 54222
 * Date: 2018/8/2
 * Time: 10:41
 */

namespace app\index\validate;


use think\Validate;

class Coupon extends Validate
{
    protected $rule = [
        'name' => 'length:32|token'
    ];

    protected $message = [
        '__token__.require' => '非法提交',
        '__token__.length' => '非法提交',
        '__token__.token'   => '请不要重复提交表单'
    ];

}