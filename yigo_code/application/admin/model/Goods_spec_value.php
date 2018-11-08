<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 上午 11:18
 */

namespace app\admin\model;

use think\Model;
use app\admin\validate\Goods_spec_value as vali;

class Goods_spec_value extends Model
{
    //添加规格
    public function add()
    {
        $data = input('post.');
        //破折号换成下划线
        $data['value']=str_replace('-','_',$data['value']);
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
            return ['code'=>false,'msg'=>'添加失败'];
        }
    }

    public function edit()
    {
        $data = input('post.');
        $Mod = new $this();
        $validate = new vali();//验证类
        //破折号换成下划线
        $data['value']=str_replace('-','_',$data['value']);
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
    //ajax规格类型接口,返回值
    public function spec_value()
    {
        $spec=input('param.spec');
        //找出规格值
        $v=$this->where('spec_name',$spec)->value('value');
        //拆分为数组
        $arr=explode('_',$v);
        return json(['code'=>200,'arr'=>$arr]);
    }

}