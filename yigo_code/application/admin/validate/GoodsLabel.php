<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 下午 9:49
 */

namespace app\admin\validate;


use think\Validate;

class GoodsLabel extends Validate
{
    protected $rule = [

        'sort|排序'=>'require|number',
    ];
}