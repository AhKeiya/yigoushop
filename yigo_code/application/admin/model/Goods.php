<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 上午 11:18
 */

namespace app\admin\model;


use think\Model;
use app\admin\validate\Goods as vali;
use think\Request;
use think\Validate;


class Goods extends Model
{
    //添加商品api
    public function add()
    {
        $data = Request::instance()->param(true);
        if ($data == array())
            return ['code' => false, 'msg' => '错误 可能原因:图片过大,图片不能超过2M'];

        $Mod = new $this($data);
        $validate = new vali();//验证类
//        商品验证
        if (!$validate->check($data)) {
            return ['msg' => ($validate->getError()), 'code' => '0'];
        }
        //判断有没有选择分类或品牌
        if ($data['goods_classify_id'] == 0 or $data['goods_brand_id'] == 0)
            return ['code' => false, 'msg' => '没有选择分类或品牌'];
//        商品保存
        $r = $Mod->allowField(true)->save();
        $data['goods_id'] = $Mod->goods_id;

        //商品描述表保存
        $dmod = new GoodsDescribe($data);
        $dr = $dmod->allowField(true)->save();
        //保存失败时删除保存和返回
        if (!($r and $dr)) {
            $dmod->where('des_id', $dmod->des_id)->delete();
            $this->where('goods_id', $data['goods_id'])->delete();
            return ['code' => false, 'msg' => '添加失败 错误:检查商品基本信息'];
        }

        //商品图片保存
        $ir = $this->addimg($data);
        //出错删除和返回
        if ($ir['code'] != 200) {
            $dmod->where('des_id', $dmod->des_id)->delete();
            $this->where('goods_id', $data['goods_id'])->delete();
            return $ir;
        }
        //商品规格保存
        $sr = $this->addspec($data);
        if ($sr['code'] != 200)
            return $sr;
        if ($r) {
            return ['code' => 200, 'msg' => '添加成功'];
        } else {
            $dmod->where('des_id', $dmod->des_id)->delete();
            $this->where('goods_id', $data['goods_id'])->delete();
            //删除商品图
            $t = 0;
            do {
                $imgurl = Photo_image::get($ir['imgarr'][$t])->value('img_url');
                @unlink('.' . $imgurl);
                $t += 1;
            } while (array_key_exists($t, $ir['imgarr']));

            return ['code' => false, 'msg' => '添加失败'];
        }
    }

    //商品规格保存
    public function addspec($data, $specNumber = 1)
    {
        //验证
        $validate = new Validate([
            'spec' . $specNumber => 'require',
            'value' . $specNumber => 'require'
        ]);
        if (!$validate->check($data)) {
            return ['code' => false, 'msg' => $validate->getError()];
        }
        $smod = new Goods_spec();
        $svmod1 = new Goods_spec_value();
        $smod->spec_id = $svmod1->where('spec_name', $data['spec' . $specNumber])->value('spec_id');
        $smod->spec_value = $data['value' . $specNumber];
        $smod->goods_id = $data['goods_id'];
        //有图片则保存
        if (isset($data['img' . $specNumber])) {
            //删除上次的图片
//                @unlink('.'.$user1['thumb']);
            $file = $data['img' . $specNumber];
            // 移动到框架应用根目录/public/uploads/ 目录下
            if ($file) {
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                    $data['url'] = '\\uploads\\' . $info->getSaveName();
                    //图片url
                    $smod->url = $data['url'];
                } else {
                    // 上传失败获取错误信息
                    echo $file->getError();
                }
            }
        }
        //保存
        if ($specNumber == 1) {
            $smod->save();
        } else {
            // 第二次开始必须使用下面的方式新增
            $smod->isUpdate(false)->save();
        }
        //--end 保存---------------
        //额外规格存在则递归
        if (array_key_exists('spec' . ($specNumber + 1), $data) and $specNumber <= 10)
            $this->addspec($data, $specNumber + 1);
        return ['code' => 200];
    }

    //商品图片保存
    public function addimg($data)
    {
        $t = 0;
        $imgId = '';
        //判断有多少张上传
        for ($i = 1; $i <= 5; $i++) {
            if (isset($data['goodsimg' . $i]))
                $t += 1;
        }
        //少于3张时结束返回
        if ($t < 3)
            return ['code' => false, 'msg' => '商品预览图片最少3张,图片不能大于2M'];
        //保存图片id(数组)
        $arrimg = array();
        //循环保存图片
        for ($i = 1; $i <= 5; $i++) {
            $imgmod = new Photo_image();
            //有图片则保存
            if (isset($data['goodsimg' . $i])) {
                $file = $data['goodsimg' . $i];
                // 移动到框架应用根目录/public/uploads/ 目录下
                if ($file) {
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if ($info) {
                        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                        $data['url'] = '\\uploads\\' . $info->getSaveName();
                        //图片url
                        $imgmod->img_url = $data['url'];
                    } else {
                        // 上传失败获取错误信息
                        //已删除上传的图片和字段
                        for ($a = $i - 2; $a >= 0; $a--) {
                            $delarr = explode(',', $imgId);
                            //查询图片url
                            $delurl = Photo_image::Where('img_id', $delarr[$a])->value('img_url');
                            //删除图片
                            @unlink('.' . $delurl);
                            //删除字段
                            Photo_image::get($delarr[$a])->delete();
                        }
                        return ['code' => false, 'msg' => '第' . $i . '图上传错误'];
                    }
                }
                $imgmod->photo_id = 1;
                $imgmod->allowField(true)->save();
                if ($i == 1) {
                    $imgId = $imgmod->img_id;
                    $arrimg[] = $imgmod->img_id;
                    //主图id
                    $img1 = $imgId;
                } else {
                    $imgId .= ',' . $imgmod->img_id;
                    $arrimg[] = $imgmod->img_id;
                }
            }
        }
        $this->update(['goods_id' => $data['goods_id'], 'goods_img_id' => $imgId, 'goods_img' => $img1]);
        return ['code' => 200, 'imgarr' => $arrimg];
    }

    //编辑商品api
    public function edit()
    {
        $data = Request::instance()->param(true);
        if ($data == array()) {
            return ['code' => 0, 'msg' => '错误:可能图片过大'];
        }
        $validate = new vali();//验证类
        //商品验证
        if (!$validate->check($data)) {
            return ['msg' => ($validate->getError()), 'code' => '0'];
        }
        //商品信息1保存
        $gr = $this->allowField(true)->isUpdate(true)->save($data);
        $msg1 = $gr ? '信息1更新成功<br>' : '信息1更新失败<br>';
        //商品信息2
        $data['des_id'] = (new GoodsDescribe())->where('goods_id', $data['goods_id'])->value('des_id');
        $gdr = (new GoodsDescribe)->allowField(true)->isUpdate(true)->save($data);
        $msg2 = $gdr ? '信息2更新成功<br>' : '信息2更新失败<br>';
        //商品图片更新
        $this->editImg($data);
        //商品规格更新
        $this->editspec($data);
        //
        //商品图片保存
        return ['code' => 200, 'msg' => $msg1 . $msg2];
    }

    //编辑商品更新图片
    public function editImg($data)
    {
        //商品图片保存
        $gnow = $this->where('goods_id', $data['goods_id']);
        $imgNum = explode(',', $gnow->value('goods_img_id'));
        for ($i = 1; $i <= 5; $i++) {
            //要更新的图片
            if (array_key_exists($i - 1, $imgNum)) {
                //有图片上传就更新
                if (isset($data['goodsimg' . $i])) {
                    //删除原来的图片
                    $delurl = Photo_image::Where('img_id', $imgNum[$i - 1])->value('img_url');
                    //删除图片
                    @unlink('.' . $delurl);
                    $file = $data['goodsimg' . $i];
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    if ($file) {
                        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                        if ($info) {
                            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                            $url = '\\uploads\\' . $info->getSaveName();
                            (new Photo_image())->isUpdate(true)->save(['img_url' => $url], ['img_id' => $imgNum[$i - 1]]);
                        }
                    }
                }
            } else {
                //新增图片
                if (isset($data['goodsimg' . $i])) {
                    $file = $data['goodsimg' . $i];
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    if ($file) {
                        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                        if ($info) {
                            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                            $url = '\\uploads\\' . $info->getSaveName();
                            $imgMod = new Photo_image();
                            $imgMod->isUpdate(false)->save(['img_url' => $url, 'photo_id' => 1]);
                            $imgNum[$i - 1] = $imgMod->img_id;

                        }

                    }
                }
            }
        }
        $strimgId = implode(',', $imgNum);
        $this->isUpdate(true)->save(['goods_img_id' => $strimgId], ['goods_id' => $data['goods_id']]);
    }

    //商品规格保存
    public function editspec($data)
    {
        //商品规格保存
        $gnow = $this->where('goods_id', $data['goods_id']);
        $specNow = (new Goods_spec)->where('goods_id', $data['goods_id']);
        $smod = new Goods_spec();
        //已有的规格数
        $imgarr['count'] = $specNow->count();
        //已有的规格全部数据
        $imgarr['all'] = (new Goods_spec)->where('goods_id', $data['goods_id'])->select();
        //循环10个规格
        for ($i = 1; $i <= 10; $i++) {
            //判断有没有规格,有则更新或增加----没有则删除规格,数据库没有则不删除
            if ($data['spec']['name'][$i] !== '0') {
                //要更新的规格
                if ($i <= $imgarr['count']) {
                    //要更新的模型
                    $upSMod= Goods_spec::get($imgarr['all'][$i - 1]['id']);
                    //有图片上传就更新
                    if (isset($data['specimg' . $i])) {
                        //删除原来的图片
                        $delurl = $smod->Where('id', $imgarr['all'][$i - 1]['id'])->value('url');
                        //删除图片
                        @unlink('.' . $delurl);
                        $file = $data['specimg' . $i];
                        if ($file) {
                            // 移动到框架应用根目录/public/uploads/ 目录下
                            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                            if ($info) {
                                // 上传的图片保存的url
                                $upSMod->url = '\\uploads\\' . $info->getSaveName();
                            }
                        }
                        //有图片的更新数据
                    }
                    $svId = Goods_spec_value::where('spec_name', $data['spec']['name'][$i])->value('spec_id');//本次规格id
                    $upSMod->spec_id=$svId;
                    $upSMod->spec_value=$data['value']['name'][$i];
                    $upSMod->isUpdate()->save();
                    dump('$i='.$i);
                    dump($data['value']['name'][$i]);
                } else {//新增规格
                    //新增的模型
                    $newSMod= new Goods_spec();
                    //有没图片
                    if (isset($data['specimg' . $i])) {
                        $file = $data['specimg' . $i];
                        // 移动到框架应用根目录/public/uploads/ 目录下
                        if ($file) {
                            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                            if ($info) {
                                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                                $newSMod->url = '\\uploads\\' . $info->getSaveName();
                            }
                        }
                    }
                    //新增
                    $newSMod->isUpdate(false)->save([
                        'spec_id' => (new Goods_spec_value())->where('spec_name',$data['spec']['name'][$i])->value('spec_id'),
                        'goods_id' => $data['goods_id'],
                        'spec_value' => $data['value']['name'][$i],
                    ]);
                    dump('$i='.$i);
                    dump($data['value']['name'][$i]);
                }
            } else {
                //数据库有则删除规格
                if ($i <= $imgarr['count']) {
                    //图片url
                    $delurl = (new Goods_spec())->Where('id', $imgarr['all'][$i - 1]['id'])->value('url');
                    //删除图片
                    @unlink('.' . $delurl);
                    //删除库数据
                    Goods_spec::get($imgarr['all'][$i - 1]['id'])->delete();
                }
            }
        }
    }

}