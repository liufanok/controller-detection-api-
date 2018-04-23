#创建数据库
CREATE DATABASE controller;

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

