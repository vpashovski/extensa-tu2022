<?php

class ModelExtensionModuleLatestNews extends Model {
	public function getInformations($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC";

		if (!empty($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
}
