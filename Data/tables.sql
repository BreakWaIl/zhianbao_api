-- 小云铺数据库表结构 
--  
-- 
-- 建表备注：
-- 每个表一个自增ID
-- 时间：创建时间 create_time int(10) unsigned NOT NULL，更新时间：update_time int(10) unsigned NOT NULL
-- 金额：decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT ''

-- 商家店铺表结构
DROP TABLE IF EXISTS `user_store`;
CREATE TABLE `user_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',  
  `alias` varchar(10) NOT NULL COMMENT '别名ID',
  `province_id` int(10) unsigned NOT NULL COMMENT '省ID',
  `city_id` int(10) unsigned NOT NULL COMMENT '市ID',
  `district_id` int(10) unsigned NOT NULL COMMENT '县区ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '商家ID',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名字',
  `logo` varchar(255) NOT NULL COMMENT '店铺logo',
  `tel` varchar(20) NOT NULL COMMENT '联系电话',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `category` int(10) unsigned NOT NULL COMMENT '经营类目',
  `store_type` tinyint(3) unsigned NOT NULL COMMENT '店铺类型:1 B2C、2 B2B',
  `create_time` int(10) unsigned NOT NULL COMMENT '开店时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 客户订单主表结构
DROP TABLE IF EXISTS `customer_order`;
CREATE TABLE `customer_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `order_no` varchar(15) NOT NULL COMMENT '订单号',
  `user_store_id` int(10) unsigned NOT NULL COMMENT '所属店铺',
  `type` tinyint(3) unsigned NOT NULL COMMENT '订单类型',
  `status` tinyint(3) unsigned NOT NULL COMMENT '订单状态',
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `total_fee` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总额(含邮费)',
  `total_weight` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '货品重量',
  `post_fee` int(2) unsigned NOT NULL DEFAULT '0' COMMENT '运费',
  `payment_type` tinyint(1) unsigned NOT NULL COMMENT '支付类型 线上/线下',
  `payment_method` tinyint(2) unsigned NOT NULL COMMENT '支付方式',
  `mobile` varchar(11) NOT NULL COMMENT '收货人手机号',
  `province` varchar(20) NOT NULL COMMENT '省',
  `city` varchar(20) NOT NULL COMMENT '市',
  `district` varchar(20) NOT NULL COMMENT '县区',
  `address` varchar(255) NOT NULL COMMENT '收货地址',
  `zip` varchar(6) NOT NULL COMMENT '邮编',
  `remark` varchar(255) NOT NULL COMMENT '订单备注',
  `create_time` int(10) unsigned NOT NULL COMMENT '下单时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',  
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 客户订单明细表结构
DROP TABLE IF EXISTS `customer_order_item`;
CREATE TABLE `customer_order_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单ID',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品ID',
  `product_id` int(10) unsigned NOT NULL COMMENT '货品ID',
  `name` varchar(100) NOT NULL COMMENT '商品名称',
  `specification` varchar(255) NOT NULL COMMENT '规格',
  `price` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单价',
  `num` int(10) unsigned NOT NULL COMMENT '数量',
  `amount` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '小计',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 商家表结构
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `login_password` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `contact_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `contact_mobile` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `contact_tel` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `contact_addr` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 商家订单表结构
DROP TABLE IF EXISTS `user_order`;
CREATE TABLE IF NOT EXISTS `user_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `total_fee` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '',
  `update_time` int(10) unsigned NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 商家订单明细表结构
DROP TABLE IF EXISTS `user_order_item`;
CREATE TABLE IF NOT EXISTS `user_order_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_order_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '',
  `update_time` int(10) unsigned NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
