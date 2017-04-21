alter table php41_goods add is_qiang enum('不抢','抢') not null default '不抢' comment '是否抢购' after is_sale;

--增加一个中图字段
alter table php41_goods_pics add pics_mid char(100) not null comment '相册中图' after pics_big;

--"商品-(多对多)-属性"中间联系表
create table php41_goods_attr(
    id mediumint unsigned not null auto_increment comment '主键id',
    goods_id mediumint unsigned not null comment '商品id',
    attr_id mediumint unsigned not null comment '属性id',
    attr_value varchar(64) not null default '' comment '属性对应的值',
    primary key (id),
    key (goods_id),
    key (attr_id)
)engine=Myisam charset=utf8 comment '商品-属性关联表';

--"分类"数据表
drop table if exists php41_category;
create table php41_category(
    cat_id smallint unsigned not null auto_increment comment '主键id',
    cat_name varchar(32) not null comment '分类名称',
    cat_pid smallint unsigned not null default 0 comment '上级id',
    cat_path varchar(32) not null comment '全路径',
    cat_level tinyint not null default 0 comment '等级',
    primary key (cat_id),
    key (cat_pid)
)engine=Myisam charset=utf8 comment '商品分类表';

--"商品-(多对多)-分类"中间联系表
drop table if exists php41_goods_cat;
create table php41_goods_cat(
    id mediumint unsigned not null auto_increment comment '主键id',
    goods_id mediumint unsigned not null comment '商品id',
    cat_id mediumint unsigned not null comment '分类id',
    primary key (id),
    key (goods_id),
    key (cat_id)
)engine=Myisam charset=utf8 comment '商品-分类关联表';

--会员表
drop table if exists php41_user;
create table php41_user(
    user_id mediumint unsigned not null auto_increment comment '主键id',
    user_name varchar(32) not null comment '会员名称',
    user_email varchar(64) not null default '' comment '会员邮箱',
    user_pwd char(32) not null comment '密码',
    user_sex enum('男','女','保密') not null default '男' comment '性别',
    user_weight smallint not null default 0 comment '体重',
    user_height decimal(5,2) not null default 0 comment '身高',
    user_logo varchar(128) not null default '' comment '头像',
    user_tel char(11) not null default '' comment '手机号',
    user_identify char(18) not null default '' comment '身份号码',
    user_check enum('0','1') not null default '0' comment '是否激活，0:未激活 1:已激活', 
    user_check_code char(32) not null default '' comment '邮箱验证激活码',
    add_time int not null comment '注册时间',
    is_del enum('0','1') not null default '0' comment '是否删除，0:正常 1:被删除',
    primary key (user_id),
    key (user_name),
    key (user_tel)
)engine=Myisam charset=utf8 comment '会员表';

----- 一对多关系的数据表要“分别”创建
--收货人地址信息表(会员表----(一对多)----收货人表)
drop table if exists php41_consignee;
create table php41_consignee(
    cgn_id mediumint unsigned not null auto_increment comment '主键id',
    user_id mediumint unsigned not null comment '会员id',
    province smallint unsigned not null comment '省份',
    city smallint unsigned not null comment '城市',
    area smallint unsigned not null comment '地区',
    detail_addr varchar(256) not null comment '详细地址',
    tel char(11) not null comment '联系人手机',
    post char(6) not null comment '邮编号码',
    status tinyint not null default 0 comment '0:正常 1:默认地址',
    receiver_name varchar(32) not null default '' comment '昵称',
    primary key (cgn_id),
    key (tel),
    key (user_id) 
)engine=Myisam charset=utf8 comment '收货人信息表';

--订单表(订单表----(一对多)----订单商品表)
drop table if exists php41_order;
create table php41_order(
    order_id mediumint unsigned not null auto_increment comment '主键id',
    user_id mediumint unsigned not null comment '会员id',
    cgn_id mediumint unsigned not null comment '订单关联的收货地址',
    order_number char(20) not null comment '订单编号',
    order_price decimal(10,2) not null default 0 comment '订单商品总价格',
    order_mail varchar(32) not null comment '送货快递公司', 
    oder_pay varchar(32) not null comment '支付方式',
    order_fapiao_status tinyint not null default 0 comment '0:无，1:个人，2:公司',
    order_fapiao varchar(40) not null default '' comment '发票标题抬头',
    order_atatus tinyint not null default 0 comment '状态：0未支付 1已支付 2未确认 3已经确认 4已经发货 5正在派送 6已到货 7已签收',
    order_cancel tinyint not null default 0 comment '0:正常，1:取消',
    add_time int not null comment '下单时间',
    primary key (order_id),
    key (user_id),
    key (order_number)
)engine=Myisam charset=utf8 comment '订单表';

--订单商品表
drop table if exists php41_order_goods;
create table php41_order_goods(
    id mediumint unsigned not null auto_increment comment '主键id',
    goods_id mediumint not null comment '商品id',
    goods_name varchar(32) not null comment '商品名称',
    order_id mediumint unsigned not null comment '订单id',
    good_price decimal(10,2) not null default 0 comment '商品价格',
    goods_number tinyint not null default 1 comment '购买数量',
    goods_attr varchar(100) not null default '' comment '商品(多选)属性',
    primary key (id),
    key (goods_id),
    key (order_id) 
)engine=Myisam charset=utf8 comment '订单商品表';

--商品评论表
drop table if exists php41_comment;
create table php41_comment(
    cmt_id mediumint unsigned not null auto_increment comment '主键id',
    cmt_msg text comment '评论内容',
    user_id mediumint unsigned not null comment '会员id',
    goods_id mediumint unsigned not null comment '商品id',
    cmt_star tinyint not null default 1 comment '1-2-3-4-5星级
    ',
    cmt_zhan mediumint unsigned not null default 0 comment '点赞的数量',
    add_time int not null comment '生成记录时间',
    primary key (cmt_id),
    key (goods_id),
    key (user_id) 
)engine=Myisam charset=utf8 comment '商品评论表';

--评论回复表
drop table if exists php41_comment_back;
create table php41_comment_back(
    back_id mediumint unsigned not null auto_increment comment '主键id',
    back_msg text comment '回复内容',
    cmt_id mediumint unsigned not null comment '评论id',
    user_id mediumint unsigned not null comment '会员id', 
    add_time int not null comment '生成记录时间',
    primary key (back_id),
    key (cmt_id),
    key (user_id) 
)engine=Myisam charset=utf8 comment '商品评论回复表';

--是否点赞的比较表
drop table if exists php41_havezhan;
create table php41_havezhan(
    cmt_id mediumint unsigned not null comment '评论id',
    user_id mediumint unsigned not null comment '会员id', 
    primary key (cmt_id,user_id)
)engine=Myisam charset=utf8 comment '是否点赞的比较表';


