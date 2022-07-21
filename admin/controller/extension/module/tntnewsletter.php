<?php

class ControllerExtensionModuletntnewsletter extends Controller {

    private $error = array();



    public function index() {
$this->session->data['module_id'] = $this->request->get['module_id'];
        $this->load->language('extension/module/tntnewsletter');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/language');

        $this->load->model('setting/module');

        $this->load->model('tool/image');



        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatesetting()) {



            if (!isset($this->request->get['module_id'])) {

                $this->model_setting_module->addModule('tntnewsletter', $this->request->post);

            } else {

                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);

            }



            $this->session->data['success'] = $this->language->get('text_success');



            $this->response->redirect($this->url->link('extension/module/tntnewsletter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

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



        $data['breadcrumbs']    = array();



        $data['breadcrumbs'][]  = array(

            'text' => $this->language->get('text_home'),

            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)

        );



        $data['breadcrumbs'][]  = array(

            'text' => $this->language->get('text_extension'),

            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)

        );



        if (!isset($this->request->get['module_id'])) {

            $data['breadcrumbs'][] = array(

                'text' => $this->language->get('heading_title'),

                'href' => $this->url->link('extension/module/tntnewsletter', 'user_token=' . $this->session->data['user_token'] , true)

            );

        } else {

            $data['breadcrumbs'][] = array(

                'text' => $this->language->get('heading_title'),

                'href' => $this->url->link('extension/module/tntnewsletter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)

            );

        }



        $data['languages']    = $this->model_localisation_language->getLanguages();

        $data['user_token']   = $this->session->data['user_token'];



        if (!isset($this->request->get['module_id'])) {

            $data['action'] = $this->url->link('extension/module/tntnewsletter', 'user_token=' . $this->session->data['user_token'] , true);

        } else {

            $data['action'] = $this->url->link('extension/module/tntnewsletter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);

        }

        

        $data['cancel']     = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);



        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {

            $module_info    = $this->model_setting_module->getModule($this->request->get['module_id']);

        }

        

        if (isset($this->request->post['name'])) {

            $data['name']   = $this->request->post['name'];

        } elseif (!empty($module_info)) {

            $data['name']   = $module_info['name'];

        } else {

            $data['name']   = '';

        }



        if (isset($this->request->post['status'])) {

            $data['status'] = $this->request->post['status'];

        } elseif (!empty($module_info)) {

            $data['status'] = $module_info['status'];

        } else {

            $data['status'] = "";

        }



        $default_image = $this->model_tool_image->resize('no_image.png', 100, 100);



        if (isset($this->request->post['tntnewsletter'])) {

            $data['tntnewsletter']   = $this->request->post['tntnewsletter'];

        } elseif (isset($module_info)) {

            $data['tntnewsletter']   = $module_info['tntnewsletter'];

        } else {

            $data['tntnewsletter']   = array();

        }

        

        $data['header']         = $this->load->controller('common/header');

        $data['column_left']    = $this->load->controller('common/column_left');

        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntnewsletter', $data));

    }



    public function install(){



        $this->load->model('setting/module');

        $this->load->model('localisation/language');



        $main           = array();

        $main['name']   = "News Letter";

        $main['status'] = 1;



        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $value) {

            $main['tntnewsletter'][$value['language_id']] =  array('heading'=>"Newsletter",'subtitle'=>"Wants to get latest updates!");

        }



        $this->model_setting_module->addModule('tntnewsletter', $main);

    }

    



    protected function validatesetting() {

        $this->load->language('extension/module/tntimageslider');

        if (!$this->user->hasPermission('modify', 'extension/module/tntnewsletter')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {

            $this->error['name'] = $this->language->get('error_name');

        }

        return !$this->error;

    }

}