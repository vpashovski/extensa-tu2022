<?php
class ControllerExtensionModuletntslider extends Controller {
    private $error = array();

    public function index() {
$this->session->data['module_id'] = $this->request->get['module_id'];
        $this->load->language('extension/module/tntslider');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntslider');

        $this->sliderList();

    }

    public function install(){

        $parent           = array();

        $parent['name']   = "Slider";

        $parent['status'] = 1;

        $parent['speed']  = 5000;

        $parent['hover']  = 1;

        $parent['loop']   = 1;

        $parent['animationslider'] = 1;



        $this->load->model('setting/module');

        $this->load->model('localisation/language');

        $this->load->model('tnt/tntslider');



        $languages = $this->model_localisation_language->getLanguages();



        $this->model_setting_module->addModule('tntslider', $parent);

        

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntsliderparent` 

        (   `tntsliderparent_id` int(11) AUTO_INCREMENT,

            `tntsliderparent_position` int(11),

        PRIMARY KEY (`tntsliderparent_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");



        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntsliderchild` 

        (   `tntsliderchild_id` INT NOT NULL AUTO_INCREMENT ,

            `tntsliderparent_id` INT NOT NULL ,

            `tntsliderchild_link` VARCHAR(255),

            `tntsliderchild_image` VARCHAR(255),

            `tntsliderchild_title` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,

            `tntsliderchild_subtitle` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,

            `tntsliderchild_textaligment` VARCHAR(255),

            `tntsliderchild_buttontext` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,

            `tntsliderchild_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,

            `tntsliderchild_enable` INT NOT NULL ,

            `tntsliderchildlang_id` INT NOT NULL ,

        PRIMARY KEY (`tntsliderchild_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");



        $num_of_data = 2;

        $sub         = array();



        for ($i = 1; $i<=$num_of_data; $i++) {

            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntsliderparent`

            SET      

                tntsliderparent_position        = '.$i.''); 

            foreach ($languages as $value) {

                $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntsliderchild`

                SET 

                            tntsliderparent_id             = '.$i.',

                            tntsliderchild_link            = "#",

                            tntsliderchild_description     = "Aenean quam neque, ullamcorper eget dui ut,",

                            tntsliderchild_image           = "catalog/themefactory/slider/'.$i.'.jpg",

                            tntsliderchild_title           = "The Most Healthy",

                            tntsliderchild_subtitle        = "Organic Foods",

                            tntsliderchild_textaligment    = "hide",

                            tntsliderchild_buttontext      = "Read More",

                            tntsliderchild_enable          = "1",

                            tntsliderchildlang_id          = '.$value['language_id'].'');

            }

        }



    }



    public function uninstall(){

        $pre = DB_PREFIX;

        $this->db->query("DROP TABLE `{$pre}tntsliderparent`");

        $this->db->query("DROP TABLE `{$pre}tntsliderchild`");

    }



    public function sorting() {

        $this->load->model('tnt/tntslider');

        $editdataposition   = $this->request->get['action'];

        $position           = $this->request->get['recordsArray'];

        $return_data        = array();

        if ($editdataposition == 'editdataposition') {

            $return_data['success'] = 'right';

            $this->model_tnt_tntslider->sortingdata($position);

            echo $res = implode("##", $return_data);

        }

    }   



    public function add() {

        $this->load->language('extension/module/tntslider');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntslider');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_tnt_tntslider->insertrecord($this->request->post);

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

            $this->response->redirect($this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . $url, true));

        }



        $this->getForm();

    }



    public function edit() {

        $this->load->language('extension/module/tntslider');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntslider');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_tnt_tntslider->editimageslider($this->request->get['tntsliderparent_id'], $this->request->post);

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

            $this->response->redirect($this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . $url, true));

        }

        $this->getForm();

    }



    public function delete() {

        $this->load->language('extension/module/tntslider');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntslider');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {

            foreach ($this->request->post['selected'] as $tntsliderparent_id) {

                $this->model_tnt_tntslider->deleteimageslider($tntsliderparent_id);

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

            $this->sliderList();

        }

        $this->response->redirect($this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . $url, true));

    }



    public function copy() {

        $this->load->language('extension/module/tntslider');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntslider');

        if (isset($this->request->post['selected']) && $this->validateCopy()) {

            foreach ($this->request->post['selected'] as $tntsliderparent_id) {

                $this->model_tnt_tntslider->copyimageslider($tntsliderparent_id);

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

            $this->sliderList();

        }

        $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

    }



    public function sliderList() {

        $this->load->model('setting/module');

        $this->load->model('tnt/tntslider');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatesetting()) {

            if (!isset($this->request->get['module_id'])) {

                $this->model_setting_module->addModule('tntslider', $this->request->post);

            } else {

                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);

            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

        }

        $this->load->language('extension/module/tntslider');

        

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

                'href' => $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . $url, true)

            );

        } else {

            $data['breadcrumbs'][] = array(

                'text' => $this->language->get('heading_title'),

                'href' => $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)

            );

        }

        if (!isset($this->request->get['module_id'])) {

            $data['action'] = $this->url->link('extension/module/tntslider/sliderList', 'user_token=' . $this->session->data['user_token'] . $url, true);

        } else {

            $data['action'] = $this->url->link('extension/module/tntslider/sliderList', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);

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

        if (isset($this->request->post['speed'])) {

            $data['speed'] = $this->request->post['speed'];

        } elseif (!empty($module_info)) {

            $data['speed'] = $module_info['speed'];

        } else {

            $data['speed'] = "";

        }

        if (isset($this->request->post['hover'])) {

            $data['hover'] = $this->request->post['hover'];

        } elseif (!empty($module_info)) {

            $data['hover'] = $module_info['hover'];

        } else {

            $data['hover'] = "";

        }

        if (isset($this->request->post['loop'])) {

            $data['loop'] = $this->request->post['loop'];

        } elseif (!empty($module_info)) {

            $data['loop'] = $module_info['loop'];

        } else {

            $data['loop'] = "";

        }

        if (isset($this->request->post['animationslider'])) {

            $data['animationslider'] = $this->request->post['animationslider'];

        } elseif (!empty($module_info)) {

            $data['animationslider'] = $module_info['animationslider'];

        } else {

            $data['animationslider'] = "";

        }

        $data['add']    = $this->url->link('extension/module/tntslider/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['copy']   = $this->url->link('extension/module/tntslider/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['delete'] = $this->url->link('extension/module/tntslider/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['sliderdata'] = array();

        $filter_data = array(

            'filter_name'     => $filter_name,

            'filter_status'   => $filter_status,

            'sort'            => $sort,

            'order'           => $order,

            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),

            'limit'           => $this->config->get('config_limit_admin')

        );

        $this->load->model('tool/image');

        $imageslider_total = $this->model_tnt_tntslider->gettotalsliderimage($filter_data);

        $results = $this->model_tnt_tntslider->getsliderimage($filter_data);

        foreach ($results as $result) {

            if (is_file(DIR_IMAGE . $result['tntsliderchild_image'])) {

                $image = $this->model_tool_image->resize($result['tntsliderchild_image'], 40, 40);

            } else {

                $image = $this->model_tool_image->resize('no_image.png', 40, 40);

            }

            $data['sliderdata'][] = array(

                'id'        => $result['tntsliderparent_id'],

                'image'     => $image,

                'title'     => $result['tntsliderchild_title'],

                'subtitle'     => $result['tntsliderchild_subtitle'],

                'aling'     => $result['tntsliderchild_textaligment'],

                'buttontext'    => $result['tntsliderchild_buttontext'],

                'description'   => $result['tntsliderchild_description'],

                'link'      => $result['tntsliderchild_link'],

                'status'    => $result['tntsliderchild_enable'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),

                'edit'      => $this->url->link('extension/module/tntslider/edit', 'user_token=' . $this->session->data['user_token'] . '&tntsliderparent_id=' . $result['tntsliderparent_id'] . $url, true)

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

        $data['sort_tntslider_title'] = $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntsliderchild_title' . $url, true);

        $data['sort_tntslider_subtitle'] = $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntsliderchild_subtitle' . $url, true);

        $data['sort_tntslider_textaligment'] = $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntslider_textaligment' . $url, true);

        $data['sort_tntslider_buttontext'] = $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntslider_buttontext' . $url, true);

        $data['sort_tntslider_description'] = $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntslider_description' . $url, true);

        $data['sort_tntslider_enable'] = $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntslider_enable' . $url, true);

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

        $pagination->total  = $imageslider_total;

        $pagination->page   = $page;

        $pagination->limit  = $this->config->get('config_limit_admin');

        $pagination->url    = $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($imageslider_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($imageslider_total - $this->config->get('config_limit_admin'))) ? $imageslider_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $imageslider_total, ceil($imageslider_total / $this->config->get('config_limit_admin')));

        $data['filter_name']    = $filter_name;

        $data['filter_status']  = $filter_status;

        $data['sort']           = $sort;

        $data['order']          = $order;

        $data['header']         = $this->load->controller('common/header');

        $data['column_left']    = $this->load->controller('common/column_left');

        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntslider_list', $data));

    }



    protected function getForm() {

        $this->load->model('localisation/language');

        if (isset($this->error['warning'])) {

            $data['error_warning'] = $this->error['warning'];

        } else {

            $data['error_warning'] = '';

        }

        if (isset($this->error['tntsliderchild_title'])) {

            $data['error_title'] = $this->error['tntsliderchild_title'];

        } else {

            $data['error_title'] = array();

        }
        if (isset($this->error['tntsliderchild_subtitle'])) {

            $data['error_subtitle'] = $this->error['tntsliderchild_subtitle'];

        } else {

            $data['error_subtitle'] = array();

        }

        if (isset($this->error['tntsliderchild_image'])) {

            $data['error_image'] = $this->error['tntsliderchild_image'];

        } else {

            $data['error_image'] = array();

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

            'href' => $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . $url, true)

        );

        if (!isset($this->request->get['tntsliderparent_id'])) {

            $data['action'] = $this->url->link('extension/module/tntslider/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

        } else {

            $data['action'] = $this->url->link('extension/module/tntslider/edit', 'user_token=' . $this->session->data['user_token'] . '&tntsliderparent_id=' . $this->request->get['tntsliderparent_id'] . $url, true);

        }

        $data['cancel'] = $this->url->link('extension/module/tntslider', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['tntsliderparent_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {

            $imageslider_info = $this->model_tnt_tntslider->getimageslidesingle($this->request->get['tntsliderparent_id']);

        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        if (isset($this->request->post['tntslider'])) {

            $data['tntslider'] = $this->request->post['tntslider'];

            foreach ($this->request->post['tntslider'] as $key => $value) {

                if($value['tntsliderchild_image']){

                    $data['image'][$key] = $this->model_tool_image->resize($value['tntsliderchild_image'], 100, 100);                

                }else{

                    $data['image'][$key] =$data['placeholder']; 

                }

            }

        } elseif (!empty($imageslider_info)) {

            $editdata = array();

            foreach ($imageslider_info as $key => $value) {

                $editdata[$value['tntsliderchildlang_id']] = $value;

            }

            $data['tntslider'] = $editdata;

            foreach ($editdata as $key => $value) {

                $data['image'][$key] =  $this->model_tool_image->resize($value['tntsliderchild_image'], 100, 100);               

            }

        } else {

            foreach ($data['languages'] as $key => $value) {

                $data['image'][$value['language_id']] =$data['placeholder'];

            }

            $data['tntslider'] = array();

        }

        $data['text_form']      = !isset($this->request->get['tntsliderparent_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

       

        $data['header']         = $this->load->controller('common/header');

        $data['column_left']    = $this->load->controller('common/column_left');

        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntslider_form', $data));

    }



    protected function validateForm() {

        if (!$this->user->hasPermission('modify', 'extension/module/tntslider')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }

        foreach ($this->request->post['tntslider'] as $language_id => $value) {

            if ((utf8_strlen($value['tntsliderchild_title']) < 1) || (utf8_strlen($value['tntsliderchild_title']) > 255)) {

                $this->error['tntsliderchild_title'][$language_id] = $this->language->get('error_title');

            }
             if ((utf8_strlen($value['tntsliderchild_subtitle']) < 1) || (utf8_strlen($value['tntsliderchild_subtitle']) > 255)) {

                $this->error['tntsliderchild_subtitle'][$language_id] = $this->language->get('error_subtitle');

            }

            if ((utf8_strlen($value['tntsliderchild_image']) < 1) || (utf8_strlen($value['tntsliderchild_image']) > 255)) {

                $this->error['tntsliderchild_image'][$language_id] = $this->language->get('error_image');

            }           

        }

        if ($this->error && !isset($this->error['warning'])) {

            $this->error['warning'] = $this->language->get('error_warning');

        }

        return !$this->error;

    }

    protected function validateDelete() {

        if (!$this->user->hasPermission('modify', 'extension/module/tntslider')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }

        return !$this->error;

    }

    protected function validateCopy() {

        if (!$this->user->hasPermission('modify', 'extension/module/tntslider')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }



        return !$this->error;

    }

    protected function validatesetting() {

        $this->load->language('extension/module/tntslider');



        if (!$this->user->hasPermission('modify', 'extension/module/tntslider')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {

            $this->error['name'] = $this->language->get('error_name');

        }

        return !$this->error;

    }

}