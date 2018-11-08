<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 下午 3:41
 */

namespace app\index\model;


use think\Model;
use think\Request;

class Orderdata extends Model
{
    public function order1()
    {
        //post提交过来
        if(\request()->isPost()){
            $data=input('post.');

        }
        //delete过来
        if(\request()->isDelete()){

        }

    }
}