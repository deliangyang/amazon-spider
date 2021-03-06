
CREATE TABLE `amazon` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `md5_url` VARCHAR(32) NOT NULL DEFAULT '',
  `rank` INT NOT NULL DEFAULT 0,
  `star` VARCHAR(20) NOT NULL DEFAULT '',
  `price` VARCHAR(20) NOT NULL DEFAULT '',
  `review` VARCHAR(10) NOT NULL DEFAULT '',
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `category` VARCHAR(20) NOT NULL DEFAULT '',
  `asin` VARCHAR(20) NOT NULL DEFAULT '',
  `date` VARCHAR(20) NOT NULL DEFAULT '',
  `category_id` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `create_at` INT NOT NULL DEFAULT 0,
  `update_at` INT NOT NULL DEFAULT 0,
  UNIQUE KEY (`md5_url`),
  PRIMARY KEY (`id`)
);


create table `spider_results` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` INT NOT NULL  DEFAULT 0 COMMENT '用户编号',
  `amount` DECIMAL(10, 7) NOT NULL  DEFAULT 0 COMMENT '金额',
  `date` timestamp not null default current_timestamp,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
