<?php
class ControllerExtensionModuletntcategoryslider extends Controller {
	public function index($setting) {
		if(isset($setting['status'])){
			$this->load->model('tnt/tntallquery');
			$this->load->model('tool/image');
			$language_id 		= $this->config->get('config_language_id');
    		$parentdata 		= $setting['tntcategoryslider_parent'][$language_id];
    		$data['title'] 		= $parentdata['title'];
    		$data['subtitle'] 	= $parentdata['subtitle'];
			$width 				= $this->config->get('tntthemesetting_categoryimage_width');
			$height 			= $this->config->get('tntthemesetting_categoryimage_height');

			$info 				= $this->model_tnt_tntallquery->categoryslider();
			$data['categorysliders'] 		= array();
			foreach ($info as $key => $value) {
				if(isset($value['tntcategorysliderparent_status'])){
					$data['categorysliders'][] = array(
						'tntcategorysliderparent_category_id'	=> $value['tntcategorysliderparent_category_id'],
						'tntcategorysliderchild_name'	=> $value['tntcategorysliderchild_name'],
						'tntcategorysliderchild_description'	=> html_entity_decode($value['tntcategorysliderchild_description']),
					    'tntcategorysliderparent_image' => $this->model_tool_image->resize($value['tntcategorysliderparent_image'], $width, $height)
					);
				}
			}
			return $this->load->view('extension/module/tntcategoryslider', $data);
		}
	}
}
