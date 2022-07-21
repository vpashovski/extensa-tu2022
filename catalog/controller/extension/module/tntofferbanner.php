<?php
class ControllerExtensionModuletntofferbanner extends Controller {
	public function index($setting) {
        if(isset($setting['status'])){
            $this->load->model('tool/image');
            $language_id                  = $this->config->get('config_language_id');
            $width                        = $this->config->get('tntthemesetting_offerbanner_width');
            $height                       = $this->config->get('tntthemesetting_offerbanner_height');
            $text                         = $setting['tntofferbanner_description'][$language_id];
            $data['image']                = $this->model_tool_image->resize($text['image'], $width, $height);
            $data['title']                = $text['title'];
            $data['description']          = $text['description'];
            $data['short_description']    = $text['short_description'];
            $data['link']                 = "#";
		$data['buttontext']           = "Read More";
            return $this->load->view('extension/module/tntofferbanner', $data);
		}
	}
}