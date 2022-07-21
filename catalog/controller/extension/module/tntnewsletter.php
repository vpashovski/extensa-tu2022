<?php
class ControllerExtensionModuletntnewsletter extends Controller {
	public function index($setting) {
		if($setting['status']){
			$data 					= array();
			$language_id 			= $this->config->get('config_language_id');
			if(!empty($setting['status'])){
				$text = $setting['tntnewsletter'][$language_id];
				$data['headding']   = $text['heading'];
				$data['buttontext'] = $text['subtitle'];
				return $this->load->view('extension/module/tntnewsletter', $data);
			}
		}
	}
}