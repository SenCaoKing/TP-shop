<?php
namespace Home\Controller;
use Common\Tools\HomeController;//前台所有控制器，都继承新的父类控制器HomeController;
//use Think\Controller;

class UserController extends HomeController {
    //用户登录
    public function login() {
        //俩个逻辑：展示、收集
        if(IS_POST){
            //用户名/密码校验
            $user = D('User');
            $name = $_POST['user_name'];
            $pwd = $_POST['user_pwd'];
            $info = $user->where(array('user_name'=>$name,'user_pwd'=>md5($pwd)))->find();
            if($info){
                //持久化用户信息
                session('user_id',$info['user_id']);
                session('user_name',$name);
                //页面跳转
                //$this -> redirect($url,$params=array(),$delay=间隔时间,$msg='');
                
                //判断用户是否有定义要跳转到的地址
                $back_url = session('back_url');
                if(!empty($back_url)){
                    session('back_url',null);//销毁，该地址只用一次
                    $this -> redirect($back_url);
                }
                $this -> redirect('Index/index');
            }
            $this -> error('用户名或密码不存在',U('login'),1);
        }else{
            $this->display();            
        }
    }

    //用户注册
    public function regist() {
        //两个逻辑：展示、收集
        $user = new \Model\UserModel();
        if(IS_POST){
            //dump($_POST);
            $data = $user -> create();//过滤非法字段【手册】
            //dump($data);
            if($user->add($data)){
                $this -> success('注册成功',U('Index/index'),1);
            }else{
                $this -> error('注册失败',U('login'),1);
            }
        }else{
            $this->display();            
        }
    }

    //验证码
    function verifyImg(){
        //显示验证码
        $cfg = array(
            'imageH'    => 40,    //验证码图片高度
            'imageW'    => 100,   //验证码图片宽度
            'length'    => 4,     //验证码位数
            'fontttf'   => '4.ttf',//验证码图片背景
            //验证码字体，不设置随机获取
            'fontSize'  => 15,
        );
        $very = new \Think\Verify($cfg);//显示验证码
        $very -> entry();
    }

    //ajax过来校验验证码
    function checkCode(){
        $code = I('get.code');//获得用户输入的验证码
        $vry = new \Think\Verify();
        if($vry -> check($code)){
            echo json_encode(array('status'=>1));
        }else{
            echo json_encode(array('status'=>2));
        }
    }

    //会员邮箱激活
    function jihuo(){
        $user_id = I('get.user_id');
        $checkcode = I('get.checkcode');

        //更改user_check=1,user_check_code=null
        $user = D('User');
        //首先需要验证，再激活
        $userinfo = $user -> where(array('user_check'=>0)) -> find($user_id);
        if($userinfo['user_check_code']===$checkcode){
            //验证码比较成功再激活
            $z = $user -> setField(array('user_id'=>$user_id,'user_check'=>1,'user_check_code'=>''));
            if($z){
                $this -> success('会员激活成功',U('login',1));
            }
        }else{
            $this -> error('操作有错误或账号已经激活',U('login'),1);
        }
    }

}
