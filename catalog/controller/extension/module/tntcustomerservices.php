<?php
class ControllerExtensionModuletntcustomerservices extends Controller {
	public function index($setting) {
        
		if($setting['status']){
			$data 					= array();
			$language_id 			= $this->config->get('config_language_id');
            $this->load->model('tool/image');
			/*$parentdata 			= $setting['tntcustomerservices_parent'][$language_id];
			$data['parenttitle'] 	= $parentdata['title'];
			$data['parentsubtitle'] = $parentdata['subtitle'];*/
			$subdata 				= $setting['tntcustomerservices'][$language_id];

        	$data['status_1']		= $subdata['tntcustomerservices_status_1'];
			$data['title_1'] 		= $subdata['tntcustomerservices_title_1'];
            $data['description_1']  = $subdata['tntcustomerservices_description_1'];
            $data['image_1']        = $this->model_tool_image->resize($subdata['tntcustomerservices_image_1'], 50,100);

            $data['status_2']       = $subdata['tntcustomerservices_status_2'];
            $data['title_2']        = $subdata['tntcustomerservices_title_2'];
            $data['description_2']  = $subdata['tntcustomerservices_description_2'];
            $data['image_2']        = $this->model_tool_image->resize($subdata['tntcustomerservices_image_2'], 50,100);
            

            $data['status_3']       = $subdata['tntcustomerservices_status_3'];
            $data['title_3']        = $subdata['tntcustomerservices_title_3'];
            $data['description_3']  = $subdata['tntcustomerservices_description_3'];
            $data['image_3']        = $this->model_tool_image->resize($subdata['tntcustomerservices_image_3'], 50,100);

            $data['status_4']       = $subdata['tntcustomerservices_status_4'];
            $data['title_4']        = $subdata['tntcustomerservices_title_4'];
            $data['description_4']  = $subdata['tntcustomerservices_description_4'];
            $data['image_4']        = $this->model_tool_image->resize($subdata['tntcustomerservices_image_4'], 50,100);
            
			return $this->load->view('extension/module/tntcustomerservices', $data);
		}
	}
}