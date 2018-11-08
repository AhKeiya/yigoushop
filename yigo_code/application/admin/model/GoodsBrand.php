<?php
/**
 * Created by PhpStorm.
 * User: zhoubairao
 * Date: 2018/7/28
 * Time: 上午11:23
 */

namespace app\admin\model;


use think\Model;

/* 作者：周柏桡
 * Class GoodsBrand
 * @package app\admin\model 订单品牌模型
 */
class GoodsBrand extends Model
{
    /*
     * 钩子 执行删除之前删除掉数据库存在的图片
     */
    protected static function init()
    {
        GoodsBrand::event('before_delete',function ($id){
            //判断原来的图片路径文件内容是否存在，$_SERVER['DOCUMENT_ROOT']获取图片路径前缀
            $img_url = $_SERVER['DOCUMENT_ROOT'].$id['goods_brand_img'];
            if (file_exists($img_url)){
                @unlink($img_url);//存在的删除掉
            }
        });



        /*
        * 钩子  执行修改之前删除数据库原本存在的图片
        */
        GoodsBrand::event('before_update', function ($data){
            //判断是否有文件传入
            if ($_FILES['goods_brand_img']['tmp_name']){
                //在更新之前判断是否有图片文件，查找自身的ID索引删除
                $atrs = GoodsBrand::find();//获取到自身的id
                //拼接图片路径
                $picurl = $_SERVER['DOCUMENT_ROOT'].$atrs['goods_brand_img'];
                //判断原来的图片路径文件内容是否存在，$_SERVER['DOCUMENT_ROOT']获取图片路径前缀
                if ($picurl){
                    @unlink($picurl);//存在的删除掉
                }
                $file = request()->file('goods_brand_img');//接收文件
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info){
                    $data['goods_brand_img'] = '/uploads/'.$info->getSaveName();//存储图片路径
                }
            }
        });

    }

}