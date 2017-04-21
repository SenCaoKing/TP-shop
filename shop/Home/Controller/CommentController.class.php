<?php
namespace Home\Controller;
use Common\Tools\HomeController;//前台所有控制器，都继承新的父类控制器HomeController;
//use Think\Controller;

class CommentController extends HomeController {
    //添加评论
    function sendComment(){
        $star = I('post.star');
        dump($star);
        $msg = I('post.msg');
        $goods_id = I('post.goods_id');
        $comment = new \Model\CommentModel();
        $data = $comment -> create();
        //富文本编辑器内容避免tp框架的符号实体转换
        $data['cmt_msg'] = $_POST['cmt_msg'];

        if($comment -> add($data)){
            echo json_encode(array('status'=>1,'info'=>'添加评论成功！'));
        }else{
            echo json_encode(array('status'=>2,'info'=>'添加评论失败'));
        }
    }



}
