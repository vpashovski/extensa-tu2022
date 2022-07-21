<?php
class ControllerExtensionModuletntlefttestimonial extends Controller {
	public function index($setting) {
		if(isset($setting['status'])){

			$this->load->model('tnt/tntallquery');
			$language_id 		 		= $this->config->get('config_language_id');
			//$parentdata  				= $setting['tnttestimonial_parentsettingdata'][$language_id];
			//$data['parentheading'] 		= $parentdata['heading'];
			$data['parentheading'] 		= "Testimonial";
			$gettestimonial_all 	 	= $this->model_tnt_tntallquery->gettestimoniallist();
			$this->load->model('tool/image');
			$width 	= $this->config->get('tntthemesetting_leftrighttestimoinal_width');
			$height = $this->config->get('tntthemesetting_leftrighttestimoinal_height');

			$data['testimonials'] 		= array();
			foreach ($gettestimonial_all as $key => $value) {

				if(isset($value['tnttestimonialparent_status'])){
					$data['testimonials'][] = array(
						'tnttestimonialchild_name'			=> $value['tnttestimonialchild_name'],
						'tnttestimonialchild_description'	=> $value['tnttestimonialchild_description'],
						'tnttestimonialchild_designation'	=> $value['tnttestimonialchild_designation'],
						'tnttestimonialparent_link'			=> $value['tnttestimonialparent_link'],
						'tnttestimonialparent_image' 		=> $this->model_tool_image->resize($value['tnttestimonialparent_image'], $width, $height),
					);
				}
			}
			return $this->load->view('extension/module/tntlefttestimonial', $data);
		}
	}
}
