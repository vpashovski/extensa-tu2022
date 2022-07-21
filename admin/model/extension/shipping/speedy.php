<?php
class ModelExtensionShippingSpeedy extends Model {
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "speedy_address` (
		  `address_id` int(11) NOT NULL,
		  `customer_id` int(11) NOT NULL,
		  `postcode` varchar(10) NOT NULL DEFAULT '',
		  `city` varchar(255) NOT NULL DEFAULT '',
		  `city_id` int(11) NOT NULL DEFAULT '0',
		  `city_nomenclature` varchar(15) NOT NULL DEFAULT '',
		  `to_office` tinyint(1) NOT NULL DEFAULT '0',
		  `office_id` int(11) NOT NULL DEFAULT '0',
		  `quarter` varchar(255) NOT NULL DEFAULT '',
		  `quarter_id` int(11) NOT NULL DEFAULT '0',
		  `street` varchar(255) NOT NULL DEFAULT '',
		  `street_id` int(11) NOT NULL DEFAULT '0',
		  `street_no` varchar(255) NOT NULL DEFAULT '',
		  `block_no` varchar(255) NOT NULL DEFAULT '',
		  `entrance_no` varchar(255) NOT NULL DEFAULT '',
		  `floor_no` varchar(255) NOT NULL DEFAULT '',
		  `apartment_no` varchar(255) NOT NULL DEFAULT '',
		  `note` varchar(255) NOT NULL DEFAULT '',
		  `country` varchar(255) NOT NULL DEFAULT '',
		  `country_id` int(11) NOT NULL DEFAULT '0',
		  `country_nomenclature` varchar(15) NOT NULL DEFAULT '',
		  `state` varchar(255) NOT NULL DEFAULT '',
		  `state_id` varchar(50) NOT NULL DEFAULT '',
		  `required_state` tinyint(1) NOT NULL DEFAULT '0',
		  `required_postcode` tinyint(1) NOT NULL DEFAULT '0',
		  `address_1` varchar(255) NOT NULL DEFAULT '',
		  `address_2` varchar(255) NOT NULL DEFAULT '',
		  `abroad` tinyint(1) NOT NULL DEFAULT '0',
		  KEY `address_id` (`address_id`),
		  KEY `customer_id` (`customer_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "speedy_order` (
		  `speedy_order_id` INT(11) NOT NULL AUTO_INCREMENT,
		  `order_id` INT(11) NOT NULL DEFAULT '0',
		  `bol_id` VARCHAR(15) NOT NULL,
		  `data` TEXT NOT NULL,
		  `date_created` DATETIME NOT NULL,
		  `courier` TINYINT(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`speedy_order_id`),
		  KEY `order_id` (`order_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "speedy_table_rate` (
		  `service_id` INT(11) NOT NULL,
		  `take_from_office` TINYINT(1) NOT NULL,
		  `weight` DECIMAL(15,4) NOT NULL,
		  `order_total`  DECIMAL(15,4) NOT NULL,
		  `price_without_vat` DECIMAL(15,4) NOT NULL,
		  `fixed_time_delivery` TINYINT(1) NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "speedy_product_setting` (
		  `speedy_quantity_dimension_id` INT(11) NOT NULL AUTO_INCREMENT,
		  `product_id` INT(11) NOT NULL DEFAULT '0',
		  `quantity_dimentions` VARCHAR(255) NOT NULL DEFAULT '',
		  PRIMARY KEY (`speedy_quantity_dimension_id`),
		  KEY `product_id` (`product_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "speedy_weight_dimension` (
		  `speedy_weight_dimension_id` INT(11) NOT NULL AUTO_INCREMENT,
		  `WEIGHT` VARCHAR(255) NOT NULL DEFAULT '',
		  `XS` VARCHAR(255) NOT NULL DEFAULT '',
		  `S` VARCHAR(255) NOT NULL DEFAULT '',
		  `M` VARCHAR(255) NOT NULL DEFAULT '',
		  `L` VARCHAR(255) NOT NULL DEFAULT '',
		  `XL` VARCHAR(255) NOT NULL DEFAULT '',
		  PRIMARY KEY (`speedy_weight_dimension_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "speedy_address`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "speedy_order`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "speedy_table_rate`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "speedy_product_setting`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "speedy_weight_dimension`");
	}

	public function importFilePrice($data) {
		$this->db->query("TRUNCATE `" . DB_PREFIX . "speedy_table_rate`");

		foreach ($data as $row) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "speedy_table_rate SET service_id = '" . (int)$row['service_id'] . "', take_from_office = '" . (int)$row['take_from_office'] . "', weight = '" . (float)$row['weight'] . "', order_total = '" . (float)$row['order_total'] . "', price_without_vat = '" . (float)$row['price_without_vat'] . "', fixed_time_delivery = '" . (float)$row['fixed_time_delivery'] . "'");
		}
	}

	public function getWeightDimentions() {
		$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "speedy_weight_dimension'");

		if ($query->num_rows) {
			$rows = $this->db->query("SELECT * FROM `" . DB_PREFIX . "speedy_weight_dimension`");

			return $rows->rows;
		}
	}

	public function addWeightDimentions($data) {
		$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "speedy_weight_dimension'");

		if ($query->num_rows) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "speedy_weight_dimension`");

			foreach ($data as $row) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "speedy_weight_dimension SET WEIGHT = '" . (int)$row['WEIGHT'] . "', XS = '" . (int)$row['XS'] . "', S = '" . (int)$row['S'] . "', M ='" . (int)$row['M'] . "', L = '" . (int)$row['L'] . "', XL ='" . (int)$row['XL'] . "'");
			}
		}
	}

	public function updateTablesV410() {
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "speedy_order` ADD `operation_code` INT(11) NOT NULL");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "speedy_order` ADD `delivery_info` VARCHAR(255) NULL");
	}

	public function updateTablesV408() {
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "speedy_address` ADD `is_apt` TINYINT(1) NOT NULL DEFAULT '0'");
	}
}