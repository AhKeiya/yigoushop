<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/28 0028
 * Time: 下午 3:51
 */

namespace app\admin\model;


use think\Model;
use app\admin\validate\Goods_classify as vali;//本验证类

class Goods_classify extends Model
{
    public function Tree()
    {
        $cate = $this->select();
        return $this->sort($cate);
    }

    public function sort($data, $pid = 0, $level = 0)
    {
        static $arr = array();
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['level'] = $level;
                $arr[] = $v;
                $this->sort($data, $v['cid'], $level + 1);
            }
        }
        return $arr;
    }

    //找子级
    public function getChilrenId($cateId)
    {
        $cate = $this->select();
        return $this->_getChilrenId($cate, $cateId);

    }

    public function _getChilrenId($cate, $cateId)
    {
        static $arr = array();
        foreach ($cate as $k => $v) {
            if ($v['pid'] == $cateId) {
                $arr[] = $v['cid'];
                $this->_getChilrenId($cate, $v['cid']);

            }
        }
        return $arr;
    }

    //新增分类
    public function add()
    {
        $data = input('post.');
        $Mod = new $this($data);
        $validate = new vali();//验证类

        //验证
        if (!$validate->check($data)) {
            return ['msg'=>($validate->getError()),'code'=>'0'];
        }
        //保存
        $r = $Mod->allowField(true)->save();
        if($r){
            return ['code'=>200,'msg'=>'添加成功'];
        }else{
            return ['code'=>false,'msg'=>'未知错误'];
        }

    }
    public function edit()
    {
        $data = input('post.');
        $Mod = new $this();
        $validate = new vali();//验证类

        //验证
        if (!$validate->check($data)) {
            return ['code'=>'0','msg'=>($validate->getError())];
        }
        //保存
        $r=$Mod->update($data);
        if($r){
            return ['code'=>200,'msg'=>'添加成功'];
        }else{
            return ['code'=>false,'msg'=>'未知错误'];
        }

    }

}