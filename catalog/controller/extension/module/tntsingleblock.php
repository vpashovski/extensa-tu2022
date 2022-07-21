<?php
class ControllerExtensionModuletntsingleblock extends Controller {
	public function index($setting) {
		if(isset($setting['status'])){
			$this->load->model('tool/image');
			$language_id = $this->config->get('config_language_id');

			$width1 	= $this->config->get('tntthemesetting_singleblock_width');
			$height1 = $this->config->get('tntthemesetting_singleblock_height');

			$width2 	= $this->config->get('tntthemesetting_testimonialbgimage_width');
			$height2 = $this->config->get('tntthemesetting_testimonialbgimage_height');

			$data['image1'] = $this->model_tool_image->resize($setting['image1'], $width1, $height1);
			$data['image2'] = $this->model_tool_image->resize($setting['image2'], $width2, $height2);
			$data['link'] 	= $setting['link'];
   			
   			$text = $setting['tntsingleblock_parent'][$language_id];

   			$data['title'] = $text['title'];
   			$data['subtitle'] = $text['subtitle'];
   			$data['description'] = $text['description'];
   			$data['buttontext'] = "Read More";

			return $this->load->view('extension/module/tntsingleblock', $data);
		}
	}
}