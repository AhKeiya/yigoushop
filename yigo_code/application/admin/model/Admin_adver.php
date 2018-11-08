<?php
/**
 * Created by PhpStorm.
 * User: zouzhikang
 * Date: 2018/8/2
 * Time: 下午3:16
 */

namespace app\admin\model;


use think\Model;

class Admin_adver extends Model
{
    /*
     * 添加广告之前上传图片
     */
    protected static function init()
    {
        self::event('before_insert', function ($data) {
            // 判断是否有文件上传
            if ($_FILES['thumb']['tmp_name']) {
                $file = request()->file('thumb');
                // 移动图片
                $info = $file->move(ROOT_PATH . 'public' . DS . 'advertisement');
                if ($info) {
                    $data['thumb'] = DS . 'advertisement' . DS . $info->getSaveName();
                }
            }
        });

        //图片更换之前把上一张图片删除
        self::beforeUpdate(function ($data) {
            if ($_FILES['thumb']['tmp_name']) {
                $adver = self::find($data['aid']);
                $adverpath = $_SERVER['DOCUMENT_ROOT'] . $adver['thumb'];
                if (file_exists($adverpath)){
                    @unlink($adverpath);
                }
                $file = request()->file('thumb');
                $info = $file->move(ROOT_PATH . 'public' . DS . 'advertisement');
                if ($info) {
                    $data['thumb'] = DS . 'advertisement' . DS . $info->getSaveName();
                }
            }
        });
    }

}