<?php
/**
 * 会员软删除
 */

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Member extends Model
{
//会员软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    //会员 注册时间搜索框 （未完成）
    public function selectime($data){
        $member=$this->whereTime('member_retime', '>=', $data)->select();
        if ($member){
            return $member;  //返回数据
        }else{
            return 0;  //没有数据
        }

    }
}