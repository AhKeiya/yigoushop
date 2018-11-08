<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function qqmail($mail1,$title,$data){
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.qq.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = '284205833@qq.com';                 // SMTP username
        $mail->Password = 'xjthatlloiyncafg';                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to
        $mail->From = '284205833@qq.com';
        $mail->isHTML(true);

        $mail->setFrom('284205833@qq.com',"易购团队");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
        $mail->addAddress($mail1,'Liang');// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
        $mail->addCC($mail1);// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址

        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $title;
        $mail->Body    = $data;

        $con = $mail->send();
        if($con){
            return json_encode('123');
        }else{
            return json_encode('qwe');
        }
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}





//    // 实例化PHPMailer核心类
//    $mail = new PHPMailer();
//// 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
//    $mail->SMTPDebug = 1;
//// 使用smtp鉴权方式发送邮件
//    $mail->isSMTP();
//// smtp需要鉴权 这个必须是true
//    $mail->SMTPAuth = true;
//// 链接qq域名邮箱的服务器地址
//    $mail->Host = 'smtp.qq.com';
//// 设置使用ssl加密方式登录鉴权
//    $mail->SMTPSecure = 'ssl';
//// 设置ssl连接smtp服务器的远程服务器端口号
//    $mail->Port = 465;
//// 设置发送的邮件的编码
//    $mail->CharSet = 'UTF-8';
//// 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
//    $mail->FromName = '发件人昵称';
//// smtp登录的账号 QQ邮箱即可
//    $mail->Username = '12345678@qq.com';
//// smtp登录的密码 使用生成的授权码
//    $mail->Password = '**********';
//// 设置发件人邮箱地址 同登录账号
//    $mail->From = '12345678@qq.com';
//// 邮件正文是否为html编码 注意此处是一个方法
//    $mail->isHTML(true);
//// 设置收件人邮箱地址
//    $mail->addAddress('87654321@qq.com');
//// 添加多个收件人 则多次调用方法即可
//    $mail->addAddress('87654321@163.com');
//// 添加该邮件的主题
//    $mail->Subject = '邮件主题';
//// 添加邮件正文
//    $mail->Body = '<h1>Hello World</h1>';
//// 为该邮件添加附件
//    $mail->addAttachment('./example.pdf');
//// 发送邮件 返回状态
//    $status = $mail->send();

