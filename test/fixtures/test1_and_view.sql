CREATE TABLE `test1` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `col1` int(11) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE ALGORITHM=UNDEFINED DEFINER=`testUser`@`localhost` SQL SECURITY DEFINER VIEW `my_view` AS select `test1`.`id` AS `id`,`test1`.`col1` AS `col1` from `test1`;
