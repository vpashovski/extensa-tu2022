<?php
class ControllerCommontntnewsletter extends Controller {

	public function index() {
		$this->load->model('tnt/tntallquery');
		$this->load->model('tool/image');
		$name		= "tntnewsletter";
		$detail		= $this->model_tnt_tntallquery->getcommonmoduledetail($name);
		$setting 	= json_decode($detail['setting'],1);
		if($setting['status']){
			$data 			= array();
			$language_id 	= $this->config->get('config_language_id');
			if(!empty($setting['status'])){
				$text = $setting['tntnewsletter'][$language_id];
				$data['headding']   = $text['heading'];
				$data['description'] = $text['subtitle'];
				return $this->load->view('extension/module/tntnewsletter', $data);
			}
		}
	}
}