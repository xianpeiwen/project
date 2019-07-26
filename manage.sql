/*
Navicat MySQL Data Transfer

Source Server         : 本机
Source Server Version : 50717
Source Host           : 127.0.0.1:3306
Source Database       : manage

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2019-07-16 18:30:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for manage_admin
-- ----------------------------
DROP TABLE IF EXISTS `manage_admin`;
CREATE TABLE `manage_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理员表',
  `admin_name` varchar(30) DEFAULT NULL COMMENT '用户名',
  `admin_pass` varchar(255) DEFAULT NULL COMMENT '密码',
  `admin_phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `admin_email` varchar(30) DEFAULT NULL COMMENT '邮箱',
  `admin_add_time` int(11) DEFAULT NULL COMMENT '注册时间',
  `admin_status` tinyint(4) DEFAULT '2' COMMENT '1:在线 2：离线 3：禁用',
  `admin_sort` int(11) DEFAULT '1' COMMENT '排序',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manage_admin
-- ----------------------------
INSERT INTO `manage_admin` VALUES ('5', 'admin', '$2y$10$kLkAHZUmOore.LUOenRCW.qzO0J85RDxoyQXELEQMvxSps6Fm2r.e', '15888888888', 'admin@admin.com', '1557862140', '1', '1');
INSERT INTO `manage_admin` VALUES ('7', '12344', '$2y$10$ZJD/Dc.xorfOIy3Td4ZD1OX1t9gGnK2kfOOmQoaiWuPgOzB3vSvBO', '15805044444', 'admin@php.cn', '1563180233', '1', '1');
INSERT INTO `manage_admin` VALUES ('8', 'ddd', '$2y$10$fpzn7dMonf5fh0azJadFROss49VeW8iA4VyFYBmDvkHvnUCek6lui', '15805044445', 'admin@php.com', '1563181256', '1', '1');

-- ----------------------------
-- Table structure for manage_agent
-- ----------------------------
DROP TABLE IF EXISTS `manage_agent`;
CREATE TABLE `manage_agent` (
  `agent_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `agent_name` varchar(255) NOT NULL DEFAULT '' COMMENT '门店名称',
  `agent_addr` varchar(1000) NOT NULL DEFAULT '' COMMENT '门店地址',
  `agent_username` varchar(20) NOT NULL DEFAULT '' COMMENT '账号',
  `agent_psw` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `agent_link` varchar(20) NOT NULL DEFAULT '' COMMENT '门店联系人',
  `agent_phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系人电话',
  `agent_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态，1可用，0不可用',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sort` int(11) NOT NULL DEFAULT '1' COMMENT '排序',
  PRIMARY KEY (`agent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manage_agent
-- ----------------------------

-- ----------------------------
-- Table structure for manage_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `manage_auth_group`;
CREATE TABLE `manage_auth_group` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '用户组表',
  `title` char(100) DEFAULT NULL COMMENT '用户组中文名称',
  `rules` text COMMENT '用户组拥有的规则id',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `update_time` int(11) DEFAULT NULL COMMENT '编辑时间',
  `pid` mediumint(8) DEFAULT NULL COMMENT '父类ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manage_auth_group
-- ----------------------------
INSERT INTO `manage_auth_group` VALUES ('1', '网络部', '9,30,7,28,29,23,1,5,16,19,17,18,22,4,10,21,8,25,26,2,11,20,15,13', '1', '99', '1558408654', '1558452010', '0');
INSERT INTO `manage_auth_group` VALUES ('2', '组长', '1,5,2,4,8,9,7', '1', '88', '1558408673', '1558433617', '1');
INSERT INTO `manage_auth_group` VALUES ('3', '副组长', '1,5,2', '1', '88', '1558408700', '1558433629', '1');

-- ----------------------------
-- Table structure for manage_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `manage_auth_group_access`;
CREATE TABLE `manage_auth_group_access` (
  `uid` mediumint(8) NOT NULL COMMENT '用户组明细表 用户id',
  `group_id` mediumint(8) NOT NULL COMMENT '用户组id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manage_auth_group_access
-- ----------------------------
INSERT INTO `manage_auth_group_access` VALUES ('5', '1');
INSERT INTO `manage_auth_group_access` VALUES ('6', '1');
INSERT INTO `manage_auth_group_access` VALUES ('6', '2');
INSERT INTO `manage_auth_group_access` VALUES ('6', '3');
INSERT INTO `manage_auth_group_access` VALUES ('7', '1');
INSERT INTO `manage_auth_group_access` VALUES ('7', '2');
INSERT INTO `manage_auth_group_access` VALUES ('7', '3');
INSERT INTO `manage_auth_group_access` VALUES ('8', '1');
INSERT INTO `manage_auth_group_access` VALUES ('8', '2');
INSERT INTO `manage_auth_group_access` VALUES ('8', '3');

-- ----------------------------
-- Table structure for manage_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `manage_auth_rule`;
CREATE TABLE `manage_auth_rule` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT '规则表',
  `name` char(80) NOT NULL COMMENT '规则唯一标识',
  `title` char(20) DEFAULT NULL COMMENT '规则中文名称',
  `pid` mediumint(9) NOT NULL DEFAULT '0' COMMENT '父类ID',
  `icon` char(20) DEFAULT NULL COMMENT '图标',
  `sort` mediumint(9) NOT NULL DEFAULT '1' COMMENT '排序',
  `condition` varchar(80) DEFAULT NULL COMMENT '规则表达式',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `level` int(11) NOT NULL DEFAULT '0' COMMENT '等级',
  `type` char(100) DEFAULT '1',
  `update_time` int(11) DEFAULT NULL COMMENT '编辑时间',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `show` int(11) NOT NULL DEFAULT '1' COMMENT '是否左侧显示 1为显示 2为不显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manage_auth_rule
-- ----------------------------
INSERT INTO `manage_auth_rule` VALUES ('1', '', '后台中心', '0', '&#xe631;', '1', null, '1', '1', '1', '1558441142', '1529782987', '1');
INSERT INTO `manage_auth_rule` VALUES ('2', 'manage/auth/index', '权限管理', '1', '&#xe653;', '1', null, '1', '2', '1', '1558437989', '1529782987', '1');
INSERT INTO `manage_auth_rule` VALUES ('4', 'manage/admin/index', '管理员', '1', '&#xe770;', '1', null, '1', '2', '1', '1558437998', '1529782987', '1');
INSERT INTO `manage_auth_rule` VALUES ('5', 'manage/group/index', '管理员组', '1', '&#xe613;', '88', null, '2', '2', '1', '1558437982', '1558407302', '2');
INSERT INTO `manage_auth_rule` VALUES ('7', 'manage/index/get_the_menu', '左侧菜单', '9', '', '99', null, '1', '3', '1', '1558432144', '1558430426', '2');
INSERT INTO `manage_auth_rule` VALUES ('8', 'manage/admin/admin_add_form', '添加管理员ajax', '4', '', '97', null, '1', '3', '1', '1558439924', '1558430969', '2');
INSERT INTO `manage_auth_rule` VALUES ('9', '', '基础', '0', '', '1', null, '1', '1', '1', '1558432180', '1558432122', '2');
INSERT INTO `manage_auth_rule` VALUES ('10', 'manage/admin/admin_add', '添加管理员页面', '4', '', '99', null, '1', '3', '1', '1558437818', '1558437818', '2');
INSERT INTO `manage_auth_rule` VALUES ('11', 'manage/auth/auth_add', '添加权限页面', '2', '', '99', null, '2', '3', '1', '1558438487', '1558437929', '2');
INSERT INTO `manage_auth_rule` VALUES ('13', 'manage/auth/auto_add_form', '添加权限ajax', '2', '', '97', null, '2', '3', '1', '1558439869', '1558438153', '2');
INSERT INTO `manage_auth_rule` VALUES ('15', 'manage/auth/auto_del', '删除权限', '2', '', '97', null, '2', '3', '1', '1558438464', '1558438464', '2');
INSERT INTO `manage_auth_rule` VALUES ('16', 'manage/group/group_add', '管理组添加页面', '5', '', '99', null, '2', '3', '1', '1558438616', '1558438616', '2');
INSERT INTO `manage_auth_rule` VALUES ('17', 'manage/group/group_add_form', '管理组添加ajax', '5', '', '97', null, '2', '3', '1', '1558439679', '1558438822', '2');
INSERT INTO `manage_auth_rule` VALUES ('18', 'manage/group/group_del', '删除管理组', '5', '', '96', null, '2', '3', '1', '1558439686', '1558439124', '2');
INSERT INTO `manage_auth_rule` VALUES ('19', 'manage/group/grout_list', '管理员组列表ajax', '5', '', '98', null, '2', '3', '1', '1558439643', '1558439643', '2');
INSERT INTO `manage_auth_rule` VALUES ('20', 'manage/auth/auto_list', '权限管理列表ajax', '2', '', '98', null, '2', '3', '1', '1558439748', '1558439748', '2');
INSERT INTO `manage_auth_rule` VALUES ('21', 'manage/admin/admin_list', '管理员列表ajax', '4', '', '98', null, '1', '3', '1', '1558439959', '1558439959', '2');
INSERT INTO `manage_auth_rule` VALUES ('22', 'manage/group/group_auth_list', '权限树', '5', '', '95', null, '2', '3', '1', '1558440372', '1558440087', '2');
INSERT INTO `manage_auth_rule` VALUES ('23', 'manage/base/modular_status', '更新禁用方法', '9', '', '97', null, '1', '2', '1', '1558440227', '1558440227', '2');
INSERT INTO `manage_auth_rule` VALUES ('25', 'manage/admin/admin_auth_list', '管理组权限树', '4', '', '95', null, '1', '3', '1', '1558440415', '1558440415', '2');
INSERT INTO `manage_auth_rule` VALUES ('26', 'manage/admin/admin_del', '删除管理员', '4', '', '90', null, '1', '3', '1', '1558440588', '1558440588', '2');
INSERT INTO `manage_auth_rule` VALUES ('27', 'manage/index/page', '首页', '9', '', '100', null, '1', '2', '1', '1558441158', '1558441158', '2');
INSERT INTO `manage_auth_rule` VALUES ('28', 'manage/index/lock', '锁屏页面', '9', '', '99', null, '1', '2', '1', '1558451123', '1558450961', '2');
INSERT INTO `manage_auth_rule` VALUES ('29', 'manage/index/locking', '锁屏ajax', '9', '', '99', null, '1', '2', '1', '1558451145', '1558451145', '2');
INSERT INTO `manage_auth_rule` VALUES ('30', 'manage/index/unlock', '解锁权限', '9', '', '99', null, '1', '2', '1', '1558451543', '1558451543', '1');
INSERT INTO `manage_auth_rule` VALUES ('32', 'manage/agent/index', '门店管理', '1', '&#xe613;', '0', null, '1', '2', '1', '1563179239', '1563179173', '1');
INSERT INTO `manage_auth_rule` VALUES ('33', '', '配置管理', '1', '&#xe716;', '0', null, '1', '2', '1', '1563245268', '1563244175', '1');
INSERT INTO `manage_auth_rule` VALUES ('34', 'manage/setting/index', '小程序配置', '33', '', '0', null, '1', '3', '1', '1563245483', '1563245483', '1');

-- ----------------------------
-- Table structure for manage_image
-- ----------------------------
DROP TABLE IF EXISTS `manage_image`;
CREATE TABLE `manage_image` (
  `image_id` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT '图片表',
  `image_surface_name` varchar(255) NOT NULL COMMENT '关联表名',
  `image_surface_id` int(11) NOT NULL COMMENT '关联表的id',
  `image_path` varchar(255) NOT NULL COMMENT '图片路径',
  `image_name` varchar(255) NOT NULL COMMENT '图片名',
  `image_sort` int(11) NOT NULL DEFAULT '1' COMMENT '排序',
  `image_is_url` int(11) NOT NULL DEFAULT '2' COMMENT '是否显示链接',
  `image_is_disable` int(11) NOT NULL DEFAULT '1' COMMENT '是否禁用图片',
  `image_url` varchar(255) DEFAULT NULL COMMENT '图片的链接',
  `image_bz` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manage_image
-- ----------------------------
INSERT INTO `manage_image` VALUES ('3', 'admin', '5', 'admin/', '5cdb173c723af.png', '1', '2', '1', '', null);
INSERT INTO `manage_image` VALUES ('5', 'admin', '7', 'admin/', '5d2c3cc9320c8.png', '1', '2', '1', '', null);
INSERT INTO `manage_image` VALUES ('6', 'admin', '8', 'admin/', '5d2c40c859d80.png', '1', '2', '1', '', null);

-- ----------------------------
-- Table structure for manage_setting
-- ----------------------------
DROP TABLE IF EXISTS `manage_setting`;
CREATE TABLE `manage_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manage_setting
-- ----------------------------

-- ----------------------------
-- Table structure for manage_thumb
-- ----------------------------
DROP TABLE IF EXISTS `manage_thumb`;
CREATE TABLE `manage_thumb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) DEFAULT '0' COMMENT '分组id',
  `url` varchar(255) DEFAULT NULL,
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manage_thumb
-- ----------------------------
INSERT INTO `manage_thumb` VALUES ('1', '0', '/images/upload/34e4c2e5b2d994c8/b456f80b862efd28.png', '2019-07-16 16:13:21');
INSERT INTO `manage_thumb` VALUES ('2', '0', '/images/upload/250c7a3a46d29c78/1dd7a82da0245b90.png', '2019-07-16 16:14:40');

-- ----------------------------
-- Table structure for manage_thumb_group
-- ----------------------------
DROP TABLE IF EXISTS `manage_thumb_group`;
CREATE TABLE `manage_thumb_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) DEFAULT NULL COMMENT '分组名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of manage_thumb_group
-- ----------------------------
INSERT INTO `manage_thumb_group` VALUES ('1', '1');
INSERT INTO `manage_thumb_group` VALUES ('2', '2');
INSERT INTO `manage_thumb_group` VALUES ('3', '3');
INSERT INTO `manage_thumb_group` VALUES ('4', '4');
INSERT INTO `manage_thumb_group` VALUES ('5', '5');
