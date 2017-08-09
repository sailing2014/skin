/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50173
Source Host           : 192.168.3.243:3306
Source Database       : skin

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2016-08-24 15:53:27
*/
USE `skin`;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for push
-- ----------------------------
DROP TABLE IF EXISTS `push`;
CREATE TABLE `push` (
  `id` char(13) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `content` text,
  `thumb` varchar(80) DEFAULT NULL,
  `image` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of push
-- ----------------------------
INSERT INTO `push` VALUES ('5795d4865b030', '轻度防晒', '紫外线强度虽不大，但不要让阳光有可乘之机！', '轻度防晒是指紫外线超过一定的阀值，为防止肌肤被晒黑、晒伤等目的，所采取的防晒措施。色素沉着性肌肤，紫外线等级为弱时就应进行轻度防晒。建议使用SPF12-15的防晒霜。如果在室外活动，需要每隔两小时重复涂抹。\n\n干性肌肤：建议选择油分含量较高、具有补水功效的防晒霜。\n油性肌肤：建议选择不含油份、轻薄的防晒霜。\n敏感性肌肤：建议选择具有舒缓、抗过敏功效的防晒霜。\n易皱纹性肌肤：建议选择具有抗衰老、抗皱纹成分的防晒霜。\n', 'http://oss-qiwo-dev.qiwocloud1.com/skin/5799a996131c6.png', '');
INSERT INTO `push` VALUES ('57469fc66c141', '重度防晒', '这个紫外线强度持续晒十分钟，就有潜质成为黑美人呢!', '重度防晒是指紫外线超过安全值，非常强烈，为防止肌肤被晒黑、晒伤等目的，所采取的防晒措施。\n当您的肌肤是色素沉着性肌肤时，紫外线等级为强、很强时就应进行重度防晒。\n当您的肌肤是非色素沉着性肌肤时，紫外线等级为很强时就应进行重度防晒。\n\n如果您的肌肤是干性，建议选择油分含量较高、具有补水功效， SPF20以上的防晒霜。\n如果您的肌肤是油性，建议选择不含油份、轻薄， SPF20以上的防晒霜。\n如果您的肌肤是敏感性，建议选择具有舒缓、抗过敏功效， SPF20以上的防晒霜。\n如果您的肌肤是易皱纹性，建议选择具有抗衰老、抗皱纹成分，SPF20以上的防晒霜。\n\n如果您在室内居多，上午下午各涂抹一次防晒霜即可；如果您在户外的时间居多，建议您每隔2小时涂抹一次ff。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/5799a98846311.png', '');
INSERT INTO `push` VALUES ('57469e56d9db2', '中度防晒', '紫外线强度不小哦，我怕你晒黑了不漂亮。', '中度防晒是指紫外线指数偏高，接近安全值时，为为防止肌肤被晒黑、晒伤等目的，所采取的防晒措施。\n当您的肌肤是色素沉着性肌肤时，紫外线等级为中等时就应进行中度防晒。\n当您的肌肤是非色素沉着性肌肤时，紫外线等级为强时就应进行中度防晒。\n\n如果您的肌肤是干性，建议选择油分含量较高、具有补水功效， SPF15-20的防晒霜。\n如果您的肌肤是油性，建议选择不含油份、轻薄， SPF15-20的防晒霜。\n如果您的肌肤是敏感性，建议选择具有舒缓、抗过敏功效， SPF15-20的防晒霜。\n如果您的肌肤是易皱纹性，建议选择具有抗衰老、抗皱纹成分，SPF15-20的防晒霜。\n\n如果您在室内居多，上午下午各涂抹一次防晒霜即可；如果您在户外的时间居多，建议每隔三小时涂抹一次ffrr。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/5799a97b0ab0c.png', '');
INSERT INTO `push` VALUES ('57456c7a29e09', '控油', '温度这么高怪不得满面油光，快去控油吧！', '控油指的是调整皮肤分泌的油脂含量。不管是中性皮肤，还是油性、混合性皮肤，一般都会出油，而容易出油的地方就是T区。一般的控油方法就是用控油产品和勤洗脸。\n\n其实，对付油性皮肤很简单，虽然大体上有所不同，但总体上只要做到清洁--收缩毛孔--加强护理就可以告别油光脸啦！\n\n彻底清洁是避免油性皮肤泛油光的不二法门，最好使用无油脂的洗面膏或具有控油效果的洗面乳，这样可以祛除多余的油脂。\n洁肤后，务必使用具有收敛因子和抗菌素的喷雾，缩小毛孔，使皮肤细腻。同时使用不含油脂的补水护肤品美白保湿温和紧肤滋润爽肤水来润肤保湿，促进水油平衡。\n定期做面膜，祛除黑头，有消炎减少粉刺的作用。\n市面上有好多专为油光脸设计的产品，它们不但可以把油吸得光光的，还可以还你一张清爽粉嫩的脸fff。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/5799a970c558c.png', '');
INSERT INTO `push` VALUES ('57456c5263932', '抗敏', '这种天气正是敏感的诱发温室，记得要抗敏哦。', '空气中的粉尘、微生物附着在皮肤上会刺激皮肤导致敏感不适。建议您出门时要涂隔离霜，戴口罩，避免粉层及微生物直接接触皮肤ffdd。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/5799a9670abee.png', '');
INSERT INTO `push` VALUES ('57456c482f589', '清洁', 'PM2.5超出标准值，空气中污染颗粒和微生物含量过高，记得要深层清洁肌肤哦。', '空气中的粉尘微生物附着在皮肤上会刺激皮肤、堵塞毛孔，建议您出门时要涂隔离霜，戴口罩。晚间要深层清洁肌肤表层，彻底清洗毛孔中的PM2.5残留物质。可以选用含有竹炭精华的洁面乳和面膜之类的产品fff。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/5799a95c287dd.png', '');
INSERT INTO `push` VALUES ('57bd4ad4844e2', '深度补水', '主人，空气干燥皮肤好渴，需要深度补水。', '空气湿度在一定的温度下在一定体积的空气里含有的水汽的百分比。空气湿度为40-60%通常被视为最理想值。当空气湿度低于45%，空气干燥，会导致皮肤干燥，所以需要采取补水措施hf。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/5799a94e1436e.png', null);
INSERT INTO `push` VALUES ('57456c3f81a88', '补水', '气温过高或过低都会导致皮肤缺水，脸渴了给口水喝吧。', '空气湿度在一定的温度下在一定体积的空气里含有的水汽的百分比。空气湿度为40-60%通常被视为最理想值。当空气湿度低于45%，空气干燥，会导致皮肤干燥，所以需要采取补水措施hf。', 'http://oss-qiwo-dev.qiwocloud1.com/skin/5799a94e1436e.png', '');