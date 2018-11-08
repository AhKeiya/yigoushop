<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25 0025
 * Time: 下午 7:41
 */

namespace app\admin\controller;

use app\admin\model\Goods as mod;//商品表模型
use app\admin\model\Goods_cgoods;
use app\admin\model\Goods_classify;//分类模型
use app\admin\model\GoodsDescribe;
use app\admin\model\Goods_spec;
use app\admin\model\Goods_spec_value;
use app\admin\model\Photo_image;
use think\Controller;

use think\Db;
use think\Paginator;//引入分页
use app\admin\model\GoodsBrand;//商品品牌模型
class Goods extends Controller
{

    //删除的子分类
    private $_del_son;

    //编辑时当前规格拥有的规格
    private static $_spec_v;
    //前置操作
    protected $beforeActionList = [
//        //全执行
//        'first',
//        //非时执行
//        'second' => ['except'=>'hello'],
//        //是时执行
//        'three' => ['only'=>'hello,data'],
        'del_son' => ['only' => 'del_classify'],

    ];

    //商品列表
    public function lst()
    {
        //商品和图片 //上架的商品
        $gyes = Db::table('yigou_goods')
            ->alias(['yigou_goods' => 'g', 'yigou_photo_image' => 'i'])
            ->join('yigou_photo_image', 'g.goods_img= i.img_id')
            ->where('g.goods_status', '1')
            ->paginate(10);
        //未上架的商品
        $gno = Db::table('yigou_goods')
            ->alias(['yigou_goods' => 'g', 'yigou_photo_image' => 'i'])
            ->join('yigou_photo_image', 'g.goods_img= i.img_id')
            ->where('g.goods_status', '0')
            ->paginate(10);

        $this->assign(array(
            'gyes' => $gyes,
            'gno' => $gno,
        ));
        return $this->fetch();
    }

    //发布商品界面
    public function newgoods()
    {

        $this->assign(array(
            'spec' => Goods_spec_value::all(),
            'class' => (new Goods_classify)->Tree(),
            'brand' => GoodsBrand::all(),
        ));
        return $this->fetch();
    }

    //ajax规格类型接口,返回值
    public function spec_value()
    {
        return (new Goods_spec_value())->spec_value();
    }

    //发布商品接口
    public function newgoods_api()
    {
        //模型添加
        $r = (new \app\admin\model\Goods())->add();
        if ($r['code']) {
            $this->success($r['msg'], 'lst');
        } else {
            $this->error($r['msg']);
        }
    }

    //编辑商品界面(get)
    public function editgoods()
    {

        $id = input('param.id');
        //验证有没有此商品
        if(mod::where('goods_id',$id)->find()===null)
        {
            $this->error('正在试图编辑没有的商品...');
        }
        //id对应描述表
        $gd = GoodsDescribe::where('goods_id', $id)->find();
        //商品图片
        $imgIdArr = mod::where('goods_id', $id)->value('goods_img_id');
        //转为数组
        $imgIdArr = explode(',', $imgIdArr);
        //张数变量
        $arrt = 0;
        //数组值转为url地址
        foreach ($imgIdArr as $k => $item) {
            $imgIdArr[$k] = Photo_image::where('img_id', $item)->value('img_url');
            $arrt += 1;
        }
        for ($i = 3; $i <= 4; $i++) {
            if (!array_key_exists($i, $imgIdArr)) {
                $imgIdArr[$i] = '/static/img/选择图片.png';
            }
        }
        //-end商品图片
        //商品规格数据
        $sarr = array();//规格数组
        $snameArr = array();
        $nowSpecValueAll=array();
        $gsarr = Goods_spec::where('goods_id', $id)->select();//数据库已有规格二维数组
        foreach ($gsarr as $index => $item) {
            $item['spec_name'] = (new Goods_spec_value())->where('spec_id', $item['spec_id'])->value('spec_name');
            $item['key'] = $index + 1;
            $snameArr[$index + 1] = $item['spec_value'];
            $sarr[] = $item;
            //当前规格的规格值(所有)
            $t=(new Goods_spec_value())->where('spec_id', $item['spec_id'])->value('value');
            //所有值拆分成数组
            $t=explode('_',$t);
            $nowSpecValueAll[$index+1]=$t;
        }
        for ($i = 0; $i <= 9; $i++) {
            if (!array_key_exists($i, $sarr)) {
                $sarr[$i] = ['key' => $i + 1, 'spec_id' => null];
                $snameArr[$i + 1] = null;
            }
        }
        //为了给其他函数用
        self::$_spec_v = $snameArr;
        //-----end规格

        $this->assign(array(
            'goods' => mod::get($id),
            'gd' => $gd,
            //所有规格的数据
            'spec' => Goods_spec_value::all(),
            //编辑中商品的规格数据
            'goods_spec'=> Goods_spec::where('goods_id',$id)->select(),
            'class' => (new Goods_classify)->Tree(),
            //编辑的商品归那个类的id
            'classnow'=> (new Goods_classify)
                ->where('cid',mod::where('goods_id',$id)->value('goods_classify_id') )
                ->value('cid'),
            'brand' => GoodsBrand::all(),
            //编辑的商品归那个品牌的id
            'brandNow'=> (new GoodsBrand())
                ->where('goods_brand_id',mod::where('goods_id',$id)->value('goods_brand_id') )
                ->value('goods_brand_id'),
            'gimg' => $imgIdArr,
            'gs' => $sarr, //二维数组
            'nowSpecValueAll'=>$nowSpecValueAll,

        ));
        return $this->fetch();
    }
    //编辑商品界面ajax数据
    public function getspec()
    {
        $r[]=Goods_spec_value::all();
        $r[]=self::$_spec_v;
        return json($r);


//        return json(Goods_spec_value::all());
    }

    //编辑商品接口
    public function editgoodsApi()
    {
        $r = (new mod())->edit();
        if ($r['code']) {
            $this->success($r['msg'], 'lst');
        } else {
            $this->error($r['msg']);
        }
    }

    //删除一个商品接口
    public function delgoods($id)
    {
        $gd = GoodsDescribe::where('goods_id', $id)->delete();
        $gd = $gd ? '商品描述表删除成功 ' : '商品描述表删除失败 ';
        //删除规格表和图片
        $gsmod = new Goods_spec();
        //图片url
        $imgIdArr = $gsmod->where('goods_id', $id)->column('url');
        //删除规格图片
        foreach ($imgIdArr as $item) {
            @unlink('.' . $item);
        }
        $gs = Goods_spec::where('goods_id', $id)->delete();
        $gs = $gs ? '商品规格表删除成功 ' : '商品规格表删除失败 ';
        //删除商品图片
        //查到图片数据id(字符串)
        $imgIdArr = mod::where('goods_id', $id)->value('goods_img_id');
        //转为数组
        $imgIdArr = explode(',', $imgIdArr);
        //为了删除字段
        $imgIdArr1 = $imgIdArr;
        //数组值转为url地址
        foreach ($imgIdArr as $k => $item) {
            $imgIdArr[$k] = Photo_image::where('img_id', $item)->value('img_url');
        }
        //删除图片表数据
        foreach ($imgIdArr1 as $k => $item) {
            Photo_image::where('img_id', $item)->delete();
        }
        //删除url图片
        foreach ($imgIdArr as $item) {
            @unlink('.' . $item);
        }
        //删除商品表
        $g = mod::get($id)->delete();
        $g = $g ? '商品表删除成功 ' : '商品表删除失败 ';
        return json(['code' => 200, 'msg' => $gd . $gs . $g]);
    }

    //批量删除(Batch Remove)
    public function batchRemoveGoods()
    {

        foreach (input('param.') as $item) {
            //删除从上向下
            $r = $this->delGoods($item);
        }
        $this->success('....<-(没错我懒弄)', 'lst');
    }

    //商品上下架ajax
    public function goodsUpAndDown($id)
    {
        $s = mod::where('goods_id', $id)->value('goods_status');
        $s = $s == 1 ? 0 : 1;
        $r = mod::update(['goods_id' => $id, 'goods_status' => $s]);
        $r = $r ? json(['code' => 200]) : json(['code' => 0]);
        return $r;
    }

    //类型列表
    public function gtypelst()
    {
        return $this->fetch();
    }

    //新建类型
    public function gtypenew()
    {
        return $this->fetch();
    }

    //品牌列表

    /**作者：周柏桡
     * @return mixed 输出品牌视图，并且分页显示
     */
    public function gbrandlst()
    {
        $list = Db::name('goods_brand')->order('level')->paginate(5);
        $this->assign('list', $list);
        return $this->fetch();
    }

    //新建品牌
    public function g_brand_new()
    {
        return $this->fetch();
    }


    //规格列表
    public function g_spec_lst()
    {
        $smod = new Goods_spec();
        $svmod = new Goods_spec_value();

        $this->assign(array(
            'spec' => $smod->select(),
            'specVal' => $svmod->select(),
        ));
        return $this->fetch();
    }

    //新建规格渲染
    public function g_spec_new()
    {
        return $this->fetch();
    }

    //新建规格api
    public function addSpec()
    {
        dump(input('param.'));
        $r = (new Goods_spec_value())->add();
        if ($r['code']) {
            $this->success($r['msg'], 'g_spec_lst');
        } else {
            $this->error($r['msg']);
        }
    }

    //编辑规格渲染
    public function g_spec_edit()
    {

        $this->assign(array(
            's1' => Goods_spec_value::get(input('param.id')),

        ));
        return $this->fetch();
    }

    //编辑规格api
    public function editSpec()
    {
        dump(input('param.'));
        $r = (new Goods_spec_value())->edit();
        if ($r['code']) {
            $this->success($r['msg'], 'g_spec_lst');
        } else {
            $this->error($r['msg']);
        }
    }

    //删除一个规格
    public function del_spec($id)
    {
        $r = Goods_spec_value::get($id)->delete();
        if ($r) {
            return json(['code' => 200, 'msg' => '成功']);
        } else {
            return json(['code' => 400, 'msg' => '删除失败']);
        }
    }

    //批量删除
    public function del_batch_spec()
    {
        foreach (input('param.') as $item) {
            $cate = new Goods_spec_value();
            //数据库没有就跳出一次

            //删除从上向下
            $r = $cate->where(['spec_id' => $item])->delete();
        }
        $this->success('..', 'g_spec_lst');
    }

    //分类列表
    public function g_classify_lst()
    {
        $cm = new Goods_classify();

        $page = new ArrPage();
        $this->assign(array(
            //分页
            'classify' => ArrPage::arrPage1($cm->Tree(), 10),
            //分布样式
            'page' => $page->arrCss1(),
        ));
        return $this->fetch();
    }

    //新建分类界面
    public function g_classify_new()
    {
        $cmod = new Goods_classify();
        $this->assign(array(
            'class' => $cmod->Tree(),
        ));

        return $this->fetch();
    }

    //新建分类api
    public function add_classify()
    {
        $r = (new Goods_classify)->add();
        if ($r['code']) {
            $this->success($r['msg'], 'g_classify_lst');
        } else {
            $this->error($r['msg']);
        }
    }

    //编辑分类界面
    public function g_classify_edit()
    {
        $cmod = new Goods_classify();
        $this->assign(array(
            'class' => $cmod->Tree(),
            'own' => $cmod->where('cid', input('id'))->find(),
        ));

        return $this->fetch();
    }

    //编辑分类api
    public function edit_classify()
    {
        //编辑方法
        $r = (new Goods_classify)->edit();
        if ($r['code']) {
            $this->success($r['msg'], 'g_classify_lst');
        } else {
            $this->error($r['msg']);
        }
    }


    //删除一个分类
    public function del_classify($id)
    {
        $r = Goods_classify::get($id)->delete();
        if ($r) {
            return json(['code' => 200, 'msg' => '成功', 'del_son' => $this->_del_son]);
        } else {
            return json(['code' => 400, 'msg' => '删除失败']);
        }
    }

    //删除子分类
    public function del_son()
    {
        $cate = new Goods_classify();

        $res = $cate->getChilrenId(input('id'));

        //子级不为空时执行
        if (!$res == array()) {
            //要删除的子级转给_del_son
            $this->_del_son = $res;
            foreach ($res as $k => $v) {
                $resId['cid'] = $v;
                //删除子级
                $cate->where($resId)->delete();
            }
        }
    }

    //批量删除分类
    public function del_batch_classify()
    {

        foreach (input('param.') as $item) {
            $cate = new Goods_classify();
            //找子级
            $res = $cate->getChilrenId(intval($item));
            //子级不为空时执行
            if (!$res == array()) {
                //要删除的子级转给_del_son
                $this->_del_son = $res;
                foreach ($res as $k => $v) {
                    $resId['cid'] = $v;
                    //删除子级
                    $cate->where($resId)->delete();
                }
            }
            //删除从上向下
            $cate->where(['cid' => $item])->delete();
        }
        $this->success('...', 'g_classify_lst');
    }


    /*作者：周柏桡
     *  处理品牌添加逻辑
     */
    public function add_g_brand_new()
    {
        $data = input('post.');

        //接收图片文件
        $goods_brand_name = request()->file('goods_brand_img');
        //拼接保存图片路径
        $info = $goods_brand_name->move(ROOT_PATH . 'public' . DS . 'uploads');
        //获取图片返回路径
        if ($info) {
            $data['goods_brand_img'] = '/uploads/' . $info->getSaveName();//存储图片路径
            //写入数据操作
            $con = new GoodsBrand($data);
            $sql = $con->allowField(true)->save();
            if ($sql) {
                $this->success('添加品牌成功', 'gbrandlst');
            } else {
                $this->error('添加品牌失败');
            }
        }
    }

    /**作者：周柏桡
     * @param $id
     * 模型前置操作方法 原本图片存在自动删除
     */
    public function goods_brand_del_one($id)
    {
        $con = GoodsBrand::destroy($id);
        if ($con) {
            $this->success('删除品牌成功', 'gbrandlst');
        } else {
            $this->error('删除失败');
        }

    }

    /**作者：周柏桡
     *  执行品牌排序提交处理逻辑
     */
    public function goods_brand_sort()
    {
        $con = new GoodsBrand();
        $level = input('post.');
        foreach ($level as $k => $v) {
            $con->update(['goods_brand_id' => $k, 'level' => $v]);
        }
        $this->success('更新成功');
    }


    //返回json函数
    function retjson($bool = false, $msg = '成功', $msg1 = '错误')
    {
        if ($bool) {
            $j = json([
                'Status' => 200,
                'msg' => $msg
            ]);
        } else {
            $j = json([
                'Status' => 400,
                'msg' => $msg1
            ]);
        }
        return $j;
    }

    //验证类函数
    function val($Mod, $data)
    {
        $validate = new $Mod();

        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
    }
}