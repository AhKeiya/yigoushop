<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25 0025
 * Time: 下午 3:26
 */

namespace app\admin\controller;


use think\Controller;

class Photo extends Controller
{
    public function lst()
    {
        return $this->fetch('photo');
    }

    public function photonew()
    {
        return $this->fetch();
    }

    public function img()
    {
        return view();
    }
}