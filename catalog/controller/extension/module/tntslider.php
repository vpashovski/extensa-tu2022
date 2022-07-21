<?php
class ControllerExtensionModuletntslider extends Controller {
	public function index($setting) {

	if(isset($setting['status'])){
			$this->load->model('tnt/tntallquery');
			$this->load->model('tool/image');
			$data['speed'] 							= $setting['speed'];
			$data['hover'] 							= $setting['hover'];
			$data['loop'] 							= $setting['loop'];
			$data['animationslideranimationslider'] = $setting['animationslider'];
			$getsliderlist_all 						= $this->model_tnt_tntallquery->getsliderlist();
			$data['slider'] 						= array();
			$width 	= $this->config->get('tntthemesetting_sliderimage_width');
			$height = $this->config->get('tntthemesetting_sliderimage_height');
			foreach ($getsliderlist_all as $key => $value) {
				if(isset($value['tntsliderchild_enable'])){
					if(!empty($value['tntsliderchild_image'])){
						$image = $this->model_tool_image->resize($value['tntsliderchild_image'], $width, $height);
					}else{
						$image = "";
					}
					$data['sliders'][] = array(
						'tntsliderchild_link'			=> $value['tntsliderchild_link'],
						'tntsliderchild_image' 			=> $image,
						'tntsliderchild_title'			=> $value['tntsliderchild_title'],
						'tntsliderchild_subtitle'			=> $value['tntsliderchild_subtitle'],
						'tntsliderchild_textaligment'	=> $value['tntsliderchild_textaligment'],
						'tntsliderchild_buttontext'		=> $value['tntsliderchild_buttontext'],
						'tntsliderchild_description'	=> $value['tntsliderchild_description'],
					);
				}
			}

			return $this->load->view('extension/module/tntslider', $data);
		}
	}
}
