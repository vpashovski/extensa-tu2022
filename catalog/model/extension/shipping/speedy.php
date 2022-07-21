<?php
class ModelExtensionShippingSpeedy extends Model {
	private $parcel_sizes = array(
		1 => 'XS',
		2 => 'S',
		3 => 'M',
		4 => 'L',
		5 => 'XL',
	);

	function getQuote($address) {
		$this->language->load('extension/shipping/speedy');

		if (isset($address['validate'])) {
			$status = true;
		} else {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_speedy_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if (!$this->config->get('shipping_speedy_geo_zone_id')) {
				$status = true;
			} elseif ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}
		}

		$method_data = array();

		if ($status) {
			$quote_data = array();

			$quote_data['speedy'] = array(
				'code'         => 'speedy.speedy',
				'title'        => $this->language->get('text_description'),
				'cost'         => 0.00,
				'tax_class_id' => 0,
				'text'         => ''
			);

			$method_data = array(
				'code'       => 'speedy',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_speedy_sort_order'),
				'error'      => false
			);

			if (isset($this->session->data['speedy']) &&
				($this->customer->isLogged() && isset($this->session->data['shipping_address']['address_id'])
				&& isset($this->session->data['speedy_customer']) &&
				$this->session->data['speedy_customer']['shipping_address_id'] == $this->session->data['shipping_address']['address_id'] ||
				isset($this->session->data['guest']) && isset($this->session->data['speedy_guest']) &&
				$this->session->data['speedy_guest']['city'] == $this->session->data['shipping_address']['city'] &&
				$this->session->data['speedy_guest']['postcode'] == $this->session->data['shipping_address']['postcode'] ||
				isset($this->session->data['api_id']))
			) {
				$this->load->library('speedy');

				$totals = array();
				$taxes = $this->cart->getTaxes();
				$total = 0;

				$total_data = array(
					'totals' => &$totals,
					'taxes'  => &$taxes,
					'total'  => &$total
				);

				if (isset($this->session->data['coupon'])) {
					$this->load->model('extension/total/coupon');

					$coupon_info = $this->model_extension_total_coupon->getCoupon($this->session->data['coupon']);

					$coupon = $this->session->data['coupon'];
				}

				$this->load->model('setting/extension');

				$results = $this->model_setting_extension->getExtensions('total');

				$sort_order = array();

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_DESC, $results);

				foreach ($results as $result) {
					if ($this->config->get('total_' . $result['code'] . '_status')) {
						if ($result['code'] != 'shipping' || isset($coupon_info) && !empty($coupon_info['shipping'])) {
							$this->load->model('extension/total/' . $result['code']);

							$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
						}
					}
				}

				if (!empty($coupon)) {
					$this->session->data['coupon'] = $coupon;
				}

				$total = $this->currency->convert($total, $this->config->get('config_currency'), $this->session->data['currency']);
				$weight = 0;
				$totalNoShipping = 0;

				foreach ($this->cart->getProducts() as $product) {
					if ($product['shipping']) {
						$product_weight = (float)$product['weight'];
						if (!empty($product_weight)) {
							$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], 1);
						} else {
							$weight += ($this->config->get('shipping_speedy_default_weight') * $product['quantity']);
						}

						$totalNoShipping += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->session->data['currency']) * $product['quantity'];
					}
				}

				if ($this->config->get('shipping_speedy_documents') && (float)$weight > 0.25) {
					$weight = 0.25;
				}

				$this->session->data['speedy']['total'] = number_format($total, 2);
				$this->session->data['speedy']['totalNoShipping'] = number_format($this->currency->convert($totalNoShipping, $this->config->get('config_currency'), $this->session->data['currency']), 2);
				$this->session->data['speedy']['order_currency_code'] = $this->session->data['currency'];
				$this->session->data['speedy']['weight'] = $weight;
				$this->session->data['speedy']['count'] = 1; // ParcelsCount
				$this->session->data['speedy']['taking_date'] = ($this->config->get('shipping_speedy_taking_date') ? strtotime('+' . (int)$this->config->get('shipping_speedy_taking_date') . ' day', mktime(9, 0, 0)) : time());
				$this->session->data['speedy']['client_id'] = $this->config->get('shipping_speedy_client_id');
				$this->session->data['speedy']['convertion_to_win1251'] = $this->config->get('shipping_speedy_convertion_to_win1251');

				if ($this->config->get('shipping_speedy_pricing') == 'fixed' || $this->config->get('shipping_speedy_pricing') == 'calculator_fixed') {
					$this->session->data['speedy']['total'] += $this->currency->convert($this->config->get('shipping_speedy_fixed_price'), $this->speedy->baseCurrency, $this->session->data['currency']);
				}

				$this->session->data['speedy']['fixed_time'] = null;

				if (!$this->config->get('payment_speedy_cod_status')) {
					$this->session->data['speedy']['cod'] = false;
				}

				$this->session->data['speedy']['parcels_size'] = array(
					1 => array(
						'weight' => '',
						'width'  => '',
						'height' => '',
						'depth'  => '',
					)
				);

				$cart_products = $this->cart->getProducts();
				$countProducts = 0;
				$parcel_size = $this->parcel_sizes[1];
				$products = array();

				if (!$this->session->data['speedy']['abroad']) {
					foreach ($cart_products as $product) {
						if ($product['shipping']) {
							$countProducts += $product['quantity'];
							$sizes = $this->getSpeedyQuantityDimention($product['product_id'], $product['quantity']);

							if (!empty($sizes) || $this->speedyHasQuantityDimention($product['product_id'])) {
								$sizes['quantity'] = $product['quantity'];
								$sizes['name'] = $product['name'];
								$products[] = $sizes;

								if (!empty($sizes['size'])) {
									$parcel_size = $this->compareSizes($parcel_size, $sizes['size']);
								}
							} else {
								$no_parcel_size = true;
							}
						}
					}

					$weight_size = $this->getSpeedyWeightDimention($this->cart->getWeight(), $countProducts);

					if (!empty($products) && empty($no_parcel_size)) {
						for ($i = 1;$i <= count($this->parcel_sizes); $i++) {
							$parcel_full = 0;

							foreach ($products as $product) {
								if (empty($product['sizes'])) {
									$parcel_size = '';
									break 2;
								}
								$parcel_full += $product['quantity'] / $product['sizes'][$parcel_size];
							}

							if ($parcel_full > 1) {
								$next_size = array_search($parcel_size, $this->parcel_sizes) + 1;

								if (isset($this->parcel_sizes[$next_size])) {
									$parcel_size = $this->parcel_sizes[$next_size];
								} else {
									$parcel_size = '';
									break;
								}
							} else {
								break;
							}
						}
					} elseif ($weight_size) {
						$size_compare = $this->calculateSize($products, $parcel_size);

						if ($size_compare) {
							$parcel_size = $this->compareSizes($size_compare, $weight_size);
						} else {
							$parcel_size = $weight_size;
						}
					} elseif ($this->config->get('shipping_speedy_min_package_dimention')) {
						$size_compare = $this->calculateSize($products, $parcel_size);

						if ($size_compare) {
							$parcel_size = $this->compareSizes($size_compare, $this->config->get('shipping_speedy_min_package_dimention'));
						} else {
							$parcel_size = $this->config->get('shipping_speedy_min_package_dimention');
						}
					} else {
						$parcel_size = '';
					}
				} else {
					$parcel_size = '';
				}

				$this->session->data['speedy']['parcel_size'] = $parcel_size;

				if (isset($address['validate']) || !isset($this->session->data['speedy_guest_address']) || (isset($this->session->data['speedy_guest_address']) && md5(serialize($this->session->data['speedy_guest_address'])) == md5(serialize($address)))) {
					$session = $this->session->data['speedy'];

					if (!empty($this->session->data['speedy']['fixed_time_cb'])) {
						$session['fixed_time'] = $this->session->data['speedy']['fixed_time_hour'] . $this->session->data['speedy']['fixed_time_min'];
					}

					$methods = $this->speedy->calculate($session);
				} else {
					$methods = array();
				}

				$lang = ($this->session->data['speedy']['abroad']) ? 'en' : $this->language->get('code');
				$services = $this->speedy->getServices($lang);
				$methods_count = 0;

				if (!$this->speedy->getError()) {
					foreach ($methods as $method) {
						if (empty($method['error'])) {
							if (($this->config->get('shipping_speedy_pricing') == 'free') && ($this->currency->convert($total, $this->session->data['currency'], $this->speedy->baseCurrency) >= (float)$this->config->get('shipping_speedy_free_shipping_total')) &&
								($method['serviceId'] == $this->config->get('shipping_speedy_free_method_city') || $method['serviceId'] == $this->config->get('shipping_speedy_free_method_intercity') || in_array($method['serviceId'], $this->config->get('shipping_speedy_free_method_international')))) {
								$method_total = 0;
							} elseif ($this->config->get('shipping_speedy_pricing') == 'fixed') {
								$method_total = $this->config->get('shipping_speedy_fixed_price');
							} elseif ($this->config->get('shipping_speedy_pricing') == 'table_rate') {
								$filter_data = array(
									'service_id' => $method['serviceId'],
									'take_from_office' => $this->session->data['speedy']['to_office'],
									'weight' => $weight,
									'order_total' => $this->currency->convert($total, $this->session->data['currency'], $this->speedy->baseCurrency),
									'fixed_time_delivery' => isset($this->session->data['speedy']['fixed_time_cb']) ? $this->session->data['speedy']['fixed_time_cb'] : 0,
								);

								$speedy_table_rate = $this->getSpeedyTableRate($filter_data);

								if (empty($speedy_table_rate)) {
									continue;
								} else {
									$method_total = $speedy_table_rate['price_without_vat'];
								}
							} else {
								$method_total = $method['price']['total'];

								if ($this->config->get('shipping_speedy_pricing') == 'calculator_fixed') {
									$method_total += $this->config->get('shipping_speedy_fixed_price');
								}
							}

							if ($this->currency->has($this->speedy->baseCurrency)) {
								$method_cost = $this->currency->convert($method_total, $this->speedy->baseCurrency, $this->config->get('config_currency'));// Винаги връща в BGN
								$method_text = $this->currency->convert($method_total, $this->speedy->baseCurrency, $this->session->data['currency']);
							} else {
								$method_cost = $method_total;
								$method_text = $method_total;
							}

							if ($method['serviceId'] == 500 && !empty($this->session->data['speedy']['parcel_size'])) { // for SPEEDY POST
								$method_title = $this->language->get('text_description') . ' - ' . $services[$method['serviceId']] . ' (' . $parcel_size . ')';
							} else {
								$method_title = $this->language->get('text_description') . ' - ' . $services[$method['serviceId']];
							}

							$quote_data[$method['serviceId']] = array(
								'code'         => 'speedy.' . $method['serviceId'],
								'title'        => $method_title,
								'cost'         => $method_cost,
								'tax_class_id' => 0,
								'text'         => $this->currency->format($method_text, $this->session->data['currency'], 1)
							);

							$methods_count++;
						}
					}

					if ($methods_count) {
						unset($quote_data['speedy']);
						$method_data['quote'] = $quote_data;
					} elseif (!$methods_count && $this->config->get('shipping_speedy_pricing') == 'table_rate') {
						$method_data['speedy_error'] = $this->language->get('error_calculate_table_rate');
					} else {
						$method_data['speedy_error'] = $this->language->get('error_calculate_empty_methods');
					}
				} else {
					$method_data['speedy_error'] = $this->speedy->getError();
				}
			} else {
				$method_data['speedy_error'] = $this->language->get('error_calculate');
				unset($this->session->data['speedy']);
			}
		}

		if (isset($method_data['speedy_error'])) {
			$method_data['quote']['speedy']['text'] = '';
		}

		return $method_data;
	}

	public function addAddress($address_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "speedy_address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "speedy_address SET
		address_id = '" . (int)$address_id . "',
		customer_id = '" . (int)$this->customer->getId() . "',
		postcode = '" . (isset($data['postcode']) ? $this->db->escape($data['postcode']) : 0) . "',
		city = '" . (isset($data['city']) ? $this->db->escape($data['city']) : '') . "',
		city_id = '" . (isset($data['city_id']) ? (int)$data['city_id'] : 0) . "',
		city_nomenclature = '" . (isset($data['city_nomenclature']) ? $this->db->escape($data['city_nomenclature']) : '') . "',
		is_apt = '" . (isset($data['is_apt']) ? (int)$data['is_apt'] : 0) . "',
		to_office = '" . (isset($data['to_office']) ? (int)$data['to_office'] : 0) . "',
		office_id = '" . (isset($data['office_id']) ? (int)$data['office_id'] : 0) . "',
		quarter = '" . (isset($data['quarter']) ? $this->db->escape($data['quarter']) : '') . "',
		quarter_id = '" . (isset($data['quarter_id']) ? (int)$data['quarter_id'] : 0) . "',
		street = '" . (isset($data['street']) ? $this->db->escape($data['street']) : '') . "',
		street_id = '" . (isset($data['street_id']) ? (int)$data['street_id'] : 0) . "',
		street_no = '" . (isset($data['street_no']) ? $this->db->escape($data['street_no']) : '') . "',
		block_no = '" . (isset($data['block_no']) ? $this->db->escape($data['block_no']) : '') . "',
		entrance_no = '" . (isset($data['entrance_no']) ? $this->db->escape($data['entrance_no']) : '') . "',
		floor_no = '" . (isset($data['floor_no']) ? $this->db->escape($data['floor_no']) : '') . "',
		apartment_no = '" . (isset($data['apartment_no']) ? $this->db->escape($data['apartment_no']) : '') . "',
		note = '" . (isset($data['note']) ? $this->db->escape($data['note']) : '') . "',
		country = '" . (isset($data['country']) ? $this->db->escape($data['country']) : '') . "',
		country_id = '" . (isset($data['country_id']) ? (int)$data['country_id'] : 0) . "',
		country_nomenclature = '" . (isset($data['country_nomenclature']) ? $this->db->escape($data['country_nomenclature']) : '') . "',
		state = '" . (isset($data['state']) ? $this->db->escape($data['state']) : '') . "',
		state_id = '" . (isset($data['state_id']) ? $this->db->escape($data['state_id']) : '') . "',
		required_state = '" . (isset($data['required_state']) ? (int)$data['required_state'] : 0) . "',
		required_postcode = '" . (isset($data['required_postcode']) ? (int)$data['required_postcode'] : 0) . "',
		address_1 = '" . (isset($data['address_1']) ? $this->db->escape($data['address_1']) : '') . "',
		address_2 = '" . (isset($data['address_2']) ? $this->db->escape($data['address_2']) : '') . "',
		abroad = '" . (isset($data['abroad']) ? (int)$data['abroad'] : 0) . "'");
	}

	public function getAddress($address_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "speedy_address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row;
	}

	public function addOrder($order_id, $data) {
		$data['price_gen_method'] = $this->config->get('shipping_speedy_pricing');

		$this->db->query("INSERT INTO " . DB_PREFIX . "speedy_order SET order_id = '" . (int)$order_id . "', data = '" . $this->db->escape(serialize($data)) . "'");
	}

	public function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "speedy_order WHERE order_id = '" . (int)$order_id . "'");

		return $query->row;
	}

	public function editOrder($order_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "speedy_order SET data = '" . $this->db->escape(serialize($data)) . "' WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getSpeedyTableRate($data) {
		$query = $this->db->query("SELECT price_without_vat FROM " . DB_PREFIX . "speedy_table_rate WHERE service_id = '" . (int)$data['service_id'] . "' AND take_from_office = '" . (int)$data['take_from_office'] . "' AND weight >= '" . (float)$data['weight'] . "' AND order_total >= '" . (int)$data['order_total'] . "' AND fixed_time_delivery = '" . (int)$data['fixed_time_delivery'] . "' ORDER BY weight, order_total ASC");

		return $query->row;
	}

	private function getSpeedyQuantityDimention($product_id, $product_quantity) {
		$data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "speedy_product_setting` WHERE product_id = " . (int)$product_id);

		if ($data->num_rows) {
			$sizes = unserialize($data->row['quantity_dimentions']);

			uasort($sizes, array('ModelExtensionShippingSpeedy', 'cmp'));

			foreach ($sizes as $size => $quantity) {
				if ($quantity >= $product_quantity) {
					return array(
						'size'     => $size,
						'sizes'    => $sizes,
					);
				}
			}

			return false;
		} else {
			return false;
		}
	}

	private function speedyHasQuantityDimention($product_id) {
		$data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "speedy_product_setting` WHERE product_id = " . (int)$product_id);

		return $data->num_rows;
	}

	private function getSpeedyWeightDimention($weight, $product_quantity) {
		$sizes = $this->db->query("SELECT " . implode(',', $this->parcel_sizes) . " FROM `" . DB_PREFIX . "speedy_weight_dimension` WHERE WEIGHT >= " . (int)$weight . " ORDER BY WEIGHT DESC LIMIT 1");

		if ($sizes->num_rows) {
			$sizes = $sizes->row;
			uasort($sizes, array('ModelExtensionShippingSpeedy', 'cmp'));

			foreach ($sizes as $size => $quantity) {
				if ($quantity >= $product_quantity) {
					return $size;
				}
			}
		} else {
			return false;
		}
	}

	// sorts the array by quantity without deleting the keys
	private function cmp($a, $b) {
		if ($a == $b) {
			return 0;
		}

		return ($a < $b) ? -1 : 1;
	}

	private function compareSizes($current_size, $compare_size) {
		if (!in_array($current_size, $this->parcel_sizes) || !in_array($compare_size, $this->parcel_sizes)) {
			return false;
		}

		if (array_search($current_size, $this->parcel_sizes) < array_search($compare_size, $this->parcel_sizes)) {
			return $compare_size;
		} else {
			return $current_size;
		}
	}

	private function calculateSize($products, $size_compare) {
		if (!empty($products)) {
			for ($i = 1;$i <= count($this->parcel_sizes); $i++) {
				$parcel_full = 0;

				foreach ($products as $product) {
					if (!empty($product['sizes'])) {
						$parcel_full += $product['quantity'] / $product['sizes'][$size_compare];
					}
				}

				if ($parcel_full > 1) {
					$next_size = array_search($size_compare, $this->parcel_sizes) + 1;

					if (isset($this->parcel_sizes[$next_size])) {
						$size_compare = $this->parcel_sizes[$next_size];
					} else {
						$size_compare = '';
						break;
					}
				} else {
					break;
				}
			}
		}

		return $size_compare;
	}
}