<?php
/**
 * Created by PhpStorm.
 * User: zhoubairao
 * Date: 2018/7/31
 * Time: 下午12:09
 */

namespace app\admin\model;


use think\Model;
use traits\model\SoftDelete;//使用软删除功能必须要引入SoftDelete
class IndexCoupon extends  Model
{
    //设置软删除
    use SoftDelete;//使用软删除必须要在类里面调用SoftDelete
    protected static $deleteTime = "delete_time";//传入数据库需要软删除的字段赋值给新变量   使用protected和静态变量
}