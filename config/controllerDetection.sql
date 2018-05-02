#创建数据库
CREATE DATABASE controller;
USE controller;

#用户信息表
CREATE TABLE `user`(
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
	`username` VARCHAR(255) NOT NULL COMMENT '用户名' ,
	`roles` set('admin', 'normal') NOT NULL DEFAULT 'normal' COMMENT '用户的角色，admin管理员 normal普通用户',
	`password_hash` VARCHAR(255) NOT NULL COMMENT '加密密码' ,
	`password_reset_token` VARCHAR(255) DEFAULT NULL COMMENT '重置密码token' ,
	`phone` VARCHAR(32) NOT NULL COMMENT '手机号' ,
	`email` VARCHAR(255) NOT NULL COMMENT '邮箱' ,
	`auth_key` VARCHAR(32) NOT NULL COMMENT '自动登录key' ,
	`status` enum('0','10') NOT NULL DEFAULT '10' COMMENT '用户状态 0已禁用 10正常' ,
	`login_times` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户登录次数' ,
	`last_login_time` TIMESTAMP NULL DEFAULT NULL COMMENT '最近一次的登录时间' ,
	`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间' ,
	`update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间' ,
	PRIMARY KEY(`id`) ,
	UNIQUE KEY `username`(`username`) ,
	UNIQUE KEY `phone`(`phone`) ,
	UNIQUE KEY `email`(`email`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '用户表信息';

CREATE TABLE plant(
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
	`name` VARCHAR(64) NOT NULL COMMENT '厂区名称' ,
	`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间' ,
	`update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间' ,
	PRIMARY KEY(`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '厂区表';

CREATE TABLE workshop(
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
	`name` VARCHAR(64) NOT NULL COMMENT '车间名称' ,
	`plant_id` INT(11) NOT NULL COMMENT '车间对应的厂区id' ,
	`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间' ,
	`update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间' ,
	PRIMARY KEY(`id`) ,
	FOREIGN KEY(plant_id) REFERENCES plant(id)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '车间表';

CREATE TABLE plant_workshop(
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
	`plant_id` INT(11) NOT NULL COMMENT '厂区id' ,
	`workshop_id` INT(11) NOT NULL COMMENT '车间id' ,
	`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间' ,
	`update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间' ,
	PRIMARY KEY(`id`) ,
	FOREIGN KEY(plant_id) REFERENCES plant(id) ,
	FOREIGN KEY(workshop_id) REFERENCES workshop(id)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '厂区车间关系表';

CREATE TABLE loops(
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
	`name` VARCHAR(64) NOT NULL COMMENT '回路名称' ,
	`workshop_id` INT(11) NOT NULL COMMENT '车间id' ,
	`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间' ,
	`update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间' ,
	PRIMARY KEY(`id`) ,
	FOREIGN KEY(workshop_id) REFERENCES workshop(id)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '回路表';

CREATE TABLE user_belong(
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
	`user_id` INT(11) NOT NULL COMMENT '用户id' ,
	`belong_id` INT(11) DEFAULT NULL COMMENT '车间或者厂区的id' ,
	`belong_type` ENUM('1' , '2') NOT NULL DEFAULT '1' COMMENT '1属于某个车间 2属于某个厂区' ,
	`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间' ,
	`update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间' ,
	PRIMARY KEY(`id`) ,
	FOREIGN KEY(user_id) REFERENCES USER(id)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '用户对应的车间/厂区关系表';

CREATE TABLE `data`(
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
	`loop_id` INT(11) NOT NULL COMMENT '回路的id' ,
	`time` TIMESTAMP NOT NULL COMMENT '测量的时间：eg：2017-01-01 1:55' ,
	`mv` DOUBLE DEFAULT NULL COMMENT '' ,
	`pv` DOUBLE DEFAULT NULL COMMENT '测量值 PV' ,
	`sp` DOUBLE DEFAULT NULL COMMENT '给定值 SV' ,
	`mode` DOUBLE DEFAULT NULL COMMENT '手动自动模式值 0手动 1自动' ,
	PRIMARY KEY(`id`) ,
	FOREIGN KEY(loop_id) REFERENCES loops(id)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '实时数据表';

CREATE TABLE `result`(
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
	`loop_id` INT(11) NOT NULL COMMENT '回路的id' ,
	`date` DATE NOT NULL COMMENT '测量时间 eg:2018-03-09' ,
	`suggest` VARCHAR(10) DEFAULT '' COMMENT '分析建议代号（1-8），如有多个用，隔开' ,
	`performance` VARCHAR(10) NOT NULL COMMENT '评价结果 Excellent/Good/Fair/Poor' ,
	`rpi` FLOAT NOT NULL COMMENT '相对性能指标' ,
	`osci` FLOAT NOT NULL COMMENT '震荡指数' ,
	`set_time` INT NULL DEFAULT NULL COMMENT '稳态时间，单位秒' ,
	`dev_err` FLOAT NULL DEFAULT NULL COMMENT '偏差的标准差' ,
	`e_err` FLOAT NULL DEFAULT NULL COMMENT '偏差的均值' ,
	`e_sf` FLOAT NULL DEFAULT NULL COMMENT '有效投用率' ,
	`sf` FLOAT NULL DEFAULT NULL COMMENT '投用率' ,
	`dev_sv` FLOAT NULL DEFAULT NULL COMMENT '设定值的标准差' ,
	`dev_pv` FLOAT NULL DEFAULT NULL COMMENT '实测值的标准差' ,
	`dev_mv` FLOAT NULL DEFAULT NULL COMMENT '阀位值的标准差' ,
	`e_sv` FLOAT NULL DEFAULT NULL COMMENT '设定值的均值' ,
	`e_pv` FLOAT NULL DEFAULT NULL COMMENT '实测值的均值' ,
	`e_mv` FLOAT NULL DEFAULT NULL COMMENT '发位值的均值' ,
	`switch` INT NOT NULL DEFAULT 0 COMMENT '切换次数' ,
	`start_time` TIME NOT NULL COMMENT '统计的开始时间' ,
	`end_time` TIME NOT NULL COMMENT '统计的结束时间' ,
	`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间' ,
	`update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间' ,
	PRIMARY KEY(`id`) ,
	FOREIGN KEY(loop_id) REFERENCES loops(id)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '计算结果';