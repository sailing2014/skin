/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50173
Source Host           : 192.168.3.243:3306
Source Database       : skin

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2016-09-21 14:59:17
*/
USE `skin`;
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for todo
-- ----------------------------
DROP TABLE IF EXISTS `todo`;
CREATE TABLE `todo` (
  `id` char(13) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `content` text,
  `thumb` varchar(80) DEFAULT NULL,
  `image` varchar(80) DEFAULT NULL,
  `case` varchar(255) DEFAULT NULL,
  `key_point` varchar(255) DEFAULT NULL,
  `suggestion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of todo
-- ----------------------------
INSERT INTO `todo` VALUES ('5795d4865b030', '轻度防晒', '今天您需要进行轻度防晒哦，建议使用SPF12-15的防晒霜。', '轻度防晒是指紫外线超过一定的阀值，为防止肌肤被晒黑、晒伤等目的，所采取的防晒措施。色素沉着性肌肤，紫外线等级为弱时就应进行轻度防晒。建议使用SPF12-15的防晒霜。如果在室外活动，需要每隔两小时重复涂抹。\n\n干性肌肤：建议选择油分含量较高、具有补水功效的防晒霜。\n油性肌肤：建议选择不含油份、轻薄的防晒霜。\n敏感性肌肤：建议选择具有舒缓、抗过敏功效的防晒霜。\n易皱纹性肌肤：建议选择具有抗衰老、抗皱纹成分的防晒霜。\n', 'http://oss-qiwo-dev.qiwocloud1.com/skin/57c4087b77282.png', '', '紫外线指数为5-6区间值时为中等，对肌肤会造成伤害，需进行轻度防晒；而色素沉着性肌肤在紫外线为弱（3-4区间值）的情况下就应进行轻度防晒。', '早晨出门前使用SPF12-15的防晒霜。室外活动需每两小时涂抹、佩戴防晒帽或伞。', '干性肌：补水、保湿类防晒霜。\n油性肌：无油、补水类防晒霜。\n敏感肌：舒缓、抗敏类防晒霜。\n易皱纹肌：紧致、抗皱类防晒霜。\n');
INSERT INTO `todo` VALUES ('57469fc66c141', '重度防晒', '您需要进行重度防晒哟，建议选择SPF20以上的防晒霜', '重度防晒是指紫外线超过安全值，非常强烈，为防止肌肤被晒黑、晒伤等目的，所采取的防晒措施。\n当您的肌肤是色素沉着性肌肤时，紫外线等级为强、很强时就应进行重度防晒。\n当您的肌肤是非色素沉着性肌肤时，紫外线等级为很强时就应进行重度防晒。\n\n如果您的肌肤是干性，建议选择油分含量较高、具有补水功效， SPF20以上的防晒霜。\n如果您的肌肤是油性，建议选择不含油份、轻薄， SPF20以上的防晒霜。\n如果您的肌肤是敏感性，建议选择具有舒缓、抗过敏功效， SPF20以上的防晒霜。\n如果您的肌肤是易皱纹性，建议选择具有抗衰老、抗皱纹成分，SPF20以上的防晒霜。\n\n如果您在室内居多，上午下午各涂抹一次防晒霜即可；如果您在户外的时间居多，建议您每隔2小时涂抹一次ff。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/57c40882be7b3.png', '', '紫外线在10-15区间值时属于很强的级别，为防止肌肤晒伤，应采取重度防晒措施；而色素沉着性肌肤在紫外线为强（7-9区间值）时，就应进行重度防晒。', '早晨出门前使用SPF20以上的防晒霜。尽量避免上午10点至下午4点时段外出活动，或者尽量不外出。', '干性肌：补水、保湿类防晒霜。\n油性肌：无油、补水类防晒霜。\n敏感肌：舒缓、抗敏类防晒霜。\n易皱纹肌：紧致、抗皱类防晒霜。');
INSERT INTO `todo` VALUES ('57456c482f590', '深层清洁', '今天雾霾指数很高，空气污染为重度，一定要深层清洁哦。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57c408a633a25.png', '', 'PM2.5高于75微克/立方米，空气含大量污染物和微生物，依附在皮肤上，堵塞毛孔，影响皮肤的代谢循环，引起过敏、痘痘等肌肤问题。', '晚间要使用卸妆乳液彻底清洁肌肤。在户外时需佩戴口罩。', '干性肌：保湿类卸妆膏。\n油性肌：无油清爽类卸妆乳。\n敏感肌：舒缓、抗敏、不含酒精类卸妆乳。');
INSERT INTO `todo` VALUES ('57469e56d9db2', '中度防晒', '今天您需要进行中度防晒哟，建议使用SPF15-20的防晒霜。', '中度防晒是指紫外线指数偏高，接近安全值时，为为防止肌肤被晒黑、晒伤等目的，所采取的防晒措施。\n当您的肌肤是色素沉着性肌肤时，紫外线等级为中等时就应进行中度防晒。\n当您的肌肤是非色素沉着性肌肤时，紫外线等级为强时就应进行中度防晒。\n\n如果您的肌肤是干性，建议选择油分含量较高、具有补水功效， SPF15-20的防晒霜。\n如果您的肌肤是油性，建议选择不含油份、轻薄， SPF15-20的防晒霜。\n如果您的肌肤是敏感性，建议选择具有舒缓、抗过敏功效， SPF15-20的防晒霜。\n如果您的肌肤是易皱纹性，建议选择具有抗衰老、抗皱纹成分，SPF15-20的防晒霜。\n\n如果您在室内居多，上午下午各涂抹一次防晒霜即可；如果您在户外的时间居多，建议每隔三小时涂抹一次ffrr。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/57c4088ad1307.png', '', '紫外线指数为7-9区间值时为强，对肌肤危害较大，需进行中度防晒；而色素沉着性肌肤在紫外线为中等（5-6区间值）时，就需进行重度中度防晒。', '早晨出门前使用SPF15-20的防晒霜。室外活动需每两小时涂抹，佩戴防晒帽或伞。', '干性肌：补水、保湿类防晒霜。\n油性肌：无油、补水类防晒霜。\n敏感肌：舒缓、抗敏类防晒霜。\n易皱纹肌：紧致、抗皱类防晒霜。');
INSERT INTO `todo` VALUES ('57456c3f81a89', '深度补水', '湿度低于40%，空气干燥，会导致皮肤干燥缺水，所以今天需要深度补水哦。', null, 'http://oss-qiwo-dev.qiwocloud1.com/skin/57c408ad988fd.png', '', '空气湿度为40-60%是最理想值。当空气湿度低于40%，空气干燥，加速皮肤水分蒸发引起干燥缺水，需要采取补水措施。', '晚间使用补水面膜，敷10-15分钟。', '干性肌：补水、保湿类面膜。\n油性肌：控油、补水类面膜。\n敏感肌：舒缓、抗敏、补水类面膜。\n易皱纹肌：补水、紧致类面膜。');
INSERT INTO `todo` VALUES ('57456c7a29e09', '控油', '当气温高于25℃时，会导致皮脂分泌过旺，造成肌肤水油不均衡。', '人体舒适温度为15-25℃，当气温高于25℃时，会影响肌肤新陈代谢，皮脂分泌过旺，造成肌肤水油不均衡。再加上长期待在空调房、在户外暴晒过度，造成肌肤缺水只剩下油。油性皮肤的你这时需要注意控油了。应使用控油、清爽型的洗面奶、爽肤水、乳液。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/57c4089282d79.png', '', '人体舒适温度为15-25℃，当气温高于25℃时，人体水分蒸发快，会影响肌肤新陈代谢，油性肌肤皮脂分泌过旺，造成肌肤水油不均衡。', '早晚注意清洁肌肤去油，上午及下午时段可用喷雾进行补水。', '选择补水、无油类型的喷雾型爽肤水。');
INSERT INTO `todo` VALUES ('57456c5263932', '抗敏', '根据您的肌肤状况及物理环境因素，建议进行抗敏护理哟。', '人体舒适温度为15-25℃，当气温低于15℃时，人体会感觉寒冷，此时空气中的湿度也相对较低，会导致皮肤干燥或刺激，引起皮肤过敏；当温度高于25℃时，人体水分流失加速，会导致皮肤干燥引起过敏。\n\n当空气湿度低于40%，空气干燥，会导致皮肤干燥、瘙痒，从而引起皮肤过敏；当空气湿度高于60%，空气比较湿润，不容易因为空气干燥而导致缺水，空气湿度过高容易容易滋生霉菌和小虫，刺激皮肤，导致皮肤敏感。\n\nPM2.5高于国家标准时，空气中含有大量污染颗粒和微生物，会依附在皮肤上，堵塞毛孔，会致皮肤的代谢循环受到影响，从而引起皮肤过敏。\n\n敏感性肌肤在以上情况下时，应该使用抗过敏的护肤品哦。\n', 'http://oss-qiwo-dev.qiwocloud1.com/skin/57c4089a76019.png', '', '气温低于15℃或高于25℃时，人体水分流失加快，肌肤干燥；湿度低于40%空气过于干燥，而高于60%,容易滋生细菌刺激皮肤，引起过敏；PM2.5高于75微克每立方米时，空气中大量污染物会导致肌肤过敏。', '早晚使用抗敏、舒缓类产品；雾霾天气外出需佩戴口罩。', '从洁面到乳液都应选购抗敏、舒缓的护肤品，乳液的应选择少油、补水类型。');
INSERT INTO `todo` VALUES ('57456c3f81a88', '补水', '空气湿度为40-60%时皮肤为舒适状态，但干性肌肤的你，“五行缺水”需补水护理。', '空气湿度在一定的温度下在一定体积的空气里含有的水汽的百分比。空气湿度为40-60%通常被视为最理想值。当空气湿度低于45%，空气干燥，会导致皮肤干燥，所以需要采取补水措施hf。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/57c408ad988fd.png', '', '空气湿度为40-60%为最理想值，皮肤也处于最佳状态，不会粘腻或干燥。但干性肌肤，容易干燥缺水，需补水护理。', '上午和下午时段应进行一次额外补水护理，可使用喷雾式爽肤水补水。', '干性肌：补水、保湿类。\n油性肌：补水、控油类。\n敏感肌：舒缓、抗敏、补水类。\n易皱纹肌：补水、紧致类。');
INSERT INTO `todo` VALUES ('57d61578674e6', '晨间护理(干性，耐受性）', '', '                                                          晨间护理\n\n干性、耐受性肌肤护理最大的任务是补水，日间还要做好防晒这个防护工作。\n\n护理步骤：\n1.	洁面：温水清洁，不会夺走肌肤水分的温水进行清洁。清洁重点以双手横向打圈的方式，从中间朝两侧清洁，用力轻柔。\n\n2.	爽肤水：选用补水保湿类型。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。\n\n3.	面霜：选用补水保湿类型。将乳液五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。\n\n4.	防晒霜：选用补水保湿类型。将防晒霜五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d6155328ce9.png', '', null, null, null);
INSERT INTO `todo` VALUES ('57d615a2963c8', '晚间护理（干性， 耐受性）', '', '                                                           晚间护理\n\n晚间护肤最大的任务是还是补水，补水! 同时，要注意清洁肌肤，做按摩、舒缓放松。\n\n晚间护理步骤：\n1.	卸妆乳： 选用补水保湿类型。晚间要彻底清洁肌肤，将卸妆乳五点式涂抹在脸上，轻轻打圈清洗肌肤。特别注意T区部位，要重点清洗。\n\n2.	爽肤水：选用补水保湿类型。将爽肤水倒入掌心，用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。\n\n3.	精华液：选用补水保湿类型。将精华五点式（额头、鼻子、脸颊、下巴）抹开，双手掌心从额头涂抹至脸颊、下巴至脖子。\n\n4.	面霜：选用补水保湿类型。在补水的同时必须使用具有保护层作用，能够直达肌肤深层的乳液或者乳霜。\n', 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d615a0d8dbf.png', '', null, null, null);
INSERT INTO `todo` VALUES ('57d615c2002f0', '晚间升华护理（干性，耐受性）', '', '                                                          晚间升华护理\n\n周末晚间护肤最大的任务是还是补水，密集补水! 同时，要注意去除死皮，做按摩、舒缓放松。\n\n晚间护理步骤：\n1．	磨砂膏：细微颗粒类型。将洁面奶用手搓出泡沫，再把磨砂膏加入其中。（洁面奶泡沫具有缓冲作用，这样用磨砂膏做清洁，对肌肤更温和，不刺激。）将混合好的洗面奶磨砂膏泡沫抹在额头、两颊、鼻子、下巴五个地方，再用手掌轻轻抹开。用指腹按摩，特别注意鼻翼和嘴角两侧以及眼角等容易长皱纹的部位。\n\n2.	卸妆乳：选用补水保湿类型。晚间要彻底清洁肌肤，将卸妆乳五点式涂抹在脸上，轻轻打圈清洗肌肤。特别注意T区部位，要重点清洗。\n\n3.	爽肤水面膜：选用补水保湿类型。化妆水倒满化妆棉，湿敷3分钟。完后用双手掌心从额头轻拍脸颊、下巴至脖子，轻拍至吸收。\n\n4.	面膜：选用补水保湿的泥面膜类型。敷10-15分钟即可。\n\n5.	精华液：选用补水保湿类型。精华液能够将能量输送到肌肤深层，选择保湿型精华液。\n\n6.	面霜：选用补水保湿类型。在补水的同时必须使用具有保护层作用，能够直达肌肤深层的乳液或者乳霜。\n', 'http://oss-qiwo-dev.qiwocloud1.com/skin/57d615beabaac.png', '', null, null, null);
