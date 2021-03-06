<?php

namespace Back\Controller;

use Common\Tools\BackController;
//use Think\Controller;

class AuthController extends BackController {
  //权限列表
  public function showlist() {
    //获得权限列表信息

    $info = D('Auth')->order('auth_path')->select(); //根据auth_path排序，使得权限按照上下级关系显示
    //设置面包屑
    $bread = array(
        'first' => '权限管理',
        'second' => '权限列表',
        'linkTo' => array(
            '【添加权限】', U('Auth/tianjia')
        ),
    );
    $this->assign('bread', $bread);

    $this->assign('info', $info);
    $this->display();
  }

  //添加权限
  public function tianjia() {
    //展示、收集俩个逻辑
    if(IS_POST){
      $auth = new \Model\AuthModel();
      $data = $auth -> create();
      //通过_after_insert()实现path和level俩个字段维护
      if($auth->add($data)){
        $this -> success('添加权限成功',U('showlist'),1);
      }else{
        $this -> error('添加权限失败',U('tianjia'),1);
      }
    }else{
      /*****获得可供选取的上级权限(level-0/1)******/
      $pinfo = D('Auth')->where(array('auth_level' => array('in', '0,1')))->order('auth_path')->select();
      //SELECT * FROM `php41_auth` WHERE `auth_level` IN ('0','1') ORDER BY auth_path
      $this->assign('pinfo', $pinfo);
      /*****获得可供选取的上级权限(level-0/1)******/
      //dump($pinfo);
      //设置面包屑
      $bread = array(
          'first' => '权限管理',
          'second' => '权限添加',
          'linkTo' => array(
              '【返回】', U('showlist')
          ),
      );
      $this->assign('bread', $bread);
      $this->display();
    }
        
  }







}
