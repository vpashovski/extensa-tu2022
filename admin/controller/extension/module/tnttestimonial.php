<?php
class ControllerExtensionModuletnttestimonial extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/module/tnttestimonial');
$this->session->data['module_id'] = $this->request->get['module_id'];
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tnttestimonial');
        
        $this->getList();
    }
    public function install(){

        $settingdata           = array();
        $settingdata['name']   = "Testimonial";
        $settingdata['status'] = 1;

        $this->load->model('setting/module');
        $this->load->model('localisation/language');
        $this->load->model('tnt/tnttestimonial');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $value) {
            $settingdata['tnttestimonial_parentsettingdata'][$value['language_id']] =  array('image'=>"catalog/themefactory/testimonial/main.png",'heading'=>"Client Says",'subtitle'=>"What Our Customer Say Fresh & Silky Daily");
        }

        $this->model_setting_module->addModule('tnttestimonial', $settingdata);
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tnttestimonialparent` 
        (   `tnttestimonialparent_id` int(11) AUTO_INCREMENT,
            `tnttestimonialparent_position` int(11),
            `tnttestimonialparent_image` VARCHAR(255),
            `tnttestimonialparent_link` VARCHAR(255),
            `tnttestimonialparent_status` int(11),
        PRIMARY KEY (`tnttestimonialparent_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tnttestimonialchild` 
        (   `tnttestimonialchild_id` INT NOT NULL AUTO_INCREMENT ,
            `tnttestimonialparent_id` INT NOT NULL ,
            `tnttestimonialchildlanguage_id` INT NOT NULL ,
            `tnttestimonialchild_name` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
            `tnttestimonialchild_designation` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
            `tnttestimonialchild_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        PRIMARY KEY (`tnttestimonialchild_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
        $num_of_data = 3;
        $sub         = array();
        for ($i = 1; $i<=$num_of_data; $i++) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tnttestimonialparent`
            SET   
                tnttestimonialparent_link       = "#" ,
                tnttestimonialparent_image      = "catalog/themefactory/testimonial/'.$i.'.jpg",
                tnttestimonialparent_position   = '.$i.',
                tnttestimonialparent_status           = 1;'); 
            foreach ($languages as $value) {
                $this->db->query('INSERT INTO `' . DB_PREFIX . 'tnttestimonialchild`
                SET 
                    tnttestimonialparent_id                = '.$i.',
                    tnttestimonialchild_name               = "By Jonson",
                    tnttestimonialchild_designation        = "CEO & Founder DooTr",
                     tnttestimonialchild_description        = "Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit.",
                    tnttestimonialchildlanguage_id         = '.$value['language_id'].'');
            }
        }
    }
    public function uninstall(){
        $pre = DB_PREFIX;
        $this->db->query("DROP TABLE `{$pre}tnttestimonialparent`");
        $this->db->query("DROP TABLE `{$pre}tnttestimonialchild`");
    }
    
    public function sortdata() {
        $this->load->model('tnt/tnttestimonial');
        $editdataposition   = $this->request->get['action'];
        $position           = $this->request->get['recordsArray'];
        $return_data        = array();
        if ($editdataposition == 'editdataposition') {
            $return_data['success'] = 'right';
            $this->model_tnt_tnttestimonial->sortingdata($position);
            echo $res = implode("##", $return_data);
        }
    }   
    public function add() {
        $this->load->language('extension/module/tnttestimonial');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tnt/tnttestimonial');
        if ($this->request->server['REQUEST_METHOD'] == 'POST')  {
            $this->model_tnt_tnttestimonial->insertrecord($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            $this->response->redirect($this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }
        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/module/tnttestimonial');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tnt/tnttestimonial');
        if ($this->request->server['REQUEST_METHOD'] == 'POST')  {
            $this->model_tnt_tnttestimonial->edittestimonial($this->request->get['tnttestimonialparent_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            $this->response->redirect($this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }
        $this->getForm();
    }

    public function delete() {
        $this->load->language('extension/module/tnttestimonial');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tnt/tnttestimonial');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $tnttestimonialparent_id) {
                $this->model_tnt_tnttestimonial->deletetestimonial($tnttestimonialparent_id);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            $this->getList();
        }
        $this->response->redirect($this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
    }

    public function copy() {
        $this->load->language('extension/module/tnttestimonial');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tnt/tnttestimonial');
        if (isset($this->request->post['selected']) && $this->validateCopy()) {
            foreach ($this->request->post['selected'] as $tnttestimonialparent_id) {
                $this->model_tnt_tnttestimonial->copytestimonial($tnttestimonialparent_id);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            $this->getList();
        }
        $this->response->redirect($this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
    }

    public function getList() {
        $this->load->model('setting/module');
        $this->load->model('tnt/tnttestimonial');
        $this->load->model('localisation/language');
        $this->load->model('tool/image');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatesetting()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('tnttestimonial', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }
        $this->load->language('extension/module/tnttestimonial');
        $data['column_heading']      = $this->language->get('column_heading');
        $data['column_action']       = $this->language->get('column_action');
        $data['column_name']         = $this->language->get('column_name');
        $data['column_description']  = $this->language->get('column_description');
        $data['column_status']       = $this->language->get('column_status');
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }
        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = '';
        }
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'pd.name';
        }
        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }
        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
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
                'href' => $this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . $url, true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }
        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/tnttestimonial/getList', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/tnttestimonial/getList', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }
        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
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
        if (isset($this->request->post['tnttestimonial_parentsettingdata'])) {
            $data['tnttestimonial_parentsettingdata'] = $this->request->post['tnttestimonial_parentsettingdata'];
            foreach ($this->request->post['tnttestimonial_parentsettingdata'] as $key => $value) {
                if($value['image']){
                    $data['image'][$key] =  $this->model_tool_image->resize($value['image'], 100, 100);               
                }else{
                    $data['image'][$key] =  $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        } elseif (!empty($module_info)) {
            $data['tnttestimonial_parentsettingdata'] = $module_info['tnttestimonial_parentsettingdata'];
            foreach ($module_info['tnttestimonial_parentsettingdata'] as $key => $value) {
                $data['image'][$key] =  $this->model_tool_image->resize($value['image'], 100, 100);               
            }
            
        } else {
            foreach ($data['languages'] as $key => $value) {
                $data['image'][$value['language_id']] =  $this->model_tool_image->resize('no_image.png', 100, 100);              
            }
            $data['tnttestimonial_parentsettingdata'] = array();
        }
        $data['add']    = $this->url->link('extension/module/tnttestimonial/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['copy']   = $this->url->link('extension/module/tnttestimonial/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('extension/module/tnttestimonial/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['testimonialdata'] = array();
        $filter_data = array(
            'filter_name'     => $filter_name,
            'filter_status'   => $filter_status,
            'sort'            => $sort,
            'order'           => $order,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );
        $testimonial_total = $this->model_tnt_tnttestimonial->getTotaltestimonial($filter_data);
        $results = $this->model_tnt_tnttestimonial->gettestimonial($filter_data);
        foreach ($results as $result) {

            $image = $this->model_tool_image->resize($result['tnttestimonialparent_image'], 100, 100);
            $data['testimonialdata'][] = array(
                'id'                => $result['tnttestimonialparent_id'],
                'title'             => $result['tnttestimonialchild_name'],
                'description'       => $result['tnttestimonialchild_description'],
                'link'              => $result['tnttestimonialparent_link'],
                'image'             => $image,
                'designation'       => $result['tnttestimonialchild_designation'],
                'status'            => $result['tnttestimonialparent_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'language_id'       => (int)$this->config->get('config_language_id'),
                'edit'              => $this->url->link('extension/module/tnttestimonial/edit', 'user_token=' . $this->session->data['user_token'] . '&tnttestimonialparent_id=' . $result['tnttestimonialparent_id'] . $url, true)
            );
        }
        $data['user_token'] = $this->session->data['user_token'];
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }
        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['sort_tnttestimonialchild_name'] = $this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tnttestimonialchild_name' . $url, true);
        $data['sort_tnttestimonialchild_description'] = $this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tnttestimonialchild_description' . $url, true);
        $data['sort_tnttestimonialchild_designation'] = $this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tnttestimonialchild_designation' . $url, true);
        $data['sort_tnttestimonialparent_status'] = $this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=p.tnttestimonialparent_status' . $url, true);
        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        $pagination         = new Pagination();
        $pagination->total  = $testimonial_total;
        $pagination->page   = $page;
        $pagination->limit  = $this->config->get('config_limit_admin');
        $pagination->url    = $this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
        $data['pagination'] = $pagination->render();
        $data['results']    = sprintf($this->language->get('text_pagination'), ($testimonial_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($testimonial_total - $this->config->get('config_limit_admin'))) ? $testimonial_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $testimonial_total, ceil($testimonial_total / $this->config->get('config_limit_admin')));
        $data['filter_name']    = $filter_name;
        $data['filter_status']  = $filter_status;
        $data['sort']           = $sort;
        $data['order']          = $order;
        $data['sortdata']       = $this->url->link('extension/module/tnttestimonial/sortdata', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/tnttestimonial_list', $data));
    }

    protected function getForm() {
        $this->load->model('tool/image');
        $data['text_form']          = !isset($this->request->get['tnttestimonialparent_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_form_parent']     = !isset($this->request->get['tnttestimonialparent_id']) ? $this->language->get('text_parent_add') : $this->language->get('text_parent_edit');
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
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
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );
        if (!isset($this->request->get['tnttestimonialparent_id'])) {
            $data['action'] = $this->url->link('extension/module/tnttestimonial/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/tnttestimonial/edit', 'user_token=' . $this->session->data['user_token'] . '&tnttestimonialparent_id=' . $this->request->get['tnttestimonialparent_id'] . $url, true);
        }
        $data['cancel'] = $this->url->link('extension/module/tnttestimonial', 'user_token=' . $this->session->data['user_token'] . $url, true);
        if (isset($this->request->get['tnttestimonialparent_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $testimonial_info = $this->model_tnt_tnttestimonial->gettestimonialsingle($this->request->get['tnttestimonialparent_id']);
        }
        $data['user_token'] = $this->session->data['user_token'];
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        if (isset($this->request->post['tnttestimonial'])) {
            $data['tnttestimonial'] = $this->request->post['tnttestimonial'];
        } elseif (!empty($testimonial_info)) {
            $editdata = array();
            foreach ($testimonial_info as $key => $value) {
                $editdata[$value['tnttestimonialchildlanguage_id']] = $value;
            }
            $data['tnttestimonial'] = $editdata;
        } else {
            $data['tnttestimonial'] = array();
        }
        if (isset($this->request->post['tnttestimonialparent_status'])) {
            $data['tnttestimonialparent_status'] = $this->request->post['tnttestimonialparent_status'];
        } elseif (!empty($testimonial_info)) {
            $data['tnttestimonialparent_status'] = $testimonial_info[0]['tnttestimonialparent_status'];
        } else {
            $data['tnttestimonialparent_status'] = "";
        }
        if (isset($this->request->post['tnttestimonialparent_link'])) {
            $data['tnttestimonialparent_link'] = $this->request->post['tnttestimonialparent_link'];
        } elseif (!empty($testimonial_info)) {
            $data['tnttestimonialparent_link'] = $testimonial_info[0]['tnttestimonialparent_link'];
        } else {
            $data['tnttestimonialparent_link'] = "";
        }
        $data['placeholder']    = $this->model_tool_image->resize('no_image.png', 100, 100);
        if (isset($this->request->post['tnttestimonialparent_image'])) {
            $data['tnttestimonialparent_image'] = $this->request->post['tnttestimonialparent_image'];

            if(!empty($data['tnttestimonialparent_image'])){
                $data['image_thumb'] = $this->model_tool_image->resize($data['tnttestimonialparent_image'], 100, 100);
            }else{
                $data['image_thumb'] = $data['placeholder'];
            }

        } elseif (!empty($testimonial_info)) {
            $data['tnttestimonialparent_image'] = $testimonial_info[0]['tnttestimonialparent_image'];

            if(!empty($testimonial_info[0]['tnttestimonialparent_image'])){
                $data['image_thumb'] = $this->model_tool_image->resize($testimonial_info[0]['tnttestimonialparent_image'], 100, 100);
            }else{
                $data['image_thumb'] = $data['placeholder'];
            }
                    
        } else {
            $data['tnttestimonialparent_image'] = "";
            $data['image_thumb'] = $data['placeholder'];

        }

        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/tnttestimonial_form', $data));
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/tnttestimonial')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    protected function validateCopy() {
        if (!$this->user->hasPermission('modify', 'extension/module/tnttestimonial')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    protected function validatesetting() {
        $this->load->language('extension/module/tnttestimonial');
        if (!$this->user->hasPermission('modify', 'extension/module/tnttestimonial')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        return !$this->error;
    }
}