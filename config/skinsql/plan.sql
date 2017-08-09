/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50173
Source Host           : 192.168.3.243:3306
Source Database       : skin

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2016-08-04 16:18:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for plan
-- ----------------------------
DROP TABLE IF EXISTS `plan`;
CREATE TABLE `plan` (
  `id` char(13) NOT NULL,
  `title` varchar(100) NOT NULL,
  `tags` text,
  `description` text,
  `difficulty_level` tinyint(4) DEFAULT '0',
  `tools` varchar(20) DEFAULT NULL,
  `circle` smallint(6) DEFAULT '0',
  `principle` varchar(200) DEFAULT NULL,
  `fit` varchar(100) DEFAULT NULL,
  `tips` varchar(255) DEFAULT NULL,
  `image` varchar(80) DEFAULT NULL,
  `thumb` varchar(80) DEFAULT NULL,
  `body_part` varchar(30) DEFAULT NULL,
  `basic` int(255) DEFAULT NULL,
  `top` tinyint(2) DEFAULT NULL,
  `participant_num` int(11) DEFAULT '0',
  `create_at` int(10) DEFAULT NULL,
  `update_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of plan
-- ----------------------------
INSERT INTO `plan` VALUES ('577f543b9f4da', '零基础晚间护肤', null, '<p>夜间是肌肤新陈代谢的黄金时段，如何把握这段精华时间，多加保养，每天8小时，我们就能把平均老化年龄向后挪移，一直保持年轻。</p>', '1', '无', '1', '零基础晚间护肤', '护肤初学者', '该训练强度较低，建议坚持练习效果更佳,训练时有任何不适，需根据自身情况调整或停止', null, null, '脸部', '200', '0', '0', '1467962427', '1467966216');
INSERT INTO `plan` VALUES ('577ddbe8f3cd8', '6步促进精华吸收', null, '<p>精华是一种高浓度及高机能性的保养品，有极好的美容效果。使用精华时稍稍加上一点按摩动作，会更有效地促进成分的吸收。</p>', '2', '无', '1', '促进肌肤对精华的吸收', '通用', '该训练强度中等，建议经常练习效果更佳,训练时有任何不适，需根据自身情况调整或停止。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/577ddbbc4628a.png', 'http://oss-qiwo-dev.qiwocloud1.com/skin/577ddbc61d0df.png', '脸部', '200', '0', '0', '1467866088', '1467966225');
INSERT INTO `plan` VALUES ('577f0fbb22171', '去黑眼圈计划', null, '<p>熬夜、情绪波动大、压力大等都会导致眼部血流速度滞缓，影响眼部代谢，从而色素沉着形成黑眼圈。每日眼部保养，重获迷人双眸哦！</p>', '3', '无', '7', '加速眼部血液循环，促进代谢，减轻黑眼圈', '有黑眼圈者', '该训练强度中等，建议经常练习效果更佳,训练时有任何不适，需根据自身情况调整或停止,', 'http://oss-qiwo-dev.qiwocloud1.com/skin/577f671052df3.jpg', 'http://oss-qiwo-dev.qiwocloud1.com/skin/577f0f84a3cf9.jpg', '眼部', '200', '0', '0', '1467944891', '1467967268');
