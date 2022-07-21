<?php
class ControllerExtensionModuletntbanners extends Controller {
    private $error = array();
    public function index() {
                $this->session->data['module_id'] = $this->request->get['module_id'];
        $this->load->language('extension/module/tntbanners');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/module');
        $this->load->model('tool/image');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('tntbanners', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/tntbanners', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
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
        $data['limit']          = 3;
        for ($i=1; $i <= $data['limit']; $i++) { 
            if (isset($this->error['tntbanners_image_'.$i.''])) {
                $data['error'][$i] = $this->error['tntbanners_image_'.$i.''];
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
                'href' => $this->url->link('extension/module/tntbanners', 'user_token=' . $this->session->data['user_token'], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tntbanners', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }
        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/tntbanners', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/tntbanners', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }
        $data['cancel']     = $this->url->link('extension/module/tntbanners', 'user_token=' . $this->session->data['user_token'] . $url, true);
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
        $data['placeholder']    = $this->model_tool_image->resize('no_image.png', 100, 100);
        if (isset($this->request->post['tntbanners'])) {
            $data['tntbanners'] = $this->request->post['tntbanners'];
            foreach ($data['tntbanners'] as $key => $value) {
                for ($i=1; $i <= $data['limit']; $i++) { 
                    if($value['tntbanners_image_'.$i.'']){
                        $data['thumb'][$key]['img_'.$i.''] =  $this->model_tool_image->resize($value['tntbanners_image_'.$i.''], 100, 100);                
                    }else{
                        $data['thumb'][$key]['img_'.$i.''] =  $data['placeholder'];               
                    }       
                }
            }
        } elseif (!empty($module_info['tntbanners'])) {
            $data['tntbanners'] = $module_info['tntbanners'];
            foreach ($data['tntbanners'] as $key => $value) {
                for ($i=1; $i <= $data['limit']; $i++) {
                    if(!empty($value['tntbanners_image_'.$i.''])){
                        $data['thumb'][$key]['img_'.$i.''] =  $this->model_tool_image->resize($value['tntbanners_image_'.$i.''], 100, 100);                
                    }else{
                        $data['thumb'][$key]['img_'.$i.''] =  $data['placeholder'];
                    }
                    
                }
            }
        } else{
            foreach ($data['languages'] as $key => $value) {
                for ($i=1; $i <= $limit; $i++) { 
                    $data['thumb'][$value['language_id']]['img_'.$i.''] =  $data['placeholder'];
                }
            }
            $data['tntbanners'] = array();
        }
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/tntbanners', $data));
    }
    public function install(){
        $settingdata           = array();
        $settingdata['name']   = "Banners";
        $settingdata['status'] = 1;
        $this->load->model('setting/module');
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $value) {
            $settingdata['tntbanners'][$value['language_id']] = array('tntbanners_image_1'=>"catalog/themefactory/banners/1.jpg",'language_id'=>$value['language_id'],'tntbanners_link_1'=>"#",'tntbanners_status_1'=>"1",'tntbanners_width_1'=>"640",'tntbanners_height_1'=>"400",
                'tntbanners_image_2'=>"catalog/themefactory/banners/2.jpg",'language_id'=>$value['language_id'],'tntbanners_link_2'=>"#",'tntbanners_status_2'=>"1",'tntbanners_width_2'=>"640",'tntbanners_height_2'=>"400",
                'tntbanners_image_3'=>"catalog/themefactory/banners/3.jpg",'language_id'=>$value['language_id'],'tntbanners_link_3'=>"#",'tntbanners_status_3'=>"1",'tntbanners_width_3'=>"640",'tntbanners_height_3'=>"400");
        }
        $this->model_setting_module->addModule('tntbanners', $settingdata);
        
    }
    
    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/tntbanners')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
    
        $limit = 2;
        foreach ($this->request->post['tntbanners'] as $language_id => $value) {
            for ($i=1; $i <= $limit; $i++) { 
                if ((utf8_strlen($value['tntbanners_image_'.$i.'']) < 1) || (utf8_strlen($value['tntbanners_image_'.$i.'']) > 255)) {
                    $this->error['tntbanners_image_'.$i.''][$language_id] = $this->language->get('error_image')." ".$i;
                }
            }   
        }
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
        return !$this->error;
    }   
}