<?php
/**
 * 订单查询验证
 */

namespace app\admin\validate;


use think\Validate;

class Addorder extends Validate
{
//   订单查询验证规则
    protected $rule = [
        'select' => 'require',  //搜索查询关键字
    ];

//    订单查询验证提示 提示报错代码： $validate->getError()
    protected $message = [
        'select.require' => '请输入查询关键字',
    ];

    //    验证场景
    protected $scenc = [

    ];
}