<?php

use think\Route;

$r = new Route();
//url
$api = 'api/';
//跳转
$order = 'index/order/';

//get接口
$get=[
    //查询退款
    $api.'refund/[:value]'=>  $order. 'nowRefund',
    $api.'refund'=>$order. 'demandRefund',
    'test'=>'/index/test/test',
];
//post接口
$post = [
    $api . 'order' => $order . 'demand',
    $api . 'addorder' => $order . 'add',
];

$delete = [
        //删除订单
    $api . 'order' => $order . 'del',
];

$r->get($get);
$r->post($post);
$r->delete($delete);