DROP TABLE IF EXISTS `hoges`;

CREATE TABLE `hoges` (
  `user_id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `address` char(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  `token` blob,
  `create_at` datetime DEFAULT NULL COMMENT 'The created date',
  PRIMARY KEY (`user_id`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8