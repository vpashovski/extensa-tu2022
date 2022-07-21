<?php
class ControllerCommontntimagegallery extends Controller {

	public function index() {
		$this->load->model('tnt/tntallquery');
		$this->load->model('tool/image');
		$name		 				= "tntimagegallery";
		$detail		 				= $this->model_tnt_tntallquery->getcommonmoduledetail($name);
		$commonmoduledetail   		= json_decode($detail['setting'],1);
		$language_id 				= $this->config->get('config_language_id');
		$data['heading']			= $commonmoduledetail['tntimagegallery_parent'][$language_id]['title'];
		$width 						= $this->config->get('tntthemesetting_imagegallery_width');
		$height 					= $this->config->get('tntthemesetting_imagegallery_height');

		if(isset($commonmoduledetail['status'])){
			$imagegallerylist 		= $this->model_tnt_tntallquery->getimagegallerylist();
			$data['imagegallerys'] 	= array(); 		
			foreach ($imagegallerylist as $key => $value) {
				$image = $this->model_tool_image->resize($value['tntimagegalleryparent_image'], $width, $height);
				if(isset($value['tntimagegalleryparent_status'])){
					$data['imagegallerys'][] = array(
						'tntimagegalleryparent_link'			=> $value['tntimagegalleryparent_link'],
						'tntimagegalleryparent_image'			=> $image,
						'tntimagegallerychild_name'				=> $value['tntimagegallerychild_name']
					);
				}
			}
        	return $this->load->view('extension/module/tntimagegallery', $data);
		}
	}
}