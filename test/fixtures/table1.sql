CREATE TABLE `table1` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `f1` int(11) unsigned NOT NULL,
  `f2` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rel12` (`f1`),
  KEY `rel13` (`f2`),
  CONSTRAINT `rel12` FOREIGN KEY (`f1`) REFERENCES `table2` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `rel13` FOREIGN KEY (`f2`) REFERENCES `table3` (`other_id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
