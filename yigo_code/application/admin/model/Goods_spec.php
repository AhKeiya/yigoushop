<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 上午 11:18
 */

namespace app\admin\model;


use think\Model;
use traits\model\SoftDelete;

class Goods_spec extends Model
{
    use SoftDelete;
    protected $deleteTime='delete_time';
}