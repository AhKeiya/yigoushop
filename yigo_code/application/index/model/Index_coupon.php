<?php
/**
 * Created by PhpStorm.
 * User: 54222
 * Date: 2018/7/30
 * Time: 18:30
 */

namespace app\index\model;


use think\Model;
use traits\model\SoftDelete;

/**
 * Class Index_coupon
 * @package app\index\model
 */
class Index_coupon extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $updateTime = false;
}