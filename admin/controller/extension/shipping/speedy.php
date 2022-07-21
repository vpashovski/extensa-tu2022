<?php
class ControllerExtensionShippingSpeedy extends Controller {
	private $error = array();
	const MIN_PHP_VERSION_REQUIRED = '5.6';
	const MIN_MySQL_VERSION_REQUIRED = '5.0';

	public function index() {
		$this->load->language('extension/shipping/speedy');
		$this->load->language('extension/catalog/speedy');

		$this->load->model('setting/setting');
		$this->load->model('extension/shipping/speedy');

		if (!$this->config->get('shipping_speedy_updated_v410')) { // operation code
			$this->updateV410();
		}

		if (!$this->config->get('shipping_speedy_updated_v408')) { // is_api column
			$this->updateV408();
		}

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$new_user = $this->request->post['shipping_speedy_username'] != $this->config->get('shipping_speedy_username');
			$control = $this->request->post['shipping_speedy_password'] == $this->language->get('hide_pass');

			if (!$new_user && ($control || $this->config->get('shipping_speedy_password'))) {
				if ($this->request->post['shipping_speedy_password'] != $this->config->get('shipping_speedy_password')) {
					if ($control) {
						$this->request->post['shipping_speedy_password'] = $this->config->get('shipping_speedy_password');
					}
				}
			}
		}

		$this->load->library('speedy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('./view/javascript/speedy/css/speedy.css');

		if ($this->speedy->getError() && $this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->error['warning'] = $this->speedy->getError();
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->request->post['shipping_speedy_updated_v410'] = true; //operation code
			$this->request->post['shipping_speedy_updated_v408'] = true; //is_api column

			if (!empty($this->request->post['weight_dimensions'])) {
				$weight_dimensions = $this->request->post['weight_dimensions'];
			} else {
				$weight_dimensions = array();
			}

			$this->model_extension_shipping_speedy->addWeightDimentions($weight_dimensions);

			unset($this->request->post['weight_dimensions']);

			if (isset($this->request->post['shipping_speedy_default_weight'])) {
				$this->request->post['shipping_speedy_default_weight'] = str_replace(',', '.', $this->request->post['shipping_speedy_default_weight']);
			}

			if (!$this->speedy->isAvailableMoneyTransfer()) {
				$this->request->post['shipping_speedy_money_transfer'] = 0;
			}

			$this->model_setting_setting->editSetting('shipping_speedy', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['client_id'])) {
			$data['error_client_id'] = $this->error['client_id'];
		} else {
			$data['error_client_id'] = '';
		}

		if (isset($this->error['default_weight'])) {
			$data['error_default_weight'] = $this->error['default_weight'];
		} else {
			$data['error_default_weight'] = '';
		}

		if (isset($this->error['allowed_methods'])) {
			$data['error_allowed_methods'] = $this->error['allowed_methods'];
		} else {
			$data['error_allowed_methods'] = '';
		}

		if (isset($this->error['free_method_international'])) {
			$data['error_free_method_international'] = $this->error['free_method_international'];
		} else {
			$data['error_free_method_international'] = '';
		}

		if (isset($this->error['taking_date'])) {
			$data['error_taking_date'] = $this->error['taking_date'];
		} else {
			$data['error_taking_date'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_shipping'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/speedy', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => ' :: '
		);

		$data['action'] = $this->url->link('extension/shipping/speedy', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		// Check requirments
		$php_version = preg_replace('/^([0-9\.]+).*/', '$1', phpversion());
		$mysql_version = $this->db->query("SELECT VERSION() as mysql_version");
		$mysql_version = preg_replace('/^([0-9\.]+).*/', '$1', $mysql_version->row['mysql_version']);

		$php_version_fulfilled = version_compare($php_version, self::MIN_PHP_VERSION_REQUIRED, '>=');
		$mysql_version_fulfilled = version_compare($mysql_version, self::MIN_MySQL_VERSION_REQUIRED, '>=');
		$soap_fulfilled = class_exists('SOAPClient');

		$data['requirements'] = array(
			array(
				'name' => $this->language->get('text_php_version'),
				'required' => self::MIN_PHP_VERSION_REQUIRED,
				'current' => $php_version,
				'is_success' => $php_version_fulfilled,
				'result' => $php_version_fulfilled ? $this->language->get('text_fulfilled') : $this->language->get('text_not_fulfilled')
			),
			array(
				'name' => $this->language->get('text_mysql_version'),
				'required' => self::MIN_MySQL_VERSION_REQUIRED,
				'current' => $mysql_version,
				'is_success' => $mysql_version_fulfilled,
				'result' => $mysql_version_fulfilled ? $this->language->get('text_fulfilled') : $this->language->get('text_not_fulfilled')
			),
			array(
				'name' => $this->language->get('text_soap_extension'),
				'required' => '-',
				'current' => $soap_fulfilled ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'is_success' => $soap_fulfilled,
				'result' => $soap_fulfilled ? $this->language->get('text_fulfilled') : $this->language->get('text_not_fulfilled')
			),
		);

		if (!empty($this->request->post['shipping_speedy_server_address'])) {
			$data['shipping_speedy_server_address'] = $this->request->post['shipping_speedy_server_address'];
		} elseif ($this->config->get('shipping_speedy_server_address')) {
			$data['shipping_speedy_server_address'] = $this->config->get('shipping_speedy_server_address');
		} else {
			$data['shipping_speedy_server_address'] = 'https://www.speedy.bg/eps/main01.wsdl';
		}

		if (isset($this->request->post['shipping_speedy_username'])) {
			$data['shipping_speedy_username'] = $this->request->post['shipping_speedy_username'];
		} else {
			$data['shipping_speedy_username'] = $this->config->get('shipping_speedy_username');
		}

		if (isset($this->request->post['shipping_speedy_password'])) {
			$data['shipping_speedy_password'] = $this->request->post['shipping_speedy_password'];
		} else {
			if ($this->config->get('shipping_speedy_password')) {
				$data['shipping_speedy_password'] = $this->language->get('hide_pass');
			} else {
				$data['shipping_speedy_password'] = '';
			}
		}

		if (isset($this->request->post['shipping_speedy_name'])) {
			$data['shipping_speedy_name'] = $this->request->post['shipping_speedy_name'];
		} else {
			$data['shipping_speedy_name'] = $this->config->get('shipping_speedy_name');
		}

		if (isset($this->request->post['shipping_speedy_telephone'])) {
			$data['shipping_speedy_telephone'] = $this->request->post['shipping_speedy_telephone'];
		} else {
			$data['shipping_speedy_telephone'] = $this->config->get('shipping_speedy_telephone');
		}

		if (isset($this->request->post['shipping_speedy_workingtime_end_hour'])) {
			$data['shipping_speedy_workingtime_end_hour'] = $this->request->post['shipping_speedy_workingtime_end_hour'];
		} else {
			$data['shipping_speedy_workingtime_end_hour'] = $this->config->get('shipping_speedy_workingtime_end_hour');
		}

		if (isset($this->request->post['shipping_speedy_workingtime_end_min'])) {
			$data['shipping_speedy_workingtime_end_min'] = $this->request->post['shipping_speedy_workingtime_end_min'];
		} else {
			$data['shipping_speedy_workingtime_end_min'] = $this->config->get('shipping_speedy_workingtime_end_min');
		}

		if (isset($this->request->post['shipping_speedy_allowed_methods'])) {
			$data['shipping_speedy_allowed_methods'] = $this->request->post['shipping_speedy_allowed_methods'];
		} elseif ($this->config->get('shipping_speedy_allowed_methods') && $this->request->server['REQUEST_METHOD'] != 'POST') {
			$data['shipping_speedy_allowed_methods'] = $this->config->get('shipping_speedy_allowed_methods');
		} else {
			$data['shipping_speedy_allowed_methods'] = array();
		}

		if (isset($this->request->post['shipping_speedy_client_id'])) {
			$data['shipping_speedy_client_id'] = $this->request->post['shipping_speedy_client_id'];
		} else {
			$data['shipping_speedy_client_id'] = $this->config->get('shipping_speedy_client_id');
		}

		if (isset($this->request->post['shipping_speedy_pricing'])) {
			$data['shipping_speedy_pricing'] = $this->request->post['shipping_speedy_pricing'];
		} else {
			$data['shipping_speedy_pricing'] = $this->config->get('shipping_speedy_pricing');
		}

		if (isset($this->request->post['shipping_speedy_option_before_payment'])) {
			$data['shipping_speedy_option_before_payment'] = $this->request->post['shipping_speedy_option_before_payment'];
		} else {
			$data['shipping_speedy_option_before_payment'] = $this->config->get('shipping_speedy_option_before_payment');
		}

		if (isset($this->request->post['shipping_speedy_return_payer_type'])) {
			$data['shipping_speedy_return_payer_type'] = $this->request->post['shipping_speedy_return_payer_type'];
		} else {
			$data['shipping_speedy_return_payer_type'] = $this->config->get('shipping_speedy_return_payer_type');
		}

		if (isset($this->request->post['shipping_speedy_return_package_city_service_id'])) {
			$data['shipping_speedy_return_package_city_service_id'] = $this->request->post['shipping_speedy_return_package_city_service_id'];
		} else {
			$data['shipping_speedy_return_package_city_service_id'] = $this->config->get('shipping_speedy_return_package_city_service_id');
		}

		if (isset($this->request->post['shipping_speedy_return_package_intercity_service_id'])) {
			$data['shipping_speedy_return_package_intercity_service_id'] = $this->request->post['shipping_speedy_return_package_intercity_service_id'];
		} else {
			$data['shipping_speedy_return_package_intercity_service_id'] = $this->config->get('shipping_speedy_return_package_intercity_service_id');
		}

		if (isset($this->request->post['shipping_speedy_ignore_obp'])) {
			$data['shipping_speedy_ignore_obp'] = $this->request->post['shipping_speedy_ignore_obp'];
		} else {
			$data['shipping_speedy_ignore_obp'] = $this->config->get('shipping_speedy_ignore_obp');
		}

		if (isset($this->request->post['shipping_speedy_return_voucher'])) {
			$data['shipping_speedy_return_voucher'] = $this->request->post['shipping_speedy_return_voucher'];
		} else {
			$data['shipping_speedy_return_voucher'] = $this->config->get('shipping_speedy_return_voucher');
		}

		if (isset($this->request->post['shipping_speedy_return_voucher_city_service_id'])) {
			$data['shipping_speedy_return_voucher_city_service_id'] = $this->request->post['shipping_speedy_return_voucher_city_service_id'];
		} else {
			$data['shipping_speedy_return_voucher_city_service_id'] = $this->config->get('shipping_speedy_return_voucher_city_service_id');
		}

		if (isset($this->request->post['shipping_speedy_return_voucher_intercity_service_id'])) {
			$data['shipping_speedy_return_voucher_intercity_service_id'] = $this->request->post['shipping_speedy_return_voucher_intercity_service_id'];
		} else {
			$data['shipping_speedy_return_voucher_intercity_service_id'] = $this->config->get('shipping_speedy_return_voucher_intercity_service_id');
		}

		if (isset($this->request->post['shipping_speedy_return_voucher_payer_type'])) {
			$data['shipping_speedy_return_voucher_payer_type'] = $this->request->post['shipping_speedy_return_voucher_payer_type'];
		} else {
			$data['shipping_speedy_return_voucher_payer_type'] = $this->config->get('shipping_speedy_return_voucher_payer_type');
		}

		if (isset($this->request->post['shipping_speedy_fixed_price'])) {
			$data['shipping_speedy_fixed_price'] = $this->request->post['shipping_speedy_fixed_price'];
		} else {
			$data['shipping_speedy_fixed_price'] = $this->config->get('shipping_speedy_fixed_price');
		}

		if (isset($this->request->post['shipping_speedy_free_shipping_total'])) {
			$data['shipping_speedy_free_shipping_total'] = $this->request->post['shipping_speedy_free_shipping_total'];
		} else {
			$data['shipping_speedy_free_shipping_total'] = $this->config->get('shipping_speedy_free_shipping_total');
		}

		if (isset($this->request->post['shipping_speedy_free_method_city'])) {
			$data['shipping_speedy_free_method_city'] = $this->request->post['shipping_speedy_free_method_city'];
		} else {
			$data['shipping_speedy_free_method_city'] = $this->config->get('shipping_speedy_free_method_city');
		}

		if (isset($this->request->post['shipping_speedy_free_method_intercity'])) {
			$data['shipping_speedy_free_method_intercity'] = $this->request->post['shipping_speedy_free_method_intercity'];
		} else {
			$data['shipping_speedy_free_method_intercity'] = $this->config->get('shipping_speedy_free_method_intercity');
		}

		if (isset($this->request->post['shipping_speedy_free_method_international'])) {
			$data['shipping_speedy_free_method_international'] = $this->request->post['shipping_speedy_free_method_international'];
		} elseif ($this->config->get('shipping_speedy_free_method_international') && $this->request->server['REQUEST_METHOD'] != 'POST') {
			$data['shipping_speedy_free_method_international'] = $this->config->get('shipping_speedy_free_method_international');
		} else {
			$data['shipping_speedy_free_method_international'] = array();
		}

		if (isset($this->request->post['shipping_speedy_back_documents'])) {
			$data['shipping_speedy_back_documents'] = $this->request->post['shipping_speedy_back_documents'];
		} else {
			$data['shipping_speedy_back_documents'] = $this->config->get('shipping_speedy_back_documents');
		}

		if (isset($this->request->post['shipping_speedy_back_receipt'])) {
			$data['shipping_speedy_back_receipt'] = $this->request->post['shipping_speedy_back_receipt'];
		} else {
			$data['shipping_speedy_back_receipt'] = $this->config->get('shipping_speedy_back_receipt');
		}

		if (isset($this->request->post['shipping_speedy_default_weight'])) {
			$data['shipping_speedy_default_weight'] = $this->request->post['shipping_speedy_default_weight'];
		} else {
			$data['shipping_speedy_default_weight'] = $this->config->get('shipping_speedy_default_weight');
		}

		if (isset($this->request->post['shipping_speedy_packing'])) {
			$data['shipping_speedy_packing'] = $this->request->post['shipping_speedy_packing'];
		} else {
			$data['shipping_speedy_packing'] = $this->config->get('shipping_speedy_packing');
		}

		if (isset($this->request->post['shipping_speedy_label_printer'])) {
			$data['shipping_speedy_label_printer'] = $this->request->post['shipping_speedy_label_printer'];
		} else {
			$data['shipping_speedy_label_printer'] = $this->config->get('shipping_speedy_label_printer');
		}

		if (isset($this->request->post['shipping_speedy_additional_copy_for_sender'])) {
			$data['shipping_speedy_additional_copy_for_sender'] = $this->request->post['shipping_speedy_additional_copy_for_sender'];
		} else {
			$data['shipping_speedy_additional_copy_for_sender'] = $this->config->get('shipping_speedy_additional_copy_for_sender');
		}

		if (isset($this->request->post['shipping_speedy_insurance'])) {
			$data['shipping_speedy_insurance'] = $this->request->post['shipping_speedy_insurance'];
		} else {
			$data['shipping_speedy_insurance'] = $this->config->get('shipping_speedy_insurance');
		}

		if (isset($this->request->post['shipping_speedy_fragile'])) {
			$data['shipping_speedy_fragile'] = $this->request->post['shipping_speedy_fragile'];
		} else {
			$data['shipping_speedy_fragile'] = $this->config->get('shipping_speedy_fragile');
		}

		if (isset($this->request->post['shipping_speedy_from_office'])) {
			$data['shipping_speedy_from_office'] = $this->request->post['shipping_speedy_from_office'];
		} else {
			$data['shipping_speedy_from_office'] = $this->config->get('shipping_speedy_from_office');
		}

		if (isset($this->request->post['shipping_speedy_office_id'])) {
			$data['shipping_speedy_office_id'] = $this->request->post['shipping_speedy_office_id'];
		} else {
			$data['shipping_speedy_office_id'] = $this->config->get('shipping_speedy_office_id');
		}

		if (isset($this->request->post['shipping_speedy_documents'])) {
			$data['shipping_speedy_documents'] = $this->request->post['shipping_speedy_documents'];
		} else {
			$data['shipping_speedy_documents'] = $this->config->get('shipping_speedy_documents');
		}

		if (isset($this->request->post['shipping_speedy_fixed_time'])) {
			$data['shipping_speedy_fixed_time'] = $this->request->post['shipping_speedy_fixed_time'];
		} else {
			$data['shipping_speedy_fixed_time'] = $this->config->get('shipping_speedy_fixed_time');
		}

		if (isset($this->request->post['shipping_speedy_check_office_work_day'])) {
			$data['shipping_speedy_check_office_work_day'] = $this->request->post['shipping_speedy_check_office_work_day'];
		} else if ($this->config->get('shipping_speedy_check_office_work_day') != null) {
			$data['shipping_speedy_check_office_work_day'] = $this->config->get('shipping_speedy_check_office_work_day');
		} else {
			$data['shipping_speedy_check_office_work_day'] = 1;
		}

		if (isset($this->request->post['shipping_speedy_taking_date'])) {
			$data['shipping_speedy_taking_date'] = $this->request->post['shipping_speedy_taking_date'];
		} else {
			$data['shipping_speedy_taking_date'] = $this->config->get('shipping_speedy_taking_date');
		}

		if (isset($this->request->post['shipping_speedy_order_status_id'])) {
			$data['shipping_speedy_order_status_id'] = $this->request->post['shipping_speedy_order_status_id'];
		} else {
			$data['shipping_speedy_order_status_id'] = $this->config->get('shipping_speedy_order_status_id');
		}

		if (isset($this->request->post['shipping_speedy_geo_zone_id'])) {
			$data['shipping_speedy_geo_zone_id'] = $this->request->post['shipping_speedy_geo_zone_id'];
		} else {
			$data['shipping_speedy_geo_zone_id'] = $this->config->get('shipping_speedy_geo_zone_id');
		}

		if (isset($this->request->post['shipping_speedy_order_status_update'])) {
			$data['shipping_speedy_order_status_update'] = $this->request->post['shipping_speedy_order_status_update'];
		} else {
			$data['shipping_speedy_order_status_update'] = $this->config->get('shipping_speedy_order_status_update');
		}

		if (isset($this->request->post['shipping_speedy_final_statuses'])) {
			$data['shipping_speedy_final_statuses'] = $this->request->post['shipping_speedy_final_statuses'];
		} else if ($this->config->get('shipping_speedy_final_statuses')) {
			$data['shipping_speedy_final_statuses'] = $this->config->get('shipping_speedy_final_statuses');
		} else {
			$data['shipping_speedy_final_statuses'] = array();
		}

		if (isset($this->request->post['shipping_speedy_min_package_dimention'])) {
			$data['shipping_speedy_min_package_dimention'] = $this->request->post['shipping_speedy_min_package_dimention'];
		} else {
			$data['shipping_speedy_min_package_dimention'] = $this->config->get('shipping_speedy_min_package_dimention');
		}

		$weight_dimentions = $this->model_extension_shipping_speedy->getWeightDimentions();

		if (isset($this->request->post['weight_dimensions'])) {
			$data['weight_dimensions'] = $this->request->post['weight_dimensions'];
		} else {
			$data['weight_dimensions'] = $weight_dimentions;
		}

		if (isset($this->request->post['shipping_speedy_convertion_to_win1251'])) {
			$data['shipping_speedy_convertion_to_win1251'] = $this->request->post['shipping_speedy_convertion_to_win1251'];
		} else {
			$data['shipping_speedy_convertion_to_win1251'] = $this->config->get('shipping_speedy_convertion_to_win1251');
		}

		if (isset($this->request->post['shipping_speedy_status'])) {
			$data['shipping_speedy_status'] = $this->request->post['shipping_speedy_status'];
		} else {
			$data['shipping_speedy_status'] = $this->config->get('shipping_speedy_status');
		}

		if (isset($this->request->post['shipping_speedy_sort_order'])) {
			$data['shipping_speedy_sort_order'] = $this->request->post['shipping_speedy_sort_order'];
		} else {
			$data['shipping_speedy_sort_order'] = $this->config->get('shipping_speedy_sort_order');
		}

		if (isset($this->request->post['shipping_speedy_invoice_courier_sevice_as_text'])) {
			$data['shipping_speedy_invoice_courier_sevice_as_text'] = $this->request->post['shipping_speedy_invoice_courier_sevice_as_text'];
		} else {
			$data['shipping_speedy_invoice_courier_sevice_as_text'] = $this->config->get('shipping_speedy_invoice_courier_sevice_as_text');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['services'] = $this->speedy->getServices($this->language->get('code'));

		$data['clients'] = $this->speedy->getListContractClients();

		$data['offices'] = $this->speedy->getOffices(null, null, $this->language->get('code'));

		$data['speedy_version'] = $this->speedy->version;

		if ($this->speedy->isAvailableMoneyTransfer()) {
			$data['available_money_transfer'] = true;

			if (isset($this->request->post['shipping_speedy_money_transfer'])) {
				$data['shipping_speedy_money_transfer'] = $this->request->post['shipping_speedy_money_transfer'];
			} else {
				$data['shipping_speedy_money_transfer'] = $this->config->get('shipping_speedy_money_transfer');
			}
		} else {
			$data['available_money_transfer'] = false;
			$data['shipping_speedy_money_transfer'] = false;
		}

		$data['pricings'] = array(
			'calculator'       => $this->language->get('text_calculator'),
			'calculator_fixed' => $this->language->get('text_calculator_fixed'),
			'fixed'            => $this->language->get('text_fixed_price'),
			'free'             => $this->language->get('text_free_shipping'),
			'table_rate'       => $this->language->get('text_table_rate'),
		);

		$data['options_before_payment'] = array(
			'no_option' => $this->language->get('text_no'),
			'test'      => $this->language->get('text_test_before_payment'),
			'open'      => $this->language->get('text_open_before_payment'),
		);

		$data['return_payer_types'] = array(
			0      => $this->language->get('text_sender'),
			1      => $this->language->get('text_receiver'),
		);

		$data['package_dimentions'] = array(
			'XS' => 'XS',
			'S'  => 'S',
			'M'  => 'M',
			'L'  => 'L',
			'XL' => 'XL',
		);

		$data['final_operation'] = Speedy::FINAL_OPERATION;

		$data['hours'] = array();

		foreach (range(0, 24) as $hour) {
			$data['hours'][] = str_pad($hour, 2, '0', STR_PAD_LEFT);
		}

		$data['minutes'] = array();

		foreach (range(0, 60) as $minute) {
			$data['minutes'][] = str_pad($minute, 2, '0', STR_PAD_LEFT);
		}

		$data['payer_types'] = array(
			ParamCalculation::PAYER_TYPE_SENDER   => $this->language->get('text_sender'),
			ParamCalculation::PAYER_TYPE_RECEIVER => $this->language->get('text_receiver'),
		);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/speedy', $data));
	}

	public function checkcredentials($return = false) {
		$this->load->language('extension/shipping/speedy');

		$this->load->library('speedy');

		if (!$this->request->post['shipping_speedy_username'] || !$this->request->post['shipping_speedy_password']) {
			$this->response->setOutput(json_encode(array('error' => 1, 'message' => $this->language->get('incorrect_user_and_pass'))));

			return;
		}

		$username = $this->request->post['shipping_speedy_username'];
		$password = $this->request->post['shipping_speedy_password'];

		if ($password == $this->language->get('hide_pass') && !$this->request->post['pass_changed']) {
			$password = $this->config->get('shipping_speedy_password');
		}

		$isCredentialsCorrect = $this->speedy->checkCredentials($username, $password);

		if ($return) {
			return $isCredentialsCorrect;
		}

		if ($isCredentialsCorrect) {
			$this->response->setOutput(json_encode(array('ok' => 1, 'message' => $this->language->get('correct_user_and_pass'))));
		} else {
			$this->response->setOutput(json_encode(array('error' => 1, 'message' => $this->language->get('incorrect_user_and_pass'))));
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/speedy')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['shipping_speedy_server_address']) {
			$this->request->post['shipping_speedy_server_address'] = 'https://www.speedy.bg/eps/main01.wsdl';
		}

		if (!$this->request->post['shipping_speedy_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->checkcredentials(true)) {
			$this->request->post['shipping_speedy_password'] = '';
		}

		if (!$this->request->post['shipping_speedy_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['shipping_speedy_name']) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['shipping_speedy_telephone']) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if (!$this->request->post['shipping_speedy_client_id']) {
			$this->error['client_id'] = $this->language->get('error_client_id');
		}

		if (!$this->request->post['shipping_speedy_default_weight']) {
			$this->error['default_weight'] = $this->language->get('error_default_weight');
		} elseif ($this->request->post['shipping_speedy_default_weight'] && !is_numeric($this->request->post['shipping_speedy_default_weight'])) {
			$this->error['default_weight'] = $this->language->get('error_invalid_number');
		}

		if (!isset($this->request->post['shipping_speedy_allowed_methods'])) {
			$this->error['allowed_methods'] = $this->language->get('error_allowed_methods');
		}

		if ($this->request->post['shipping_speedy_pricing'] == 'free' && !isset($this->request->post['shipping_speedy_free_method_international'])) {
			$this->error['free_method_international'] = $this->language->get('error_free_method_international');
		}

		if ($this->request->post['shipping_speedy_taking_date'] && !is_numeric($this->request->post['shipping_speedy_taking_date'])) {
			$this->error['taking_date'] = $this->language->get('error_invalid_number');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('setting/setting');

		$shipping_data = array(
			'total_shipping_estimator' => 0,
			'total_shipping_status' => 1,
			'total_shipping_sort_order' => $this->config->get('total_shipping_sort_order')
		);

		$this->model_setting_setting->editSetting('total_shipping', $shipping_data);

		$cod_data = array(
			'payment_cod_status' => 0
		);

		$this->model_setting_setting->editSetting('payment_cod', $cod_data);

		$this->load->model('extension/shipping/speedy');

		$this->model_extension_shipping_speedy->install();

		@mail('support@extensadev.com', 'Speedy Shipping Module installed', HTTP_CATALOG . ' - ' . $this->config->get('config_name') . "\r\n" . 'version - ' . VERSION . "\r\n" . 'IP - ' . $this->request->server['REMOTE_ADDR'], 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n" . 'From: ' . $this->config->get('config_owner') . ' <' . $this->config->get('config_email') . '>' . "\r\n");
	}

	public function uninstall() {
		$this->load->model('extension/shipping/speedy');

		$this->model_extension_shipping_speedy->uninstall();
	}

	public function upload() {
		$this->load->language('extension/shipping/speedy');

		$this->load->model('extension/shipping/speedy');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/shipping/speedy')) {
			$json['error'] = $this->language->get('error_permission');
		}


		if (!$json) {
			if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
				// Sanitize the filename
				$filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

				// Validate the filename length
				if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 128)) {
					$json['error'] = $this->language->get('error_filename');
				}

				// Allowed file extension types
				$allowed = array();

				$extension_allowed = preg_replace('~\r?\n~', "\n", 'csv');

				$filetypes = explode("\n", $extension_allowed);

				foreach ($filetypes as $filetype) {
					$allowed[] = trim($filetype);
				}

				if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Return any upload error
				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
		}

		if (!$json) {
			$data = array();

			if (($handle_import = fopen($this->request->files['file']['tmp_name'], 'r')) !== false) {
				$handle_import_data = fgetcsv($handle_import, 100000); // remove title line

				$file_columns = array(
					'ServiceID',
					'TakeFromOffice',
					'Weight',
					'OrderTotal',
					'PriceWithoutVAT',
					'FixedTimeDelivery',
				);

				$file_columns_indexes = array();

				foreach ($handle_import_data as $index => $columnName) {
					$file_columns_indexes[$columnName] = array_search($columnName, $handle_import_data);
				}

				sort($handle_import_data);
				sort($file_columns);

				if ($handle_import_data != $file_columns) {
					$json['error'] = $this->language->get('error_file_columns');
				}

				if (!$json) {
					while (($handle_import_data = fgetcsv($handle_import, 100000)) !== false) {
						$data[] = array(
							'service_id' => $handle_import_data[$file_columns_indexes['ServiceID']],
							'take_from_office' => $handle_import_data[$file_columns_indexes['TakeFromOffice']],
							'weight' => str_replace(',', '.', $handle_import_data[$file_columns_indexes['Weight']]),
							'order_total' => str_replace(',', '.', $handle_import_data[$file_columns_indexes['OrderTotal']]),
							'price_without_vat' => str_replace(',', '.', $handle_import_data[$file_columns_indexes['PriceWithoutVAT']]),
							'fixed_time_delivery' => str_replace(',', '.', $handle_import_data[$file_columns_indexes['FixedTimeDelivery']]),
						);
					}

					$this->model_extension_shipping_speedy->importFilePrice($data);

					$json['fileprice_name'] = $filename;

					$json['success'] = $this->language->get('text_upload');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function updateV410() { // operation code
		$this->load->model('extension/shipping/speedy');

		$this->model_extension_shipping_speedy->updateTablesV410();

		$this->load->model('setting/setting');

		$data = $this->model_setting_setting->getSetting('shipping_speedy');
		$data['shipping_speedy_updated_v410'] = true;

		$this->model_setting_setting->editSetting('shipping_speedy', $data);
	}

	private function updateV408() { // is_apt column
		$this->load->model('extension/shipping/speedy');

		$this->model_extension_shipping_speedy->updateTablesV408();

		$this->load->model('setting/setting');

		$data = $this->model_setting_setting->getSetting('shipping_speedy');
		$data['shipping_speedy_updated_v408'] = true;

		$this->model_setting_setting->editSetting('shipping_speedy', $data);
	}
}