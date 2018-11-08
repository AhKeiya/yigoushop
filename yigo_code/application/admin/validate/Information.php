<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 下午 4:52
 */

namespace app\admin\validate;

use think\Validate;

class Information extends Validate
{
//   验证规则
    protected $rule = [
        'information_name' => 'require',  //网站名称
        'information_desc' => 'require',  //网站关键字
        'information_title' => 'require',  //网站标题
//        'information_tell' => 'require|alphaDash|between:1,20',  //网站联系方式
        'information_email' => 'require|email',  //网站邮箱
        'information_qq' => 'require|number',  //网站QQ
        'information_wechat' => 'require|alphaNum',  //网站微信
        'information_address' => 'require|chsDash',  //网站地址
        'information_icp' => 'require',     //网站ICP备案号
        'information_filing' => 'require',  //网站公安备案
//        'information_filingurl' => 'require|url',  //网站公安备案链接
    ];

//    验证提示 提示报错代码： $validate->getError()
    protected $message=[
        'information_name.require' => '网站名称不得为空',
        'information_desc.require' => '网站关键字不得为空',
        'information_title.require' => '网站标题不得为空',
//        'information_tell.between:1,20' => '网站联系方式不正确',
        'information_tell.require' => '网站联系方式不得为空',
        'information_email.email' => '网站邮箱格式不正确',
        'information_qq.number' => '网站qq邮箱格式不正确',
        'information_wechat.alphaNum' => '网站微信格式不正确',
        'information_address.require' => '网站地址不得为空',
        'information_icp.require' => '网站ICP备案号不得为空',
        'information_filing.require' => '网站公安备案不得为空',
//        'information_filingurl.url' => '网站公安备案链接格式不正确',

    ];

    //    验证场景
    protected $scenc=[
            'information' => ['information_name','information_desc','information_title',
            'information_email','information_qq','information_wechat','information_address','information_icp','information_filing'
            ,'information_filingurl'],
    ];


}