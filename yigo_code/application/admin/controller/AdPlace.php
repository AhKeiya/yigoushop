<?php
/**
 * 广告位置
 */

namespace app\admin\controller;

use app\admin\model\Admin_adplace;
use think\Controller;
use think\Db;

class AdPlace extends Controller
{
    /*
     * 广告位信息展示
     */
    public function lst()
    {
        $placeModel = new Admin_adplace();
        $placeRes = $placeModel->select();
        $adverRes = Db::name('admin_adver')->field('pid')->select();
        $arr = array();
        // 显示每个广告位有多少张图片
        foreach ($placeRes as $key => $value) {
            $num = 0;//清零后再循环求出结果
            foreach ($adverRes as $k => $v) {
                if ($value['pid'] == $v['pid']) {
                    $num++;
                }
                $arr[$value['pid']] = $num;
            }
        }
        foreach ($arr as $item => $value) {
            foreach ($placeRes as $k => $v) {
                if ($item == $v['pid']) {
                    $v['count'] = $value;
                }
            }
        }
        $this->assign('res', $placeRes);
        return view();
    }

    /*
     * 添加广告位
     */
    public function add()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $placeModel = new Admin_adplace();
            $data['create_time'] = date('Y-m-d H:i:s');
            $res = $placeModel->save($data);
            if ($res) {
                $this->success('添加广告位成功', 'lst');
            } else {
                $this->error('添加失败');
            }
            return;
        }
        return view();
    }

    /*
     * 编辑广告位
     */
    public function edit($id)
    {
        $placeModel = new Admin_adplace();
        if (request()->isPost()) {
            $data = input('post.');
            $data['update_time'] = date('Y-m-d H:i:s');
            //执行更新,save(),第二个参数作为更新条件
            $res = $placeModel->save($data, ['pid' => $id]);
            if ($res) {
                $this->success('广告位修改成功', 'lst');
            } else {
                $this->error('修改失败');
            }
            return;
        }
        $editRes = $placeModel->where(['pid' => $id])->find();
        $this->assign('res', $editRes);
        return view();
    }

    /**
     * 删除广告位
     * @param $id 广告位ID
     */
    public function del($id)
    {
        $this->error('若删除广告位请联系超级管理员');
//        $res = Admin_adplace::destroy($id);
//        if ($res) {
//            $this->success('广告位删除成功', 'lst');
//        } else {
//            $this->error('删除失败');
//        }
    }
}