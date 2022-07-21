<?php
class ControllerExtensionShippingSpeedy extends Controller {
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->language('checkout/checkout');
		$this->load->language('extension/shipping/speedy');

		$this->load->library('speedy');
	}

	public function index() {
		$this->load->model('extension/shipping/speedy');

		$results = array();

		if (!empty($this->request->post['validate'])) {
			$this->validate();
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && empty($this->error)) {
			if ($this->customer->isLogged() && isset($this->session->data['shipping_address']['address_id'])) {
				$this->model_extension_shipping_speedy->addAddress($this->session->data['shipping_address']['address_id'], $this->request->post);
			}

			if (!isset($this->request->post['to_office']) && isset($this->session->data['speedy']['to_office'])) {
				$this->request->post['to_office'] = $this->session->data['speedy']['to_office'];
			}

			$this->session->data['speedy'] = $this->request->post;

			$results['submit'] = true;

			$this->response->setOutput(json_encode($results));
			return;
		}

		if ((!$this->cart->hasProducts() && (!isset($this->session->data['vouchers']) || !$this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$results['redirect'] = $this->url->link('checkout/cart');

			$this->response->setOutput(json_encode($results));
		}

		if (!$this->customer->isLogged() && !isset($this->session->data['guest'])) {
			$results['redirect'] = $this->url->link('checkout/checkout', '', true);

			$this->response->setOutput(json_encode($results));
		}

		$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
		$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 6);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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

		if (isset($this->error['cod'])) {
			$data['error_cod'] = $this->error['cod'];
		} else {
			$data['error_cod'] = '';
		}

		$data['action'] = $this->url->link('extension/shipping/speedy', '', true);

		if ($this->session->data['shipping_address']['iso_code_2'] == 'BG') {
			$data['abroad'] = false;
		} else {
			$data['abroad'] = true;
		}

		$lang = ($data['abroad']) ? 'en' : $this->language->get('code');

		if ($this->customer->isLogged() && isset($this->session->data['shipping_address']['address_id'])) {
			$shipping_address = $this->model_extension_shipping_speedy->getAddress($this->session->data['shipping_address']['address_id']);

			$this->session->data['speedy_customer']['shipping_address_id'] = $this->session->data['shipping_address']['address_id'];
		} elseif (isset($this->session->data['guest'])) {
			if (isset($this->session->data['speedy']) && isset($this->session->data['speedy_guest']) &&
				$this->session->data['speedy_guest']['city'] == $this->session->data['shipping_address']['city'] &&
				$this->session->data['speedy_guest']['postcode'] == $this->session->data['shipping_address']['postcode'] &&
				$this->session->data['speedy_guest']['iso_code_2'] == $this->session->data['shipping_address']['iso_code_2']
			) {
				$shipping_address = $this->session->data['speedy'];
			} else {
				unset($this->session->data['speedy']);
			}

			$this->session->data['speedy_guest']['city'] = $this->session->data['shipping_address']['city'];
			$this->session->data['speedy_guest']['postcode'] = $this->session->data['shipping_address']['postcode'];
			$this->session->data['speedy_guest']['iso_code_2'] = $this->session->data['shipping_address']['iso_code_2'];
		}

		if (isset($this->session->data['speedy'])) {
			$data['speedy_precalculate'] = false;
		} else {
			$data['speedy_precalculate'] = true;
		}

		if (!isset($this->session->data['speedy_guest_address'])) {
			$this->session->data['speedy_guest_address'] = $this->session->data['shipping_address'];
		} elseif (md5(serialize($this->session->data['speedy_guest_address'])) != md5(serialize($this->session->data['shipping_address']))) {
			$this->session->data['speedy_guest_address'] = $this->session->data['shipping_address'];
			unset($shipping_address);
		}

		if (!$this->config->get('payment_speedy_cod_status')) {
			$data['cod'] = false;
		} elseif (isset($this->request->post['cod'])) {
			$data['cod'] = $this->request->post['cod'];
		} elseif (isset($this->session->data['speedy']['cod'])) {
			$data['cod'] = $this->session->data['speedy']['cod'];
		} else {
			$data['cod'] = null;
		}

		if (isset($this->request->post['to_office'])) {
			$data['to_office'] = $this->request->post['to_office'];
		} elseif (isset($shipping_address['to_office'])) {
			$data['to_office'] = $shipping_address['to_office'];
		}

		if (isset($this->request->post['postcode'])) {
			$data['postcode'] = $this->request->post['postcode'];
		} elseif (isset($shipping_address['postcode'])) {
			$data['postcode'] = $shipping_address['postcode'];
		} else {
			$data['postcode'] = '';
		}

		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} elseif (isset($shipping_address['city'])) {
			$data['city'] = $shipping_address['city'];
		} else {
			$data['city'] = '';
		}

		if (isset($this->request->post['city_id'])) {
			$data['city_id'] = $this->request->post['city_id'];
		} elseif (isset($shipping_address['city_id'])) {
			$data['city_id'] = $shipping_address['city_id'];
		} else {
			$data['city_id'] = 0;
		}

		if (isset($this->request->post['city_nomenclature'])) {
			$data['city_nomenclature'] = $this->request->post['city_nomenclature'];
		} elseif (isset($shipping_address['city_nomenclature'])) {
			$data['city_nomenclature'] = $shipping_address['city_nomenclature'];
		} else {
			$data['city_nomenclature'] = '';
		}

		if (isset($this->request->post['quarter'])) {
			$data['quarter'] = $this->request->post['quarter'];
		} elseif (isset($shipping_address['quarter'])) {
			$data['quarter'] = $shipping_address['quarter'];
		} else {
			$data['quarter'] = '';
		}

		if (isset($this->request->post['quarter_id'])) {
			$data['quarter_id'] = $this->request->post['quarter_id'];
		} elseif (isset($shipping_address['quarter_id'])) {
			$data['quarter_id'] = $shipping_address['quarter_id'];
		} else {
			$data['quarter_id'] = 0;
		}

		if (isset($this->request->post['street'])) {
			$data['street'] = $this->request->post['street'];
		} elseif (isset($shipping_address['street'])) {
			$data['street'] = $shipping_address['street'];
		} else {
			$data['street'] = '';
		}

		if (isset($this->request->post['street_id'])) {
			$data['street_id'] = $this->request->post['street_id'];
		} elseif (isset($shipping_address['street_id'])) {
			$data['street_id'] = $shipping_address['street_id'];
		} else {
			$data['street_id'] = 0;
		}

		if (isset($this->request->post['street_no'])) {
			$data['street_no'] = $this->request->post['street_no'];
		} elseif (isset($shipping_address['street_no'])) {
			$data['street_no'] = $shipping_address['street_no'];
		} else {
			$data['street_no'] = '';
		}

		if (isset($this->request->post['block_no'])) {
			$data['block_no'] = $this->request->post['block_no'];
		} elseif (isset($shipping_address['block_no'])) {
			$data['block_no'] = $shipping_address['block_no'];
		} else {
			$data['block_no'] = '';
		}

		if (isset($this->request->post['entrance_no'])) {
			$data['entrance_no'] = $this->request->post['entrance_no'];
		} elseif (isset($shipping_address['entrance_no'])) {
			$data['entrance_no'] = $shipping_address['entrance_no'];
		} else {
			$data['entrance_no'] = '';
		}

		if (isset($this->request->post['floor_no'])) {
			$data['floor_no'] = $this->request->post['floor_no'];
		} elseif (isset($shipping_address['floor_no'])) {
			$data['floor_no'] = $shipping_address['floor_no'];
		} else {
			$data['floor_no'] = '';
		}

		if (isset($this->request->post['apartment_no'])) {
			$data['apartment_no'] = $this->request->post['apartment_no'];
		} elseif (isset($shipping_address['apartment_no'])) {
			$data['apartment_no'] = $shipping_address['apartment_no'];
		} else {
			$data['apartment_no'] = '';
		}

		if (isset($this->request->post['office_id'])) {
			$data['office_id'] = $this->request->post['office_id'];
		} elseif (isset($shipping_address['office_id'])) {
			$data['office_id'] = $shipping_address['office_id'];
		} else {
			$data['office_id'] = 0;
		}

		if (isset($this->request->post['is_apt'])) {
			$data['is_apt'] = $this->request->post['is_apt'];
		} elseif (isset($shipping_address['is_apt'])) {
			$data['is_apt'] = $shipping_address['is_apt'];
		}

		if (isset($this->request->post['office_name'])) {
			$data['office_name'] = $this->request->post['office_name'];
		} elseif (isset($shipping_address['office_name'])) {
			$data['office_name'] = $shipping_address['office_name'];
		} else {
			$data['office_name'] = '';
		}

		if (isset($this->request->post['to_office'])) {
			$data['to_office'] = $this->request->post['to_office'];
		} elseif (isset($shipping_address['to_office'])) {
			$data['to_office'] = $shipping_address['to_office'];
		}

		if (isset($this->request->post['note'])) {
			$data['note'] = $this->request->post['note'];
		} elseif (isset($shipping_address['note'])) {
			$data['note'] = $shipping_address['note'];
		} else {
			$data['note'] = '';
		}


		if (isset($this->request->post['country'])) {
			$data['country'] = $this->request->post['country'];
		} elseif (isset($shipping_address['country'])) {
			$data['country'] = $shipping_address['country'];
		} else {
			$data['country'] = '';
		}

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} elseif (isset($shipping_address['country_id'])) {
			$data['country_id'] = $shipping_address['country_id'];
		} else {
			$data['country_id'] = '';
		}


		if (isset($this->request->post['country_nomenclature'])) {
			$data['country_nomenclature'] = $this->request->post['country_nomenclature'];
		} elseif (isset($shipping_address['country_nomenclature'])) {
			$data['country_nomenclature'] = $shipping_address['country_nomenclature'];
		} else {
			$data['country_nomenclature'] = '';
		}

		if (isset($this->request->post['country_address_nomenclature'])) {
			$data['country_address_nomenclature'] = $this->request->post['country_address_nomenclature'];
		} elseif (isset($shipping_address['country_address_nomenclature'])) {
			$data['country_address_nomenclature'] = $shipping_address['country_address_nomenclature'];
		} else {
			$data['country_address_nomenclature'] = '';
		}

		if (isset($this->request->post['required_state'])) {
			$data['required_state'] = $this->request->post['required_state'];
		} elseif (isset($shipping_address['required_state'])) {
			$data['required_state'] = $shipping_address['required_state'];
		} else {
			$data['required_state'] = '';
		}

		if (isset($this->request->post['required_postcode'])) {
			$data['required_postcode'] = $this->request->post['required_postcode'];
		} elseif (isset($shipping_address['required_postcode'])) {
			$data['required_postcode'] = $shipping_address['required_postcode'];
		} else {
			$data['required_postcode'] = '';
		}

		if (isset($this->request->post['active_currency_code'])) {
			$data['active_currency_code'] = $this->request->post['active_currency_code'];
		} elseif (isset($this->session->data['speedy']['active_currency_code'])) {
			$data['active_currency_code'] = $this->session->data['speedy']['active_currency_code'];
		} else {
			$data['active_currency_code'] = $this->session->data['currency'];
		}

		if (isset($this->request->post['state'])) {
			$data['state'] = $this->request->post['state'];
		} elseif (isset($shipping_address['state'])) {
			$data['state'] = $shipping_address['state'];
		} else {
			$data['state'] = '';
		}

		if (isset($this->request->post['state_id'])) {
			$data['state_id'] = $this->request->post['state_id'];
		} elseif (isset($shipping_address['state_id'])) {
			$data['state_id'] = $shipping_address['state_id'];
		} else {
			$data['state_id'] = '';
		}

		if (isset($this->request->post['address_1'])) {
			$data['address_1'] = $this->request->post['address_1'];
		} elseif (isset($shipping_address['address_1'])) {
			$data['address_1'] = $shipping_address['address_1'];
		} elseif (isset($this->session->data['shipping_address'])) {
			$data['address_1'] = $this->session->data['shipping_address']['address_1'];
		} else {
			$data['address_1'] = '';
		}

		if (isset($this->request->post['address_2'])) {
			$data['address_2'] = $this->request->post['address_2'];
		} elseif (isset($shipping_address['address_2'])) {
			$data['address_2'] = $shipping_address['address_2'];
		} elseif (isset($this->session->data['shipping_address'])) {
			$data['address_2'] = $this->session->data['shipping_address']['address_2'];
		} else {
			$data['address_2'] = '';
		}

		if (isset($this->request->post['fixed_time_cb'])) {
			$data['fixed_time_cb'] = $this->request->post['fixed_time_cb'];
		} elseif (isset($this->session->data['speedy']['fixed_time_cb'])) {
			$data['fixed_time_cb'] = $this->session->data['speedy']['fixed_time_cb'];
		} else {
			$data['fixed_time_cb'] = false;
		}

		if (!isset($this->session->data['speedy']['methods_fixed_time']) || (isset($this->request->post['shipping_method']) && !isset($this->session->data['speedy']['methods_fixed_time'][trim($this->request->post['shipping_method'], 'speedy.')]))) {
			$data['fixed_time_cb'] = false;
			$data['fixed_time_cb_enable'] = false;
		} else {
			$data['fixed_time_cb_enable'] = true;
		}

		if (isset($this->request->post['fixed_time_hour'])) {
			$data['fixed_time_hour'] = $this->request->post['fixed_time_hour'];
		} elseif (isset($this->session->data['speedy']['fixed_time_hour'])) {
			$data['fixed_time_hour'] = $this->session->data['speedy']['fixed_time_hour'];
		} else {
			$data['fixed_time_hour'] = 17;
		}

		if (isset($this->request->post['fixed_time_min'])) {
			$data['fixed_time_min'] = $this->request->post['fixed_time_min'];
		} elseif (isset($this->session->data['speedy']['fixed_time_min'])) {
			$data['fixed_time_min'] = $this->session->data['speedy']['fixed_time_min'];
		} else {
			$data['fixed_time_min'] = '';
		}

		$data['fixed_time'] = $this->config->get('shipping_speedy_fixed_time');
		$data['option_before_payment'] = $this->config->get('shipping_speedy_option_before_payment');
		$data['ignore_obp'] = $this->config->get('shipping_speedy_ignore_obp');

		$data['hours'] = array();

		foreach (range(10, 17) as $hour) {
			$data['hours'][] = str_pad($hour, 2, '0', STR_PAD_LEFT);
		}

		$data['minutes'] = array();

		$min_fixed_time_mins = ($data['fixed_time_hour'] == 10 ? 30 : 0);
		$max_fixed_time_mins = ($data['fixed_time_hour'] == 17 ? 30 : 59);

		foreach (range($min_fixed_time_mins, $max_fixed_time_mins) as $minute) {
			$data['minutes'][] = str_pad($minute, 2, '0', STR_PAD_LEFT);
		}

		$data['offices'] = array();

		if ($this->customer->isLogged() && isset($this->session->data['shipping_address']['address_id'])) {
			$this->load->model('account/address');

			$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address']['address_id']);
		} elseif (isset($this->session->data['guest'])) {
			$shipping_address = $this->session->data['shipping_address'];
		}

		$data['cod_status'] = $this->config->get('payment_speedy_cod_status');
		$data['session_speedy'] = $this->session->data['shipping_methods']['speedy'];

		$data['country_disabled'] = true;
		$data['state_disabled'] = true;

		$country_filter = array();

		if (!empty($shipping_address['iso_code_2'])) {
			$country_filter['iso_code_2'] = $shipping_address['iso_code_2'];
		} elseif (!empty($data['country_id'])) {
			$country_filter['country_id'] = $data['country_id'];
		} else {
			$country_filter = $shipping_address['country'];
		}

		$countryCache = $this->cache->get('speedy.countries.' . md5(json_encode($country_filter)));

		if ($countryCache) {
			$countries = $countryCache;
		} else {
			$countries = $this->speedy->getCountries($country_filter, $lang);
			$this->cache->set('speedy.countries.' . md5(json_encode($country_filter)), $countries);
		}

		if (!$this->speedy->getError()) {
			if (count($countries) == 1) {
				$country = $countries[0];

				$data['country'] = $country['name'];
				$data['country_id'] = $country['id'];
				$data['country_nomenclature'] = $country['nomenclature'];
				$data['country_address_nomenclature'] = $country['address_nomenclature'];
				$data['required_state'] = $country['required_state'];
				$data['required_postcode'] = $country['required_postcode'];

				if (!$country['active_currency_code']) {
					$data['cod_status'] = false;
				} else {
					$data['active_currency_code'] = $country['active_currency_code'];
				}

				if ($data['abroad']) {
					$stateCache = $this->cache->get('speedy.states.' . md5($country['id'] . $shipping_address['zone_code']));

					if ($stateCache) {
						$states = $stateCache;
					} else {
						$states = $this->speedy->getStates($country['id'], $shipping_address['zone_code']);
						$this->cache->set('speedy.states.' . md5($country['id'] . $shipping_address['zone_code']), $states);
					}

					if (!$this->speedy->getError()) {
						if (count($states) == 1) {
							$state = $states[0];
							$data['state'] = $state['name'];
							$data['state_id'] = $state['id'];
						} else {
							foreach ($states as $state) {
								if ($shipping_address['zone_code'] == $state['code']) {
									$data['state'] = $state['name'];
									$data['state_id'] = $state['id'];
								}
							}
							$data['state_disabled'] = false;
						}
					} else {
						$data['error_address'] = $this->speedy->getError();
					}
				}
			} else {
				$data['country_disabled'] = false;
			}
		} else {
			$data['error_address'] = $this->speedy->getError();
		}

		if (!$data['city_id']) {
			$cities = $this->speedy->getCities($shipping_address['city'], $shipping_address['postcode'], $data['country_id'], $lang);

			if (!$this->speedy->getError()) {
				if (count($cities) == 1) {
					$data['postcode'] = ($cities[0]['postcode'] ? $cities[0]['postcode'] : $shipping_address['postcode']);
					$data['city'] = $cities[0]['value'];
					$data['city_id'] = $cities[0]['id'];
					$data['city_nomenclature'] = $cities[0]['nomenclature'];
				} elseif ($data['country_nomenclature'] != 'FULL') {
					if (isset($this->request->post['city']) && isset($this->request->post['postcode'])) {
						$data['city'] = $this->request->post['city'];
						$data['postcode'] = $this->request->post['postcode'];
					} elseif (isset($this->session->data['speedy'])) {
						$data['city'] = $this->session->data['speedy']['city'];
						$data['postcode'] = $this->session->data['speedy']['postcode'];
					} else {
						$data['city'] = $shipping_address['city'];
						$data['postcode'] = $shipping_address['postcode'];
					}
				}
			} else {
				$data['error_address'] = $this->speedy->getError();
			}
		}

		if ($data['city_id'] && !empty($countries[0]['id'])) {
			$officeCache = $this->cache->get('speedy.offices.' . md5($data['city_id'] . $lang . $countries[0]['id']));

			if ($officeCache) {
				$data['offices'] = $officeCache;
			} else {
				$data['offices'] = $this->speedy->getOffices(null, $data['city_id'], $lang, $countries[0]['id']);
				$this->cache->set('speedy.offices.' . md5($data['city_id'] . $lang . $countries[0]['id']), $data['offices']);
			}

			if (empty($data['offices'])) {
				$data['to_office'] = 0;
			}

			if (isset($data['offices']) && !isset($data['to_office'])) {
				$data['to_office'] = 1;

				if (!isset($data['is_apt'])) {
					foreach ($data['offices'] as $office) {
						if (!empty($office['is_apt'])) {
							$data['is_apt'] = 1;
							break;
						}
					}
				}
			}

			if ($this->speedy->getError()) {
				$data['error_office'] = $this->speedy->getError();
			}
		}

		if (!isset($this->session->data['speedy']['to_office'])) {
			$this->session->data['speedy']['to_office'] = empty($data['to_office']) ? 0 : 1;
		}

		$results['html'] = $this->load->view('extension/shipping/speedy', $data);

		$this->response->setOutput(json_encode($results));
	}

	protected function validate() {
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

		if (!isset($this->request->post['cod']) && $this->request->post['cod_status']) {
			$this->error['cod'] = $this->language->get('error_cod');
		}

		return !$this->error;
	}

	public function compareAddresses() {
		$data = array();

		if ($this->request->post['postcode'] != $this->session->data['shipping_address']['postcode']) {
			$data['error'] = $this->language->get('error_different_address');
		} else {
			if (isset($this->request->post['country_id'])) {
				$country_id = $this->request->post['country_id'];
			} else {
				$country_id = '';
			}

			$lang = ($this->request->post['abroad']) ? 'en' : $this->language->get('code');
			$cities = $this->speedy->getCities($this->session->data['shipping_address']['city'], $this->session->data['shipping_address']['postcode'], $country_id, $lang);

			if (!$this->speedy->getError()) {
				if (empty($cities)) {
					if ($this->request->post['city'] != $this->session->data['shipping_address']['city']) {
						$data['error'] = $this->language->get('error_different_address');
					}
				} elseif (isset($this->request->post['city_id'])) {
					if ($this->request->post['city_id'] != $cities[0]['id']) {
						$data['error'] = $this->language->get('error_different_address');
					}
				}
			}
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getCities() {
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

		if (isset($this->session->data['speedy']['abroad']) && $this->session->data['speedy']['abroad']) {
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

		if (isset($this->session->data['speedy']['abroad']) && $this->session->data['speedy']['abroad']) {
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
			$data = array('error' => $this->language->get('error_city'));
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getStreets() {
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

		if (isset($this->session->data['speedy']['abroad']) && $this->session->data['speedy']['abroad']) {
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
			$data = array('error' => $this->language->get('error_city'));
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getBlocks() {
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

		if (isset($this->session->data['speedy']['abroad']) && $this->session->data['speedy']['abroad']) {
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
			$data = array('error' => $this->language->get('error_city'));
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getOffices() {
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

		if (isset($this->session->data['speedy']['abroad']) && $this->session->data['speedy']['abroad']) {
			$lang = 'en';
		} else {
			$lang = $this->language->get('code');
		}

		if ($city_id && $country_id) {
			$data = $this->cache->get('speedy.offices.' . md5($city_id . $lang . $country_id));

			if ((empty($data) && empty($name)) || empty($name)) {
				$data = $this->speedy->getOffices('', $city_id, $lang, $country_id);
				$this->cache->set('speedy.offices.' . md5($city_id . $lang . $country_id), $data);
			} else {
				$data = $this->speedy->getOffices($name, $city_id, $lang, $country_id);
			}

			if ($this->speedy->getError()) {
				$data = array('error' => $this->speedy->getError());
			}
		} else {
			$data = array('error' => $this->language->get('error_city'));
		}

		$this->response->setOutput(json_encode($data));
	}

	public function getCountries() {
		if (isset($this->request->get['term'])) {
			$name = $this->request->get['term'];
		} else {
			$name = '';
		}

		if (isset($this->session->data['speedy']['abroad']) && $this->session->data['speedy']['abroad']) {
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
}