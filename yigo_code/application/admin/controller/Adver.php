<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25 0025
 * Time: 下午 11:28
 */

namespace app\admin\controller;

use app\admin\model\Admin_adplace;
use app\admin\model\Admin_adver;
use think\Controller;
use think\Db;

class Adver extends Controller
{
    /*
     * 显示广告信息
     */
    public function lst()
    {
        $listRes = Db::name('admin_adver')
            ->alias('a')
            ->join('yigou_admin_adplace b','b.pid=a.pid')
            ->paginate(8);
        $this->assign('res',$listRes);
        return view();
    }

    /**
     * 添加广告
     * @return \think\response\View|void
     */
    public function add()
    {
        if (request()->isPost()){
            $data = input('post.');
            $data['create_time'] = date('Y-m-d H:i:s');
            $adver = new Admin_adver();
            $res = $adver->save($data);
            if ($res){
                $this->success('添加广告成功','lst');
            }else{
                $this->error('添加失败');
            }
            return;
        }
        $adplace = new Admin_adplace();
        $placeRes = $adplace->adplace();
        if ($placeRes == null){
            $placeRes[]['pid'] = 0;
            $placeRes[0]['ad_place'] = '请先添加广告位';
        }
        $this->assign('res',$placeRes);
        return view();
    }

    /*
     * 编辑广告
     */
    public function edit($id)
    {
        $adver = new Admin_adver();
        if (request()->isPost()){
            $data = input('post.');
            $data['update_time'] = date('Y-m-d H:i:s');
            $res = $adver->save($data,['id'=>$id]);
            if ($res){
                $this->success('修改广告成功','lst');
            }else{
                $this->error('修改失败');
            }
        }
        $adplace = new Admin_adplace();
        $placeRes = $adplace->adplace();
        $editRes = $adver->where(['aid'=>$id])->find();
        $this->assign(['res'=>$editRes,'placeRes'=>$placeRes]);
        return view();
    }

    /*
     * 删除广告
     */
    public function del($id)
    {
        $res = Admin_adver::get($id)->delete();
        if ($res){
            $this->success('删除广告成功','lst');
        }else{
            $this->error('删除失败');
        }
    }

}