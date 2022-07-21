<?php
class ControllerExtensionModuletntnewsletterpopup extends Controller {
	public function index($setting) {
		if($setting['status']){
			$data 					= array();
			$language_id 			= $this->config->get('config_language_id');
			if(!empty($setting['status'])){
                $this->load->model('tool/image');
				$text  		        = $setting['tntnewsletterpopup'][$language_id];
				$data['heading'] 	= $text['heading'];
                $data['subtitle']   = $text['subtitle'];
				$data['description'] 	= $text['description'];
                $width  = $this->config->get('tntthemesetting_newsletterpopup_width');
                $height = $this->config->get('tntthemesetting_newsletterpopup_height');
                $data['image']      = $this->model_tool_image->resize($text['image'], $width, $height);

				return $this->load->view('extension/module/tntnewsletterpopup', $data);
			}
		}
	}
	public function adddata() {
        $json = array();
        $this->load->model('tnt/tntallquery');
        $this->load->language('extension/module/tntcustomtext');
        if (!empty($this->request->get['email'])) {
            $email = $this->request->get['email'];
                
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $json['text_error_email'] = "<span class='text-danger'>Please enter a valid email address</span>";
            }else{
                $checkemail = $this->model_tnt_tntallquery->checknewsletter($email);
                if(empty($checkemail)){
                    $alldataget = $this->model_tnt_tntallquery->insertnewsletter($email);
                        $json['text_success_email'] = "<span class='text-success'>Thank you for subscribing</span>";
                }else{
                    $json['text_repeat_email'] = "<span class='text-warning'>You are already subscribed</span>";
                }
            }
        } else {
           $json['text_enter_email'] = "<span class='text-danger'>Please enter a valid email address</span>";
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}