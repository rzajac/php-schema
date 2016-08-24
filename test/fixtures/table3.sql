CREATE TABLE `table3` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `other_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `other_i` (`other_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
