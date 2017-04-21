<?php

namespace Home\Controller;

use Common\Tools\HomeController;//前台所有控制器，都继承新的父类控制器HomeController;
//use Think\Controller;

class GoodsController extends HomeController {
    //商品列表展示
    public function showlist(){
        $cat_id = I('get.cat_id');

        $goods = new \Model\GoodsModel();
        $cdt['is_del'] = '不删除';

        /*****关联分类条件*****/
        //获取当前选取的分类 和 其内部全部子分类，并合并为一个集合
        //商品的主分类、扩展分类必须在此集合中
        //获得全部子级分类
        //全路径以当前选取分类的全路径为开始内容的分类信息
        $cat = D('Category');
        $now_cat = $cat -> find($cat_id); //当前选取的分类信息【此处 find()内的参数不要单引号！】
        $now_path = $now_cat['cat_path'];
        $ext_cat = D('Category') -> field('cat_id') -> where("cat_path like '$now_path%'") -> select();
        //SELECT `cat_id` FROM `php41_category` WHERE ( cat_path like '2%' ) 
        //dump($ext_cat);//选取的、子级的都存在
        
        $s = "";
        foreach($ext_cat as $k => $v){
            $s .= $v['cat_id'].",";
        }
        $s = rtrim($s,',');
        //dump($s); //string(22) "2,10,11,16,17,18,19,20"
        
        //获得商品列表，条件是：“主分类” 或 “扩展分类”都在$s里边
        $sql = "select goods_id from __GOODS__ where cat_id in ($s) union select goods_id from __GOODS_CAT__ where cat_id in ($s)";
        $ids = D() -> query($sql);
        //dump($ids);//二维数组信息，符合要求的商品id
        
        $w = "";
        foreach($ids as $kk => $vv){
            $w .= $vv['goods_id'].",";
        }
        $w = rtrim($w,',');
        //dump($w);
        //从$w的条件里边，获得需要的商品列表信息
        $cdt['goods_id'] = array('in',$w);
        /*****关联分类条件*****/

        $info = $goods -> where($cdt) -> order('goods_id desc') -> select();
        //SELECT * FROM `php41_goods` WHERE `is_del` = '不删除' AND `goods_id` IN ('40') ORDER BY goods_id desc
        //dump($info);
        $this -> assign('info',$info);
        
        $this -> display();
    }

    //商品详情页面
    function detail(){
        /*****获取商品的详细信息*****/
        $goods_id = I('get.goods_id');
        $info = D('Goods') -> find($goods_id);
        $this -> assign('info',$info);
        /*****获取商品的详细信息*****/

        /*****获取商品的主、扩展分类信息*****/
        //主、扩展分类
        $sql = "select cat_id from __GOODS__ where goods_id='$goods_id' union select cat_id from __GOODS_CAT__ where goods_id='$goods_id'";
        $cat_ids = D() -> query($sql);
        //从二维数组获得一个指定字段的字符串信息
        $cat_ids = arrayToString($cat_ids,'cat_id');
        //根据$cat_ids获得所有的分类信息(根据全路径排序)
        $catinfo = D('Category')
        ->where(array('cat_id'=>array('in',$cat_ids)))
        ->order('cat_path')
        ->select();
        //dump($catinfo);
        $this -> assign('catinfo',$catinfo);
        /*****获取商品的主、扩展分类信息*****/

        /*****获取商品的“多选”属性信息*****/
        //数据表：php41_goods_attr、php41_attribute
        $attrinfo = D('GoodsAttr')
        ->alias('ga')
        ->join('__ATTRIBUTE__ a on ga.attr_id=a.attr_id')
        ->where(array('a.attr_is_sel'=>1,'ga.goods_id'=>$goods_id))
        ->field('a.attr_id,a.attr_name,ga.attr_value')
        ->select();
        //dump($attrinfo);//打印结果为二维数组
        //把$attrinfo的二维数组(多选属性值)变为三维数组
        $attrinfoT = array();
        foreach($attrinfo as $k => $v){
            $attrinfoT[$v['attr_id']]['attr_id'] = $v['attr_id'];
            $attrinfoT[$v['attr_id']]['attr_name'] = $v['attr_name'];
            $attrinfoT[$v['attr_id']]['attr_value'][] = $v['attr_value'];
        }
        //dump($attrinfoT);
        $this -> assign('attrinfoT',$attrinfoT);
        /*****获取商品的“多选”属性信息*****/

        /*****获取商品的相关分类信息*****/
        //获得商品的最后一级分类
        $last_catinfo = $catinfo[count($catinfo)-1];//count()统计数量，参数是要统计的字段名
        //dump($last_catinfo);
        //获得最后一级分类的同级分类信息(它们的父id一致，排除最后一级分类)
        $other_catinfo = D('Category')
        ->where(array('cat_pid'=>$last_catinfo['cat_pid'],'cat_id'=>array('neq',$last_catinfo['cat_id'])))
        ->select();
        //SELECT * FROM `php41_category` WHERE `cat_pid` = 21 AND `cat_id` <> '25
        $this -> assign('other_catinfo',$other_catinfo);
        /*****获取商品的相关分类信息*****/
        /*****获取商品的相册信息*****/
        $goodspics = D('GoodsPics') -> where(array('goods_id'=>$goods_id)) -> select();
        $this -> assign('goodspics',$goodspics);
        /*****获取商品的相册信息*****/

        /*****获取商品的“单选”属性信息*****/
        $attrinfoS = D('GoodsAttr')
        ->alias('ga')
        ->join('__ATTRIBUTE__ a on ga.attr_id=a.attr_id')
        ->where(array('a.attr_is_sel'=>0,'ga.goods_id'=>$goods_id))
        ->field('a.attr_id,a.attr_name,ga.attr_value')
        ->select();
        $this -> assign('attrinfoS',$attrinfoS);
        //dump($attrinfoS);
        /*****获取商品的“单选”属性信息*****/


        $this -> display();
    }

}
