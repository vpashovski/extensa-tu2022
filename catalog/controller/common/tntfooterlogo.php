<?php
class ControllerCommontntfooterlogo extends Controller {

	public function index() {
		$this->load->model('tnt/tntallquery');
		$this->load->model('tool/image');
		$name		= "tntfooterlogo";
		$detail		= $this->model_tnt_tntallquery->getcommonmoduledetail($name);
		$setting 	= json_decode($detail['setting'],1);
		if($setting['status']){
			$data 			= array();
			$language_id 	= $this->config->get('config_language_id');
			if(!empty($setting['status'])){
				$text 					= $setting['tntfooterlogo_description'][$language_id];
				$width 					= 227;
				$height 				= 55;
				$data['description']   	= $text['description'];
				$data['image'] 			= $this->model_tool_image->resize($text['image'], $width, $height);

				
				return $this->load->view('extension/module/tntfooterlogo', $data);
			}
		}
	}
}