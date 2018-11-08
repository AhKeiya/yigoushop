<?php
/**
 * Created by PhpStorm.
 * User: zouzhikang
 * Date: 2018/7/28
 * Time: 下午3:08
 */

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class Site extends Model
{
    //设置软删除
    use SoftDelete;
    protected static $deleteTime = 'delete_time';
    protected $updateTime = false; //忽略更新字段
}