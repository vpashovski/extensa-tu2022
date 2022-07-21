<?php
class ControllerExtensionModuletntofferbanner extends Controller {
    private $error = array();
    public function index() {
        $this->session->data['module_id'] = $this->request->get['module_id'];
        $this->load->language('extension/module/tntofferbanner');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/module');
        $this->load->model('tool/image');
        $this->load->model('localisation/language');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('tntofferbanner', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/tntofferbanner', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning']  = $this->error['warning'];
        } else {
            $data['error_warning']  = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name']     = $this->error['name'];
        } else {
            $data['error_name']     = '';
        }

        $data['breadcrumbs']        = array();

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
                'href' => $this->url->link('extension/module/tntofferbanner', 'user_token=' . $this->session->data['user_token'], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tntofferbanner', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/tntofferbanner', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/tntofferbanner', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['user_token'] = $this->session->data['user_token'];

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

        if (isset($this->request->post['tntofferbanner_description'])) {
            $data['tntofferbanner_description'] = $this->request->post['tntofferbanner_description'];
            foreach ($this->request->post['tntofferbanner_description'] as $key => $value) {
                if($value['image']){
                    $data['image'][$key] =  $this->model_tool_image->resize($value['image'], 100, 100);               
                }else{
                    $data['image'][$key] =  $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        } elseif (!empty($module_info)) {
            $data['tntofferbanner_description'] = $module_info['tntofferbanner_description'];
            foreach ($module_info['tntofferbanner_description'] as $key => $value) {
                $data['image'][$key] =  $this->model_tool_image->resize($value['image'], 100, 100);               
            }
        } else {
            foreach ($data['languages'] as $key => $value) {
                $data['image'][$value['language_id']] =  $this->model_tool_image->resize('no_image.png', 100, 100);              
            }
            $data['tntofferbanner_description'] = array();
        }

        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntofferbanner', $data));
    }
    public function install(){

        $settingdata           = array();
        $settingdata['name']   = "offer banner";
        $settingdata['status'] = 1;

        $this->load->model('setting/module');
        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $value) {
            $settingdata['tntofferbanner_description'][$value['language_id']] = array('image'=>'catalog/themefactory/offerbanner/1.jpg','title'=>'Our Stories','description'=>'We`ve recently updated our entire product  portfolio to give customers and partners the best products with the newest technology.','short_description'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis risus leo , elementum in malesuada an darius ut augue. Cras sit amet lectus et justo feugiat euismod sed non erat. Nulla non felis id metus bibendum iaculis quis sit amet eros. Nam suscipit mollis tellus vel malesuada. Duis dan molestie, sem in sollicitudin sodales mi justo sagittis est id consequat ipsum ligula a ante');
        }

        $this->model_setting_module->addModule('tntofferbanner', $settingdata);
    
    }

    protected function status(){
        return $this->webvoltystatus->footerstatus();
    }
    
    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/tntofferbanner')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        return !$this->error;
    }
}
