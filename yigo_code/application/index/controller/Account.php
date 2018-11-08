<?php

namespace app\index\controller;

use app\index\model\Site;
use app\index\model\Userdata;
use think\Db;
use think\Session;

/**
 * 账号管理类
 */
class Account extends Base
{

    /**
     * 新增收件人地址
     */
    public function address_add()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $SiteModel = new Site();
            $data_v1 = $data;
            //销毁数组中的字段
            unset($data_v1["collectname"], $data_v1["collectphone"], $data_v1["default_site"]);
            //把数组元素组合为字符串
            $arr_v1['collectsite'] = implode(",", $data_v1);
            $arr_v2 = $data;
            //销毁数组中的字段
            unset($arr_v2["province"], $arr_v2["city"], $arr_v2["area"], $arr_v2["town"], $arr_v2["address"]);
            //合并数组
            $arrData = array_merge($arr_v1, $arr_v2);
            //没有default_site字段就设置值为0
            if (!isset($arrData["default_site"])) {
                $arrData["default_site"] = '0';
            }
            $arrData["create_time"] = date('Y-m-d H:i:s');
            //设为默认地址时把数据库中default_site都改了0
            if ($arrData['default_site'] == '1') {
                $set_default = $SiteModel->select();
                $setArr = array();
                foreach ($set_default as $k => $v) {
                    $setArr[$v['sid']] = 0;
                }
                foreach ($setArr as $k => $v) {
                    $SiteModel->where(['default_site' => 1])->update(['default_site' => $v]);
                }
            }
            // 通过Session查找用户ID并存入数据库
            $uid = Db::name('index_users')
                ->where('username', Session::get('username'))
                ->field('uid')
                ->find();
            $arrData['uid'] = $uid['uid'];
            //写入数据库
            $res = $SiteModel->insert($arrData);
            if ($res !== false) {
                $this->return_msg(200, '新增地址成功', $arrData);
            } else {
                $this->return_msg(400, '新增地址失败');
            }
            return;
        }
        return $this->fetch();
    }

    /*
     * 修改收件人地址
     */
    public function address_edit()
    {
        $SiteModel = new Site();
        if (request()->isPost() && input('sid')) {
            $data = input('post.');
            $data_v1 = $data;
            //销毁数组中的字段
            unset($data_v1["collectname"], $data_v1["collectphone"], $data_v1["default_site"], $data_v1['sid']);
            //把数组元素组合为字符串
            $arr_v1['collectsite'] = implode(",", $data_v1);
            $arr_v2 = $data;
            //销毁数组中的字段
            unset($arr_v2["province"],
                $arr_v2["city"],
                $arr_v2["area"],
                $arr_v2["town"],
                $arr_v2["address"],
                $arr_v2['sid']);
            //合并数组
            $arrData = array_merge($arr_v1, $arr_v2);
            //没有default_site字段就设置值为0
            if (!isset($arrData["default_site"])) {
                $arrData["default_site"] = '0';
            }
            $arrData["update_time"] = date('Y-m-d H:i:s');
            //设为默认地址时把数据库中default_site都改了0
            if ($arrData['default_site'] == '1') {
                $set_default = $SiteModel->select();
                $setArr = array();
                foreach ($set_default as $k => $v) {
                    $setArr[$v['sid']] = 0;
                }
                foreach ($setArr as $k => $v) {
                    $SiteModel->where(['default_site' => 1])->update(['default_site' => $v]);
                }
            }
            //更新数据库
            $res = $SiteModel->where(['sid' => input('sid')])->update($arrData);
            if ($res !== false) {
                $this->return_msg(200, '编辑地址成功', $arrData);
            } else {
                $this->return_msg(400, '编辑地址失败');
            }
            return;
        }
        // 查找对应用户对应的地址列表
        $siteRes = Db::name('site')
            ->alias('a')
            ->join('yigou_index_users b', 'a.uid = b.uid')
            ->where('username', Session::get('username'))
            ->select();
        $this->assign('siteRes', $siteRes);
        $editRes = Db::name('site')->where(['sid' => input('id')])->find();
        $this->assign('editRes', $editRes);
        // 判断该用户是否存在此地址，防止恶意修改地址id
        if (input('id')) {
            foreach ($siteRes as $k => $v) {
                $authArr[] = $v['sid'];
            }
            $postid = intval(input('id'));
            if (!in_array($postid, $authArr)) {
                $this->return_msg(401, '没有该地址');
            }
        }
        return $this->fetch();
    }

    /*
     * 设为默认地址
     */
    public function set_default()
    {
        $SiteModel = new Site();
        if (request()->isGet()) {
            $sid = input('id');
            $res = $SiteModel->select();
            $arr = array();
            //以sid为键，default_site字段为值组合新数组
//            dump($res);die();
            foreach ($res as $k => $v) {
                $arr[$v['sid']] = $v['default_site'];
            }
            //将当前点击的default_site改为1：设为默认，其他设为0：取消默认
            foreach ($arr as $k => $v) {
                $arr[$k] = 0;
                $arr[$sid] = 1;
            }
            //循环更新
            foreach ($arr as $k => $v) {
                $set_res = $SiteModel->where(['sid' => $k])->update(['default_site' => $v]);
            }
            $SiteModel->where('sid', $sid)->update(['update_time' => date('Y-m-d H:i:s')]);
            if ($set_res !== false) {
                $this->return_msg(200, '设为默认地址成功');
            } else {
                $this->return_msg(400, '设为默认地址失败');
            }
            return;
        }
    }

    /**
     * 删除收件人地址（软删除）
     */
    public function address_del()
    {
        $res = Site::destroy(input('id'));
        if ($res) {
            $this->return_msg(200, '删除地址成功');
        } else {
            $this->return_msg(400, '删除地址失败');
        }
    }

    /*
     * 修改用户个人资料
     * 上传头像
     * @return mixed|void 返回json参数
     */
    public function users()
    {
        if (request()->isPost()) {
            $UserModel = new Userdata();
            $data = input('post.');
            //写入当前更新时间
            $data['update_time'] = date('Y-m-d H:i:s');
            // 过滤post数组中的非数据表字段数据
            $res = $UserModel->allowField(true)->save($data,['uid'=>Session::get('uid')]);
            if ($res) {
                $this->return_msg(200, '用户资料修改成功', $data);
            } else {
                $this->return_msg(400, '用户资料修改失败');
            }
            return;
        }
        $UserModel = new Userdata();
        //查找当前用户个人信息
        $UserData = $UserModel->where('uid',Session::get('uid'))->find();
        $this->assign('userdata',$UserData);
        return $this->fetch();
    }

    public function forrefund()
    {
        return $this->fetch();//申请退款
    }

    public function modifypay_1()
    {
        return $this->fetch();//修改支付密码1
    }

    public function modifypay_2()
    {
        return $this->fetch();//修改支付密码2
    }

    public function modifypay_3()
    {
        return $this->fetch();//修改支付密码3
    }

    public function modifypwd_1()
    {
        return $this->fetch();//修改登录密码1
    }

    public function modifypwd_2()
    {
        return $this->fetch();//修改登录密码2
    }

    public function modifypwd_3()
    {
        return $this->fetch();//修改登录密码3
    }
}