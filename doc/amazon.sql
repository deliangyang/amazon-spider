CREATE TABLE `amazon` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `rank` INT NOT NULL DEFAULT 0,
  `star` VARCHAR(20) NOT NULL DEFAULT '',
  `price` VARCHAR(20) NOT NULL DEFAULT '',
  `review` VARCHAR(10) NOT NULL DEFAULT '',
  `prime` VARCHAR(10) NOT NULL DEFAULT '',
  `date` VARCHAR(20) NOT NULL DEFAULT '',
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `category` VARCHAR(50) NOT NULL DEFAULT '',
  UNIQUE KEY `unique_category_rank` (`rank`, `category`),
  PRIMARY KEY (`id`)
);
