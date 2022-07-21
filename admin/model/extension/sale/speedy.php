<?php
class ModelExtensionSaleSpeedy extends Model {
	public function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "speedy_order WHERE order_id = '" . (int) $order_id . "'");

		return $query->row;
	}

	public function addOrder($order_id, $data) {
		$data['price_gen_method'] = $this->config->get('shipping_speedy_pricing');
		$this->db->query("INSERT INTO " . DB_PREFIX . "speedy_order SET order_id = '" . (int)$order_id . "', data = '" . $this->db->escape(serialize($data)) . "'");
	}

	public function editOrder($order_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "speedy_order SET bol_id = '" . $this->db->escape($data['bol_id']) . "', date_created = NOW() WHERE order_id  = '" . (int) $order_id . "'");
	}

	public function editOrderCourier($bol_id, $courier) {
		$this->db->query("UPDATE " . DB_PREFIX . "speedy_order SET courier = '" . (int) $courier . "' WHERE bol_id  = '" . $this->db->escape($bol_id) . "'");
	}

	public function getDeliveryInfo($bol_id, $order_id) {
		$this->load->library('speedy');

		if ($this->config->get('shipping_speedy_updated_v410')) {
			$final_operation = array_keys(Speedy::FINAL_OPERATION);

			$order = $this->db->query("SELECT * FROM " . DB_PREFIX . "speedy_order WHERE bol_id = " . (int)$bol_id . " AND operation_code IN (" . implode(',', $final_operation) . ")");

			if ($order->num_rows) {
				$return = $order->row;
				return unserialize($return['delivery_info']);
			} else {
				$delivery_info = $this->speedy->track($bol_id);

				if (in_array($delivery_info['operationCode'], $final_operation)) {
					$info = serialize($delivery_info);

					if ($this->config->get('shipping_speedy_order_status_update')) {
						$this->db->query("UPDATE " . DB_PREFIX . "speedy_order SET operation_code = '" . (int)$delivery_info['operationCode'] . "', delivery_info = '" . $this->db->escape($info) . "' WHERE bol_id = " . (int)$bol_id);

						$status = $this->config->get('shipping_speedy_final_statuses');

						if (!empty($status[$delivery_info['operationCode']])) {
							$this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = " . (int)$status[$delivery_info['operationCode']] . " WHERE order_id = " . (int)$order_id);
							$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = " . (int)$order_id . ", order_status_id = " . (int)$status[$delivery_info['operationCode']] . ", notify = 0, comment = 'Speedy UPDATE status', date_added = NOW()");
						}
					}

					return $delivery_info;
				}
			}
		}
	}

	public function updateOrderInfo($order_id, $data = array()) {
		$comment = '';

		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int) $order_id . "'");

		if ($order_query->num_rows) {
			$order_total = array();

			$order_total_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "'");

			foreach ($order_total_query->rows as $row) {
				$order_total[$row['code']] = $row;
			}

			if (!empty($order_total['shipping']) && !empty($order_total['total'])) {
				$language_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int) $order_query->row['language_id'] . "'");

				$this->language->load('extension/sale/speedy');

				$speedy_receiver_address = array();

				if (!isset($data['to_office']) || !$data['to_office']) {
					if (isset($data['quarter']) && $data['quarter']) {
						$speedy_receiver_address[] = $data['quarter'];
					}

					if (isset($data['street']) && $data['street']) {
						$speedy_receiver_address[] = $data['street'];
					}

					if (isset($data['street_no']) && $data['street_no']) {
						$speedy_receiver_address[] = $this->language->get('entry_street_no') . ' ' . $data['street_no'];
					}

					if (isset($data['block_no']) && $data['block_no']) {
						$speedy_receiver_address[] = $this->language->get('entry_block_no') . ' ' . $data['block_no'];
					}

					if (isset($data['entrance_no']) && $data['entrance_no']) {
						$speedy_receiver_address[] = $this->language->get('entry_entrance_no') . ' ' . $data['entrance_no'];
					}

					if (isset($data['floor_no']) && $data['floor_no']) {
						$speedy_receiver_address[] = $this->language->get('entry_floor_no') . ' ' . $data['floor_no'];
					}

					if (isset($data['apartment_no']) && $data['apartment_no']) {
						$speedy_receiver_address[] = $this->language->get('entry_apartment_no') . ' ' . $data['apartment_no'];
					}

					$speedy_receiver_address[0] = $this->language->get('text_delivery_to_address') . '<br/> ' . $speedy_receiver_address[0];
				} else {
					$speedy_receiver_address[] = $this->language->get('text_pickup_from_office') . '<br/> ' . $data['shipping_address_1'];
				}

				if (isset($data['note']) && $data['note']) {
					$speedy_receiver_address[] = $this->language->get('entry_note') . ' ' . $data['note'];
				}

				$shipping_city = explode(',',  $data['city']);

				$old_shipping_value = $order_total['shipping']['value'];
				$shipping_value = $this->currency->convert($data['shipping_method_cost'], $order_query->row['currency_code'], $this->config->get('config_currency'));
				$shipping_text = $shipping_value;

				$this->db->query("UPDATE " . DB_PREFIX . "order_total SET title = '" . (isset($data['shipping_method_title']) ? ($this->language->get('text_description') . ' - ' . $data['shipping_method_title']) : $order_total['shipping']['title']) . "', value = '" . (float) $shipping_value . "' WHERE order_total_id = '" . (int) $order_total['shipping']['order_total_id'] . "'");

				$comment .= (isset($data['shipping_method_title']) ? ($this->language->get('text_description') . ' - ' . $data['shipping_method_title']) : $order_total['shipping']['title']) . ' ' . $shipping_text;

				$total_value = $order_total['total']['value'] - $old_shipping_value + $shipping_value;
				$total_text = $total_value;

				$this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '" . (float) $total_value . "' WHERE order_total_id = '" . (int) $order_total['total']['order_total_id'] . "'");

				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float) $total_value . "', shipping_address_1 = '" . $this->db->escape(implode(', ', $speedy_receiver_address)) . "', shipping_city = '" . $this->db->escape($shipping_city[0]) . "', shipping_postcode = '" . $this->db->escape($data['postcode']) . "', shipping_code = '" . (isset($data['shipping_method']) ? $data['shipping_method'] : $order_query->row['shipping_code']) . "', shipping_method = '" . (isset($data['shipping_method_title']) ? ($this->language->get('text_description') . ' - ' . $data['shipping_method_title']) : $order_query->row['shipping_method']) . "' WHERE order_id = '" . (int) $order_id . "'");

				$comment .= "\n" . $order_total['total']['title'] . ' ' . $total_text;
			}

			if ($this->session->data['is_speedy_bol_recalculated']) {
				$data['price_gen_method'] = $this->config->get('shipping_speedy_pricing');
			}

			$this->db->query("UPDATE " . DB_PREFIX . "speedy_order SET data = '" . $this->db->escape(serialize($data)) . "' WHERE order_id = '" . (int) $order_id . "'");
		}

		return $comment;
	}

	public function deleteOrder($order_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "speedy_order SET bol_id = '', date_created = '0000-00-00 00:00:00', courier = '0' WHERE order_id  = '" . (int) $order_id . "'");
	}

	public function getOrders($data = array()) {
		$sql = "SELECT so.*, CONCAT(o.firstname, ' ', o.lastname) AS customer FROM " . DB_PREFIX . "speedy_order so INNER JOIN `" . DB_PREFIX . "order` o ON o.order_id = so.order_id WHERE so.bol_id > 0";

		if (!empty($data['filter_bol_id'])) {
			$sql .= " AND so.bol_id = '" . $this->db->escape($data['filter_bol_id']) . "'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND so.order_id = '" . (int) $data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_created'])) {
			$sql .= " AND DATE(so.date_created) = DATE('" . $this->db->escape($data['filter_date_created']) . "')";
		}

		$sort_data = array(
			'so.bol_id',
			'so.order_id',
			'customer',
			'so.date_created'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY so.date_created";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(so.speedy_order_id) AS total FROM " . DB_PREFIX . "speedy_order so INNER JOIN `" . DB_PREFIX . "order` o ON o.order_id = so.order_id WHERE so.bol_id > 0";

		if (!empty($data['filter_bol_id'])) {
			$sql .= " AND so.bol_id = '" . $this->db->escape($data['filter_bol_id']) . "'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND so.order_id = '" . (int) $data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_created'])) {
			$sql .= " AND DATE(so.date_created) = DATE('" . $this->db->escape($data['filter_date_created']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getSpeedyTableRate($data) {
		$query = $this->db->query("SELECT price_without_vat FROM " . DB_PREFIX . "speedy_table_rate WHERE service_id = '" . (int)$data['service_id'] . "' AND take_from_office = '" . (int)$data['take_from_office'] . "' AND weight >= '" . (float)$data['weight'] . "' AND order_total >= '" . (int)$data['order_total'] . "' AND fixed_time_delivery = '" . (int)$data['fixed_time_delivery'] . "' ORDER BY weight, order_total ASC");

		return $query->row;
	}

	public function changeOrderStatus($order_id, $status_id) {
		$query = $this->db->query("UPDATE " . DB_PREFIX . "order SET `order_status_id` = '" . (int)$status_id . "' WHERE order_id =" . (int)$order_id);
	}
}