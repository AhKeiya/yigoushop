<?php
/**
 * 操作日志
 */

namespace app\admin\controller;

use think\Controller;
//操作日志
class Logs extends Controller
{
//    操作日志列表
    public function lst()
    {
        return view();
    }

//    删除
    public function del()
    {
        echo '删除';
    }

}