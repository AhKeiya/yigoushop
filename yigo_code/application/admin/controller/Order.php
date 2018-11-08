<?php
/**
 * Created by PhpStorm.
 * User: zhoubairao
 * Date: 2018/7/25
 * Time: 下午2:59
 */

namespace app\admin\controller;


use think\Controller;

class Order extends Controller
{
    //输出全部订单视图模板
    public function lst()
    {
        return view();
    }

    //输出待付款订单视图模板
    public function obligation()
    {
        return view();
    }

    /*
     * 输出订单待发货模板
     */
    public function dropshipping()
    {
        return view();
    }

    /*
     * 输出已发货视图模板
     */
    public function dropalready()
    {
        return view();
    }
    /*
     * 输出退款中订单模板
     */
    public function reimburse()
    {
        return view();
    }
    /*
     * 输出已收货订单模板
     */
    public function acquire()
    {
        return view();
    }
    /*
     *  输出订单查询模板
     */
    public function addorder()
    {
        //如果没有查询的参数，就存储空值渲染
        $this->assign("order", '');

        if (request()->isPost()) {
            //获取查询字段
            $words = input('post.select');

            if ($words!==""){
                //查询数据库
                $order = Db::name('orderdata')
                    ->where('order_cnname|order_id', 'like', '%' . $words . '%')
                    ->field('a.*,b.logistics_cnname,c.collectsite,collectname,collectphone')
                    ->alias('a')
                    ->join('yigou_logistics b', 'a.order_logistics_id=b.order_logistics_id', 'LEFT')
                    ->join('yigou_site c', 'a.siteid=c.siteid', 'LEFT')
                    ->paginate(1);
                //数据缓存
                $this->assign("order", $order);
            }else{
               $this->error('请输入查询关键字');
            }

        }
        return view();
    }
}