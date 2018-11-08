<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 下午 9:49
 */

namespace app\admin\validate;


use think\Validate;

class Goods_classify extends Validate
{
    protected $rule = [

        'c_name|分类名'=>'require|chs|unique:Goods_classify',
    ];
}