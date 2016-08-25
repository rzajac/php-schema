CREATE TABLE IF NOT EXISTS `table1` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `f1` int(11) unsigned NOT NULL,
  `f2` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `f1` (`f1`),
  KEY `rel13` (`f2`),
  CONSTRAINT `f1_c` FOREIGN KEY (`f1`) REFERENCES `table2` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `rel13` FOREIGN KEY (`f2`) REFERENCES `table3` (`other_id`) ON DELETE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;