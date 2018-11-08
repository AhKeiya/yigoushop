<?php
namespace app\index\controller;

/**
 * 订单管理类
 */
class Order extends Base
{
    public function order()
    {
        return $this->fetch();
    }

    public function wishlist()
    {
        return $this->fetch();//收藏夹
    }

    public function refund()
    {
        return $this->fetch();//退款退货
    }

    public function receipted()
    {
        return $this->fetch();//确认收货
    }



    public function pay()
    {
        return $this->fetch();//支付页面
    }
}
