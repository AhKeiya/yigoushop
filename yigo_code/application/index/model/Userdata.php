<?php
/**
 * Created by PhpStorm.
 * User: zouzhikang
 * Date: 2018/7/28
 * Time: 上午10:30
 */

namespace app\index\model;

use think\Model;

class Userdata extends Model
{
    //模型事件是指在进行模型的写入操作的时候触发的操作行为，包括模型的save方法和delete方法。
    protected static function init()
    {
        //编辑信息中上传图片时将原有图片删除再上传
        self::event('before_update', function ($data) {
            //判断是否是有上传图片
            if ($_FILES['thumb']['tmp_name']) {
                //获取编辑的表单信息
                $userIcon = self::find($data['did']);
                //拼接绝对路径
                $artpath = $_SERVER['DOCUMENT_ROOT'] . $userIcon['thumb'];
                //判断文件是否存在，系统默认头像不删除
                if (file_exists($artpath) && $userIcon['thumb'] !== '/iconUpload/20180801/defaultIcon.JPG') {
                    @unlink($artpath); //删除
                }
                // 获取表单上传文件 例如上传了001.jpg
                $file = request()->file('thumb');
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'iconUpload');
                if ($info) {
                    // 成功上传后 获取上传信息
                    // $info->getSaveName() 输出 20160820/42a79759f284b76.jpg
                    $thumb = DS . 'iconUpload' . '/' . $info->getSaveName();
                    $data['thumb'] = $thumb;
                }
            }
        });

    }

}