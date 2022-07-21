<?php
class ControllerExtensionSaleSpeedy extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/sale/speedy');

		$this->session->data['is_speedy_bol_recalculated'] = 0;
		$this->session->data['is_speedy_bol_recalculated_orderid'] = 0;

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/sale/speedy');
		$this->load->model('sale/order');
		$this->load->model('localisation/order_status');

		$this->load->library('speedy');

		$this->getList();
	}

	public function create() {
		$this->load->language('extension/sale/speedy');

		$this->session->data['is_speedy_bol_recalculated'] = 0;
		$this->session->data['is_speedy_bol_recalculated_orderid'] = 0;

		$this->document->setTitle($this->language->get('heading_title_details'));

		$this->load->model('extension/sale/speedy');
		$this->load->model('sale/order');

		$this->load->library('speedy');

		$url = '';

		if (isset($this->request->get['filter_bol_id'])) {
			$url .= '&filter_bol_id=' . $this->request->get['filter_bol_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['order_id'])) {
			$speedy_order_info = $this->model_extension_sale_speedy->getOrder($this->request->get['order_id']);
		}

		if (isset($this->request->get['order_id'])) {
			$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);
		}

		if (!empty($speedy_order_info) && (!empty($order_info) && strpos($order_info['shipping_code'], 'speedy.') !== false)) {
			$speedy_order_data = unserialize($speedy_order_info['data']);

			if (!$speedy_order_data) {
				$speedy_order_data = array();
			}

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm() && !$this->request->post['calculate']) {
				$this->request->post['taking_date'] = strtotime($this->request->post['taking_date']);

				if (isset($this->request->post['shipping_method'])) {
					$shipping_method_info = explode('.', $this->request->post['shipping_method']);

					$this->request->post['shipping_method_id'] = $shipping_method_info[1];
					$this->request->post['shipping_method_cost'] = $this->session->data['shipping_method_cost'][$shipping_method_info[1]];
					$this->request->post['shipping_method_title'] = $this->session->data['shipping_method_title'][$shipping_method_info[1]];
				} else {
					$this->request->post['shipping_method_id'] = $speedy_order_data['shipping_method_id'];
					$this->request->post['shipping_method_cost'] = $speedy_order_data['shipping_method_cost'];
					$this->request->post['shipping_method_title'] = $speedy_order_data['shipping_method_title'];
				}

				if ($this->request->post['shipping_method_id'] != 500) {
					unset($this->request->post['parcel_size']);
				} else {
					foreach ($this->request->post['parcels_size'] as $key => $parcel_size) {
						$this->request->post['parcels_size'][$key]['depth'] = '';
						$this->request->post['parcels_size'][$key]['height'] = '';
						$this->request->post['parcels_size'][$key]['width'] = '';
					}
				}

				if (isset($this->request->post['fixed_time_cb'])) {
					$this->request->post['fixed_time'] = $this->request->post['fixed_time_hour'] . $this->request->post['fixed_time_min'];
				} else {
					$this->request->post['fixed_time'] = null;
				}

				$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);

				$speedy_order_data['sender'] = $this->config->get('shipping_speedy_name');

				unset($speedy_order_data['fixed_time_cb']);

				$speedy_data = array_merge($speedy_order_data, $this->request->post);

				$bol = $this->speedy->createBillOfLading($speedy_data, $order_info);

				if (!$this->speedy->getError() && $bol) {
					if (empty($speedy_order_data)) {
						$this->model_extension_sale_speedy->addOrder($this->request->get['order_id'], $speedy_data);
					}

					$this->model_extension_sale_speedy->editOrder($this->request->get['order_id'], array('bol_id' => $bol['bol_id']));

					if ($this->session->data['is_speedy_bol_recalculated']) {
						if (($this->config->get('shipping_speedy_pricing') == 'free') && ($speedy_data['total'] >= (float) $this->config->get('shipping_speedy_free_shipping_total')) &&
								($speedy_data['shipping_method_id'] == $this->config->get('shipping_speedy_free_method_city') || $speedy_data['shipping_method_id'] == $this->config->get('shipping_speedy_free_method_intercity') || in_array($speedy_data['shipping_method_id'], $this->config->get('shipping_speedy_free_method_international')))) {
							$speedy_data['shipping_method_cost'] = 0;
						} elseif ($this->config->get('shipping_speedy_pricing') == 'fixed') {
							$speedy_data['shipping_method_cost'] = $this->config->get('shipping_speedy_fixed_price');
						} elseif ($this->config->get('shipping_speedy_pricing') == 'table_rate') {
							$filter_data = array(
								'service_id' => $this->request->post['shipping_method_id'],
								'take_from_office' => $this->request->post['to_office'],
								'weight' => $this->request->post['weight'],
								'order_total' => $speedy_data['total'],
								'fixed_time_delivery' => isset($this->request->post['fixed_time_cb']) ? $this->request->post['fixed_time_cb'] : 0,
							);

							$speedy_table_rate = $this->model_extension_sale_speedy->getSpeedyTableRate($filter_data);

							if (!empty($speedy_table_rate)) {
								$speedy_data['shipping_method_cost'] = $speedy_table_rate['price_without_vat'];
							}
						} else {
							$speedy_data['shipping_method_cost'] = $bol['total'];

							if ($this->config->get('shipping_speedy_pricing') == 'calculator_fixed') {
								$speedy_data['shipping_method_cost'] += $this->config->get('shipping_speedy_fixed_price');
							}
						}
					}

					if (!empty($this->request->post['to_office']) && !empty($this->request->post['office_id'])) {
						$office = $this->speedy->getOfficeById($this->request->post['office_id'], $this->request->post['city_id']);
						$speedy_data['shipping_address_1'] = $office['id'] . ' ' . $office['name'] . ', ' . $office['address']['fullAddressString'];
					} else {
						$speedy_data['shipping_address_1'] = array();
					}

					$comment = $this->model_extension_sale_speedy->updateOrderInfo($this->request->get['order_id'], $speedy_data);

					$history_data = array(
						'order_status_id' => $this->config->get('shipping_speedy_order_status_id'),
						'notify' => true,
						'comment' => $comment
					);

					// API - Add Order History
					if (!isset($this->session->data['cookie'])) {
						$this->load->model('user/api');

						$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

						if ($api_info) {
							$curl = curl_init();

							// Set SSL if required
							if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
								curl_setopt($curl, CURLOPT_PORT, 443);
							}

							curl_setopt($curl, CURLOPT_HEADER, false);
							curl_setopt($curl, CURLINFO_HEADER_OUT, true);
							curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
							curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
							curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
							curl_setopt($curl, CURLOPT_POST, true);
							curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

							$json = curl_exec($curl);

							if (!$json) {
								$this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
							} else {
								$response = json_decode($json, true);

								if (isset($response['cookie'])) {
									$this->session->data['cookie'] = $response['cookie'];
								}

								curl_close($curl);
							}
						}
					}

					if (isset($this->session->data['cookie'])) {
						$curl = curl_init();

						// Set SSL if required
						if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
							curl_setopt($curl, CURLOPT_PORT, 443);
						}

						curl_setopt($curl, CURLOPT_HEADER, false);
						curl_setopt($curl, CURLINFO_HEADER_OUT, true);
						curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
						curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/order/history&order_id=' . $this->request->get['order_id']);
						curl_setopt($curl, CURLOPT_POST, true);
						curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($history_data));
						curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');


						$response = curl_exec($curl);
					}
					// End API
					$this->model_extension_sale_speedy->changeOrderStatus($this->request->get['order_id'], $this->config->get('shipping_speedy_order_status_id'));

					$this->session->data['success'] = sprintf($this->language->get('text_success_create'), '<a href="' . $this->url->link('extension/sale/speedy/printPDF', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . $url, true) . '" target="_blank">' . $bol['bol_id'] . '</a>');
					$this->session->data['is_speedy_bol_recalculated'] = 0;
					$this->session->data['is_speedy_bol_recalculated_orderid'] = 0;

					$this->response->redirect($this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . $url, true));
				} else {
					if (stripos($this->speedy->getError(), 'Not valid serviceTypeId') !== false) {
						$this->error['warning'] = $this->language->get('error_calculate');
					} else {
						$this->error['warning'] = $this->speedy->getError();
					}
				}
			}

			$data['heading_title'] = $this->language->get('heading_title_details');

			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->error['contents'])) {
				$data['error_contents'] = $this->error['contents'];
			} else {
				$data['error_contents'] = '';
			}

			if (isset($this->error['weight'])) {
				$data['error_weight'] = $this->error['weight'];
			} else {
				$data['error_weight'] = '';
			}

			if (isset($this->error['packing'])) {
				$data['error_packing'] = $this->error['packing'];
			} else {
				$data['error_packing'] = '';
			}

			if (isset($this->error['count'])) {
				$data['error_count'] = $this->error['count'];
			} else {
				$data['error_count'] = '';
			}

			if (isset($this->error['address'])) {
				$data['error_address'] = $this->error['address'];
			} else {
				$data['error_address'] = '';
			}

			if (isset($this->error['office'])) {
				$data['error_office'] = $this->error['office'];
			} else {
				$data['error_office'] = '';
			}

			if (isset($this->error['fixed_time'])) {
				$data['error_fixed_time'] = $this->error['fixed_time'];
			} else {
				$data['error_fixed_time'] = '';
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/deshboard', 'user_token=' . $this->session->data['user_token'], true),
				'separator' => false
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title_details'),
				'href' => $this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . $url, true),
				'separator' => ' :: '
			);

			$data['action'] = $this->url->link('extension/sale/speedy/create', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . $url, true);
			$data['cancel'] = $this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . $url, true);

			$data['user_token'] = $this->session->data['user_token'];

			if (isset($this->request->post['abroad'])) {
				$data['abroad'] = $this->request->post['abroad'];
			} elseif (isset($speedy_order_data['abroad'])) {
				$data['abroad'] = $speedy_order_data['abroad'];
			} else {
				$data['abroad'] = false;
			}

			$lang = ($data['abroad']) ? 'en' : $this->language->get('code');

			if (isset($this->request->post['contents'])) {
				$data['contents'] = $this->request->post['contents'];
			} elseif (isset($speedy_order_data['contents'])) {
				$data['contents'] = $speedy_order_data['contents'];
			} else {
				$data['contents'] = $this->language->get('text_contents') . ' ' . $this->request->get['order_id'];
			}

			if (isset($this->request->post['weight'])) {
				$data['weight'] = $this->request->post['weight'];
			} elseif (isset($speedy_order_data['weight'])) {
				$data['weight'] = $speedy_order_data['weight'];
			} else {
				$data['weight'] = '';
			}

			if (isset($this->request->post['packing'])) {
				$data['packing'] = $this->request->post['packing'];
			} elseif (isset($speedy_order_data['packing'])) {
				$data['packing'] = $speedy_order_data['packing'];
			} else {
				$data['packing'] = $this->config->get('shipping_speedy_packing');
			}

			if (isset($this->request->post['client_id'])) {
				$data['client_id'] = $this->request->post['client_id'];
			} elseif (isset($speedy_order_data['client_id'])) {
				$data['client_id'] = $speedy_order_data['client_id'];
			} else {
				$data['client_id'] = $this->config->get('shipping_speedy_client_id');
			}

			if (isset($this->request->post['option_before_payment'])) {
				$data['option_before_payment'] = $this->request->post['option_before_payment'];
			} elseif (isset($speedy_order_data['option_before_payment'])) {
				$data['option_before_payment'] = $speedy_order_data['option_before_payment'];
			} else {
				$data['option_before_payment'] = $this->config->get('shipping_speedy_option_before_payment');
			}

			if (isset($this->request->post['count'])) {
				$data['count'] = $this->request->post['count'];
			} elseif (isset($speedy_order_data['count'])) {
				$data['count'] = $speedy_order_data['count'];
			} else {
				$data['count'] = 1;
			}

			if (isset($this->request->post['parcels_size'])) {
				$data['parcels_sizes'] = $this->request->post['parcels_size'];
			} elseif (isset($speedy_order_data['parcels_size'])) {
				$data['parcels_sizes'] = $speedy_order_data['parcels_size'];
			} else {
				if (isset($speedy_order_data['width'])) {
					$data['parcels_sizes'][1]['width'] = $speedy_order_data['width'];
				} else {
					$data['parcels_sizes'][1]['width'] = '';
				}
				if (isset($speedy_order_data['height'])) {
					$data['parcels_sizes'][1]['height'] = $speedy_order_data['height'];
				} else {
					$data['parcels_sizes'][1]['height'] = '';
				}
				if (isset($speedy_order_data['depth'])) {
					$data['parcels_sizes'][1]['depth'] = $speedy_order_data['depth'];
				} else {
					$data['parcels_sizes'][1]['depth'] = '';
				}
				if (!isset($data['parcels_sizes'][1]['weight'])) {
					$data['parcels_sizes'][1]['weight'] = '';
				}
			}

			if (isset($this->request->post['shipping_method'])) {
				$shipping_method = explode('.', $this->request->post['shipping_method']);
				$data['shipping_method_id'] = $shipping_method[1];
			} elseif (isset($speedy_order_data['shipping_method_id'])) {
				$data['shipping_method_id'] = $speedy_order_data['shipping_method_id'];
			} else {
				$data['shipping_method_id'] = '';
			}

			if (isset($this->request->post['parcel_size'])) {
				$data['parcel_size'] = $this->request->post['parcel_size'];
			} elseif (isset($speedy_order_data['parcel_size'])) {
				$data['parcel_size'] = $speedy_order_data['parcel_size'];
			} else {
				$data['parcel_size'] = '';
			}

			if (isset($this->request->post['deffered_days'])) {
				$data['deffered_days'] = $this->request->post['deffered_days'];
			} elseif (isset($speedy_order_data['deffered_days'])) {
				$data['deffered_days'] = $speedy_order_data['deffered_days'];
			} else {
				$data['deffered_days'] = 0;
			}

			if (isset($this->request->post['client_note'])) {
				$data['client_note'] = $this->request->post['client_note'];
			} elseif (isset($speedy_order_data['client_note'])) {
				$data['client_note'] = $speedy_order_data['client_note'];
			} else {
				$data['client_note'] = '';
			}

			if (isset($this->request->post['cod'])) {
				$data['cod'] = $this->request->post['cod'];
			} elseif (isset($speedy_order_data['cod'])) {
				$data['cod'] = $speedy_order_data['cod'];
			} else {
				$data['cod'] = true;
			}

			if (isset($this->request->post['total'])) {
				$data['total'] = $this->request->post['total'];
			} elseif (isset($speedy_order_data['total'])) {
				$data['total'] = $speedy_order_data['total'];
			} else {
				$data['total'] = '';
			}

			if (isset($this->request->post['convertion_to_win1251'])) {
				$data['convertion_to_win1251'] = $this->request->post['convertion_to_win1251'];
			} elseif (isset($speedy_order_data['convertion_to_win1251'])) {
				$data['convertion_to_win1251'] = $speedy_order_data['convertion_to_win1251'];
			} else {
				$data['convertion_to_win1251'] = $this->config->get('shipping_speedy_convertion_to_win1251');
			}

			if (isset($this->request->post['additional_copy_for_sender'])) {
				$data['additional_copy_for_sender'] = $this->request->post['additional_copy_for_sender'];
			} elseif (isset($speedy_order_data['additional_copy_for_sender'])) {
				$data['additional_copy_for_sender'] = $speedy_order_data['additional_copy_for_sender'];
			} else {
				$data['additional_copy_for_sender'] = $this->config->get('shipping_speedy_additional_copy_for_sender');
			}

			if (isset($this->request->post['insurance'])) {
				$data['insurance'] = $this->request->post['insurance'];
			} elseif (isset($speedy_order_data['insurance'])) {
				$data['insurance'] = $speedy_order_data['insurance'];
			} else {
				$data['insurance'] = $this->config->get('shipping_speedy_insurance');
			}

			if (isset($this->request->post['fragile'])) {
				$data['fragile'] = $this->request->post['fragile'];
			} elseif (isset($speedy_order_data['fragile'])) {
				$data['fragile'] = $speedy_order_data['fragile'];
			} else {
				$data['fragile'] = $this->config->get('shipping_speedy_fragile');
			}

			if (isset($this->request->post['totalNoShipping'])) {
				$data['totalNoShipping'] = $this->request->post['totalNoShipping'];
			} elseif (isset($speedy_order_data['totalNoShipping'])) {
				$data['totalNoShipping'] = $speedy_order_data['totalNoShipping'];
			} else {
				$data['totalNoShipping'] = '';
			}

			if (isset($this->request->post['to_office'])) {
				$data['to_office'] = $this->request->post['to_office'];
			} elseif (isset($speedy_order_data['to_office'])) {
				$data['to_office'] = $speedy_order_data['to_office'];
			} else {
				$data['to_office'] = 0;
			}

			if (isset($this->request->post['is_apt'])) {
				$data['is_apt'] = $this->request->post['is_apt'];
			} elseif (isset($speedy_order_data['is_apt'])) {
				$data['is_apt'] = $speedy_order_data['is_apt'];
			} else {
				$data['is_apt'] = 0;
			}

			if (isset($this->request->post['office_name'])) {
				$data['office_name'] = $this->request->post['office_name'];
			} elseif (isset($speedy_order_data['office_name'])) {
				$data['office_name'] = $speedy_order_data['office_name'];
			} else {
				$data['office_name'] = '';
			}

			if (isset($this->request->post['postcode'])) {
				$data['postcode'] = $this->request->post['postcode'];
			} elseif (isset($speedy_order_data['postcode'])) {
				$data['postcode'] = $speedy_order_data['postcode'];
			} else {
				$data['postcode'] = '';
			}

			if (isset($this->request->post['city'])) {
				$data['city'] = $this->request->post['city'];
			} elseif (isset($speedy_order_data['city'])) {
				$data['city'] = $speedy_order_data['city'];
			} else {
				$data['city'] = '';
			}

			if (isset($this->request->post['city_id'])) {
				$data['city_id'] = $this->request->post['city_id'];
			} elseif (isset($speedy_order_data['city_id'])) {
				$data['city_id'] = $speedy_order_data['city_id'];
			} else {
				$data['city_id'] = 0;
			}

			if (isset($this->request->post['city_nomenclature'])) {
				$data['city_nomenclature'] = $this->request->post['city_nomenclature'];
			} elseif (isset($speedy_order_data['city_nomenclature'])) {
				$data['city_nomenclature'] = $speedy_order_data['city_nomenclature'];
			} else {
				$data['city_nomenclature'] = '';
			}

			if (isset($this->request->post['quarter'])) {
				$data['quarter'] = $this->request->post['quarter'];
			} elseif (isset($speedy_order_data['quarter'])) {
				$data['quarter'] = $speedy_order_data['quarter'];
			} else {
				$data['quarter'] = '';
			}

			if (isset($this->request->post['quarter_id'])) {
				$data['quarter_id'] = $this->request->post['quarter_id'];
			} elseif (isset($speedy_order_data['quarter_id'])) {
				$data['quarter_id'] = $speedy_order_data['quarter_id'];
			} else {
				$data['quarter_id'] = 0;
			}

			if (isset($this->request->post['street'])) {
				$data['street'] = $this->request->post['street'];
			} elseif (isset($speedy_order_data['street'])) {
				$data['street'] = $speedy_order_data['street'];
			} else {
				$data['street'] = '';
			}

			if (isset($this->request->post['street_id'])) {
				$data['street_id'] = $this->request->post['street_id'];
			} elseif (isset($speedy_order_data['street_id'])) {
				$data['street_id'] = $speedy_order_data['street_id'];
			} else {
				$data['street_id'] = 0;
			}

			if (isset($this->request->post['street_no'])) {
				$data['street_no'] = $this->request->post['street_no'];
			} elseif (isset($speedy_order_data['street_no'])) {
				$data['street_no'] = $speedy_order_data['street_no'];
			} else {
				$data['street_no'] = '';
			}

			if (isset($this->request->post['block_no'])) {
				$data['block_no'] = $this->request->post['block_no'];
			} elseif (isset($speedy_order_data['block_no'])) {
				$data['block_no'] = $speedy_order_data['block_no'];
			} else {
				$data['block_no'] = '';
			}

			if (isset($this->request->post['entrance_no'])) {
				$data['entrance_no'] = $this->request->post['entrance_no'];
			} elseif (isset($speedy_order_data['entrance_no'])) {
				$data['entrance_no'] = $speedy_order_data['entrance_no'];
			} else {
				$data['entrance_no'] = '';
			}

			if (isset($this->request->post['floor_no'])) {
				$data['floor_no'] = $this->request->post['floor_no'];
			} elseif (isset($speedy_order_data['floor_no'])) {
				$data['floor_no'] = $speedy_order_data['floor_no'];
			} else {
				$data['floor_no'] = '';
			}

			if (isset($this->request->post['apartment_no'])) {
				$data['apartment_no'] = $this->request->post['apartment_no'];
			} elseif (isset($speedy_order_data['apartment_no'])) {
				$data['apartment_no'] = $speedy_order_data['apartment_no'];
			} else {
				$data['apartment_no'] = '';
			}

			if (isset($this->request->post['office_id'])) {
				$data['office_id'] = $this->request->post['office_id'];
			} elseif (isset($speedy_order_data['office_id'])) {
				$data['office_id'] = $speedy_order_data['office_id'];
			} else {
				$data['office_id'] = 0;
			}

			if (isset($this->request->post['note'])) {
				$data['note'] = $this->request->post['note'];
			} elseif (isset($speedy_order_data['note'])) {
				$data['note'] = $speedy_order_data['note'];
			} else {
				$data['note'] = '';
			}

			if (isset($this->request->post['fixed_time_cb'])) {
				$data['fixed_time_cb'] = $this->request->post['fixed_time_cb'];
			} elseif (isset($speedy_order_data['fixed_time_cb']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
				$data['fixed_time_cb'] = $speedy_order_data['fixed_time_cb'];
			} else {
				$data['fixed_time_cb'] = false;
			}

			if (isset($this->request->post['fixed_time_hour'])) {
				$data['fixed_time_hour'] = $this->request->post['fixed_time_hour'];
			} elseif (isset($speedy_order_data['fixed_time_hour'])) {
				$data['fixed_time_hour'] = $speedy_order_data['fixed_time_hour'];
			} else {
				$data['fixed_time_hour'] = '';
			}

			if (isset($this->request->post['fixed_time_min'])) {
				$data['fixed_time_min'] = $this->request->post['fixed_time_min'];
			} elseif (isset($speedy_order_data['fixed_time_min'])) {
				$data['fixed_time_min'] = $speedy_order_data['fixed_time_min'];
			} else {
				$data['fixed_time_min'] = '';
			}

			$data['fixed_time'] = $this->config->get('shipping_speedy_fixed_time');

			if (isset($this->request->post['payer_type'])) {
				$data['payer_type'] = $this->request->post['payer_type'];
			} elseif (isset($speedy_order_data['payer_type'])) {
				$data['payer_type'] = $speedy_order_data['payer_type'];
			} elseif (isset($speedy_order_data['shipping_method_cost'])) {
				$data['payer_type'] = $this->speedy->getPayerType($this->request->get['order_id'], $speedy_order_data['shipping_method_cost']);
			} else {
				$data['payer_type'] = 0;
			}

			if (isset($this->request->post['country'])) {
				$data['country'] = $this->request->post['country'];
			} elseif (isset($speedy_order_data['country'])) {
				$data['country'] = $speedy_order_data['country'];
			} else {
				$data['country'] = '';
			}

			if (isset($this->request->post['country_id'])) {
				$data['country_id'] = $this->request->post['country_id'];
			} elseif (isset($speedy_order_data['country_id'])) {
				$data['country_id'] = $speedy_order_data['country_id'];
			} else {
				$data['country_id'] = '';
			}


			if (isset($this->request->post['country_nomenclature'])) {
				$data['country_nomenclature'] = $this->request->post['country_nomenclature'];
			} elseif (isset($speedy_order_data['country_nomenclature'])) {
				$data['country_nomenclature'] = $speedy_order_data['country_nomenclature'];
			} else {
				$data['country_nomenclature'] = '';
			}

			if (isset($this->request->post['country_address_nomenclature'])) {
				$data['country_address_nomenclature'] = $this->request->post['country_address_nomenclature'];
			} elseif (isset($speedy_order_data['country_address_nomenclature'])) {
				$data['country_address_nomenclature'] = $speedy_order_data['country_address_nomenclature'];
			} else {
				$data['country_address_nomenclature'] = '';
			}

			if (isset($this->request->post['required_state'])) {
				$data['required_state'] = $this->request->post['required_state'];
			} elseif (isset($speedy_order_data['required_state'])) {
				$data['required_state'] = $speedy_order_data['required_state'];
			} else {
				$data['required_state'] = '';
			}

			if (isset($this->request->post['required_postcode'])) {
				$data['required_postcode'] = $this->request->post['required_postcode'];
			} elseif (isset($speedy_order_data['required_postcode'])) {
				$data['required_postcode'] = $speedy_order_data['required_postcode'];
			} else {
				$data['required_postcode'] = '';
			}

			if (isset($this->request->post['active_currency_code'])) {
				$data['active_currency_code'] = $this->request->post['active_currency_code'];
			} elseif (isset($speedy_order_data['active_currency_code'])) {
				$data['active_currency_code'] = $speedy_order_data['active_currency_code'];
			} else {
				$data['active_currency_code'] = $this->speedy->baseCurrency;
			}

			if (isset($this->request->post['state'])) {
				$data['state'] = $this->request->post['state'];
			} elseif (isset($speedy_order_data['state'])) {
				$data['state'] = $speedy_order_data['state'];
			} else {
				$data['state'] = '';
			}

			if (isset($this->request->post['state_id'])) {
				$data['state_id'] = $this->request->post['state_id'];
			} elseif (isset($speedy_order_data['state_id'])) {
				$data['state_id'] = $speedy_order_data['state_id'];
			} else {
				$data['state_id'] = '';
			}

			if (isset($this->request->post['address_1'])) {
				$data['address_1'] = $this->request->post['address_1'];
			} elseif (isset($speedy_order_data['address_1'])) {
				$data['address_1'] = $speedy_order_data['address_1'];
			} else {
				$data['address_1'] = '';
			}

			if (isset($this->request->post['address_2'])) {
				$data['address_2'] = $this->request->post['address_2'];
			} elseif (isset($speedy_order_data['address_2'])) {
				$data['address_2'] = $speedy_order_data['address_2'];
			} else {
				$data['address_2'] = '';
			}

			$data['offices'] = array();

			if ($data['city_id'] && $data['country_id']) {
				$data['offices'] = $this->speedy->getOffices(null, $data['city_id'], $lang, $data['country_id']);

				if ($this->speedy->getError()) {
					$data['error_office'] = $this->speedy->getError();
				}
			}

			$data['days'] = array(0, 1, 2);
			$data['taking_date'] = date('d-m-Y', ($this->config->get('shipping_speedy_taking_date') ? strtotime('+' . (int) $this->config->get('shipping_speedy_taking_date') . ' day', mktime(9, 0, 0)) : time()));

			$data['options_before_payment'] = array(
				'no_option' => $this->language->get('text_no'),
				'test'      => $this->language->get('text_test_before_payment'),
				'open'      => $this->language->get('text_open_before_payment'),
			);

			$data['ignore_obp'] = $this->config->get('shipping_speedy_ignore_obp');

			$data['parcel_sizes'] = array(
				'XS' => 'XS',
				'S'  => 'S',
				'M'  => 'M',
				'L'  => 'L',
				'XL' => 'XL'
			);

			$data['clients'] = $this->speedy->getListContractClients();

			foreach (range(10, 17) as $hour) {
				$data['hours_10_17'][] = str_pad($hour, 2, '0', STR_PAD_LEFT);
			}

			$min_fixed_time_mins = ($data['fixed_time_hour'] == 10 ? 30 : 0);
			$max_fixed_time_mins = ($data['fixed_time_hour'] == 17 ? 30 : 59);

			foreach (range($min_fixed_time_mins, $max_fixed_time_mins) as $minute) {
				$data['mins_10_17'][] = str_pad($minute, 2, '0', STR_PAD_LEFT);
			}

			if (!empty($this->session->data['loading_methods_fixed_time'])) {
				$data['loading_methods_fixed_time'] = $this->session->data['loading_methods_fixed_time'];

				$data['speedy_methods_fixed_time'] = implode('", "', array_keys($this->session->data['loading_methods_fixed_time']));
			}

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm() && $this->request->post['calculate']) {
				$data['quote'] = $this->getQuote();
				$this->session->data['is_speedy_bol_recalculated'] = 1;

				if (isset($data['quote']['speedy_error'])) {
					$data['error_warning'] = $data['quote']['speedy_error'];

					$data['quote'] = array();
				}
			} else {
				$data['quote'] = array();
			}

			if (!$data['abroad']) {
				$data['cod_status'] = true;
			} else {
				$data['cod_status'] = false;
			}

			$country_filter = array();

			if (!empty($data['country_id'])) {
				$country_filter['country_id'] = $data['country_id'];
			} else {
				$country_filter['country_id'] = Speedy::BULGARIA;
			}

			$countries = $this->speedy->getCountries($country_filter, $lang);

			if (!$this->speedy->getError()) {
				foreach ($countries as $country) {
					$data['country'] = $country['name'];
					$data['country_id'] = $country['id'];
					$data['country_nomenclature'] = $country['nomenclature'];
					$data['country_address_nomenclature'] = $country['address_nomenclature'];
					$data['required_state'] = $country['required_state'];
					$data['required_postcode'] = $country['required_postcode'];

					$data['active_currency_code'] = $country['active_currency_code'];

					if (!$country['active_currency_code']) {
						$data['cod_status'] = false;
						$data['cod'] = false;
					} else {
						$data['cod_status'] = true;
					}
				}
			} else {
					$data['error_address'] = $this->speedy->getError();
			}

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('extension/sale/speedy_form', $data));
		} else {
			$this->load->language('error/not_found');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
				'separator' => false
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('error/not_found', 'user_token=' . $this->session->data['user_token'], true),
				'separator' => ' :: '
			);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function printPDF() {
		$this->load->language('extension/sale/speedy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/sale/speedy');

		$this->load->library('speedy');

		$url = '';

		if (isset($this->request->get['filter_bol_id'])) {
			$url .= '&filter_bol_id=' . $this->request->get['filter_bol_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['order_id'])) {
			$speedy_order_info = $this->model_extension_sale_speedy->getOrder($this->request->get['order_id']);
			$speedy_order_data = unserialize($speedy_order_info['data']);
		}

		if (!empty($speedy_order_info) && !empty($speedy_order_info['bol_id']) && isset($speedy_order_data['additional_copy_for_sender'])) {
			$pdf = $this->speedy->createPDF($speedy_order_info['bol_id'], $speedy_order_data['additional_copy_for_sender']);

			if (!$this->speedy->getError() && $pdf) {
				header('Content-Type: application/pdf');
				echo $pdf;
				exit;
			} else {
				$this->session->data['warning'] = $this->speedy->getError();
			}
		} else {
			$this->session->data['warning'] = $this->language->get('error_exists');
		}

		$this->response->redirect($this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function printReturnVoucher() {
		$this->load->language('extension/sale/speedy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/sale/speedy');

		$this->load->library('speedy');

		$url = '';

		if (isset($this->request->get['filter_bol_id'])) {
			$url .= '&filter_bol_id=' . $this->request->get['filter_bol_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['order_id'])) {
			$speedy_order_info = $this->model_extension_sale_speedy->getOrder($this->request->get['order_id']);
		}

		if (!empty($speedy_order_info) && !empty($speedy_order_info['bol_id'])) {
			$pdf = $this->speedy->createReturnVoucher($speedy_order_info['bol_id']);

			if (!$this->speedy->getError() && $pdf) {
				header('Content-Type: application/pdf');
				echo $pdf;
				exit;
			} else {
				$this->session->data['warning'] = $this->speedy->getError();
			}
		} else {
			$this->session->data['warning'] = $this->language->get('error_exists');
		}

		$this->response->redirect($this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function courier() {
		$this->load->language('extension/sale/speedy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/sale/speedy');

		$this->load->library('speedy');

		$url = '';

		if (isset($this->request->get['filter_bol_id'])) {
			$url .= '&filter_bol_id=' . $this->request->get['filter_bol_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$bol_ids = array();

		if (isset($this->request->get['order_id']) || isset($this->request->post['selected'])) {
			if (isset($this->request->get['order_id'])) {
				$speedy_order_info = $this->model_extension_sale_speedy->getOrder($this->request->get['order_id']);

				if ($speedy_order_info['bol_id']) {
					$bol_ids[] = $speedy_order_info['bol_id'];
				}
			} else {
				foreach ($this->request->post['selected'] as $order_id) {
					$speedy_order_info = $this->model_extension_sale_speedy->getOrder($order_id);

					if ($speedy_order_info['bol_id']) {
						$bol_ids[] = $speedy_order_info['bol_id'];
					}
				}
			}
		}

		if ($bol_ids) {
			$results = $this->speedy->requestCourier($bol_ids);

			if (!$this->speedy->getError()) {
				$error = array();

				foreach ($results as $result) {
					if (empty($result['error'])) {
						$this->model_extension_sale_speedy->editOrderCourier($result['id'], true);
					} else {
						$error[] = $result['id'] . ' - ' . implode(', ', $result['error']);
					}
				}

				if ($error) {
					$this->session->data['warning'] = implode('<br />', $error);
				} else {
					$this->session->data['success'] = $this->language->get('text_success_courier');
				}
			} else {
				$this->session->data['warning'] = $this->speedy->getError();
			}
		} else {
			$this->session->data['warning'] = $this->language->get('error_exists');
		}

		$this->response->redirect($this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function cancel() {
		$this->load->language('extension/sale/speedy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/sale/speedy');

		$this->load->library('speedy');

		$url = '';

		if (isset($this->request->get['filter_bol_id'])) {
			$url .= '&filter_bol_id=' . $this->request->get['filter_bol_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['order_id'])) {
			$speedy_order_info = $this->model_extension_sale_speedy->getOrder($this->request->get['order_id']);
		}

		if (!empty($speedy_order_info) && !empty($speedy_order_info['bol_id']) && $this->validateDelete()) {
			$cancelled = $this->speedy->cancelBol($speedy_order_info['bol_id']);

			if (!$this->speedy->getError() && $cancelled) {
				$this->model_extension_sale_speedy->deleteOrder($this->request->get['order_id']);

				$this->session->data['success'] = $this->language->get('text_success_cancel');
			} else {
				$this->session->data['warning'] = $this->speedy->getError();
			}
		} else {
			if (isset($this->error['warning'])) {
				$this->session->data['warning'] = $this->error['warning'];
			} else {
				$this->session->data['warning'] = $this->language->get('error_exists');
			}
		}

		$this->response->redirect($this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	protected function getList() {
		if (isset($this->request->get['filter_bol_id'])) {
			$filter_bol_id = $this->request->get['filter_bol_id'];
		} else {
			$filter_bol_id = null;
		}

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_date_created'])) {
			$filter_date_created = $this->request->get['filter_date_created'];
		} else {
			$filter_date_created = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'so.date_created';
		}

		if (isset($this->request->get['order'])) {
			$sort_order = $this->request->get['order'];
		} else {
			$sort_order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_bol_id'])) {
			$url .= '&filter_bol_id=' . $this->request->get['filter_bol_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'separator' => ' :: '
		);

		if (!$this->config->get('shipping_speedy_from_office')) {
			$data['courier'] = $this->url->link('extension/sale/speedy/courier', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['courier'] = false;
		}

		$data['speedy_orders'] = array();

		$filter_data = array(
			'filter_bol_id' => $filter_bol_id,
			'filter_order_id' => $filter_order_id,
			'filter_customer' => $filter_customer,
			'filter_date_created' => $filter_date_created,
			'sort' => $sort,
			'order' => $sort_order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$speedy_order_total = $this->model_extension_sale_speedy->getTotalOrders($filter_data);

		$results = $this->model_extension_sale_speedy->getOrders($filter_data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_cancel'),
				'href' => $this->url->link('extension/sale/speedy/cancel', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true)
			);

			if ($result['courier']) {
				$action[] = array(
					'text' => $this->language->get('text_courier_sent')
				);
			} elseif (!$this->config->get('shipping_speedy_from_office')) {
				$action[] = array(
					'text' => $this->language->get('text_request_courier'),
					'href' => $this->url->link('extension/sale/speedy/courier', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true)
				);
			}

			$action[] = array(
				'text' => $this->language->get('text_track'),
				'href' => 'http://www.speedy.bg/begin.php?shipmentNumber=' . $result['bol_id'] . '&lang=' . (strtolower($this->config->get('config_admin_language')) == 'bg' ? 'bg' : 'en'),
				'target' => '_blank'
			);

			if ($this->speedy->checkReturnVoucherRequested($result['bol_id'])) {
				$action[] = array(
					'text' => $this->language->get('text_print_return_voucher'),
					'href' => $this->url->link('extension/sale/speedy/printReturnVoucher', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true),
					'target' => '_blank'
				);
			}

			$deliveryInfo = $this->model_extension_sale_speedy->getDeliveryInfo($result['bol_id'], $result['order_id']);
			$deliveryDate = '';
			$deliveryConsignee = '';
			$deliveryNote = '';

			if (!empty($deliveryInfo)) {
				if (!empty($deliveryInfo['dateTime'])) {
					$deliveryDate = date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($deliveryInfo['dateTime']));
				}

				if (!empty($deliveryInfo['consignee'])) {
					$deliveryConsignee = $deliveryInfo['consignee'];
				}

				if (!empty($deliveryInfo['comment'])) {
					$deliveryNote = $deliveryInfo['comment'];
				}
			}

			$result_data = unserialize($result['data']);

			$address = '';

			if (!empty($result_data['to_office']) && !empty($result_data['office_id'])) {
				$address .= $this->language->get('text_pickup_from_office') . '<br/> ';

				if (!empty($result_data['office_name'])) {
					$address .= $result_data['office_name'];
				} else {
					$office = $this->speedy->getOfficeById($result_data['office_id'], $result_data['city_id']);

					if ($office) {
						$address .= $office['address']['fullAddressString'];
					}
				}
			} else {
				$address .= $this->language->get('text_delivery_to_address') . '<br/> ';
				$address .= !empty($result_data['country'])      ? $result_data['country']      . ','      : '';
				$address .= !empty($result_data['state'])        ? $result_data['state']        . ','      : '';
				$address .= !empty($result_data['city'])         ? $result_data['city']         . ','      : '';
				$address .= !empty($result_data['postcode'])     ? $result_data['postcode']     . ','      : '';
				$address .= !empty($result_data['quarter'])      ? $result_data['quarter']      . ','      : '';
				$address .= !empty($result_data['street'])       ? $result_data['street']       . ','      : '';
				$address .= !empty($result_data['street_no'])    ? $result_data['street_no']    . ', '     : '';
				$address .= !empty($result_data['block_no'])     ? $result_data['block_no']     . ', '     : '';
				$address .= !empty($result_data['entrance_no'])  ? $result_data['entrance_no']  . ', '     : '';
				$address .= !empty($result_data['floor_no'])     ? $result_data['floor_no']     . ', '     : '';
				$address .= !empty($result_data['apartment_no']) ? $result_data['apartment_no'] . ', '     : '';
				$address .= !empty($result_data['note'])         ? $result_data['note']         . '<br/> ' : '';
			}

			$order = $this->model_sale_order->getOrder($result['order_id']);
			$status = $this->model_localisation_order_status->getOrderStatus($order['order_status_id']);

			$data['speedy_orders'][] = array(
				'bol_id' => $result['bol_id'],
				'bol_href' => $this->url->link('extension/sale/speedy/printPDF', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true),
				'bol_target' => '_blank',
				'order_id' => $result['order_id'],
				'order_href' => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true),
				'customer' => !empty($result_data['sender']) ? $result_data['sender'] : $this->config->get('shipping_speedy_name'),
				'recipient' => $order['shipping_firstname'] . ' ' . $order['shipping_lastname'],
				'status' => $status['name'],
				'date_created' => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($result['date_created'])),
				'selected' => isset($this->request->post['selected']) && in_array($result['order_id'], $this->request->post['selected']),
				'action' => $action,
				'address' => $address,
				'delivery_date' => $deliveryDate,
				'delivery_consignee' => $deliveryConsignee,
				'delivery_note' => $deliveryNote
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];

			unset($this->session->data['warning']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_bol_id'])) {
			$url .= '&filter_bol_id=' . $this->request->get['filter_bol_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (!empty($this->request->get['order']) && $this->request->get['order'] == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_speedy_order'] = $this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . '&sort=so.bol_id' . $url, true);
		$data['sort_order'] = $this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . '&sort=so.order_id' . $url, true);
		$data['sort_customer'] = $this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_date_created'] = $this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . '&sort=so.date_created' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_bol_id'])) {
			$url .= '&filter_bol_id=' . $this->request->get['filter_bol_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_created'])) {
			$url .= '&filter_date_created=' . $this->request->get['filter_date_created'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $speedy_order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('extension/sale/speedy', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($speedy_order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($speedy_order_total - $this->config->get('config_limit_admin'))) ? $speedy_order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $speedy_order_total, ceil($speedy_order_total / $this->config->get('config_limit_admin')));

		$data['filter_bol_id'] = $filter_bol_id;
		$data['filter_order_id'] = $filter_order_id;
		$data['filter_customer'] = $filter_customer;
		$data['filter_date_created'] = $filter_date_created;

		$data['sort'] = $sort;
		$data['order'] = $sort_order;


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/sale/speedy_list', $data));
	}

	public function validateBillOfLading() {
		$this->load->language('extension/sale/speedy');

		$this->load->library('speedy');

		$json = array();

		// checkAPT office
		if (!$this->request->post['abroad'] && $this->request->post['speedy_shipping_to_office'] && $this->request->post['speedy_option_before_payment'] != 'no_option' && !empty($this->request->post['speedy_office_id']) && !empty($this->request->post['speedy_city_id'])) {
			$office = $this->speedy->getOfficeById($this->request->post['speedy_office_id'], $this->request->post['speedy_city_id']);

			if (!empty($office) && $office['type'] == 'APT') { // 3 for APT office
				$json['error'] = true;
				$json['errors']['APT_office'] = $this->language->get('text_APT_office');
			}
		}

		// checkDate
		if (isset($this->request->post['shipping_method_id'])) {
			$shipping_method_id = $this->request->post['shipping_method_id'];
		} else {
			$shipping_method_id = '';
		}

		$taking_date = ($this->config->get('shipping_speedy_taking_date') ? strtotime('+' . (int) $this->config->get('shipping_speedy_taking_date') . ' day', mktime(9, 0, 0)) : time());
		$first_available_date = strtotime($this->speedy->getAllowedDaysForTaking(array('shipping_method_id' => $shipping_method_id, 'taking_date' => $taking_date)));

		if (!$this->speedy->getError() && $first_available_date) {
			if (date('d-m-Y', $first_available_date) != date('d-m-Y', $taking_date)) {
				$json['error'] = true;
				$json['errors']['warning'] = sprintf($this->language->get('text_confirm_date'), date($this->language->get('date_format_short'), $first_available_date));
				$json['taking_date'] = date('d-m-Y', $first_available_date);
			}
		} else {
			$json['error'] = true;
			$json['errors']['warning'] = $this->speedy->getError();
		}

		// check BackDocumentsRequest and BackReceiptRequest
		if (!empty($shipping_method_id)) {
			$service = $this->speedy->getServiceById($shipping_method_id);

			if (!empty($service)) {
				if ($service->getAllowanceBackDocumentsRequest()->getValue() == 'BANNED' && $service->getAllowanceBackReceiptRequest()->getValue() == 'BANNED') {
					$json['error'] = true;
					$json['errors']['document_receipt'] = $this->language->get('text_document_receipt');
				} elseif ($service->getAllowanceBackDocumentsRequest()->getValue() == 'BANNED') {
					$json['error'] = true;
					$json['errors']['document'] = $this->language->get('text_document');
				} elseif ($service->getAllowanceBackReceiptRequest()->getValue() == 'BANNED') {
					$json['error'] = true;
					$json['errors']['receipt'] = $this->language->get('text_receipt');
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	protected function validateForm() {
			if (!$this->user->hasPermission('modify', 'extension/sale/speedy')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}

			if ((utf8_strlen($this->request->post['contents']) < 1) || (utf8_strlen($this->request->post['contents']) > 100)) {
				$this->error['contents'] = $this->language->get('error_contents');
			}

			if ($this->request->post['weight'] <= 0) {
				$this->error['weight'] = $this->language->get('error_weight');
			}

			if ((utf8_strlen($this->request->post['packing']) < 1) || (utf8_strlen($this->request->post['packing']) > 50)) {
				$this->error['packing'] = $this->language->get('error_packing');
			}

			if ($this->request->post['count'] <= 0) {
				$this->error['count'] = $this->language->get('error_count');
			}

			if (empty($this->request->post['abroad']) || !empty($this->request->post['to_office'])) {
				if ($this->request->post['city'] && $this->request->post['city_id'] &&
						(!$this->request->post['to_office'] && (($this->request->post['quarter'] && ($this->request->post['quarter_id'] && $this->request->post['city_nomenclature'] == 'FULL' || $this->request->post['city_nomenclature'] != 'FULL') && ($this->request->post['block_no'] || $this->request->post['street_no'])) ||
						($this->request->post['street'] && ($this->request->post['street_id'] && $this->request->post['city_nomenclature'] == 'FULL' || $this->request->post['city_nomenclature'] != 'FULL') && ($this->request->post['block_no'] || $this->request->post['street_no'])) || $this->request->post['note']) || ($this->request->post['to_office'] && $this->request->post['office_id']))) {
				// do nothing
			} else {
				if ($this->request->post['to_office']) {
					$this->error['office'] = $this->language->get('error_office');
				} else {
					$this->error['address'] = $this->language->get('error_address');
				}
			}
		} else {
			$validAddress = $this->speedy->validateAddress($this->request->post);

			if ($validAddress !== true) {
				$this->error['warning'] = $validAddress;
			}

			if (isset($this->request->post['cod']) && $this->request->post['cod'] && isset($this->request->post['active_currency_code'])) {
				if (!$this->currency->has($this->request->post['active_currency_code'])) {
					$this->error['warning'] = sprintf($this->language->get('error_currency'), $this->request->post['active_currency_code']);
				}
			}
		}

		if (isset($this->request->post['fixed_time_cb'])) {
			if (!$this->request->post['fixed_time_hour'] || $this->request->post['fixed_time_hour'] < 10 || $this->request->post['fixed_time_hour'] > 17 ||
					!$this->request->post['fixed_time_min'] || $this->request->post['fixed_time_min'] < '00' || $this->request->post['fixed_time_min'] > 59 ||
					($this->request->post['fixed_time_hour'] == 10 && $this->request->post['fixed_time_min'] < 30) ||
					($this->request->post['fixed_time_hour'] == 17 && $this->request->post['fixed_time_min'] > 30)) {
				$this->error['fixed_time'] = $this->language->get('error_fixed_time');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/sale/speedy')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function getCities() {
		$this->load->library('speedy');

		if (isset($this->request->get['term'])) {
			$name = $this->request->get['term'];
		} else {
			$name = '';
		}

		if (isset($this->request->get['country_id'])) {
			$country_id = $this->request->get['country_id'];
		} else {
			$country_id = '';
		}

		if (isset($this->request->get['abroad']) && $this->request->get['abroad']) {
			$lang = 'en';
		} else {
			$lang = $this->language->get('code');
		}

		$data = $this->speedy->getCities($name, null, $country_id, $lang);

		if ($this->speedy->getError()) {
			$data = array('error' => $this->speedy->getError());
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getQuarters() {
		$this->load->library('speedy');

		if (isset($this->request->get['term'])) {
			$name = $this->request->get['term'];
		} else {
			$name = '';
		}

		if (isset($this->request->get['city_id'])) {
			$city_id = $this->request->get['city_id'];
		} else {
			$city_id = '';
		}

		if (isset($this->request->get['abroad']) && $this->request->get['abroad']) {
			$lang = 'en';
		} else {
			$lang = $this->language->get('code');
		}

		if ($city_id) {
			$data = $this->speedy->getQuarters($name, $city_id, $lang);

			if ($this->speedy->getError()) {
				$data = array('error' => $this->speedy->getError());
			}
		} else {
			$this->load->language('extension/sale/speedy');

			$data = array('error' => $this->language->get('error_city'));
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getStreets() {
		$this->load->library('speedy');

		if (isset($this->request->get['term'])) {
			$name = $this->request->get['term'];
		} else {
			$name = '';
		}

		if (isset($this->request->get['city_id'])) {
			$city_id = $this->request->get['city_id'];
		} else {
			$city_id = '';
		}

		if (isset($this->request->get['abroad']) && $this->request->get['abroad']) {
			$lang = 'en';
		} else {
			$lang = $this->language->get('code');
		}

		if ($city_id) {
			$data = $this->speedy->getStreets($name, $city_id, $lang);

			if ($this->speedy->getError()) {
				$data = array('error' => $this->speedy->getError());
			}
		} else {
			$this->load->language('extension/sale/speedy');

			$data = array('error' => $this->language->get('error_city'));
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getBlocks() {
		$this->load->library('speedy');

		if (isset($this->request->get['term'])) {
			$name = $this->request->get['term'];
		} else {
			$name = '';
		}

		if (isset($this->request->get['city_id'])) {
			$city_id = $this->request->get['city_id'];
		} else {
			$city_id = '';
		}

		if (isset($this->request->get['abroad']) && $this->request->get['abroad']) {
			$lang = 'en';
		} else {
			$lang = $this->language->get('code');
		}

		if ($city_id) {
			$data = $this->speedy->getBlocks($name, $city_id, $lang);

			if ($this->speedy->getError()) {
				$data = array('error' => $this->speedy->getError());
			}
		} else {
			$this->load->language('extension/sale/speedy');

			$data = array('error' => $this->language->get('error_city'));
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getOffices() {
		$this->load->library('speedy');

		if (isset($this->request->get['term'])) {
			$name = $this->request->get['term'];
		} else {
			$name = '';
		}

		if (isset($this->request->get['city_id'])) {
			$city_id = $this->request->get['city_id'];
		} else {
			$city_id = '';
		}

		if (isset($this->request->get['country_id'])) {
			$country_id = $this->request->get['country_id'];
		} else {
			$country_id = '';
		}

		if (isset($this->request->get['abroad']) && $this->request->get['abroad']) {
			$lang = 'en';
		} else {
			$lang = $this->language->get('code');
		}

		if ($city_id && $country_id) {
			$data = $this->speedy->getOffices($name, $city_id, $lang, $country_id);

			if ($this->speedy->getError()) {
				$data = array('error' => $this->speedy->getError());
			}
		} else {
			$this->load->language('extension/sale/speedy');

			$data = array('error' => $this->language->get('error_city'));
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getCountries() {
		$this->load->library('speedy');

		if (isset($this->request->get['term'])) {
			$name = $this->request->get['term'];
		} else {
			$name = '';
		}

		if (isset($this->request->get['abroad']) && $this->request->get['abroad']) {
			$lang = 'en';
		} else {
			$lang = $this->language->get('code');
		}

		$data = $this->speedy->getCountries($name, $lang);

		if ($this->speedy->getError()) {
			$data = array('error' => $this->speedy->getError());
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getStates() {
		$this->load->library('speedy');

		if (isset($this->request->get['term'])) {
			$name = $this->request->get['term'];
		} else {
			$name = '';
		}

		if (isset($this->request->get['country_id'])) {
			$country_id = $this->request->get['country_id'];
		} else {
			$country_id = '';
		}

		$data = $this->speedy->getStates($country_id, $name);

		if ($this->speedy->getError()) {
			$data = array('error' => $this->speedy->getError());
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getQuote() {
		$this->load->language('extension/shipping/speedy');

		$this->load->model('extension/sale/speedy');
		$this->load->model('sale/order');

		$order = $this->model_sale_order->getOrder($this->request->get['order_id']);

		$this->load->library('speedy');

		$quote_data = array();

		$quote_data['speedy'] = array(
			'code' => 'speedy.speedy',
			'title' => $this->language->get('text_description'),
			'cost' => 0.00,
			'tax_class_id' => 0,
			'text' => ''
		);

		$method_data = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ($this->config->get('shipping_speedy_documents') && (float) $this->request->post['weight'] > 0.25) {
				$data['weight'] = 0.25;
			}

			$data['taking_date'] = ($this->config->get('shipping_speedy_taking_date') ? strtotime('+' . (int) $this->config->get('shipping_speedy_taking_date') . ' day', mktime(9, 0, 0)) : time());

			$data['loading'] = true;
			$data['order_currency_code'] = $order['currency_code'];

			if (!empty($this->request->post['shipping_method'])) {
				$methood = explode('.', $this->request->post['shipping_method']);
			}

			if (!empty($methood[1]) && $methood[1] != 500) {
				unset($this->request->post['parcel_size']);
			} else {
				foreach ($this->request->post['parcels_size'] as $key => $parcel_size) {
					$this->request->post['parcels_size'][$key]['depth'] = '';
					$this->request->post['parcels_size'][$key]['height'] = '';
					$this->request->post['parcels_size'][$key]['width'] = '';
				}
			}

			$methods = $this->speedy->calculate(array_merge($data, $this->request->post));

			if (isset($this->request->post['total'])) {
				$data['total'] = (float) $this->request->post['total'];
			}

			$services = $this->speedy->getServices($this->language->get('code'));
			$methods_count = 0;

			if (!$this->speedy->getError()) {
				foreach ($methods as $method) {
					if (empty($method['error'])) {
						if (($this->config->get('shipping_speedy_pricing') == 'free') && ($data['total'] >= (float) $this->config->get('shipping_speedy_free_shipping_total')) && ($method['serviceId'] == $this->config->get('shipping_speedy_free_method_city') || $method['serviceId'] == $this->config->get('shipping_speedy_free_method_intercity') || in_array($method['serviceId'], $this->config->get('shipping_speedy_free_method_international')))) {
							$method_total = 0;
						} elseif ($this->config->get('shipping_speedy_pricing') == 'fixed') {
							$method_total = $this->config->get('shipping_speedy_fixed_price');
						} elseif ($this->config->get('shipping_speedy_pricing') == 'table_rate') {
							$filter_data = array(
								'service_id' => $method['serviceId'],
								'take_from_office' => $this->request->post['to_office'],
								'weight' => $this->request->post['weight'],
								'order_total' => $data['total'],
								'fixed_time_delivery' => isset($this->request->post['fixed_time_cb']) ? $this->request->post['fixed_time_cb'] : 0,
							);

							$speedy_table_rate = $this->model_extension_sale_speedy->getSpeedyTableRate($filter_data);

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
						$method_total = $this->currency->convert($method_total, $this->speedy->baseCurrency, $order['currency_code']);

						$this->session->data['shipping_method_cost'][$method['serviceId']] = $method_total;
						$this->session->data['shipping_method_title'][$method['serviceId']] = $services[$method['serviceId']];

						$quote_data[$method['serviceId']] = array(
							'code' => 'speedy.' . $method['serviceId'],
							'title' => $this->language->get('text_description') . ' - ' . $services[$method['serviceId']],
							'cost' => $method_total,
							'tax_class_id' => 0,
							'text' => $this->currency->format($method_total, $order['currency_code'], 1)
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
		}

		if (isset($method_data['speedy_error'])) {
			$method_data['quote']['speedy']['text'] = '';
		}

		return $method_data;
	}
}