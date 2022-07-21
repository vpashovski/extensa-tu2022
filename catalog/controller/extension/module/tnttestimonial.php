<?php
class ControllerExtensionModuletnttestimonial extends Controller {
	public function index($setting) {
	if(isset($setting['status'])){
			$this->load->model('tnt/tntallquery');
			$language_id 		 		= $this->config->get('config_language_id');
			$parentdata  				= $setting['tnttestimonial_parentsettingdata'][$language_id];
			$data['parentheading'] 		= $parentdata['heading'];
			$data['parentsubtitle'] 	= $parentdata['subtitle'];
			$gettestimonial_all 	 	= $this->model_tnt_tntallquery->gettestimoniallist();
			$this->load->model('tool/image');
			$width  					= 100;
			$height  					= 100;
			$width 	= $this->config->get('tntthemesetting_testimonial_width');
			$height = $this->config->get('tntthemesetting_testimonial_height');

			$bgwidth 	= $this->config->get('tntthemesetting_testimonialbgimage_width');
			$bgheight = $this->config->get('tntthemesetting_testimonialbgimage_height');

			$data['bgimage'] =  $this->model_tool_image->resize($parentdata['image'], $bgwidth, $bgheight);
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
			//echo "<pre>"; print_r($data); die;
			return $this->load->view('extension/module/tnttestimonial', $data);
		}
	}
}
