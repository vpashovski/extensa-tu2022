<?php
class ControllerExtensionPaymentSpeedyCod extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['continue'] = $this->url->link('checkout/success');

		return $this->load->view('extension/payment/speedy_cod', $data);
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'speedy_cod') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_speedy_cod_order_status_id'));
		}
	}
}