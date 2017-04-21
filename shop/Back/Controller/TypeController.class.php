<?php

namespace Back\Controller;

use Common\Tools\BackController;
//use Think\Controller;

class TypeController extends BackController {
  //类型列表
  public function showlist() {  
    //获得类型列表信息
    $info = D('Type') -> select();

    //设置面包屑
    $bread = array(
        'first' => '类型管理',
        'second' => '类型列表',
        'linkTo' => array(
            '【添加类型】', U('tianjia')
        ),
    );
    $this->assign('bread', $bread);

    $this -> assign('info',$info);
    $this->display();
  }

  //添加权限
  public function tianjia() {
    //展示、收集两个逻辑
    if(IS_POST){
      $auth = new \Model\TypeModel();
      $data = $auth -> create();
      if($auth->add($data)){
        $this -> success('添加类型成功',U('showlist'),1);
      }else{
        $this -> error('添加类型失败',U('tianjia'),1);
      }
    }else{
      //设置面包屑
      $bread = array(
          'first'  => '类型管理',
          'second' => '类型列表',
          'linkTo' => array(
              '【返回】',U('showlist')
            ),
        );
      $this -> assign('bread',$bread);
      $this -> display();
      }      
  }







}
