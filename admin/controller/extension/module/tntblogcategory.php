<?php
class ControllerExtensionModuletntblogcategory extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/module/tntblogcategory');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntblogcategory');
$this->session->data['module_id'] = $this->request->get['module_id'];vvsscsc
        $this->getList();
    }

    public function install(){
        $main           = array();
        $main['name']   = "Blog Category";
        $main['status'] = 1;
        
        $this->load->model('setting/module');
        $this->load->model('localisation/language');
        $this->load->model('tnt/tntblogcategory');

        $languages = $this->model_localisation_language->getLanguages();
        

        $this->model_setting_module->addModule('tntblogcategory', $main);


        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntblogcategory_parent` 
        (   `tntblogcategory_id` int(11) AUTO_INCREMENT,
            `tntblogcategory_status` INT  NOT NULL ,
            `tntblogcategory_position` int(11),
            `tntblogcategory_urlrewrite` VARCHAR(255),
            `tntblogcategory_featureimage` VARCHAR(255),
            `tntblogcategory_deafultcategory` int(11),
        PRIMARY KEY (`tntblogcategory_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntblogcategory_sub` 
        (   `tntblogcategory_sub_id` INT NOT NULL AUTO_INCREMENT,
            `tntblogcategory_sublang_id` INT NOT NULL,
            `tntblogcategory_id` INT NOT NULL,
            `tntblogcategory_sub_title` VARCHAR(255),
            `tntblogcategory_sub_categorydes` VARCHAR(255),
            `tntblogcategory_sub_metatitle` TEXT,
            `tntblogcategory_sub_metades` TEXT,
            `tntblogcategory_sub_metakeyword` TEXT,
        PRIMARY KEY (`tntblogcategory_sub_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
       
        $num_of_data = 4;
        $sub         = array();
        for ($i = 1; $i<=$num_of_data; $i++) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblogcategory_parent`
            SET 
                tntblogcategory_status            = 1,    
                tntblogcategory_featureimage      = "catalog/themefactory/blog/demo_img_'.$i.'.png",
                tntblogcategory_deafultcategory   = 1,
                tntblogcategory_urlrewrite        = "#",
                tntblogcategory_position               = '.$i.'');
            foreach ($languages as $value) {
                $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblogcategory_sub`
                SET 
                    tntblogcategory_id                    = '.$i.',
                    tntblogcategory_sub_title         = "Fashion",
                    tntblogcategory_sub_categorydes       = "",
                    tntblogcategory_sub_metatitle         = "",
                    tntblogcategory_sub_metades           = "",
                    tntblogcategory_sub_metakeyword       = "",
                    tntblogcategory_sublang_id            = '.$value['language_id'].'');
            }
        }
    }

    public function uninstall(){
        $pre = DB_PREFIX;
        $this->db->query("DROP TABLE `{$pre}tntblogcategory_parent`");
        $this->db->query("DROP TABLE `{$pre}tntblogcategory_sub`");
    }

    public function ajax() {
        $this->load->model('tnt/tntblogcategory');
        $update_position    = $this->request->get['action'];
        $position           = $this->request->get['recordsArray'];
        $return_data        = array();
        if ($update_position == 'update_position') {
            $return_data['success'] = 'right';
            $this->model_tnt_tntblogcategory->sortdata($position);
            echo $res = implode("##", $return_data);
        }
    }   

    public function add() {
        $this->load->language('extension/module/tntblogcategory');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntblogcategory');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_tnt_tntblogcategory->addrecord($this->request->post);

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

            $this->response->redirect($this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/module/tntblogcategory');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntblogcategory');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_tnt_tntblogcategory->editdatablog($this->request->get['tntblogcategory_id'], $this->request->post);

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

            $this->response->redirect($this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('extension/module/tntblogcategory');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntblogcategory');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {

            foreach ($this->request->post['selected'] as $tntblogcategorymain_id) {
                $this->model_tnt_tntblogcategory->blogdatadelete($tntblogcategorymain_id);
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
        $this->response->redirect($this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
    }

    public function copy() {
        $this->load->language('extension/module/tntblogcategory');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntblogcategory');

        if (isset($this->request->post['selected']) && $this->validateCopy()) {
            foreach ($this->request->post['selected'] as $tntblogcategorymain_id) {
                $this->model_tnt_tntblogcategory->copyblogdata($tntblogcategorymain_id);
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
            $this->response->redirect($this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
    }

    public function getList() {
        
        $this->load->model('setting/module');
        $this->load->model('tnt/tntblogcategory');
        $this->load->model('localisation/language');
        $this->load->model('tool/image');
        $this->load->language('extension/module/tntblogcategory');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatesetting()) {

            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('tntblogcategory', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }

        
        $data['setting_title']          = $this->language->get('setting_title');
        $data['setting_block']          = $this->language->get('setting_block');
        $data['text_list']              = $this->language->get('text_list');
        $data['text_add']               = $this->language->get('text_add');
        $data['text_edit']              = $this->language->get('text_edit');
        $data['text_extension']         = $this->language->get('text_extension');
        $data['entry_name']             = $this->language->get('entry_name');
        $data['entry_title']            = $this->language->get('entry_title');
        $data['entry_short_des']        = $this->language->get('entry_short_des');
        $data['entry_des']              = $this->language->get('entry_des');
        $data['entry_image']            = $this->language->get('entry_image');
        $data['entry_block_title']      = $this->language->get('entry_block_title');
        $data['entry_block_short_des']  = $this->language->get('entry_block_short_des');
        $data['entry_block_btn_cap']    = $this->language->get('entry_block_btn_cap');
        $data['entry_block_des']        = $this->language->get('entry_block_des');
        $data['entry_block_link']       = $this->language->get('entry_block_link');
        $data['entry_block_link_des']   = $this->language->get('entry_block_link_des');
        $data['entry_block_image']      = $this->language->get('entry_block_image');
        $data['entry_block_image_des']  = $this->language->get('entry_block_image_des');
        $data['entry_title']            = $this->language->get('entry_title');
        $data['entry_image']            = $this->language->get('entry_image');
        $data['entry_status']           = $this->language->get('entry_status');
        $data['entry_status_des']       = $this->language->get('entry_status_des');
    
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
                'href' => $this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . $url, true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        if (!isset($this->request->get['module_id'])) {
            $data['mainaction'] = $this->url->link('extension/module/tntblogcategory/getList', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['mainaction'] = $this->url->link('extension/module/tntblogcategory/getList', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }
        
        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        $data['add']    = $this->url->link('extension/module/tntblogcategory/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['copy']   = $this->url->link('extension/module/tntblogcategory/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('extension/module/tntblogcategory/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

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

        $no_image           = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['languages']  = $this->model_localisation_language->getLanguages();

        

        if (isset($this->request->post['tntblogcategory_parent'])) {
            $data['tntblogcategory_parent'] = $this->request->post['tntblogcategory_parent'];
            
        } elseif (!empty($module_info['tntblogcategory_parent'])) {
            $data['tntblogcategory_parent'] = $module_info['tntblogcategory_parent'];
            
        } else {
            $data['tntblogcategory_parent'] = array();
            
        }
        
        $filter_data = array(
            'filter_name'     => $filter_name,
            'filter_status'   => $filter_status,
            'sort'            => $sort,
            'order'           => $order,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        $this->load->model('tool/image');

        $blogpost_total = $this->model_tnt_tntblogcategory->getblogrecordtotal($filter_data);

        $results = $this->model_tnt_tntblogcategory->getblogdatarecord($filter_data);
        foreach ($results as $result) {
            
            $data['blogpostslist'][] = array(
                'id'        => $result['tntblogcategory_id'],
                'title'     => $result['tntblogcategory_sub_title'],
                'excerpt'   => $result['tntblogcategory_sub_categorydes'],
                'url'       => $result['tntblogcategory_urlrewrite'],
                'position'  => $result['tntblogcategory_position'],
                'status'    => $result['tntblogcategory_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit'      => $this->url->link('extension/module/tntblogcategory/edit', 'user_token=' . $this->session->data['user_token'] . '&tntblogcategory_id=' . $result['tntblogcategory_id'] . $url, true)
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


        $data['sort_tntblogcategory_sub_title'] = $this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntblogcategory_sub_title' . $url, true);

        $data['sort_tntblogcategory_sub_categorydes'] = $this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntblogcategory_sub_categorydes' . $url, true);

        $data['sort_tntblogcategory_urlrewrite'] = $this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntblogcategory_urlrewrite' . $url, true);

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
        $data['ajaxlink'] = $this->url->link('extension/module/tntblogcategory/ajax', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $pagination             = new Pagination();
        $pagination->total      = $blogpost_total;
        $pagination->page       = $page;
        $pagination->limit      = $this->config->get('config_limit_admin');
        $pagination->url        = $this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination']     = $pagination->render();

        $data['results']        = sprintf($this->language->get('text_pagination'), ($blogpost_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($blogpost_total - $this->config->get('config_limit_admin'))) ? $blogpost_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $blogpost_total, ceil($blogpost_total / $this->config->get('config_limit_admin')));

        $data['filter_name']    = $filter_name;
        $data['filter_status']  = $filter_status;
        $data['sort']           = $sort;
        $data['order']          = $order;
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntblogcategory_list', $data));
    }

    protected function getForm() {

        $data['text_form']              = !isset($this->request->get['tntblogcategory_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['entry_title']            = $this->language->get('entry_title');
        $data['entry_short_des']        = $this->language->get('entry_short_des');
        $data['entry_des']              = $this->language->get('entry_des');
        $data['entry_block_link']       = $this->language->get('entry_block_link');
        $data['entry_image']            = $this->language->get('entry_image');
        $data['entry_status']           = $this->language->get('entry_status');
        $data['entry_action']           = $this->language->get('entry_action');


        if (isset($this->error['warning'])) {
            $data['error_warning']  = $this->error['warning'];
        } else {
            $data['error_warning']  = '';
        }

        if (isset($this->error['tntblogcategory_sub_title'])) {
            $data['error_title']    = $this->error['tntblogcategory_sub_title'];
        } else {
            $data['error_title']    = array();
        }
        


        $url                        = '';

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

        $data['breadcrumbs']   = array();

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
            'href' => $this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );


        if (!isset($this->request->get['tntblogcategory_id'])) {
            $data['action'] = $this->url->link('extension/module/tntblogcategory/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/tntblogcategory/edit', 'user_token=' . $this->session->data['user_token'] . '&tntblogcategory_id=' . $this->request->get['tntblogcategory_id'] . $url, true);
        }

        $data['cancel']     = $this->url->link('extension/module/tntblogcategory', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['tntblogcategory_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $blogpost_info  = $this->model_tnt_tntblogcategory->getsingleblog($this->request->get['tntblogcategory_id']);
        }
        
        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');
        $this->load->model('tool/image');

        $data['languages']      = $this->model_localisation_language->getLanguages();

        $data['placeholder']    = $this->model_tool_image->resize('no_image.png', 100, 100);

        
        

        if (isset($this->request->post['tntblogcategory_featureimage'])) {
            $data['tntblogcategory_featureimage']     = $this->request->post['tntblogcategory_featureimage'];
            $data['featureimage']                   = $this->model_tool_image->resize($data['tntblogcategory_featureimage'], 100, 100);               
        } elseif (!empty($blogpost_info[0]['tntblogcategory_featureimage'])) {
            $data['featureimage'] = $this->model_tool_image->resize($blogpost_info[0]['tntblogcategory_featureimage'], 100, 100);             
            $data['tntblogcategory_featureimage']     = $blogpost_info[0]['tntblogcategory_featureimage'];
        } else {
            $data['tntblogcategory_featureimage']     = "";
            $data['featureimage']                   = $data['placeholder'];
        }
        if (isset($this->request->post['tntblogcategory_urlrewrite'])) {
            $data['tntblogcategory_urlrewrite'] = $this->request->post['tntblogcategory_urlrewrite'];
        } elseif (!empty($blogpost_info[0]['tntblogcategory_urlrewrite'])) {
            $data['tntblogcategory_urlrewrite'] = $blogpost_info[0]['tntblogcategory_urlrewrite'];
        } else {
            $data['tntblogcategory_urlrewrite'] = "";
        }

        $data['category_info']  = $this->model_tnt_tntblogcategory->getblogdatarecord();
        if (isset($this->request->post['tntblogcategory_deafultcategory'])) {
            $data['tntblogcategory_deafultcategory'] = $this->request->post['tntblogcategory_deafultcategory'];
        } elseif (!empty($blogpost_info[0]['tntblogcategory_deafultcategory'])) {
            $data['tntblogcategory_deafultcategory'] = $blogpost_info[0]['tntblogcategory_deafultcategory'];
        } else {
            $data['tntblogcategory_deafultcategory'] = "";
        }
        
        
        if (isset($this->request->post['tntblogcategory_status'])) {
            $data['tntblogcategory_status'] = $this->request->post['tntblogcategory_status'];
        } elseif (!empty($blogpost_info[0]['tntblogcategory_status'])) {
            $data['tntblogcategory_status'] = $blogpost_info[0]['tntblogcategory_status'];
        } else {
            $data['tntblogcategory_status'] = "";
        }
        


        if (isset($this->request->post['tntblogcategoryform'])) {
            $data['tntblogcategoryform'] = $this->request->post['tntblogcategoryform'];
        } elseif (!empty($blogpost_info)) {
            foreach ($blogpost_info as $key => $value) {
                $editdata[$value['tntblogcategory_sublang_id']] = $value;
                $data['tntblogcategoryform'] = $editdata;
            }
        } else {
            $data['tntblogcategoryform'] = array();
        }

        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntblogcategory_form', $data));
    }

    protected function validateForm() {

        if (!$this->user->hasPermission('modify', 'extension/module/tntblogcategory')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        foreach ($this->request->post['tntblogcategoryform'] as $language_id => $value) {
            if ((utf8_strlen($value['tntblogcategory_sub_title']) < 1) || (utf8_strlen($value['tntblogcategory_sub_title']) > 255)) {
                $this->error['tntblogcategory_sub_title'][$language_id] = $this->language->get('error_title');
            }   
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/tntblogcategory')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    protected function validateCopy() {
        if (!$this->user->hasPermission('modify', 'extension/module/tntblogcategory')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validatesetting() {
        $this->load->language('extension/module/tntblogcategory');
        if (!$this->user->hasPermission('modify', 'extension/module/tntblogcategory')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        return !$this->error;
    }
}