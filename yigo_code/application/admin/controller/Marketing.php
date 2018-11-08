<?php
/**
 * Created by PhpStorm.
 * User: zhoubairao
 * Date: 2018/7/25
 * Time: 下午9:18
 */

namespace app\admin\controller;


use think\Controller;
use app\admin\model\MailTemplate;
use app\admin\model\IndexUsers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use think\Db;
use app\admin\model\IndexCoupon;
use app\admin\validate\AddCoupon;
use app\admin\model\Goods_classify;
use app\admin\model\Goods;
use app\admin\model\GoodsOffPrice;
use app\admin\validate\Goods_off_add;
class Marketing extends Controller
{
    /*作者：周柏桡
     * 输出群发营销信息页面
     */
    public function marketing()
    {
        $data = IndexUsers::all();
        $this->assign('data',$data);
        return view();
    }
    /*作者：周柏绕
     *  输出优惠券配置页面信息
     */
    public function preferential()
    {
        $con = new IndexCoupon();
        //查询出所有数据包括软删除
        $data = $con->paginate(20);
        $this->assign('data',$data);
        return view();
    }

    /**作者：周柏绕
     * @return \think\response\View 输出查询已过期的优惠券
     */
    public function coupon_overdue()
    {
        $con = new IndexCoupon();
        $overdue_data = $con::all();

        foreach ($overdue_data as $k => $v){
            $date_cou_Dtime_arr=strtotime($v['cou_Dtime']);//获取数据库的过期时间戳
            $date_now = time();//获取当前时间戳
            if ($date_now > $date_cou_Dtime_arr){ //如果现在时间戳  大于 数据库到期的时间戳那么更新软删除
                $con::destroy($v['cou_id']);
            }
        }
        $data = $con::onlyTrashed()->paginate(20);
        $this->assign('data',$data);
        return view();
    }

    /**作者：周柏绕
     * 接收get参数，进行单条删除优惠券
     */
    public function del_coupon()
    {
        if (!request()->isGet()){
            $this->error('没有任何参数提交');
        }
        $id = input('get.id');
        $con = IndexCoupon::destroy($id);
        if ($con){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    
    /** 作者：周柏绕
     * 添加优惠券设置
     */
    public function add_preferential_data()
    {
        $data = input('post.');

        $val = new AddCoupon();
        if (!$val->check($data)){
            $this->error($val->getError());
        }

        for ($i=0 ; $i<$data['number_of'] ; $i++){
            //生成随机ID
            $order_number = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            //添加随机ID
            $data['cou_num'] = 'YHJ'.$order_number;

            $con = new IndexCoupon($data);
            $con = $con->allowField(true)->save();
        }
        if ($con){
            $this->success('添加完成','off_price.html');
        }else{
            $this->success('添加失败');
        }
    }
    
    
    /*作者：周柏绕
     *  输出添加优惠券页面配置
     */
    public function add_preferential()
    {
        return view();
    }

    /*作者：周柏绕
     *  输出商品价格打折视图
     */
    public function off_price()
    {
//        $data = Db::query('SELECT yigou_goods_off_price.*,yigou_goods.goods_off_type,yigou_goods.goods_cnname FROM yigou_goods_off_price inner JOIN yigou_goods on yigou_goods_off_price.goods_off_id = yigou_goods.goods_id');
        $data = Db::table('yigou_goods_off_price')->field('yigou_goods_off_price.*,yigou_goods.goods_off_type,yigou_goods.goods_cnname')->join('yigou_goods','yigou_goods_off_price.goods_off_id = yigou_goods.goods_id')->paginate(20);
        foreach ($data as $k=>$v){
            $time = strtotime($v['end_time']);//获取数据库的时间戳
            $date_now = time();//获取现在的时间戳
            if ($date_now > $time){ //如果现在时间大于定义的时间，查找商品表，折扣状态修改  并且吧折扣表全部删除
                Db::name('goods_off_price')->delete($v['of_id']);//删除折扣活动
                Db::name('goods')->update(['goods_off_type'=>0]); //更新商品列表打折状态
            }
        }
        $this->assign('data',$data);
        return view();
    }

    //输出删除折扣活动视图
    public function del_goods_off_activity()
    {
        $data = Db::query('SELECT DISTINCT goods_off_name FROM yigou_goods_off_price');
        $this->assign('data',$data);
        return view();
    }

    //接收折扣单条商品进行修改状态并且删除
    public function goods_off_oen_del()
    {
        if (!request()->isGet()){
            $this->error('没有任何删除数据提交');
        }
        $goods_off_id = input('get.goods_off_id');
        //修改商品信息的折扣状态
        $con = Db::name('goods')->where('goods_id',$goods_off_id)->update(['goods_off_type'=>0]);
        //删除折扣信息
        $con1 = Db::name('goods_off_price')->where(['goods_off_id'=>$goods_off_id])->delete();
        $con && $con1 ? $this->success('删除成功') : $this->error('删除失败');

    }

    /*作者：周柏绕
     *  输出添加活动折扣添加视图
     */
    public function add_off_price()
    {
        if (!request()->isGet()){
            $this->error('没有任何数据提交');
        }
        $id = input('get.goods_classify_id');
        //查询ID
        //$id_type = Db::name('goods')->where('goods_classify_id=90 AND goods_off_type=0')->select();
        //查询出商品列表，判断是否为正在打折中的商品
        $data = Db::query("SELECT * FROM yigou_goods WHERE goods_classify_id = ? && goods_off_type = ?",[$id,0]);
        $this->assign('data',$data);
        return view();
    }

    /** 新增商品折扣逻辑 作者：周柏绕
     * 接收折扣商品数据提交过来的内容进行写入数据库，更新商品列表的折扣状态
     */
    public function add_off_data()
    {
        if (!request()->isPost()){
            $this->error('没有任何数据提交');
        }
        $data = input('post.');

        //验证规则
        $rest = new Goods_off_add();
        if (!$rest->check($data)){
            $this->error($rest->getError());
        }

        foreach ($data['goods_off_id'] as $k=>$v){
            $con = new GoodsOffPrice();
            $con->goods_off_id = $v;
            $con->goods_off_name = $data['goods_off_name'];
            $con->goods_off_m = $data['goods_off_m'];
            $con->start_time = $data['start_time'];
            $con->end_time = $data['end_time'];
            $con->save();
            $con1 = Db::name('goods')->where('goods_id',$v)->update(['goods_off_type'=>1]);//更新商品列表打折状态
        }
        if ($con && $con1){
            $this->success('写入成功','off_price');
        }else{
            $this->error('写入失败');
        }
    }
    
    //接收折扣活动名称进行修改商品折扣状态并且删除
    public function goods_off_del()
    {
        if (!request()->isPost()){
            return $this->ech_json_data(false,'没有任何数据提交');
        }
        $goods_off_name = input('post.goods_off_name');
        //查询出数据库匹配的商品外键
        $off_data = Db::name('goods_off_price')->where(['goods_off_name'=>$goods_off_name])->field('goods_off_id,of_id')->select();
        //更新商品折扣状态修改为0 并且删除
        foreach ($off_data as $k=>$v){
            $con = new Goods();
            $con->save(['goods_off_type'=>0],['goods_id'=>$v['goods_off_id']]);
            $con1 = Db::name('goods_off_price')->delete($v['of_id']);
        }
        if ($con && $con1){
            return $this->ech_json_data(true,'删除成功');
        }else{
            return $this->ech_json_data(false,'删除成失败');
        }
    }

    //输出树形商品分类选择
    public function add_goods_type()
    {
        $con = new Goods_classify;
        $data = $con->Tree();
        $this->assign('data',$data);
        return view();
    }
    
    /*作者：周柏桡
     *  输出邮件模板配置
     */
    public function mail_template()
    {
        $con = new MailTemplate();
        $data = $con->find();
        $this->assign('data',$data);
        return view();
    }

    /**作者：周柏桡
     *  接收邮件模板内容进行更新
     */
    public function upda_maul_template()
    {
        if (!request()->isPost()){
            $this->error('没有接收到任何参数');
        }
        $data = input('post.');
        $con = new MailTemplate();
        $con->allowField(true)->save($data,$data['id']);
        if ($con){
            $this->success('模板更新成功');
        }else{
            $this->error('模板更新失败');
        }
    }

    /**作者：周柏桡
     *  接收参数，指定发送一条邮件
     */
    public function marketing_mail_one()
    {
        if (!request()->isGet()){
            $this->error('没有任何参数提交');
        }
        $mail = input('get.mail');
        $con = new MailTemplate();
        $data = $con->find();
        qqmail($mail,$data['mail_title'],$data['mail_body']);
        $this->success('发送成功');
    }

    /**
     *  接收ajax提交来的参数id 查询数据库匹配邮箱
     */
    public function marketing_mail_all()
    {
        $id = input('post.');
        $con = new IndexUsers();
        $con1 = new MailTemplate();
        $data = $con1->find();
        foreach ($id['id'] as $k => $v){
            $mail_alll = $con->field('email')->where(['uid'=>$v])->find();

            $mail = new PHPMailer(true);
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
            $mail->addAddress($mail_alll['email'],'Liang');// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
            $mail->addCC($mail_alll['email']);// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址

            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $data['mail_title'];
            $mail->Body    = $data['mail_body'];

            $conq = $mail->send();
        }
        if($conq){
            return $this->ech_json_data(true,'群发成功');
        }else{
            return $this->ech_json_data(true,'群发失败');
        }
    }

    //返回json数据参数
    public function ech_json_data($bool=false,$msg='')
    {
        if ($bool){
            $j=json([
                'Status'=> 200,
                'msg'=> $msg
            ]);
        }else{
            $j=json([
                'Status'=> 400,
                'msg'=> $msg
            ]);
        }
        return $j;

    }

}