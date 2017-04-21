<?php

namespace Home\Controller;

use Common\Tools\HomeController;//前台所有控制器，都继承新的父类控制器HomeController;
//use Think\Controller;

class IndexController extends HomeController {
    public function index() {
        /*****获得推荐商品信息*****/
        //memcache中有数据直接传递给模板使用 如果没有数据就走if，从数据库获得数据
        //推荐商品设置一个key
        S(array('type'=>'memcache','host'=>'localhost','port'=>'11247'));//端口号
        $tuijian_key = md5("qiang_rec_hot_new_like");
        $info = S($tuijian_key);//读取memcache数据
        if(empty($info)){
            //echo "此时走数据库";
            $goods = D('Goods');
            $cdt['is_del'] = "不删除";
            $cdt['is_sale'] = "上架";

            //①抢购的
            $cdt_q = $cdt;
            $cdt_q['is_qiang'] = "抢";
            $info_qiang = $goods -> where($cdt_q) -> order('goods_id desc') -> limit(5) -> select();
            //SELECT * FROM `php41_goods` WHERE `is_del` = '不删除' AND `is_sale` = '上架' AND `is_qiang` = '抢' ORDER BY goods_id desc LIMIT 5
            //获得抢购的商品id信息
            $ids_q = arrayToString($info_qiang,'goods_id');
            // dump($ids_q);//string(14) "40,39,38,33,32"
            
            //②热销的
            $cdt_h = $cdt;
            $cdt_h['is_hot'] = "热销";
            $cdt_h['goods_id'] = array('not in',$ids_q);//排除抢购的，剩下的是热销的
            $info_hot = $goods -> where($cdt_h) -> order('goods_id desc') -> limit(5) -> select();
            //SELECT * FROM `php41_goods` WHERE `is_del` = '不删除' AND `is_sale` = '上架' AND `is_hot` = '热销' AND `goods_id` NOT IN ('40','39','38','33','32') ORDER BY goods_id desc LIMIT 5 
            //获得热销的商品id信息
            $ids_h = arrayToString($info_hot,'goods_id');

            //③推荐的
            $cdt_c = $cdt;
            $cdt_c['is_rec'] = "推荐";
            //排除抢购的、热销的，剩下的是推荐的
            $cdt_c['goods_id'] = array('not in',$ids_q.",".$ids_h);
            $info_rec = $goods -> where($cdt_c) -> order('goods_id desc') -> limit(5) -> select();
            $ids_c = arrayToString($info_rec,'goods_id');

            //④新品的
            $cdt_n = $cdt;
            $cdt_n['is_new'] = "新品";
            //排除抢购的、热销的、推荐的，剩下的是新品的
            $cdt_n['goods_id'] = array('not in',$ids_q.",".$ids_h.",".$ids_c);
            $info_new = $goods -> where($cdt_n) -> order('goods_id desc') -> limit(5) -> select();
            //SELECT * FROM `php41_goods` WHERE `is_del` = '不删除' AND `is_sale` = '上架' AND `is_new` = '新品' AND `goods_id` NOT IN ('40','39','38','33','32','35','31','30','29','27','28','25','19') ORDER BY goods_id desc LIMIT 5
            $ids_n = arrayToString($info_new,'goods_id');

            //⑤猜你喜欢
            $cdt_l = $cdt;
            $cdt_l['goods_id'] = array('not in',$ids_q.",".$ids_h.",".$ids_c.",".$ids_n);
            $info_like = $goods -> where($cdt_l) -> order('goods_id desc') -> limit(5) -> select();
            $ids_l = arrayToString($info_like,'goods_id');

            //把查询好的数据放到memcache中
            //走数据库获得数据，之后把数据存入memcache中  供下次使用
            $info['qiang'] = $info_qiang;
            $info['rec'] = $info_rec;
            $info['hot'] = $info_hot;
            $info['new'] = $info_new;
            // $info['like'] = $info_like;
            S($tuijian_key,$info);
        }
       
        $this -> assign('info_qiang',$info_qiang);
        $this -> assign('info_hot',$info_hot);
        $this -> assign('info_rec',$info_rec);
        $this -> assign('info_new',$info_new);
        $this -> assign('info_like',$info_like);
        /*****获得推荐商品信息*****/

        $this->display();
    }
    
 
}
