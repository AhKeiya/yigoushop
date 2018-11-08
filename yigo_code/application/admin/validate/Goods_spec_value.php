<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 下午 9:49
 */

namespace app\admin\validate;


use think\Validate;

class Goods_spec_value extends Validate
{
    protected $rule = [

        'spec_name|规格名'=>'require|chs|unique:Goods_spec_value|max:100',
        'value|规格值'=>'chsDash|require',
    ];
}