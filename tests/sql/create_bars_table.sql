DROP TABLE IF EXISTS `bars`;

CREATE TABLE `bars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `content` text,
  `publish_date` date DEFAULT NULL,
  `author_id` int(11) unsigned DEFAULT '1',
  `rate` float DEFAULT NULL,
  `score` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`) USING BTREE,
  KEY `index_name_publish_date` (`name`,`publish_date`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8