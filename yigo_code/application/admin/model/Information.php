<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 下午 5:23
 */

namespace app\admin\model;

use app\admin\controller\Deploy;
use think\Model;

class Information extends Model
{
//    网站配置信息
    protected static function init()
    {
        Information::event('before_update', function ($data) {
//            dump($data);die;
            //文件上传
            if ($_FILES['information_logo']['tmp_name']) {
                // 获取表单上传文件 例如上传了001.jpg
                $file = request()->file('information_logo');

                //查询数据库，根据id获取以前图片的路径
                $id =Information::find($data['id']);
                //拼装一个新路径，以便能找旧图片地址然后删除。  //$_SERVER['DOCUMENT_ROOT']会获取到你的根目录
                $thumbpath = $_SERVER['DOCUMENT_ROOT'] . $id['information_logo'];
//               echo $thumbpath;die;  //查看旧图片的途径
                //判断原图是否存在    if(当前文章的以前的图片)
                if (file_exists($thumbpath)) {
                    //如果有图，就删除以前的图片
                    @unlink($thumbpath);
                }

                // 移动到 框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                //在拼装好图片的路径，以便渲染时能够显示图片
                $data['information_logo'] = '/uploads/' . $info->getSaveName();
            }

        });

    }

}

