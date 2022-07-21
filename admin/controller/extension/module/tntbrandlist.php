<?php

class ControllerExtensionModuletntbrandlist extends Controller {

    private $error = array();



    public function index() {
$this->session->data['module_id'] = $this->request->get['module_id'];
        $this->load->language('extension/module/tntbrandlist');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/language');

        $this->load->model('tnt/tntbrandlist');

        $this->load->model('tool/image');

        $this->load->model('setting/module');



        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            if(isset($this->request->post['tntbrandlist']) && !empty($this->request->post['tntbrandlist'])){

                $tabledata = $this->request->post['tntbrandlist'];

            }else{

                $tabledata = array();

            }

            $this->model_tnt_tntbrandlist->add($tabledata);

            unset($this->request->post['tntbrandlist']);

            if (!isset($this->request->get['module_id'])) {

                $this->model_setting_module->addModule('tntbrandlist', $this->request->post);

            } else {

                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);

            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/tntbrandlist', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

        }

        $data_info = $this->model_tnt_tntbrandlist->exitsdata();

        

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

                'href' => $this->url->link('extension/module/tntbrandlist', 'user_token=' . $this->session->data['user_token'], true)

            );

        } else {

            $data['breadcrumbs'][] = array(

                'text' => $this->language->get('heading_title'),

                'href' => $this->url->link('extension/module/tntbrandlist', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)

            );

        }

        $data['user_token']         = $this->session->data['user_token'];

        



        if (!isset($this->request->get['module_id'])) {

            $data['action'] = $this->url->link('extension/module/tntbrandlist', 'user_token=' . $this->session->data['user_token'], true);

        } else {

            $data['action'] = $this->url->link('extension/module/tntbrandlist', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);

        }



        $data['cancel']  = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);



        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {

            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);

        }



        if (isset($this->request->post['tntbrandlist'])) {

            $tntbrandlist = $this->request->post['tntbrandlist'];

        } elseif ($data_info->num_rows) {

            $tntbrandlist = $data_info->rows;

        } else {

            $tntbrandlist = array();

        }

    

        $data['tntbrandlists'] = array();





        if (isset($this->request->post['tntbrandlist_parent'])) {

            $data['tntbrandlist_parent'] = $this->request->post['tntbrandlist_parent'];

            

        } elseif (!empty($module_info['tntbrandlist_parent'])) {

            

            $data['tntbrandlist_parent'] = $module_info['tntbrandlist_parent'];

        } else {

            

            $data['tntbrandlist_parent'] = array();

        }

        





        if (!empty($tntbrandlist))  {

            foreach ($tntbrandlist as $list) {

                $text   = ( isset($list['tntbrandlist_text']) ? json_decode($list['tntbrandlist_text'],1):array() );

                $image      = ( isset($list['tntbrandlist_image']) ? $list['tntbrandlist_image']:'' );

                //$text       = ( isset($list['tntbrandlist_text']) ? $list['tntbrandlist_text']:'' );

                $link       = ( isset($list['tntbrandlist_link']) ? $list['tntbrandlist_link']:'' );

                $position   = ( isset($list['tntbrandlist_position']) ? $list['tntbrandlist_position']:'' );

                $status     = ( isset($list['tntbrandlist_status']) ? $list['tntbrandlist_status']:'' );

                $id         = ( isset($list['tntbrandlist_id']) ? $list['tntbrandlist_id']:'' );

                 

                $data['tntbrandlists'][] = array(

                    'tntbrandlist_id'         => $id,

                    'tntbrandlist_link'       => $link,

                    'tntbrandlist_image'      => $image,

                    'tntbrandlist_thumb'   => $this->model_tool_image->resize($image, 100, 100),

                    'tntbrandlist_text'       => $text,

                    'tntbrandlist_position'   => $position,

                    'tntbrandlist_status'     => $status

                );

            }

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

        $data['languages']      = $this->model_localisation_language->getLanguages();



        $data['placeholder']    = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['header']         = $this->load->controller('common/header');

        $data['column_left']    = $this->load->controller('common/column_left');

        $data['footer']         = $this->load->controller('common/footer');



        $this->response->setOutput($this->load->view('extension/module/tntbrandlist', $data));

    }

    public function install(){

        $tablealliance          = DB_PREFIX;

        $settingdata            = array();

        $settingdata['name']    = "Brand List";

        $settingdata['status']  = 1;



        $this->load->model('setting/module');

        $this->load->model('localisation/language');

        $languageuages      = $this->model_localisation_language->getLanguages();

        foreach ($languageuages as $value) {

            $data['language'][$value['language_id']] =  array('title'=>"Zara");

            $settingdata['tntbrandlist_parent'][$value['language_id']] =  array('title'=>"Shop by Brand",'subtitle'=>"fresh & silky daily");

        }



        $this->model_setting_module->addModule('tntbrandlist', $settingdata);



        $this->db->query("CREATE TABLE IF NOT EXISTS `{$tablealliance}tntbrandlist` 

        (  `tntbrandlist_id` INT NOT NULL AUTO_INCREMENT ,

           `tntbrandlist_link` VARCHAR(255) NOT NULL , 

           `tntbrandlist_status` INT NOT NULL , 

           `tntbrandlist_position` INT NOT NULL , 

           `tntbrandlist_image` VARCHAR(255) NOT NULL ,

           `tntbrandlist_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,

        PRIMARY KEY (`tntbrandlist_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;" );

        $num_of_data    = 8;

        for ($i = 1; $i<=$num_of_data; $i++) {

            $this->db->query("INSERT INTO " . DB_PREFIX . "tntbrandlist SET tntbrandlist_link = '#',tntbrandlist_image = 'catalog/themefactory/brandlist/".$i.".jpg',tntbrandlist_text = '" . json_encode($data['language']) . "',tntbrandlist_position = '" . (int)$i . "', tntbrandlist_status = '" . (int)1 . "'");

        }

    }

    public function uninstall(){

        $tablealliance = DB_PREFIX;

        $this->db->query("DROP TABLE `{$tablealliance}tntbrandlist`");

    }

    

    public function sorting() {

        $this->load->model('tnt/tntbrandlist');

        $edit               = $this->request->get['action'];

        $positionition      = $this->request->get['dataarray'];

        $return_data        = array();

        if ($edit == 'edit') {

            $return_data['success'] = 'right';

            $this->model_tnt_tntbrandlist->editposition($positionition);

            echo implode("##", $return_data);

        }

    }

    protected function validate() {

        if (!$this->user->hasPermission('modify', 'extension/module/tntbrandlist')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {

            $this->error['name'] = $this->language->get('error_name');

        }

        return !$this->error;

    }

}