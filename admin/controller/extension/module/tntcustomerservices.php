<?php
class ControllerExtensionModuletntcustomerservices extends Controller {
    private $error = array();
    public function index() {
                $this->session->data['module_id'] = $this->request->get['module_id'];
        $this->load->language('extension/module/tntcustomerservices');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/module');
        $this->load->model('tool/image');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('tntcustomerservices', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/tntcustomerservices', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
    
        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }
        for ($i=1; $i <= 4; $i++) { 
            if (isset($this->error['tntcustomerservices_image_'.$i.''])) {
                $data['error'][$i] = $this->error['tntcustomerservices_image_'.$i.''];
            } else {
                $data['error'][$i] = "";
            }           
        }
        
        $url = '';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tntcustomerservices', 'user_token=' . $this->session->data['user_token'], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tntcustomerservices', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }
        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/tntcustomerservices', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/tntcustomerservices', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }
        $data['cancel']     = $this->url->link('extension/module/tntcustomerservices', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['user_token'] = $this->session->data['user_token'];
        $this->load->model('localisation/language');
        $data['languages']  = $this->model_localisation_language->getLanguages();

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info  = $this->model_setting_module->getModule($this->request->get['module_id']);
        }
        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }
        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = "";
        }
       /* if (isset($this->request->post['tntcustomerservices_parent'])) {
            $data['tntcustomerservices_parent'] = $this->request->post['tntcustomerservices_parent'];
        } elseif (!empty($module_info['tntcustomerservices_parent'])) {
            $data['tntcustomerservices_parent'] = $module_info['tntcustomerservices_parent'];
        } else{
            $data['tntcustomerservices_parent'] = array();
        }*/
        if (isset($this->request->post['tntcustomerservices'])) {
            $data['tntcustomerservices'] = $this->request->post['tntcustomerservices'];
            foreach ($this->request->post['tntcustomerservices'] as $key => $value) {
                for ($i=1; $i <= 4; $i++) { 
                    if($value['tntcustomerservices_image_'.$i.'']){
                        $data['imgdata'][$key]['image'.$i.''] =  $this->model_tool_image->resize($value['tntcustomerservices_image_'.$i.''], 100, 100);               
                    }else{
                        $data['imgdata'][$key]['image'.$i.''] =  $this->model_tool_image->resize('no_image.png', 100, 100);               
                    }       
                }
            }
        } elseif (!empty($module_info['tntcustomerservices'])) {
            $data['tntcustomerservices'] = $module_info['tntcustomerservices'];
            foreach ($data['tntcustomerservices'] as $key => $value) {
                for ($i=1; $i <= 4; $i++) {
                    if(!empty($value['tntcustomerservices_image_'.$i.''])){
                        $data['imgdata'][$key]['image'.$i.''] =  $this->model_tool_image->resize($value['tntcustomerservices_image_'.$i.''], 100, 100);               
                    }else{
                        $data['imgdata'][$key]['image'.$i.''] =  $this->model_tool_image->resize('no_image.png', 100, 100);
                    }
                    
                }
            }
        } else{
            foreach ($data['languages'] as $key => $value) {
                for ($i=1; $i <= 4; $i++) { 
                    $data['imgdata'][$value['language_id']]['image'.$i.''] =  $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
            $data['tntcustomerservices'] = array();
        }
        $data['placeholder']    = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/tntcustomerservices_form', $data));
    }
    public function install(){
        $settingdata           = array();
        $settingdata['status'] = 1;
        $settingdata['name']   = "Customer Services";
        $this->load->model('setting/module');
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $value) {
           $settingdata['tntcustomerservices_parent'][$value['language_id']] = array('title'=>"Our Best Services",'subtitle'=> "Fresh & Silky Daily");

            $settingdata['tntcustomerservices'][$value['language_id']] = array(
                'tntcustomerservices_image_1'=>"catalog/themefactory/customerservice/1.png",
                'language_id'=>"'".$value['language_id']."'",'tntcustomerservices_title_1'=>"Free Shipping",'tntcustomerservices_description_1'=>"On orders over 100$",'tntcustomerservices_status_1'=>"1",'tntcustomerservices_image_2'=>"catalog/themefactory/customerservice/2.png",'language_id'=>"'".$value['language_id']."'",'tntcustomerservices_title_2'=>"Money Back 100%",'tntcustomerservices_description_2'=>"Within 30 Days after delivery",'tntcustomerservices_status_2'=>"1",'tntcustomerservices_image_3'=>"catalog/themefactory/customerservice/3.png",'language_id'=>"'".$value['language_id']."'",'tntcustomerservices_title_3'=>"online support",'tntcustomerservices_description_3'=>"Mon-Sun: 8.00-20.00",'tntcustomerservices_status_3'=>"1",'tntcustomerservices_image_4'=>"catalog/themefactory/customerservice/4.png",'language_id'=>"'".$value['language_id']."'",'tntcustomerservices_title_4'=>"1 (234) 567 89 01",'tntcustomerservices_description_4'=>"Order by phone",'tntcustomerservices_status_4'=>"1"
                );
        }
        $this->model_setting_module->addModule('tntcustomerservices', $settingdata);
        
    }
    
    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/tntcustomerservices')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        foreach ($this->request->post['tntcustomerservices'] as $language_id => $value) {
            for ($i=1; $i <= 4; $i++) { 
                if ((utf8_strlen($value['tntcustomerservices_image_'.$i.'']) < 1) || (utf8_strlen($value['tntcustomerservices_image_'.$i.'']) > 255)) {
                    $this->error['tntcustomerservices_image_'.$i.''][$language_id] = $this->language->get('error_image')." ".$i;
                }
            }   
        }
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
        return !$this->error;
    }   
}