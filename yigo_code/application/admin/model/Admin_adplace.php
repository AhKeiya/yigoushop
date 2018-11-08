<?php
/**
 * Created by PhpStorm.
 * User: zouzhikang
 * Date: 2018/8/2
 * Time: 下午3:17
 */

namespace app\admin\model;


use think\Model;

class Admin_adplace extends Model
{
    /*
     * 广告位显示
     */
    public function adplace()
    {
        return self::field('ad_place,pid')->select();
    }
}