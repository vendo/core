<?php defined('SYSPATH') or die('No direct script access.');

/**
 * initial vendo core structure
 */
class Migration_Vendo_20110610185958 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, 'CREATE TABLE `addresses` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `billing_address` varchar(100) NOT NULL,
		  `billing_city` varchar(50) NOT NULL,
		  `billing_state` varchar(50) DEFAULT NULL,
		  `billing_postal_code` varchar(25) DEFAULT NULL,
		  `shipping_address` varchar(100) NOT NULL,
		  `shipping_city` varchar(50) NOT NULL,
		  `shipping_state` varchar(50) DEFAULT NULL,
		  `shipping_postal_code` varchar(25) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		$db->query(NULL, 'CREATE TABLE `photos` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `filename` varchar(50) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		$db->query(NULL, 'CREATE TABLE `products` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(50) NOT NULL,
		  `price` decimal(10,2) NOT NULL,
		  `description` text NOT NULL,
		  `order` int(10) unsigned NOT NULL,
		  `primary_photo_id` bigint(20) unsigned DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		$db->query(NULL, 'CREATE TABLE `products_photos` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `product_id` bigint(20) unsigned NOT NULL,
		  `photo_id` bigint(20) unsigned NOT NULL,
		  `order` bigint(20) unsigned NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `fk_products_photos_product_id` (`product_id`),
		  KEY `fk_products_photos_photo_id` (`photo_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		$db->query(NULL, 'CREATE TABLE `product_categories` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(50) NOT NULL,
		  `order` int(10) unsigned NOT NULL,
		  `parent_id` bigint(20) unsigned DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `parent_id` (`parent_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		$db->query(NULL, 'INSERT INTO `product_categories` (
			`id`, `name`, `order`, `parent_id`)
			VALUES
			(1, \'Foo\', 1, NULL)');

		$db->query(NULL, 'CREATE TABLE `product_categories_products` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `product_id` bigint(20) unsigned NOT NULL,
		  `product_category_id` bigint(20) unsigned NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `fk_product` (`product_id`),
		  KEY `fk_product_category` (`product_category_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		$db->query(NULL, 'CREATE TABLE `roles` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(50) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		$db->query(NULL, 'INSERT INTO `roles` (`id`, `name`) VALUES
			(1, \'login\'),
			(2, \'admin\')');

		$db->query(NULL, 'CREATE TABLE `users` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `email` varchar(50) NOT NULL,
		  `password` blob NOT NULL,
		  `address_id` bigint(20) unsigned DEFAULT NULL,
		  `first_name` varchar(50) DEFAULT NULL,
		  `last_name` varchar(50) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `email` (`email`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		$db->query(NULL, 'INSERT INTO `users` (`id`, `email`, `password`, `address_id`, `first_name`, `last_name`) VALUES
		(1, \'admin@example.com\', 0xf5251c27aac43208936f638a47ce0722e3dbd8463bf1b0241e0f71368015880ff263ae31b88c77ada40761535d5716fbe9f02e3d20368d49a3747dfb237d591dfd3a358c275f6bb44885f6060930b022c4751c00eb4dde268042801d0b26b172, NULL, \'Foo\', \'Bar\')');

		$db->query(NULL, 'CREATE TABLE IF NOT EXISTS `users_roles` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `user_id` bigint(20) unsigned NOT NULL,
		  `role_id` bigint(20) unsigned NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `fk_user` (`user_id`),
		  KEY `fk_role` (`role_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		$db->query(NULL, 'INSERT INTO `users_roles` (`id`, `user_id`, `role_id`) VALUES
			(1, 1, 1),
			(2, 1, 2)');

		$db->query(NULL, 'ALTER TABLE `products_photos`
		  ADD CONSTRAINT `fk_products_photos_photo_id` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE,
		  ADD CONSTRAINT `fk_products_photos_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE');

		$db->query(NULL, 'ALTER TABLE `product_categories`
		  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE');

		$db->query(NULL, 'ALTER TABLE `product_categories_products`
		  ADD CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
		  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`product_category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE');

		$db->query(NULL, 'ALTER TABLE `users_roles`
		  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
		  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE');
}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(
			NULL, 'SET foreign_key_checks = 0'
		);

		$db->query(NULL, 'DROP TABLE `addresses`');
		$db->query(NULL, 'DROP TABLE `photos`');
		$db->query(NULL, 'DROP TABLE `products`');
		$db->query(NULL, 'DROP TABLE `products_photos`');
		$db->query(NULL, 'DROP TABLE `product_categories`');
		$db->query(NULL, 'DROP TABLE `product_categories_products`');
		$db->query(NULL, 'DROP TABLE `roles`');
		$db->query(NULL, 'DROP TABLE `users`');
		$db->query(NULL, 'DROP TABLE `users_roles`');

		$db->query(
			NULL, 'SET foreign_key_checks = 1'
		);
	}
}
