<?php

//管理员模型

namespace Model;

use Think\Model;

class RoleModel extends Model {

    //更新数据前的回调方法
    protected function _before_update(&$data, $options) {
        if ($_POST['now_act'] == '分配权限') {
            //维护俩个数据role_auth_ids / role_auth_ac
            //dump($options);
            //dump($data);  
            // 1)制作role_auth_ids
            $data['role_auth_ids'] = implode(',', $data['role_auth_ids']);

            // 2)制作role_auth_ac
            $authinfo = D('Auth')->where("auth_id in ({$data['role_auth_ids']})")->select();
            //dump($authinfo);
            //从authinfo中获得auth_c和auht_c并拼装
            $s = "";
            foreach ($authinfo as $k => $v) {
                if (!empty($v['auth_a']) && !empty($v['auth_c']))
                    $s.=$v['auth_c'] . "-" . $v['auth_a'] . ",";
            }
            $s = rtrim($s, ',');
            $data['role_auth_ac'] = $s;
        }
    }

    //更新数据后的回调方法
    protected function _after_update($data, $options) {
        
    }

}
