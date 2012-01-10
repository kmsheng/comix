DROP TABLE IF EXISTS `home_page`;
CREATE TABLE IF NOT EXISTS `home_page` (
	`id` int(11) NOT NULL,
	`name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`description` longtext COLLATE utf8_unicode_ci NOT NULL,
	`img_url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
