<?php
class ControllerExtensionModuletntblog extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/module/tntblog');
$this->session->data['module_id'] = $this->request->get['module_id'];
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntblog');

        $this->getList();
    }

    public function install(){
        $parent           = array();
        $parent['name']   = "Blog Post";
        $parent['status'] = 1;
        
        $this->load->model('setting/module');
        $this->load->model('localisation/language');
        $this->load->model('tnt/tntblog');

        $languages = $this->model_localisation_language->getLanguages();
        
        foreach ($languages as $value) {
            $parent['tntblog_parent'][$value['language_id']] = array('title' => "Latest Blog");
        }

        $this->model_setting_module->addModule('tntblog', $parent);

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntblog_comment` 
        (   `tntblog_comment_id` int(11) AUTO_INCREMENT,
            `tntblog_id` INT NOT NULL,
            `tntblog_comment_name` VARCHAR(255),
            `tntblog_comment_email` VARCHAR(255),
            `tntblog_coment_url` VARCHAR(255),
            `tntblog_comment_subject` VARCHAR(255),
            `tntblog_comment_comment` TEXT,
            `tntblog_comment_createdate` datetime,
            `tntblog_comment_position` int(11),
            `tntblog_comment_status` INT NOT NULL ,
        PRIMARY KEY (`tntblog_comment_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntblog_parent` 
        (   `tntblog_parent_id` int(11) AUTO_INCREMENT,
            `tntblog_parent_status` INT  NOT NULL ,
            `tntblog_parent_position` int(11),
            `tntblog_parent_positionttype` VARCHAR(255),
            `tntblog_parent_featureimage` VARCHAR(255),
            `tntblog_parent_deafultcategory` int(11),
            `tntblog_parent_url` VARCHAR(255),
            `tntblog_parent_video` VARCHAR(255),
            `tntblog_parent_commentstatus` int(11),
            `tntblog_parent_createdate` datetime,
        PRIMARY KEY (`tntblog_parent_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntblog_child` 
        (   `tntblog_child_id` INT NOT NULL AUTO_INCREMENT,
            `tntblog_child_languages_id` INT NOT NULL,
            `tntblog_parent_id` INT NOT NULL,
            `tntblog_child_title` VARCHAR(255),
            `tntblog_child_excerpt` VARCHAR(255),
            `tntblog_child_content` VARCHAR(255),
            `tntblog_child_metatitle` TEXT,
            `tntblog_child_metatag` TEXT,
            `tntblog_child_meta_description` TEXT,
            `tntblog_child_metakeyword` TEXT,
        PRIMARY KEY (`tntblog_child_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntblog_gallery` 
        (   `tntblog_gallery_id` int(11) AUTO_INCREMENT,
            `tntblog_id` INT NOT NULL,
            `image` VARCHAR(255),
        PRIMARY KEY (`tntblog_gallery_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

       
        $limit = 5;
        for ($i = 1; $i<=$limit; $i++) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblog_parent`
            SET 
                tntblog_parent_status                = 1,    
                tntblog_parent_positionttype         = "standrad",
                tntblog_parent_featureimage     = "catalog/themefactory/blog/'.$i.'.jpg",
                tntblog_parent_deafultcategory  = 1,
                tntblog_parent_url       = "#",
                tntblog_parent_video            = "",
                tntblog_parent_commentstatus    = 1,
                tntblog_parent_createdate          = NOW(),
                tntblog_parent_position              = '.$i.'');
            foreach ($languages as $value) {
                $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblog_child`
                SET 
                    tntblog_parent_id           = '.$i.',
                    tntblog_child_title         = "you avoid making basic mistakes",
                    tntblog_child_excerpt       = "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                    tntblog_child_content       = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim adminim veniam,",
                    tntblog_child_metatitle     = "you avoid making basic mistakes",
                    tntblog_child_metatag       = "",
                    tntblog_child_meta_description       = "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                    tntblog_child_metakeyword   = "new,last,old",
                    tntblog_child_languages_id        = '.$value['language_id'].'');
            }
        }
    }

    public function uninstall(){
        $pre = DB_PREFIX;
        $this->db->query("DROP TABLE `{$pre}tntblog_gallery`");
        $this->db->query("DROP TABLE `{$pre}tntblog_parent`");
        $this->db->query("DROP TABLE `{$pre}tntblog_comment`");
        $this->db->query("DROP TABLE `{$pre}tntblog_child`");
    }

    public function ajax() {
        $this->load->model('tnt/tntblog');
        $update_position    = $this->request->get['action'];
        $position           = $this->request->get['recordsArray'];
        $return_data        = array();
        if ($update_position == 'update_position') {
            $return_data['success'] = 'right';
            $this->model_tnt_tntblog->sortdata($position);
            echo $res = implode("##", $return_data);
        }
    }   

    public function add() {
        $this->load->language('extension/module/tntblog');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntblog');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_tnt_tntblog->addrecord($this->request->post);

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

            $this->response->redirect($this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/module/tntblog');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntblog');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_tnt_tntblog->editdatablog($this->request->get['tntblog_parent_id'], $this->request->post);

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

            $this->response->redirect($this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('extension/module/tntblog');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntblog');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {

            foreach ($this->request->post['selected'] as $tntblogmain_id) {
                $this->model_tnt_tntblog->blogdatadelete($tntblogmain_id);
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
        $this->response->redirect($this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
    }

    public function copy() {
        $this->load->language('extension/module/tntblog');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntblog');

        if (isset($this->request->post['selected']) && $this->validateCopy()) {
            foreach ($this->request->post['selected'] as $tntblogmain_id) {
                $this->model_tnt_tntblog->copyblogdata($tntblogmain_id);
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
            $this->response->redirect($this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
    }

    public function getList() {
        
        $this->load->model('setting/module');
        $this->load->model('tnt/tntblog');
        $this->load->model('localisation/language');
        $this->load->model('tool/image');
        $this->load->language('extension/module/tntblog');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatesetting()) {

            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('tntblog', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }

        
        $data['setting_main_title']             = $this->language->get('setting_main_title');
        $data['setting_main_block']             = $this->language->get('setting_main_block');
        $data['text_list']                      = $this->language->get('text_list');
        $data['text_add']                       = $this->language->get('text_add');
        $data['text_edit']                      = $this->language->get('text_edit');
        $data['text_extension']                 = $this->language->get('text_extension');
        $data['entry_name']                     = $this->language->get('entry_name');
        $data['entry_main_title']               = $this->language->get('entry_main_title');
        $data['entry_main_short_des']           = $this->language->get('entry_main_short_des');
        $data['entry_main_des']                 = $this->language->get('entry_main_des');
        $data['entry_main_image']               = $this->language->get('entry_main_image');
        $data['entry_main_block_title']         = $this->language->get('entry_main_block_title');
        $data['entry_main_block_short_des']     = $this->language->get('entry_main_block_short_des');
        $data['entry_main_block_des']           = $this->language->get('entry_main_block_des');
        $data['entry_main_block_btn_cap']       = $this->language->get('entry_main_block_btn_cap');
        $data['entry_main_block_link']          = $this->language->get('entry_main_block_link');
        $data['entry_main_block_link_des']      = $this->language->get('entry_main_block_link_des');
        $data['entry_main_block_image']         = $this->language->get('entry_main_block_image');
        $data['entry_main_block_image_des']     = $this->language->get('entry_main_block_image_des');
        $data['entry_title']                    = $this->language->get('entry_title');
        $data['entry_image']                    = $this->language->get('entry_image');
        $data['entry_status']                   = $this->language->get('entry_status');
        $data['entry_status_des']               = $this->language->get('entry_status_des');
    
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
                'href' => $this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . $url, true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        if (!isset($this->request->get['module_id'])) {
            $data['mainaction'] = $this->url->link('extension/module/tntblog/getList', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['mainaction'] = $this->url->link('extension/module/tntblog/getList', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }
        
        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        $data['add']    = $this->url->link('extension/module/tntblog/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['copy']   = $this->url->link('extension/module/tntblog/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('extension/module/tntblog/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

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

        

        if (isset($this->request->post['tntblog_parent'])) {
            $data['tntblog_parent'] = $this->request->post['tntblog_parent'];
            
        } elseif (!empty($module_info['tntblog_parent'])) {
            $data['tntblog_parent'] = $module_info['tntblog_parent'];
            
        } else {
            $data['tntblog_parent'] = array();
            
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

        $blogpost_total = $this->model_tnt_tntblog->getblogrecordtotal($filter_data);

        $results = $this->model_tnt_tntblog->getblogdatarecord($filter_data);
        foreach ($results as $result) {
            
            $data['blogpostslist'][] = array(
                'id'        => $result['tntblog_parent_id'],
                'title'     => $result['tntblog_child_title'],
                'excerpt'   => $result['tntblog_child_excerpt'],
                'url'       => $result['tntblog_parent_url'],
                'position'  => $result['tntblog_parent_position'],
                'status'    => $result['tntblog_parent_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit'      => $this->url->link('extension/module/tntblog/edit', 'user_token=' . $this->session->data['user_token'] . '&tntblog_parent_id=' . $result['tntblog_parent_id'] . $url, true)
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


        $data['sort_tntblog_child_title'] = $this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntblog_child_title' . $url, true);

        $data['sort_tntblog_child_excerpt'] = $this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntblog_child_excerpt' . $url, true);

        $data['sort_tntblog_parent_url'] = $this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntblog_parent_url' . $url, true);

        
        

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
        $data['ajaxlink'] = $this->url->link('extension/module/tntblog/ajax', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $pagination         = new Pagination();
        $pagination->total  = $blogpost_total;
        $pagination->page   = $page;
        $pagination->limit  = $this->config->get('config_limit_admin');
        $pagination->url    = $this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination']     = $pagination->render();

        $data['results']        = sprintf($this->language->get('text_pagination'), ($blogpost_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($blogpost_total - $this->config->get('config_limit_admin'))) ? $blogpost_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $blogpost_total, ceil($blogpost_total / $this->config->get('config_limit_admin')));

        $data['filter_name']    = $filter_name;
        $data['filter_status']  = $filter_status;
        $data['sort']           = $sort;
        $data['order']          = $order;
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntblog_list', $data));
    }

    protected function getForm() {

        $data['text_form']              = !isset($this->request->get['tntblog_parent_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['entry_title']            = $this->language->get('entry_title');
        $data['entry_main_short_des']   = $this->language->get('entry_main_short_des');
        $data['entry_main_des']         = $this->language->get('entry_main_des');
        $data['entry_main_block_link']  = $this->language->get('entry_main_block_link');
        $data['entry_image']            = $this->language->get('entry_image');
        $data['entry_status']           = $this->language->get('entry_status');
        $data['entry_action']           = $this->language->get('entry_action');


        if (isset($this->error['warning'])) {
            $data['error_warning']  = $this->error['warning'];
        } else {
            $data['error_warning']  = '';
        }

        if (isset($this->error['tntblog_child_title'])) {
            $data['error_title']    = $this->error['tntblog_child_title'];
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
            'href' => $this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );


        if (!isset($this->request->get['tntblog_parent_id'])) {
            $data['action'] = $this->url->link('extension/module/tntblog/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/tntblog/edit', 'user_token=' . $this->session->data['user_token'] . '&tntblog_parent_id=' . $this->request->get['tntblog_parent_id'] . $url, true);
        }

        $data['cancel']     = $this->url->link('extension/module/tntblog', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['tntblog_parent_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $blogpost_info  = $this->model_tnt_tntblog->getsingleblog($this->request->get['tntblog_parent_id']);
        }
        
        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');
        $this->load->model('tool/image');

        $data['languages']      = $this->model_localisation_language->getLanguages();

        $data['placeholder']    = $this->model_tool_image->resize('no_image.png', 100, 100);

        
        if (isset($this->request->post['tntblog_parent_positionttype'])) {
            $data['tntblog_parent_positionttype'] = $this->request->post['tntblog_parent_positionttype'];
        } elseif (!empty($blogpost_info[0]['tntblog_parent_positionttype'])) {
            $data['tntblog_parent_positionttype'] = $blogpost_info[0]['tntblog_parent_positionttype'];
        } else {
            $data['tntblog_parent_positionttype'] = "";
        }

        if (isset($this->request->post['tntblog_parent_featureimage'])) {
            $data['tntblog_parent_featureimage']    = $this->request->post['tntblog_parent_featureimage'];
            $data['featureimage']                   = $this->model_tool_image->resize($data['tntblog_parent_featureimage'], 100, 100);              
        } elseif (!empty($blogpost_info[0]['tntblog_parent_featureimage'])) {
            $data['tntblog_parent_featureimage']    = $blogpost_info[0]['tntblog_parent_featureimage'];
            $data['featureimage']                   = $this->model_tool_image->resize($blogpost_info[0]['tntblog_parent_featureimage'], 100, 100);      
        } else {
            $data['tntblog_parent_featureimage']    = "";
            $data['featureimage']                   = $data['placeholder'];
        }
        if (isset($this->request->post['tntblog_parent_url'])) {
            $data['tntblog_parent_url'] = $this->request->post['tntblog_parent_url'];
        } elseif (!empty($blogpost_info[0]['tntblog_parent_url'])) {
            $data['tntblog_parent_url'] = $blogpost_info[0]['tntblog_parent_url'];
        } else {
            $data['tntblog_parent_url'] = "";
        }

        $data['category_info']  = $this->model_tnt_tntblog->getblogdatarecordcategory();

        if (isset($this->request->post['tntblog_parent_deafultcategory'])) {
            $data['tntblog_parent_deafultcategory'] = $this->request->post['tntblog_parent_deafultcategory'];
        } elseif (!empty($blogpost_info[0]['tntblog_parent_deafultcategory'])) {
            $data['tntblog_parent_deafultcategory'] = $blogpost_info[0]['tntblog_parent_deafultcategory'];
        } else {
            $data['tntblog_parent_deafultcategory'] = "";
        }
        if (isset($this->request->post['tntblog_parent_video'])) {
            $data['tntblog_parent_video'] = $this->request->post['tntblog_parent_video'];
        } elseif (!empty($blogpost_info[0]['tntblog_parent_video'])) {
            $data['tntblog_parent_video'] = $blogpost_info[0]['tntblog_parent_video'];
        } else {
            $data['tntblog_parent_video'] = "";
        }
        if (isset($this->request->post['tntblog_parent_commentstatus'])) {
            $data['tntblog_parent_commentstatus'] = $this->request->post['tntblog_parent_commentstatus'];
        } elseif (!empty($blogpost_info[0]['tntblog_parent_commentstatus'])) {
            $data['tntblog_parent_commentstatus'] = $blogpost_info[0]['tntblog_parent_commentstatus'];
        } else {
            $data['tntblog_parent_commentstatus'] = "";
        }
        if (isset($this->request->post['tntblog_parent_status'])) {
            $data['tntblog_parent_status'] = $this->request->post['tntblog_parent_status'];
        } elseif (!empty($blogpost_info[0]['tntblog_parent_status'])) {
            $data['tntblog_parent_status'] = $blogpost_info[0]['tntblog_parent_status'];
        } else {
            $data['tntblog_parent_status'] = "";
        }
        if (isset($this->request->post['gallery'])) {
            $product_images = $this->request->post['gallery'];
        } elseif (isset($this->request->get['tntblog_parent_id'])) {
            $product_images = $this->model_tnt_tntblog->getgalleryImages($this->request->get['tntblog_parent_id']);
        } else {
            $product_images = $data['gallery'] = array();
        }


        $data['gallerys'] = array();

        foreach ($product_images as $product_image) {
            if (is_file(DIR_IMAGE . $product_image['image'])) {
                $image = $product_image['image'];
                $thumb = $product_image['image'];
            } else {
                $image = '';
                $thumb = 'no_image.png';
            }

            $data['gallerys'][] = array(
                'image'      => $image,
                'thumb'      => $this->model_tool_image->resize($thumb, 100, 100)
            );
        }
        

        if (isset($this->request->post['tntblogform'])) {
            $data['tntblogform'] = $this->request->post['tntblogform'];
        } elseif (!empty($blogpost_info)) {
            foreach ($blogpost_info as $key => $value) {
                $editdata[$value['tntblog_child_languages_id']] = $value;
                $data['tntblogform'] = $editdata;
            }
        } else {
            $data['tntblogform'] = array();
        }

        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntblog_form', $data));
    }

    protected function validateForm() {

        if (!$this->user->hasPermission('modify', 'extension/module/tntblog')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        foreach ($this->request->post['tntblogform'] as $language_id => $value) {
            if ((utf8_strlen($value['tntblog_child_title']) < 1) || (utf8_strlen($value['tntblog_child_title']) > 255)) {
                $this->error['tntblog_child_title'][$language_id] = $this->language->get('error_title');
            }   
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/tntblog')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    protected function validateCopy() {
        if (!$this->user->hasPermission('modify', 'extension/module/tntblog')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validatesetting() {
        $this->load->language('extension/module/tntblog');
        if (!$this->user->hasPermission('modify', 'extension/module/tntblog')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        return !$this->error;
    }
}