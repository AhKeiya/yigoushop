<?php
/**
 *数组分页
 * User: Administrator
 * Date: 2018/7/13 0013
 * Time: 上午 10:51
 *
 *
 *
 */

namespace app\admin\controller;


class ArrPage
{
    //总页数
    private static $pnum;
    //当前页数
    private static $page;

    //url的分页名字
    private static $urlName='p';

    protected static function test()
    {
        echo 1111;
    }
    //数组分页
    public static function arrPage1($array,$size=5)
    {

        if(array_key_exists(self::$urlName,$_GET))
        {
            self::$page=intval($_GET[self::$urlName]);
        }else
        {
            self::$page=1;
        }

        //多少页
        self::$pnum = ceil(count($array) / $size);
        //分布核心
        $newArray = array_slice($array,(self::$page-1)*$size,$size);

        return $newArray;
    }
    //最基本的样式
    public  function arrPageCss()
    {
        $echo='';
        $echo .= "<br/><br/>";
        $echo .= "<a href=?>第一页</a>\n";

        for($i=1;$i<=self::$pnum;$i++)
        {
            $echo .= "<a href=\"?page=$i\" target=\"_blank\"";
            if($i==self::$page)
            {
                $echo .= "style='color:red;'";
            };
            $echo .= "> ".$i."</a>\n\n";
        }
        $echo .= "<a href=?page=".self::$pnum.">最后一页</a>\n";

        return $echo;
    }
    //beyond admin样式
    public function arrCss1($ulClass='pagination',$liClass='')
    {
        $t='';
//        $ulClass='pagination';
//        $liClass='';

        $t.= '<ul class="'.$ulClass.'">';
        $t.= '<li class="'.$liClass.'"><a href=?>第一页</a></li>';

        //缩短样式
        if(self::$pnum>=10)
        {
            if(self::$pnum<self::$page+9)
            {
                $end=self::$pnum;
                $begin=self::$pnum-9;
            }elseif(self::$page<5){
                $begin=1;
                $end=10;
            }else{
                $begin=self::$page-4;
                $end=self::$page+5 ;
            }

            for($i=$begin;$i<=$end;$i++)
            {
                $t.= "<li class='$liClass'><a href='?".self::$urlName."=$i' target=\"_blank\"";
                if($i==self::$page)
                {
                    $t.= "style='color:red;'";
                };
                $t.= "> ".$i."</a></li>";
            }
            $t.='<li class="'.$liClass.'"><a>...</a></li>';

            $t.= '<li class="'.$liClass.'">
                   <a href=?'.self::$urlName.'='.self::$pnum.'>最后一页共'.self::$pnum.'</a>
                   </li>
                   
                   </ul>';

            return $t;
        }else{
            for($i=1;$i<=self::$pnum;$i++)
            {
                $t.= "<li class='$liClass'><a href=\"?".self::$urlName."=$i\" target=\"_blank\"";
                if($i==intval(self::$page))
                {
                    $t.= "style='color:red;'";
                };

                $t.= "> ".$i."</a></li>";
            }
            $t.= '<li class="'.$liClass.'"><a href=?'.self::$urlName.'='.self::$pnum.'>最后一页</a></li></ul>';

            return $t;
        }
    }

}