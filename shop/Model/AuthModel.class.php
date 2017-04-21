<?php

//权限模型

namespace Model;

use Think\Model;

class AuthModel extends Model {

    //更新数据前的回调方法
    protected function _after_insert($data, $options) {
        //维护两个字段:auth_puth/auth_level
        //dump($data);exit; //主键id
        
        //1)维护auth_path
        //1>顶级权限:等于"主键id值"
        if($data['auth_pid'] == 0){
            $path = $data['auth_id'];
        }else{
            //2>非顶级权限：等于"父级全路径-主键id值"(参见数据表)
            //获得父级全路径
            $pinfo = $this -> field('auth_path') ->find($data['auth_pid']); 
            $ppath = $pinfo['auth_path'];

            $path = $ppath."-".$data['auth_id'];
        }

        //2)维护等级auth_level,等于 全路径"-"横杠的个数
        $level = substr_count($path,'-'); //计算$path内部"-"横杠的geshu

        $this -> save(array('auth_id'=>$data['auth_id'],'auth_path'=>$path,'auth_level'=>$level));
    }

}
