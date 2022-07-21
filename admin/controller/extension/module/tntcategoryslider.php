<?php

class ControllerExtensionModuletntcategoryslider extends Controller {



    private $error = array();



    public function index() {

        $this->load->language('extension/module/tntcategoryslider');
$this->session->data['module_id'] = $this->request->get['module_id'];


        $this->document->setTitle($this->language->get('heading_title'));



        $this->load->model('tnt/tntcategoryslider');



        $this->getList();

    }

    public function install(){



        $settingdata           = array();

        $settingdata['name']   = "Category Slider";

        $settingdata['status'] = 1;



        $this->load->model('tnt/tntcategoryslider');

        $this->load->model('setting/module');

        $this->load->model('localisation/language');



        

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntcategorysliderparent` 

        (   `tntcategorysliderparent_id` int(11) AUTO_INCREMENT,

            `tntcategorysliderparent_category_id` int(11),

            `tntcategorysliderparent_position` int(11),

            `tntcategorysliderparent_image` VARCHAR(100),

            `tntcategorysliderparent_status` int(11),

        PRIMARY KEY (`tntcategorysliderparent_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");



        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntcategorysliderchild` 

        (   `tntcategorysliderchild_id` INT NOT NULL AUTO_INCREMENT ,

            `tntcategorysliderparent_id` INT NOT NULL ,

            `tntcategorysliderchildlanguage_id` INT NOT NULL ,

            `tntcategorysliderchild_name` VARCHAR(255),

            `tntcategorysliderchild_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,

        PRIMARY KEY (`tntcategorysliderchild_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");



        $limit = 6;

        $categorydata    = $this->model_tnt_tntcategoryslider->getCategories();

        $languages   = $this->model_localisation_language->getLanguages();

        foreach ($languages as $value) {

            $settingdata['tntcategoryslider_parent'][$value['language_id']] =  array('title'=>"Shop by Categories",'subtitle'=>"Fresh & Silky Daily");

        }

        $this->model_setting_module->addModule('tntcategoryslider', $settingdata);

        for ($i = 1; $i<=$limit; $i++) {

            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntcategorysliderparent`

            SET         tntcategorysliderparent_category_id = "'.$categorydata[$i]['category_id'].'" ,

                        tntcategorysliderparent_image       = "catalog/themefactory/categoryslider/'.$i.'.jpg",

                        tntcategorysliderparent_position    = '.$i.',

                        tntcategorysliderparent_status      = 1;'); 

            foreach ($languages as $value) {

                $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntcategorysliderchild`

                SET 

                            tntcategorysliderparent_id          = '.$i.',

                            tntcategorysliderchild_name         = "Women T-Shirt",

                            tntcategorysliderchild_description  = "Women T-Shirt",

                            tntcategorysliderchildlanguage_id   = '.$value['language_id'].'');

            }

        }



    }

    public function uninstall(){

        $pre = DB_PREFIX;

        $this->db->query("DROP TABLE `{$pre}tntcategorysliderparent`");

        $this->db->query("DROP TABLE `{$pre}tntcategorysliderchild`");

    }

    public function sortdata() {

        $position   = $this->request->get['data'];

        $edit       = $this->request->get['action'];

        $data       = array();

        if ($edit == 'update') {

            $data['success'] = 'right';

            $this->load->model('tnt/tntcategoryslider');

            $this->model_tnt_tntcategoryslider->updatePosition($position);

            echo $res = implode("##", $data);

        }

    }

    public function add() {

        $this->load->language('extension/module/tntcategoryslider');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntcategoryslider');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_tnt_tntcategoryslider->add($this->request->post);

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

            $this->response->redirect($this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

        }



        $this->getForm();

    }

    public function edit() {

        $this->load->language('extension/module/tntcategoryslider');



        $this->document->setTitle($this->language->get('heading_title'));



        $this->load->model('tnt/tntcategoryslider');



        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            

            $this->model_tnt_tntcategoryslider->editcategoryslider($this->request->get['tntcategorysliderparent_id'], $this->request->post);



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



            $this->response->redirect($this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

        }



        $this->getForm();

    }

    public function delete() {

        $this->load->language('extension/module/tntcategoryslider');



        $this->document->setTitle($this->language->get('heading_title'));



        $this->load->model('tnt/tntcategoryslider');



        if (isset($this->request->post['selected']) && $this->validateDelete()) {

            foreach ($this->request->post['selected'] as $tntcategorysliderparent_id) {

                $this->model_tnt_tntcategoryslider->deletecategoryslider($tntcategorysliderparent_id);

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

        $this->response->redirect($this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));



    }

    public function copy() {

        $this->load->language('extension/module/tntcategoryslider');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntcategoryslider');

        if (isset($this->request->post['selected']) && $this->validateCopy()) {

            foreach ($this->request->post['selected'] as $tntcategorysliderparent_id) {

                $this->model_tnt_tntcategoryslider->copycategoryslider($tntcategorysliderparent_id);

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

        $this->response->redirect($this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

    }

   

    public function getList() {



        $this->load->model('setting/module');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatesetting()) {

            if (!isset($this->request->get['module_id'])) {

                $this->model_setting_module->addModule('tntcategoryslider', $this->request->post);

            } else {

                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);

            }



            $this->session->data['success'] = $this->language->get('text_success');



            $this->response->redirect($this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

        }

        $this->load->model('localisation/language');



        $data['languages']  = $this->model_localisation_language->getLanguages();



        $this->load->language('extension/module/tntcategoryslider');



        

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

                'href' => $this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . $url, true)

            );

        } else {

            $data['breadcrumbs'][] = array(

                'text' => $this->language->get('heading_title'),

                'href' => $this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)

            );

        }





        if (!isset($this->request->get['module_id'])) {

            $data['action'] = $this->url->link('extension/module/tntcategoryslider/getList', 'user_token=' . $this->session->data['user_token'] . $url, true);

        } else {

            $data['action'] = $this->url->link('extension/module/tntcategoryslider/getList', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);

        }

        

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {

            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);

        }



        $data['add']        = $this->url->link('extension/module/tntcategoryslider/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['copy']       = $this->url->link('extension/module/tntcategoryslider/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['delete']     = $this->url->link('extension/module/tntcategoryslider/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);



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



        $this->load->model('tool/image');



        $default_image = $this->model_tool_image->resize('no_image.png', 100, 100);



        $data['categorysliders'] = array();



        $filter_data = array(

            'filter_name'     => $filter_name,

            'filter_status'   => $filter_status,

            'sort'            => $sort,

            'order'           => $order,

            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),

            'limit'           => $this->config->get('config_limit_admin')

        );



        $this->load->model('tool/image');

        $this->load->model('tnt/tntcategoryslider');



        $default_image = $this->model_tool_image->resize('no_image.png', 100, 100);

     

        if (isset($this->request->post['tntcategoryslider_parent'])) {

            $data['tntcategoryslider_parent'] = $this->request->post['tntcategoryslider_parent'];

        } elseif (!empty($module_info['tntcategoryslider_parent'])) {

            $data['tntcategoryslider_parent'] = $module_info['tntcategoryslider_parent'];

        } else {

            $data['tntcategoryslider_parent'] = array();

        }



        //echo "<pre>"; print_r($module_info);die;

        $categoryslider_total = $this->model_tnt_tntcategoryslider->gettotalcategorysliders($filter_data);



        $results = $this->model_tnt_tntcategoryslider->getcategoryslider($filter_data);



        foreach ($results as $result) {

            if (is_file(DIR_IMAGE . $result['tntcategorysliderparent_image'])) {

                $image = $this->model_tool_image->resize($result['tntcategorysliderparent_image'], 40, 40);

            } else {

                $image = $this->model_tool_image->resize('no_image.png', 40, 40);

            }

            $cate_namme = $this->model_tnt_tntcategoryslider->getcategoryname($result['tntcategorysliderparent_category_id']);

            if(isset($cate_namme['name'])){

                $name = $cate_namme['name'];

            }else{

                $name ="";

            }

            $data['categorysliders'][] = array(

                'id'        => $result['tntcategorysliderparent_id'],

                'image'     => $image,

                'title'     => $name,

                'alignment'     => $result['tntcategorysliderchild_name'],

                'description'       => html_entity_decode($result['tntcategorysliderchild_description']),

                'status'    => $result['tntcategorysliderparent_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),

                'edit'      => $this->url->link('extension/module/tntcategoryslider/edit', 'user_token=' . $this->session->data['user_token'] . '&tntcategorysliderparent_id=' . $result['tntcategorysliderparent_id'] . $url, true)

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





        $data['sort_tntcategorysliderchild_name']   = $this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntcategorysliderchild_name' . $url, true);



        $data['sort_tntcategorysliderchild_description']    = $this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntcategorysliderchild_description' . $url, true);



        $data['sort_tntcategorysliderparent_status'] = $this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . '&sort=p.tntcategorysliderparent_status' . $url, true);



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

        $pagination->total  = $categoryslider_total;

        $pagination->page   = $page;

        $pagination->limit  = $this->config->get('config_limit_admin');

        $pagination->url    = $this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);



        $data['pagination'] = $pagination->render();



        $data['results'] = sprintf($this->language->get('text_pagination'), ($categoryslider_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($categoryslider_total - $this->config->get('config_limit_admin'))) ? $categoryslider_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $categoryslider_total, ceil($categoryslider_total / $this->config->get('config_limit_admin')));



        $data['filter_name'] = $filter_name;

        

        $data['filter_status'] = $filter_status;



        $data['sort'] = $sort;

        $data['order'] = $order;



        $data['link'] = $this->url->link('extension/module/tntcategoryslider/ajax', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['header'] = $this->load->controller('common/header');

        $data['column_left'] = $this->load->controller('common/column_left');

        $data['footer'] = $this->load->controller('common/footer');



        $this->response->setOutput($this->load->view('extension/module/tntcategoryslider_list', $data));

    }

    protected function getForm() {



        $data['text_form']          = !isset($this->request->get['tntcategorysliderparent_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['category']           = $this->model_tnt_tntcategoryslider->getCategories();

        if (isset($this->error['warning'])) {

            $data['error_warning']  = $this->error['warning'];

        } else {

            $data['error_warning']  = '';

        }



        if (isset($this->error['tntcategorysliderparent_image'])) {

            $data['error_tntcategorysliderparent_image'] = $this->error['tntcategorysliderparent_image'];

        } else {

            $data['error_tntcategorysliderparent_image'] = array();

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



        $data['breadcrumbs']    = array();



        $data['breadcrumbs'][]  = array(

            'text' => $this->language->get('text_home'),

            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)

        );

        $data['breadcrumbs'][] = array(

            'text' => $this->language->get('text_extension'),

            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)

        );



        $data['breadcrumbs'][] = array(

            'text' => $this->language->get('heading_title'),

            'href' => $this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . $url, true)

        );



        if (!isset($this->request->get['tntcategorysliderparent_id'])) {

            $data['action'] = $this->url->link('extension/module/tntcategoryslider/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

        } else {

            $data['action'] = $this->url->link('extension/module/tntcategoryslider/edit', 'user_token=' . $this->session->data['user_token'] . '&tntcategorysliderparent_id=' . $this->request->get['tntcategorysliderparent_id'] . $url, true);

        }



        $data['cancel'] = $this->url->link('extension/module/tntcategoryslider', 'user_token=' . $this->session->data['user_token'] . $url, true);



        if (isset($this->request->get['tntcategorysliderparent_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {

            $categoryslider_info = $this->model_tnt_tntcategoryslider->getcateimageslidesigle($this->request->get['tntcategorysliderparent_id']);

        }

        

        $data['user_token'] = $this->session->data['user_token'];



        $this->load->model('localisation/language');



        $data['languages']  = $this->model_localisation_language->getLanguages();



        $this->load->model('tool/image');



        if (isset($this->request->post['tntcategoryslider'])){

            $data['tntcategoryslider'] = $this->request->post['tntcategoryslider'];

        } elseif (!empty($categoryslider_info)) {

            $editdata = array();

            foreach ($categoryslider_info as $key => $value) {

                $editdata[$value['tntcategorysliderchildlanguage_id']] = $value;

            }

            $data['tntcategoryslider'] = $editdata;

            foreach ($editdata as $key => $value) {

                $data['img'][$key] =  $this->model_tool_image->resize($value['tntcategorysliderparent_image'], 100, 100);               

            }



        } else {

            $data['tntcategoryslider'] = array();

        }

        

        if (isset($this->request->post['tntcategorysliderparent_status'])) {

            $data['tntcategorysliderparent_status']         = $this->request->post['tntcategorysliderparent_status'];

        } elseif (!empty($categoryslider_info)) {

            $data['tntcategorysliderparent_status']         = $categoryslider_info[0]['tntcategorysliderparent_status'];

        } else {

            $data['tntcategorysliderparent_status']         = "";

        }



        if (isset($this->request->post['tntcategorysliderparent_category_id'])) {

            $data['tntcategorysliderparent_category_id'] = $this->request->post['tntcategorysliderparent_category_id'];

        } elseif (!empty($categoryslider_info)) {

            $data['tntcategorysliderparent_category_id'] = $categoryslider_info[0]['tntcategorysliderparent_category_id'];

        } else {

            $data['tntcategorysliderparent_category_id'] = "";

        }

         

       

        if (isset($this->request->post['tntcategorysliderparent_image'])) {

            $data['tntcategorysliderparent_image']      = $this->request->post['tntcategorysliderparent_image'];

        } elseif (!empty($categoryslider_info)) {

            $data['tntcategorysliderparent_image']      = $categoryslider_info[0]['tntcategorysliderparent_image'];

        } else {

            $data['tntcategorysliderparent_image']      = '';

        }

        

        

        $default_image = $this->model_tool_image->resize('no_image.png', 100, 100);



        if (isset($this->request->post['tntcategorysliderparent_image']) && is_file(DIR_IMAGE . $this->request->post['tntcategorysliderparent_image'])) {

            $data['thumb'] = $this->model_tool_image->resize($this->request->post['tntcategorysliderparent_image'], 100, 100);

        } elseif (!empty($categoryslider_info) && is_file(DIR_IMAGE . $categoryslider_info[0]['tntcategorysliderparent_image'])) {

            $data['thumb'] = $this->model_tool_image->resize($categoryslider_info[0]['tntcategorysliderparent_image'], 100, 100);

        } else {

            $data['thumb'] = $default_image;

        }



        $data['placeholder'] = $default_image;



        $data['header'] = $this->load->controller('common/header');

        $data['column_left'] = $this->load->controller('common/column_left');

        $data['footer'] = $this->load->controller('common/footer');



        $this->response->setOutput($this->load->view('extension/module/tntcategoryslider_form', $data));

    }

    protected function validateForm() {



        if (!$this->user->hasPermission('modify', 'extension/module/tntcategoryslider')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }

       

        if ((utf8_strlen($this->request->post['tntcategorysliderparent_image']) < 1) || (utf8_strlen($this->request->post['tntcategorysliderparent_image']) > 255)) {

            $this->error['tntcategorysliderparent_image'] = $this->language->get('error_image');

        }       

     

        if ($this->error && !isset($this->error['warning'])) {

            $this->error['warning'] = $this->language->get('error_warning');

        }



        return !$this->error;

    }

    protected function validateDelete() {

        if (!$this->user->hasPermission('modify', 'extension/module/tntcategoryslider')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }



        return !$this->error;

    }

    protected function validateCopy() {

        if (!$this->user->hasPermission('modify', 'extension/module/tntcategoryslider')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }



        return !$this->error;

    }

    protected function validatesetting() {

        $this->load->language('extension/module/tntcategoryslider');



        if (!$this->user->hasPermission('modify', 'extension/module/tntcategoryslider')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {

            $this->error['name'] = $this->language->get('error_name');

        }

        return !$this->error;

    }



}