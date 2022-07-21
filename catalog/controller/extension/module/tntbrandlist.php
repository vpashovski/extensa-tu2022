<?php
class ControllerExtensionModuletntbrandlist extends Controller {
	public function index($setting) {
			if(isset($setting['status'])){
				$this->load->model('tnt/tntallquery');
				$this->load->model('tool/image');
				$language_id 		= $this->config->get('config_language_id');
				$parentdata 		= $setting['tntbrandlist_parent'][$language_id];
				$data['heading'] 	= $parentdata['title'];
				$data['subtitle'] 	= $parentdata['subtitle'];
				$brandlist_all 		= $this->model_tnt_tntallquery->getbrandlist();
				$data['brandlist'] 	= array();
				$width 				= $this->config->get('tntthemesetting_brandimage_width');
				$height 			= $this->config->get('tntthemesetting_brandimage_height');
	 			foreach ($brandlist_all as $key => $value) {
					if($value['tntbrandlist_status']){
						$text 	= json_decode($value['tntbrandlist_text'],1)[$language_id];
						$title 	= $text['title'];
						$data['brandlist'][] = array(
							'tntbrandlist_link'		=> $value['tntbrandlist_link'],
							'tntbrandlist_text'		=> $title,
							'tntbrandlist_image' 	=> $this->model_tool_image->resize($value['tntbrandlist_image'], $width, $height)
						);
					}
				}
				
				return $this->load->view('extension/module/tntbrandlist', $data);
			}
	}
}
