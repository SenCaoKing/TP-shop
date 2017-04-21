<?php

//权限模型

namespace Model;

use Think\Model;

class AttributeModel extends Model {
    //表单自动验证(由create()方法触发)
    protected $_validate = array(
            //类型验证
            array('type_id','0','类型必须选择',0,'notequal'),
        );
}
