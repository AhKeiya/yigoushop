<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 下午 9:49
 */

namespace app\admin\validate;


use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        'goods_cnname|商品名'=>'require|chs|max:100',
        'goods_repertory|商品库存'=>'number|require',
        'goods_classify_id|商品分类'=>'require|notBetween:0,0',
        'goods_brand_id|品牌'=>'require|notBetween:0,0',
    ];
}