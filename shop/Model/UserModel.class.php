<?php
//模型
namespace Model;
use Think\Model;

class UserModel extends Model{
    //字段映射定义
    //把form表单中自定义字段，变为数据表合法字段
    protected $_map = array(//$_map TP框架映射机制
        'name'       => 'user_name',//把表单中name映射到数据表的user_name字段
        'password'   => 'user_pwd',//把表单中password映射到数据表的user_pwd字段
        'email'      => 'user_email',//把表单中email映射到数据表的user_email字段
    );
    /*****自动完成(填充字段信息)*****/
    protected $_auto = array(//$_auto TP框架自动完成机制
        array('add_time','time',1,'function'),//添加记录完成add_time的填充
        array('user_pwd','md5',1,'function'),//添加记录完成user_pwd加密的填充
    );
    /*****自动完成(填充字段信息)*****/

    //瞻前顾后机制(顾后)
    protected function _after_insert($data,$options){
        //dump($data);
        //dump($options);
        //判断当前的动作为“注册”，并发送邮件
        if($_POST['act']=='regist'){
            //生成校验码
            $code = md5(uniqid());//生成一个唯一的校验码信息
            $this -> setField(array('user_id'=>$data['user_id'],'user_check_code'=>$code));//更新校验码到会员记录

            //具体邮件发送
            //sendMail(注册邮箱,title,content);
            //$link = "http://www.shop.net/index.php/Home/User/jihuo/user_id/".$data['user_id']."/checkcode/".$code
            $link = rtrim(C('SITE_URL'),'').U('User/jihuo',array('user_id'=>$data['user_id'],'checkcode'=>$code));
            sendMail($data['user_email'],'会员注册激活',"请点击以下超链接，激活你的账号：<a href='".$link."' target='_blank'>".$link."</a>");
        }
    }

}

