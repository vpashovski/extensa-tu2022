<?php
class ControllerExtensionModuletntpaymenticon extends Controller {

    private $error = array();

    public function index() {
        $this->session->data['module_id'] = $this->request->get['module_id'];
        $this->load->language('extension/module/tntpaymenticon');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tnt/tntpaymenticon');
        $this->getList();
    }
    public function install(){

        $data           = array();
        $data['name']   = "Payment Icon";
        $data['status'] = 1;
        $this->load->model('setting/module');
        $this->load->model('localisation/language');
        $this->load->model('tnt/tntpaymenticon');
        $languages = $this->model_localisation_language->getLanguages();
        
        $this->model_setting_module->addModule('tntpaymenticon', $data);
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntpaymenticonparent` 
        (   `tntpaymenticonparent_id` int(11) AUTO_INCREMENT,
            `tntpaymenticonparent_status` int(11),
            `tntpaymenticonparent_position` int(11),
            `tntpaymenticonparent_link` VARCHAR(255),
            `tntpaymenticonparent_image` VARCHAR(255),
        PRIMARY KEY (`tntpaymenticonparent_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntpaymenticonchild` 
        (   `tntpaymenticonchild_id` INT NOT NULL AUTO_INCREMENT ,
            `tntpaymenticonchild_title` VARCHAR(255),
            `tntpaymenticonchildlanguage_id` INT NOT NULL ,
            `tntpaymenticonparent_id` INT NOT NULL ,
        PRIMARY KEY (`tntpaymenticonchild_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
        $num_of_data = 1;
        $sub         = array();

        for ($i = 1; $i<=$num_of_data; $i++) {
                $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntpaymenticonparent`
                SET  
                     tntpaymenticonparent_id    = '.$i.',
                    tntpaymenticonparent_position    = '.$i.',
                    
                    tntpaymenticonparent_image  = "catalog/themefactory/payment/'.$i.'.png",
                    tntpaymenticonparent_link   = "#",
                    tntpaymenticonparent_status     = 1;'); 
            foreach ($languages as $value) {
                $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntpaymenticonchild`
                SET 
                    tntpaymenticonparent_id     = '.$i.',
                    tntpaymenticonchild_title   = "title'.$i.'",
                    tntpaymenticonchildlanguage_id  = '.$value['language_id'].'');
            }
        }
    }
    public function uninstall(){
        $pre = DB_PREFIX;
        $this->db->query("DROP TABLE `{$pre}tntpaymenticonparent`");
        $this->db->query("DROP TABLE `{$pre}tntpaymenticonchild`");
    }
    public function sortdata() {
        $data        = array();
        $position    = $this->request->get['dataarray'];
        $sortdata    = $this->request->get['action'];
        if ($sortdata == 'sortdata') {
            $data['success'] = 'right';
            $this->load->model('tnt/tntpaymenticon');
            $this->model_tnt_tntpaymenticon->updatePosition($position);
            echo implode("##", $data);
        }
    }   
    public function add() {
        $this->load->language('extension/module/tntpaymenticon');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tnt/tntpaymenticon');

        if ($this->request->server['REQUEST_METHOD'] == 'POST')  {
            $this->model_tnt_tntpaymenticon->insertrecord($this->request->post);
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
            $this->response->redirect($this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }
        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/module/tntpaymenticon');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tnt/tntpaymenticon');
        if ($this->request->server['REQUEST_METHOD'] == 'POST')  {
            $this->model_tnt_tntpaymenticon->editpaymenticon($this->request->get['tntpaymenticonparent_id'], $this->request->post);
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
            $this->response->redirect($this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }
        $this->getForm();
    }

    public function delete() {
        $this->load->language('extension/module/tntpaymenticon');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tnt/tntpaymenticon');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $tntpaymenticonparent_id) {
                $this->model_tnt_tntpaymenticon->deletepaymenticon($tntpaymenticonparent_id);
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
        $this->response->redirect($this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
    }

    public function copy() {
        $this->load->language('extension/module/tntpaymenticon');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tnt/tntpaymenticon');
        if (isset($this->request->post['selected']) && $this->validateCopy()) {
            foreach ($this->request->post['selected'] as $tntpaymenticonparent_id) {
                $this->model_tnt_tntpaymenticon->getpagetpaymenticon($tntpaymenticonparent_id);
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

        $this->response->redirect($this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
    }

    public function getList() {
        $this->load->model('tool/image');
        $this->load->model('tnt/tntpaymenticon');
        $this->load->model('setting/module');
        $this->load->model('localisation/language');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatesetting()) {

            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('tntpaymenticon', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }

        $this->load->language('extension/module/tntpaymenticon');

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
                'href' => $this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . $url, true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/tntpaymenticon/getList', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/tntpaymenticon/getList', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        $data['languages']     = $this->model_localisation_language->getLanguages();
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

       
        $data['add']    = $this->url->link('extension/module/tntpaymenticon/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['copy']   = $this->url->link('extension/module/tntpaymenticon/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('extension/module/tntpaymenticon/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['paymenticons'] = array();

        $filter_data = array(
            'filter_name'     => $filter_name,
            'filter_status'   => $filter_status,
            'sort'            => $sort,
            'order'           => $order,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        $paymenticon_total = $this->model_tnt_tntpaymenticon->gettotalpaymenticon($filter_data);

        $results = $this->model_tnt_tntpaymenticon->getpaymenticon($filter_data);

        foreach ($results as $result) {
            
            $data['paymenticons'][] = array(
                'id'            => $result['tntpaymenticonparent_id'],
                'language_id'   => (int)$this->config->get('config_language_id'),
                'title'         => $result['tntpaymenticonchild_title'],
                'image'         => $this->model_tool_image->resize($result['tntpaymenticonparent_image'], 100, 100),
                'link'          => $result['tntpaymenticonparent_link'],
                'status'        => $result['tntpaymenticonparent_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit'          => $this->url->link('extension/module/tntpaymenticon/edit', 'user_token=' . $this->session->data['user_token'] . '&tntpaymenticonparent_id=' . $result['tntpaymenticonparent_id'] . $url, true)
            );
             
        }
        $data['user_token']         = $this->session->data['user_token'];

        if (isset($this->error['warning'])) {
            $data['error_warning']  = $this->error['warning'];
        } else {
            $data['error_warning']  = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success']        = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success']        = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected']       = (array)$this->request->post['selected'];
        } else {
            $data['selected']       = array();
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

        $data['sort_tntpaymenticonchild_title'] = $this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntpaymenticonchild_title' . $url, true);

        $data['sort_tntpaymenticonparent_link'] = $this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . '&sort=p.tntpaymenticonparent_link' . $url, true);

        $data['sort_tntpaymenticonparent_status'] = $this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . '&sort=p.tntpaymenticonparent_status' . $url, true);

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
        $pagination->total  = $paymenticon_total;
        $pagination->page   = $page;
        $pagination->limit  = $this->config->get('config_limit_admin');
        $pagination->url    = $this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($paymenticon_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($paymenticon_total - $this->config->get('config_limit_admin'))) ? $paymenticon_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $paymenticon_total, ceil($paymenticon_total / $this->config->get('config_limit_admin')));

        $data['filter_name']    = $filter_name;
        $data['filter_status']  = $filter_status;
        $data['sort']           = $sort;
        $data['order']          = $order;
         
        $data['sortdatalink']   = $this->url->link('extension/module/tntpaymenticon/sortdata', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntpaymenticon_list', $data));
    }

    protected function getForm() {

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
            'href' => $this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['tntpaymenticonparent_id'])) {
            $data['action'] = $this->url->link('extension/module/tntpaymenticon/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/tntpaymenticon/edit', 'user_token=' . $this->session->data['user_token'] . '&tntpaymenticonparent_id=' . $this->request->get['tntpaymenticonparent_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('extension/module/tntpaymenticon', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['tntpaymenticonparent_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $paymenticon_data = $this->model_tnt_tntpaymenticon->getpaymenticonsingle($this->request->get['tntpaymenticonparent_id']);
        }
        
        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('tool/image');


        if (isset($this->request->post['tntpaymenticon'])) {
            $data['tntpaymenticon'] = $this->request->post['tntpaymenticon'];
        } elseif (!empty($paymenticon_data)) {
            $editrecord = array();
            foreach ($paymenticon_data as $key => $value) {
                $editrecord[$value['tntpaymenticonchildlanguage_id']] = $value;
            }
            $data['tntpaymenticon'] = $editrecord;
        } else {
            $data['tntpaymenticon'] = array();
        }

        if (isset($this->request->post['tntpaymenticonparent_status'])) {
            $data['tntpaymenticonparent_status'] = $this->request->post['tntpaymenticonparent_status'];
        } elseif (!empty($paymenticon_data)) {
            $data['tntpaymenticonparent_status'] = $paymenticon_data[0]['tntpaymenticonparent_status'];
        } else {
            $data['tntpaymenticonparent_status'] = array();
        }

        if (isset($this->request->post['tntpaymenticonparent_link'])) {
            $data['tntpaymenticonparent_link'] = $this->request->post['tntpaymenticonparent_link'];
        } elseif (!empty($paymenticon_data)) {
            $data['tntpaymenticonparent_link'] = $paymenticon_data[0]['tntpaymenticonparent_link'];
        } else {
            $data['tntpaymenticonparent_link'] = "";
        }

        if (isset($this->request->post['tntpaymenticonparent_image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['tntpaymenticonparent_image'],100,100);
            $data['tntpaymenticonparent_image'] = $this->request->post['tntpaymenticonparent_image'];
        } elseif (!empty($paymenticon_data)) {
            if(isset($paymenticon_data[0]['tntpaymenticonparent_image'])){
                $data['thumb'] = $this->model_tool_image->resize($paymenticon_data[0]['tntpaymenticonparent_image'], 100, 100);
                $data['tntpaymenticonparent_image'] = $paymenticon_data[0]['tntpaymenticonparent_image'];
            }else{
                $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
                $data['tntpaymenticonparent_image'] = "";
            }
        } else {
            $data['thumb'] =  $this->model_tool_image->resize('no_image.png', 100, 100);
            $data['tntpaymenticonparent_image'] = "";
        }
        $data['placeholder']    = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntpaymenticon_form', $data));
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/tntpaymenticon')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    protected function validateCopy() {
        if (!$this->user->hasPermission('modify', 'extension/module/tntpaymenticon')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
    protected function validatesetting() {
        $this->load->language('extension/module/tntpaymenticon');
        if (!$this->user->hasPermission('modify', 'extension/module/tntpaymenticon')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        return !$this->error;
    }
}