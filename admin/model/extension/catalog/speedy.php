<?php
class ModelExtensionCatalogSpeedy extends Model {
	public function getSpeedyDimentions($product_id) {
		$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "speedy_product_setting'");

		if ($query->num_rows) {
			$dimentions_rows = $this->db->query("SELECT * FROM " . DB_PREFIX . "speedy_product_setting WHERE product_id = " . (int)$product_id . " LIMIT 1");

			$result = array();

			if ($dimentions_rows->num_rows) {
				return array(
					'quantity_dimentions' => unserialize($dimentions_rows->row['quantity_dimentions'])
				);
			}

			return $result;
		}

		return array();
	}

	public function editSpeedyDimentions($product_id, $data) {
		$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "speedy_product_setting'");

		if ($query->num_rows) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "speedy_product_setting WHERE product_id = " . (int)$product_id);

			if ($data['quantity_dimentions']['XS'] || $data['quantity_dimentions']['S'] || $data['quantity_dimentions']['M'] || $data['quantity_dimentions']['L'] || $data['quantity_dimentions']['XL']) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "speedy_product_setting SET product_id = '" . (int)$product_id . "', quantity_dimentions = '" . $this->db->escape(serialize($data['quantity_dimentions'])) . "'");
			}
		}
	}
}