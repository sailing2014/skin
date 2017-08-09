/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50173
Source Host           : 192.168.3.243:3306
Source Database       : skin

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2016-07-26 17:03:31
*/

USE `skin`;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for day
-- ----------------------------
DROP TABLE IF EXISTS `day`;
CREATE TABLE `day` (
  `id` char(13) NOT NULL,
  `plan_id` char(13) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `step` tinyint(4) DEFAULT '0',
  `time` int(6) DEFAULT '0',
  `create_at` int(10) DEFAULT '0',
  `update_at` int(10) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of day
-- ----------------------------
INSERT INTO `day` VALUES ('577ddca02ee86', '577ddbe8f3cd8', '6步促进精华涂抹', '0', '28', '1467866272', '1467869277');
INSERT INTO `day` VALUES ('577f0ff42ec73', '577f0fbb22171', '第一天 眼部唤醒', '1', '155', '1467944948', '1469079474');
INSERT INTO `day` VALUES ('577f143bf02f8', '577f0fbb22171', '第二天 加速代谢', '2', '180', '1467946043', '1469079814');
INSERT INTO `day` VALUES ('577f189a5f774', '577f0fbb22171', '第三天 紧致肌肤', '3', '260', '1467947162', '1469079797');
INSERT INTO `day` VALUES ('577f18aa6a575', '577f0fbb22171', '第四天 排毒护理', '4', '205', '1467947178', '1469079824');
INSERT INTO `day` VALUES ('577f18ba7313d', '577f0fbb22171', '第五天 巩固机能', '5', '215', '1467947194', '1469079845');
INSERT INTO `day` VALUES ('577f18cab777b', '577f0fbb22171', '第六天 深度护理', '6', '155', '1467947210', '1469079860');
INSERT INTO `day` VALUES ('577f18dc79f44', '577f0fbb22171', '第七天 排毒静养', '7', '210', '1467947228', '1469079875');
INSERT INTO `day` VALUES ('577f548ead05f', '577f543b9f4da', '零基础晚间护肤', '1', '160', '1467962510', '1467962510');
