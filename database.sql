# Dump of table links
# ------------------------------------------------------------

DROP TABLE IF EXISTS `links`;

CREATE TABLE `links` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(15) DEFAULT NULL,
  `url` text,
  `visits` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `last_visited` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
