<?php
class ControllerCommontntpaymenticon extends Controller {

	public function index() {
		$this->load->model('tnt/tntallquery');
		$this->load->model('tool/image');
		$name		 				= "tntpaymenticon";
		$detail		 				= $this->model_tnt_tntallquery->getcommonmoduledetail($name);
		$commonmoduledetail   		= json_decode($detail['setting'],1);
		if(isset($commonmoduledetail['status'])){
			$socialllist 			= $this->model_tnt_tntallquery->getpaymentlist();
			$data['payments'] 		= array(); 		
			$width 					= $this->config->get('tntthemesetting_payemtnicon_width');
			$height 				= $this->config->get('tntthemesetting_payemtnicon_height');
			foreach ($socialllist as $key => $value) {
				$image = $this->model_tool_image->resize($value['tntpaymenticonparent_image'], $width, $height);
				if(isset($value['tntpaymenticonparent_status'])){
					$data['payments'][] = array(
						'tntpaymenticonparent_link'			=> $value['tntpaymenticonparent_link'],
						'tntpaymenticonchild_title'			=> $value['tntpaymenticonchild_title'],
						'tntpaymenticonparent_image'		=> $image
					);
				}
			}
        	return $this->load->view('extension/module/tntpaymenticon', $data);
		}
	}
}