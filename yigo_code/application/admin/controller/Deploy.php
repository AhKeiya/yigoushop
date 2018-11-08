<?php
/**
 * Created by PhpStorm.
 * User: zhoubairao
 * Date: 2018/7/25
 * Time: 下午5:16
 */

namespace app\admin\controller;

use think\Controller;
use think\Db;
use app\admin\model\Information as inModel;    //引入网站配置信息模型
use app\admin\model\AdminUsers as UsersModel;    //引入用户模型
use app\admin\model\Member as MemModel;    //引入会员模型
use think\Loader;

vendor("PHPExcel.PHPExcel");     //引入PHPExcel 文件

class Deploy extends Controller
{
    /*
     * 输出网站信息配置页面
     */
    public function information()
    {
        //实例化模型
        $inModel = new inModel();
        //处理上传数据,并存储进数据库
        if (request()->isPost()) {
            //接收上传参数
            $data = input('post.');
            //引入验证，执行验证，
            $validate = \think\Loader::validate('Information');
            //如果验证的数据有错误，就显示报错
            if (!$validate->scene('information')->check($data)) {
                //显示错误信息。用弹窗的形式显示错误。
                $this->error($validate->getError());
            }

            //调用模型方法写入数据库，并判断是否成功
            if ($inModel->update($data)) {
                $this->success('修改信息成功', '/admin/deploy/information');
            } else {
                $this->error('修改信息失败');
            }
        }
//        查询数据库，获取网站配置信息
        $information = $inModel->find();
        //分配数据与页数到内存中
        $this->assign('information', $information);
        return view();
    }

    /*
     * 输出网站信息配置会员管理页面
     */
    public function member()
    {
        //实例化会员模型
        $memModel = new MemModel();
        //查询会员信息  不包含被软删除的信息
        $member = $memModel
            ->alias('a')
            ->field('a.*,b.username,uid,password')
            ->join('yigou_admin_users b', 'a.member_code = b.member_code', 'left')
            ->paginate(10);

        //分页
        $page = $member->render();
        //分配数据与页数到内存中 渲染
        $this->assign('member', $member);
        $this->assign("page", $page);

        return view();
    }

    /**
     * 将会员的数据库数据导出为excel文件
     */
    public function downLoadExcle()
    {
        //查询会员数据库信息，
        $memberModel = new MemModel();
        $user = $memberModel
            ->alias('a')
            ->field('a.*,b.username')
            ->join('yigou_admin_users b', 'a.member_code = b.member_code', 'left')
            ->select();
        Loader::import('PHPExcel.PHPExcel');
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');
        Loader::import('PHPExcel.PHPExcel.Reader.Excel2007');
        $objPHPExcel = new \PHPExcel();

        //设置每列的标题
        $objPHPExcel->setActiveSheetIndex(0)
//            ->setCellValue('A1', '易购商城会员表')
            ->setCellValue('A1', '用户名')
            ->setCellValue('B1', '账户余额（元）')
            ->setCellValue('C1', '注册时间');
//            ->setCellValue('D1', '状态');

        //存取数据  这边是关键
        foreach ($user as $k => $v) {
            $num = $k + 2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $num, $v['username'])
                ->setCellValue('B' . $num, $v['member_balance'])
                ->setCellValue('C' . $num, $v['member_retime']);
//                ->setCellValue('D' . $num, $v['member_status']);
        }
        //设置工作表标题
        $objPHPExcel->getActiveSheet()->setTitle('易购商城会员信息');


        //设置列的宽度  设置宽width
//        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);


        // 设置单元格高度
        // 第一行的默认高度
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=易购商城会员用户表.xlsx");//设置文件标题
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * 会员 注册时间搜索框 （未完成）
     */
    public function selectime()
    {
        if (request()->isPost()) {
            //获取post时间
            $data = input('post.member_retime');
            //实例化模型
            $meModel = new MemModel();
            //调用模型方法
            $member = $meModel->selectime($data);
            if ($member) {
                //分配数据与页数到内存中 渲染
                $this->assign('member', $member);
            } else {
                $this->success('没有数据');
            }


        }
    }

    /*
     * 添加会员
     */
    public function member_add()
    {
        //实例化模型
        $usersModel = new UsersModel();

        if (request()->isPost()) {
            //获取参数
            $data = input('post.');
            // 调用模型方法  检验用户名及密码是否与数据库数据一致
            $users = $usersModel->memberadd($data);
            //验证一致 返回 1和用户名主键ID ，  返回2，用户名密码不正确  返回0 没有这个用户
            if ($users == 0) {
                $this->error('没有这个用户');
            } elseif ($users == 2) {
                $this->error('用户名密码不一致');
            } else {

                //随机生成一个数据，用于做会员编号
                srand((double)microtime() * 1000000);
                //随机产生0-99之间的整数
                $randval = rand(0, 99999999);

                $data = [
                    'member_code' => $randval,
                    'member_status' => input('post.member_status'),
                    'member_retime' => date('Y-m-d H:i:s')
                ];

                //写入会员表  （会员编号  及时间 等）
                $member = Db::name('member')->insert($data);
                //写入用户表 （会员编码）
                $admin = Db::name('admin_users')->where('uid', $users)->update(['member_code' => $randval]);
                if ($member == 1 & $admin == 1) {
                    $this->success('添加会员成功', '/admin/deploy/member');
                } else {
                    $this->error('添加用户失败');
                }
            }

            return;
        }
        return view();
    }

    /*
     * 会员余额调整
     */
    public function member_balance()
    {
        //先查询该会员余额
        $oldbalance = Db::name('member')->find(input('get.id'));
        //存入缓存
        $this->assign('balance', $oldbalance);

        //对post过来的 余额参数 进行处理
        if (\request()->isPost()) {
            //获取post 过来的数据
            $data = input('post.');  // dump(input('get.id'));die;

            //新的余额 =旧的余额数据$oldbalance  +  post过来的数据（$data['member_balance']
            $newbalance = $oldbalance['member_balance'] + $data['member_balance'];

            $data = [
                'member_balance' => $newbalance,
            ];

            //把新的余额更新入数据库
            $re = Db::name('member')->where('member_id', input('get.id'))->update($data);
            if ($re) {
                $this->success('操作成功', '/admin/deploy/member');
            } else {
                $this->error('操作失败');
            }
        }

        return view();
    }

    /*
    * 会员设置锁定
    */
    public function member_status()
    {
        //根据id  查询当前数据的member_status 的值
        $stastus = Db::name('member')->where('member_id', input('get.id'))->value('member_status');
        //member_status 的值 2 为正常   3为锁定
        if ($stastus == 2) {
            //member_status 为 2 时，就修改为 3 锁定
            $stastus = Db::name('member')->where('member_id', input('get.id'))->update(['member_status' => 3]);
            $this->success('操作成功', '/admin/deploy/member');
        } elseif ($stastus == 3) {
            //member_status 为 3 时，就修改为 2 开启
            $stastus = Db::name('member')->where('member_id', input('get.id'))->update(['member_status' => 2]);
            $this->success('操作成功', '/admin/deploy/member');
        } else {
            $this->error('操作失败', '/admin/deploy/member');
        }

    }

    /*
    * 会员重置密码
    */
    public function member_pwd()
    {
        if (\request()->isPost()) {
            //接收参数
            $data = input('post.');
            //根据id  重置密码并进md5加密
            $re = Db::name('admin_users')->where('uid', $data['uid'])->update(['password' => md5($data['password'])]);
            if ($re) {
                $this->success('重置密码成功', '/admin/deploy/member');
            } else {
                $this->error('重置密码失败');
            }
        }

        //查询会员信息
        $member = Db::name('admin_users')->find(input('id'));
        //分配数据与页数到内存中 渲染
        $this->assign('member', $member);


        return view();
    }

    /*
   * 会员删除
   */
    public function member_del()
    {
        //实例化会员模型
        $memModel = new MemModel();
        //软删除会员
        $del = $memModel::destroy(input('get.id'));
        if ($del) {
            $this->success('删除成功', '/admin/deploy/member');
        } else {
            $this->error('删除失败');
        }
    }

    /*
  * 会员批量删除
  */
    public function member_deletion()
    {
        //对传递过来的member_id 数组进行处理
        if (input('?param.member_id')) {
            $ids = input('param.member_id/a');
        } else {
            $ids = input('param.member_id/d', 0);
        }

        //实例化会员模型
        $memModel = new MemModel();

        //物理删除
        $flag = $memModel->where('member_id', 'in', $ids)->delete();
        if ($flag) {
            $this->success('批量删除成功', '/admin/deploy/member');
        } else {
            $this->error('批量删除失败');
        }
    }

    /*
     *  输出物流管理页面
     */
    public function logistics()
    {
        $logistics = Db::name('logistics')->paginate(10);
//        dump ($logistics) ;die;
        $page = $logistics->render();
        //分配数据与页数到内存中
        $this->assign('logistics', $logistics);
        $this->assign("page", $page);
        return view();
    }

    /*
    *  物流添加
    */
    public function logistics_add()
    {
        if (request()->isPost()) {
            //获取参数
            $data = input('post.');
            //引入验证，执行验证，
            $validate = \think\Loader::validate('Deploy');
            //如果验证的数据有错误，就显示报错
            if (!$validate->check($data)) {
                //显示错误信息。用弹窗的形式显示错误。
                $this->error($validate->getError());
            }
            $re = Db::name('logistics')->insert($data);
            if ($re) {
                $this->success('添加物流信息成功', '/admin/deploy/logistics');
            } else {
                $this->error('添加物流信息失败');
            }
            return;
        }
        return view();
    }

    /*
    *  物流修改
    */
    public function logistics_edit()
    {
        //获取修改参数
        if (request()->isPost()) {
            //获取参数
            $data = input('post.');
            //引入验证，执行验证，
            $validate = \think\Loader::validate('Deploy');

            //如果验证的数据有错误，就显示报错
            if (!$validate->check($data)) {
                //显示错误信息。用弹窗的形式显示错误。
                $this->error($validate->getError());
            } else {
                //执行修改
                $re = Db::name('logistics')->where('lid', input('post.lid'))->update($data);
                //判断是否成功
                if ($re) {
                    $this->success('修改物流信息成功', '/admin/deploy/logistics');
                } else {
                    $this->error('修改物流信息失败');
                }
            }

            return;
        }
        //获取数据库物流参数
        $logistics = Db::name('logistics')->find(input('id'));
        //分配数据到缓存中
        $this->assign('logistics', $logistics);
        return view();
    }

    /*
    *  物流删除
    */
    public function logistics_del()
    {
        $re = db('logistics')->where('lid', input('get.id'))->delete();
        if ($re) {
            $this->success('删除物流信息成功', '/admin/deploy/logistics');
        } else {
            $this->error('删除物流信息失败');
        }
        return view();
    }

    /*
    *  物流批量删除
    */
    public function logistics_deltion()
    {
        //对传递过来的member_id 数组进行处理
        if (input('?param.lid')) {
            $ids = input('param.lid/a');
        } else {
            $ids = input('param.lid/d', 0);
        }

        //物理删除
        $flag = Db::name('logistics')->where('lid', 'in', $ids)->delete();
        if ($flag) {
            $this->success('批量删除成功', '/admin/deploy/logistics');
        } else {
            $this->error('批量删除失败');
        }
    }

}