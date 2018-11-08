<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 下午 4:52
 */

namespace app\admin\validate;

use think\Validate;

class Deploy extends Validate
{
//   物流管理验证规则
    protected $rule = [
        'logistics_cnname' => 'require',  //物流名称必填
    ];

//    物流管理验证提示 提示报错代码： $validate->getError()
    protected $message=[
        'logistics_cnname.require' => '物流名称不得为空',
    ];

    //    验证场景
    protected $scenc=[

    ];


}