<?php
class ControllerExtensionModuletntbanners extends Controller {
	public function index($setting) {

		if(isset($setting['status'])){
			$data 			= array();
			$this->load->model('tool/image');
			$i 				= 1; 
			$language_id 	= $this->config->get('config_language_id');
			foreach ($setting['tntbanners'] as $key => $value) {
					
				if($value['language_id'] == $language_id){
					
					if(isset($value['tntbanners_status_1'])){
						$data['tntbanners_image_1'] = $this->model_tool_image->resize($value['tntbanners_image_1'], $value['tntbanners_width_1'],$value['tntbanners_height_1']);
						$data['tntbanners_link_1'] = $value['tntbanners_link_1'];
						$data['tntbanners_status_1'] = $value['tntbanners_status_1'];
					}
					if(isset($value['tntbanners_status_2'])){
						$data['tntbanners_image_2'] = $this->model_tool_image->resize($value['tntbanners_image_2'], $value['tntbanners_width_2'],$value['tntbanners_height_2']);
						$data['tntbanners_link_2'] = $value['tntbanners_link_2'];
						$data['tntbanners_status_2'] = $value['tntbanners_status_2'];

					}
					if(isset($value['tntbanners_status_3'])){
						$data['tntbanners_image_3'] = $this->model_tool_image->resize($value['tntbanners_image_3'], $value['tntbanners_width_3'],$value['tntbanners_height_3']);
						$data['tntbanners_link_3'] = $value['tntbanners_link_3'];
						$data['tntbanners_status_3'] = $value['tntbanners_status_3'];

					}
				}
					$i++;
			}  
			return $this->load->view('extension/module/tntbanners', $data);
		}
	}
}
