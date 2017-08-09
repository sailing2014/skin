/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50173
Source Host           : 192.168.3.243:3306
Source Database       : skin

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2016-09-13 16:34:57
*/
use `skin`;


SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for c_plan
-- ----------------------------
DROP TABLE IF EXISTS `c_plan`;
CREATE TABLE `c_plan` (
  `id` char(13) NOT NULL,
  `title` varchar(100) NOT NULL,
  `code` varchar(255) DEFAULT NULL COMMENT '皮肤类型代码',
  `type_id` varchar(255) DEFAULT NULL,
  `usage_id` varchar(255) DEFAULT NULL,
  `description` text,
  `image` varchar(80) DEFAULT NULL,
  `thumb` varchar(80) DEFAULT NULL,
  `create_at` int(10) DEFAULT '0',
  `update_at` int(10) DEFAULT '0',
  `todo_id` char(13) DEFAULT '',
  `plan_id` text,
  `time_type` tinyint(2) DEFAULT '0' COMMENT '1晨间，2晚间，3周末晚间',
  PRIMARY KEY (`id`),
  KEY `title` (`title`) USING BTREE
) ENGINE=INNODB AUTO_INCREMENT=578 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of c_plan
-- ----------------------------
INSERT INTO `c_plan` VALUES ('57d625a5855bd', '晨间护理(干性,敏感性)', '31,32,41,42', null, null, '干性、敏感性肌肤护理除了要补水，还要做好抗敏工作，日间的隔离防晒也不能少。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d6155328ce9.png', '0', '0', '', null, '1');
INSERT INTO `c_plan` VALUES ('57d645d0128ed', '晚间护理(干性,敏感性)', '31,32,41,42', null, null, '晚间护肤的任务是补水、舒缓、修复! 同时，要注意清洁肌肤，做按摩、舒缓放松。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d615a0d8dbf.png', '0', '0', '', null, '2');
INSERT INTO `c_plan` VALUES ('57d626528c2f2', '晚间升华护理(干性,敏感性)', '31,32,41,42', null, null, '周日来一个密集补水加舒缓修复的护理，靓丽迎接下一周挑战。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d615beabaac.png', '0', '0', '', null, '3');
INSERT INTO `c_plan` VALUES ('57d642b2e9d64', '晨间护理(干性,耐受性)', '33,34,43,44', null, null, '干性、耐受性肌肤护理最大的任务是补水，日间还要做好防晒这个防护工作。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d6155328ce9.png', '0', '0', '', null, '1');
INSERT INTO `c_plan` VALUES ('57d6430463441', '晚间护理(干性,耐受性)', '33,34,43,44', null, null, '晚间护肤最大的任务是还是补水，补水! 同时，要注意清洁肌肤，做按摩、舒缓放松。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d615a0d8dbf.png', '0', '0', '', null, '2');
INSERT INTO `c_plan` VALUES ('57d6433c94847', '晚间升华护理(干性,耐受性)', '33,34,43,44', null, null, '周末晚间护肤最大的任务是还是补水，密集补水! 同时，要注意去除死皮，做按摩、舒缓放松。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d615beabaac.png', '0', '0', '', null, '3');
INSERT INTO `c_plan` VALUES ('57d644a0ab325', '晨间护理(油性,敏感性)', '11,12,21,22', null, null, '油性敏感性肌肤护理要进行油田大作战的同时，还要兼顾抗敏工作。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d6155328ce9.png', '0', '0', '', null, '1');
INSERT INTO `c_plan` VALUES ('57d644d35fe96', '晚间护理(油性,敏感性)', '11,12,21,22', null, null, '晚间护肤的重点是清洁和补水! 同时，要做按摩、舒缓放松。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d615a0d8dbf.png', '0', '0', '', null, '2');
INSERT INTO `c_plan` VALUES ('57d6450046f32', '晚间升华护理(油性,敏感性)', '11,12,21,22', null, null, '周末给肌肤来个大呵护，控油、补水、修复一个都不能少。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d615beabaac.png', '0', '0', '', null, '3');
INSERT INTO `c_plan` VALUES ('57d6452896947', '晨间护理(油性,耐受性)', '13,14,23,24', null, null, '油性耐受性肌肤护理最大的任务是控油、补水，防晒隔离也同样不可或缺。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d6155328ce9.png', '0', '0', '', null, '1');
INSERT INTO `c_plan` VALUES ('57d645332e1eb', '晚间护理(油性,耐受性)', '13,14,23,24', null, null, '晚间护肤的重点是清洁和补水! 同时，要做按摩、舒缓放松。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d615a0d8dbf.png', '0', '0', '', null, '2');
INSERT INTO `c_plan` VALUES ('57d6453d5bda3', '晚间升华护理(油性,耐受性)', '13,14,23,24', null, null, '油性肌肤的永恒话题是清洁、去油、控油、补水！', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d615beabaac.png', '0', '0', '', null, '3');
