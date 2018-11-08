<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 下午 3:15
 */

namespace app\index\controller;


use think\Controller;
use app\index\model\Orderdata;

class Api extends Controller
{
    public function order()
    {
        return json((new Orderdata())->order1());
    }
}