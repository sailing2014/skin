/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50173
Source Host           : 192.168.3.243:3306
Source Database       : skin

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2016-09-13 16:35:04
*/
USE `skin`;
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for c_step
-- ----------------------------
DROP TABLE IF EXISTS `c_step`;
CREATE TABLE `c_step` (
  `id` char(13) NOT NULL,
  `step` tinyint(8) DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `type_id` varchar(255) DEFAULT NULL,
  `usage_id` varchar(255) DEFAULT NULL,
  `description` text,
  `c_plan_id` char(13) DEFAULT NULL,
  `product_id` text,
  `thumb` varchar(80) DEFAULT NULL,
  `image` varchar(80) DEFAULT NULL,
  `create_at` int(10) DEFAULT '0',
  `update_at` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of c_step
-- ----------------------------
INSERT INTO `c_step` VALUES ('57d6486015d4f', '1', '洁面', '', '', '温水清洁，不会夺走肌肤水分的温水进行清洁。清洁重点以双手横向打圈的方式，从中间朝两侧清洁，用力轻柔。', '57d625a5855bd', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d6486015e20', '2', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193cdb0c9b,skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用抗敏补水保湿类型。可以补充水分、防止肌肤红肿、瘙痒，镇静肌肤。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d625a5855bd', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d6486015e80', '3', '乳液', 'skin:product:type:57280a8dd80d6', 'skin:product:usage:572193cdb0c9b,skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用抗敏补水保湿类型。将乳液五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d625a5855bd', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d6486015ee5', '4', '防晒霜', 'skin:product:type:57280911b7f50', 'skin:product:usage:572193cdb0c9b,skin:product:usage:572039af1147f', '选用抗敏保湿类型。将防晒霜五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d625a5855bd', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d6486015f40', '1', '卸妆乳', 'skin:product:type:57280a66c878a', 'skin:product:usage:572193cdb0c9b,skin:product:usage:572039af1147f', '选用抗敏类型（且泡沫较少的温和型或不含皂碱）。沾水后在脸上轻轻打小圈揉匀，再冲净。', '57d645d0128ed', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d6486015f99', '2', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193cdb0c9b,skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用抗敏补水保湿类型。可以补充水分、防止肌肤红肿、瘙痒，镇静肌肤。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d645d0128ed', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d6486015ff1', '3', '精华液', 'skin:product:type:57280a9dd5fd2', 'skin:product:usage:572193cdb0c9b,skin:product:usage:572193dc9a9fd', '选用抗敏修复类型。将精华五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d645d0128ed', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d648f851215', '4', '乳液', 'skin:product:type:57280a8dd80d6', 'skin:product:usage:572193cdb0c9b,skin:product:usage:572039af1147f', '选用抗敏补水保湿类型。在补水的同时必须使用具有保护层作用，防止肌肤出现过敏反应。', '57d645d0128ed', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64860160a1', '1', '卸妆乳', 'skin:product:type:57280a66c878a', 'skin:product:usage:572193cdb0c9b', '选用抗敏类型（且泡沫较少的温和型或不含皂碱）。沾水后在脸上轻轻打小圈揉匀，再冲净。', '57d626528c2f2', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d6486016049', '2', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193cdb0c9b,skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用抗敏补水保湿类型。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d626528c2f2', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d648f85129d', '3', '面膜', 'skin:product:type:57280a7045c9a', 'skin:product:usage:572193cdb0c9b,kin:product:usage:572193e91e41b', '抗敏补水类型。敷10-15分钟即可。', '57d626528c2f2', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d648f8513d3', '4', '精华液', 'skin:product:type:57280a9dd5fd2', 'skin:product:usage:572193cdb0c9b,skin:product:usage:572193dc9a9fd', '选用抗敏修复类型。将精华五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d626528c2f2', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d648f851515', '5', '乳液', 'skin:product:type:57280a8dd80d6', 'skin:product:usage:572193cdb0c9b,skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用抗敏补水保湿类型。在补水的同时必须使用具有保护层作用，防止肌肤出现过敏反应。', '57d626528c2f2', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d648f8515a4', '1', '洁面', '', '', '温水清洁，不会夺走肌肤水分的温水进行清洁。清洁重点以双手横向打圈的方式，从中间朝两侧清洁，用力轻柔。', '57d642b2e9d64', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d648f851622', '2', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d642b2e9d64', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d648f851786', '3', '面霜', 'skin:product:type:57280940930b8', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。将乳液五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d642b2e9d64', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d648f85180f', '4', '防晒霜', 'skin:product:type:57280911b7f50', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。将防晒霜五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d642b2e9d64', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d648f85188b', '1', '卸妆乳', 'skin:product:type:57280a66c878a', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。晚间要彻底清洁肌肤，将卸妆乳五点式涂抹在脸上，轻轻打圈清洗肌肤。特别注意T区部位，要重点清洗。', '57d6430463441', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d649b12e596', '2', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d6430463441', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d649b12e5ea', '3', '精华液', 'skin:product:type:57280a9dd5fd2', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。将精华五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d6430463441', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d649b12e639', '4', '面霜', 'skin:product:type:57280940930b8', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。在补水的同时必须使用具有保护层作用，能够直达肌肤深层的乳液或者乳霜。', '57d6430463441', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d649b12e696', '1', '磨砂膏', 'skin:product:type:572809319468a', 'skin:product:usage:572039c686b2d', '细微颗粒类型。将洁面奶用手搓出泡沫，再把磨砂膏加入其中。（洁面奶泡沫具有缓冲作用，这样用磨砂膏做清洁，对肌肤更温和，不刺激。）将混合好的洗面奶磨砂膏泡沫抹在额头、两颊、鼻子、下巴五个地方，再用手掌轻轻抹开。用指腹按摩，特别注意鼻翼和嘴角两侧以及眼角等容易长皱纹的部位。', '57d6433c94847', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d649b12e6fb', '2', '卸妆乳', 'skin:product:type:57280a66c878a', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。晚间要彻底清洁肌肤，将卸妆乳五点式涂抹在脸上，轻轻打圈清洗肌肤。特别注意T区部位，要重点清洗。', '57d6433c94847', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d649b12e748', '3', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。化妆水倒满化妆棉，湿敷3分钟。完后用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d6433c94847', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d649b12e795', '4', '面膜', 'skin:product:type:57280a7045c9a', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿的泥面膜类型。敷10-15分钟即可。', '57d6433c94847', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d649b12e7e2', '5', '精华液', 'skin:product:type:57280a9dd5fd2', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。精华液能够将能量输送到肌肤深层，选择保湿型精华液。', '57d6433c94847', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d649b12e87c', '6', '面霜', 'skin:product:type:57280940930b8', 'skin:product:usage:572193e91e41b,skin:product:usage:572039af1147f', '选用补水保湿类型。在补水的同时必须使用具有保护层作用，能够直达肌肤深层的乳液或者乳霜。', '57d6433c94847', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a968b', '2', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b', '选用控油抗敏类型。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d644a0ab325', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a95f1', '3', '乳液', 'skin:product:type:57280a8dd80d6', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b,skin:product:usage:572193e91e41b', '选用抗敏无油补水类型。将乳液五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d644a0ab325', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a96e8', '4', '防晒霜', 'skin:product:type:57280911b7f50', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b,skin:product:usage:572193e91e41b', '选用清爽补水类型。将防晒霜五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d644a0ab325', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9735', '1', '卸妆乳', 'skin:product:type:57280a66c878a', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b', '选用控油抗敏类型。晚间要彻底清洁肌肤，将卸妆乳五点式涂抹在脸上，轻轻打圈清洗肌肤。特别注意T区部位，要重点清洗。', '57d644d35fe96', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9783', '2', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b', '选用控油抗敏类型类型。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d644d35fe96', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a97f6', '3', '精华液', 'skin:product:type:57280a9dd5fd2', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b,skin:product:usage:572193dc9a9fd', '选用抗敏无油修复类型。将精华五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d644d35fe96', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a985a', '4', '乳液', 'skin:product:type:57280a8dd80d6', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b,skin:product:usage:572193e91e41b', '选用抗敏无油补水类型。将乳液五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d644d35fe96', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a98a8', '1', '卸妆乳', 'skin:product:type:57280a66c878a', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b', '控油抗敏类型。晚间要彻底清洁肌肤，将卸妆乳五点式涂抹在脸上，轻轻打圈清洗肌肤。特别注意T区部位，要重点清洗。', '57d6450046f32', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9923', '2', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b', '选择控油抗敏类型。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d6450046f32', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9977', '3', '面膜', 'skin:product:type:57280a7045c9a', 'skin:product:usage:572193dc9a9fd,skin:product:usage:572193e91e41b', '选择补水修复类型。敷10-15分钟即可。', '57d6450046f32', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a99c7', '4', '精华液', 'skin:product:type:57280a9dd5fd2', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b,skin:product:usage:572193dc9a9fd', '抗敏无油修复类型。将精华五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d6450046f32', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9a16', '5', '乳液', 'skin:product:type:57280a8dd80d6', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b,skin:product:usage:572193e91e41b', '选择抗敏无油补水类型。将乳液五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d6450046f32', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9a64', '1', '洁面', 'skin:product:type:572809319468a', 'skin:product:usage:572193eb8bf2a', '选择泡沫类型。将洁面奶用手搓出泡沫，轻柔打圈手势洗脸，带走多余油分。', '57d6452896947', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9ab3', '2', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193e91e41b', '选用控油补水类型。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d6452896947', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9b5a', '3', '乳液', 'skin:product:type:57280a8dd80d6', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193e91e41b', '选用清爽补水类型。将乳液五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d6452896947', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9ba8', '4', '防晒霜', 'skin:product:type:57280911b7f50', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193e91e41b', '选用清爽补水类型。将防晒霜五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d6452896947', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9bf7', '1', '毛巾热敷脸部', null, null, '用温热的毛巾贴在耳朵根部及靠近耳朵部分的脸颊，促进淋巴循环，让毛孔张开，促进洁面的彻底度，也让后续护肤动作更有效。', '57d645332e1eb', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9c45', '2', '卸妆乳', 'skin:product:type:57280a66c878a', 'skin:product:usage:572193eb8bf2a', '选用控油类型。晚间要彻底清洁肌肤，将卸妆乳五点式涂抹在脸上，轻轻打圈清洗肌肤。特别注意T区部位，要重点清洗。', '57d645332e1eb', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9c93', '3', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193e91e41b', '选用控油补水类型。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。', '57d645332e1eb', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9cf3', '4', '精华液', 'skin:product:type:57280a9dd5fd2', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193e91e41b', '选用无油、补水类型。将精华五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d645332e1eb', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9d4e', '5', '乳液', 'skin:product:type:57280a8dd80d6', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193e91e41b', '选用清爽补水类型。将乳液五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d645332e1eb', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9d9f', '1', '毛巾热敷脸部', null, null, '用温热的毛巾贴在耳朵根部及靠近耳朵部分的脸颊，促进淋巴循环，让毛孔张开，促进洁面的彻底度，也让后续护肤动作更有效。', '57d6453d5bda3', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9ded', '2', '去角质', 'skin:product:type:572809319468a', 'skin:product:usage:572039c686b2d', '选用细微颗粒磨砂膏。将洁面奶用手搓出泡沫，再把磨砂膏加入其中。（洁面奶泡沫具有缓冲作用，这样用磨砂膏做清洁，对肌肤更温和，不刺激。）将混合好的洗面奶磨砂膏泡沫抹在额头、两颊、鼻子、下巴五个地方，再用手掌轻轻抹开。用指腹按摩，特别注意鼻翼和嘴角两侧以及眼角等容易长皱纹的部位。', '57d6453d5bda3', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9e3b', '3', '卸妆乳', 'skin:product:type:57280a66c878a', 'skin:product:usage:572193eb8bf2a', '选用控油类型。晚间要彻底清洁肌肤，将卸妆乳五点式涂抹在脸上，轻轻打圈清洗肌肤。特别注意T区部位，要重点清洗。', '57d6453d5bda3', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9e89', '4', '爽肤水', 'skin:product:type:57280a5ea48c8', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193e91e41b', '选用控油补水类型。化妆棉倒满爽肤水，湿敷3分钟。', '57d6453d5bda3', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9ed7', '5', '面膜', 'skin:product:type:57280a7045c9a', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193e91e41b', '选用控油补水泥面膜类型。敷10-15分钟即可。', '57d6453d5bda3', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9f26', '6', '精华液', 'skin:product:type:57280a9dd5fd2', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193e91e41b', '选用无油、补水类型。将精华五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d6453d5bda3', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a9f79', '7', '乳液', 'skin:product:type:57280a8dd80d6', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193e91e41b', '控油补水类型。将乳液五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', '57d6453d5bda3', null, null, null, '0', '0');
INSERT INTO `c_step` VALUES ('57d64ef8a968c', '1', '洁面', 'skin:product:type:572809319468a', 'skin:product:usage:572193eb8bf2a,skin:product:usage:572193cdb0c9b', '选择少泡沫类型。选泡沫较少的温和型洁面乳或不含皂碱的洁肤露，沾水后在脸上轻轻打小圈揉匀冲净。', '57d644a0ab325', '', '', '', '0', '0');
