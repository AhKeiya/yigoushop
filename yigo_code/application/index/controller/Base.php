<?php
/**
 * Created by PhpStorm.
 * User: zouzhikang
 * Date: 2018/8/4
 * Time: 上午10:56
 */

namespace app\index\controller;

use think\Controller;

class Base extends Controller
{
    /**
     * @param $code         返回状态码
     * @param $msg          返回信息
     * @param string $data 返回数据
     */
    public function return_msg($code, $msg, $data = '')
    {
        $return_msg['code'] = $code;
        $return_msg['msg'] = $msg;
        $return_msg['data'] = $data;
        echo json_encode($return_msg);
        die();
    }

    /*
     * 判断是否登录
     */
    public function _initialize()
    {
        if (!session('username')){
            $this->error('亲，你还没登录！');
        }
    }
}