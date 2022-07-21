<?php

class ControllerExtensionModuletntsocialicon extends Controller {



    private $error = array();



    public function index() {
$this->session->data['module_id'] = $this->request->get['module_id'];
        $this->load->language('extension/module/tntsocialicon');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntsocialicon');

        $this->getList();

    }

    public function install(){

        $this->load->model('setting/module');

        $this->load->model('localisation/language');

        $this->load->model('tnt/tntsocialicon');

        $data           = array();

        $data['name']   = "Social Icon";

        $data['status'] = 1;



        $languages = $this->model_localisation_language->getLanguages();



        $this->model_setting_module->addModule('tntsocialicon', $data);

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntsocialiconparent` 

        (   `tntsocialiconparent_id` int(11) AUTO_INCREMENT,

            `tntsocialiconparent_position` int(11),

            `tntsocialiconparent_class_name` VARCHAR(255),

            `tntsocialiconparent_link` VARCHAR(255),

            `tntsocialicon_status` int(11),

        PRIMARY KEY (`tntsocialiconparent_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tntsocialiconchild` 

        (   `tntsocialiconchild_id` INT NOT NULL AUTO_INCREMENT ,

            `tntsocialiconparent_id` INT NOT NULL ,

            `tntsocialiconchildlanguage_id` INT NOT NULL ,

            `tntsocialiconchild_title` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,

        PRIMARY KEY (`tntsocialiconchild_id`)) ENGINE = InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

        $limit = 5;

        for ($i = 1; $i<=$limit; $i++) {

            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntsocialiconparent`

            SET         tntsocialiconparent_link        = "#",

                        tntsocialiconparent_class_name  = "fa fa-facebook",

                        tntsocialiconparent_position    = '.$i.',

                        tntsocialicon_status            = 1;'); 

            foreach ($languages as $value) {

                $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntsocialiconchild`

                SET 

                            tntsocialiconparent_id          = '.$i.',

                            tntsocialiconchild_title        = "Title'.$i.'",

                            tntsocialiconchildlanguage_id   = '.$value['language_id'].'');

            }

        }

    }

    public function uninstall(){

        $pre = DB_PREFIX;

        $this->db->query("DROP TABLE `{$pre}tntsocialiconparent`");

        $this->db->query("DROP TABLE `{$pre}tntsocialiconchild`");

    }

   

    public function sortingdatas() {

        $this->load->model('tnt/tntsocialicon');

        $sorting_position       = $this->request->get['action'];

        $data                   = $this->request->get['recordsArray'];

        $success_data           = array();

        if ($sorting_position == 'sorting_position') {

            $success_data['success'] = 'right';

            $this->model_tnt_tntsocialicon->sortingdata($data);

            echo $res = implode("##", $success_data);

        }

    }   



    public function add() {

        $this->load->language('extension/module/tntsocialicon');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntsocialicon');

        if ($this->request->server['REQUEST_METHOD'] == 'POST')  {

            $this->model_tnt_tntsocialicon->insertrecord($this->request->post);

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

            $this->response->redirect($this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

        }

        $this->getForm();

    }



    public function edit() {

        $this->load->language('extension/module/tntsocialicon');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntsocialicon');

        if ($this->request->server['REQUEST_METHOD'] == 'POST')  {

            $this->model_tnt_tntsocialicon->editsocialicon($this->request->get['tntsocialiconparent_id'], $this->request->post);

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

            $this->response->redirect($this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

        }

        $this->getForm();

    }



    public function delete() {

        $this->load->language('extension/module/tntsocialicon');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntsocialicon');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {

            foreach ($this->request->post['selected'] as $tntsocialiconparent_id) {

                $this->model_tnt_tntsocialicon->deletesocialicon($tntsocialiconparent_id);

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

        $this->response->redirect($this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

    }



    public function copy() {

        $this->load->language('extension/module/tntsocialicon');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tnt/tntsocialicon');

        if (isset($this->request->post['selected']) && $this->validateCopy()) {

            foreach ($this->request->post['selected'] as $tntsocialiconparent_id) {

                $this->model_tnt_tntsocialicon->copysocialicon($tntsocialiconparent_id);

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

        $this->response->redirect($this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

    }



    public function getList() {

        $this->load->model('setting/module');

        $this->load->model('tnt/tntsocialicon');

        $this->load->model('localisation/language');

        $this->load->model('tool/image');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatesetting()) {

            if (!isset($this->request->get['module_id'])) {

                $this->model_setting_module->addModule('tntsocialicon', $this->request->post);

            } else {

                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);

            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));

        }

        $this->load->language('extension/module/tntsocialicon');

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

                'href' => $this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . $url, true)

            );

        } else {

            $data['breadcrumbs'][] = array(

                'text' => $this->language->get('heading_title'),

                'href' => $this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)

            );

        }

        if (!isset($this->request->get['module_id'])) {

            $data['action'] = $this->url->link('extension/module/tntsocialicon/getList', 'user_token=' . $this->session->data['user_token'] . $url, true);

        } else {

            $data['action'] = $this->url->link('extension/module/tntsocialicon/getList', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);

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

        $data['add']    = $this->url->link('extension/module/tntsocialicon/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['copy']   = $this->url->link('extension/module/tntsocialicon/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['delete'] = $this->url->link('extension/module/tntsocialicon/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['socialicons'] = array();

        $filter_data = array(

            'filter_name'     => $filter_name,

            'filter_status'   => $filter_status,

            'sort'            => $sort,

            'order'           => $order,

            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),

            'limit'           => $this->config->get('config_limit_admin')

        );

        $socialicontotal = $this->model_tnt_tntsocialicon->getTotalsocialicon($filter_data);

        $results = $this->model_tnt_tntsocialicon->getsocialicon($filter_data);

        foreach ($results as $result) {

            $data['socialicons'][] = array(

                'id'            => $result['tntsocialiconparent_id'],

                'language'       => (int)$this->config->get('config_language_id'),

                'title'         => $result['tntsocialiconchild_title'],

                'class_name'    => $result['tntsocialiconparent_class_name'],

                'link'          => $result['tntsocialiconparent_link'],

                'status'        => $result['tntsocialicon_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),

                'edit'          => $this->url->link('extension/module/tntsocialicon/edit', 'user_token=' . $this->session->data['user_token'] . '&tntsocialiconparent_id=' . $result['tntsocialiconparent_id'] . $url, true)

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

        $data['sort_tntsocialiconchild_title'] = $this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.tntsocialiconchild_title' . $url, true);

        $data['sort_tntsocialiconparent_link'] = $this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . '&sort=p.tntsocialiconparent_link' . $url, true);

        $data['sort_tntsocialiconparent_class_name'] = $this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . '&sort=p.tntsocialiconparent_class_name' . $url, true);

        $data['sort_tntsocialicon_status'] = $this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . '&sort=p.tntsocialicon_status' . $url, true);

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

        $pagination->total  = $socialicontotal;

        $pagination->page   = $page;

        $pagination->limit  = $this->config->get('config_limit_admin');

        $pagination->url    = $this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results']    = sprintf($this->language->get('text_pagination'), ($socialicontotal) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($socialicontotal - $this->config->get('config_limit_admin'))) ? $socialicontotal : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $socialicontotal, ceil($socialicontotal / $this->config->get('config_limit_admin')));

        $data['filter_name']    = $filter_name;

        $data['filter_status']  = $filter_status;

        $data['sort']           = $sort;

        $data['order']          = $order;

        $data['header']         = $this->load->controller('common/header');

        $data['column_left']    = $this->load->controller('common/column_left');

        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntsocialicon_list', $data));

    }



    protected function getForm() {

        $data['text_form'] = !isset($this->request->get['tntsocialiconparent_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['text_form_parent'] = !isset($this->request->get['tntsocialiconparent_id']) ? $this->language->get('text_parent_add') : $this->language->get('text_parent_edit');

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

            'href' => $this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . $url, true)

        );

        if (!isset($this->request->get['tntsocialiconparent_id'])) {

            $data['action'] = $this->url->link('extension/module/tntsocialicon/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

        } else {

            $data['action'] = $this->url->link('extension/module/tntsocialicon/edit', 'user_token=' . $this->session->data['user_token'] . '&tntsocialiconparent_id=' . $this->request->get['tntsocialiconparent_id'] . $url, true);

        }

        $data['cancel'] = $this->url->link('extension/module/tntsocialicon', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['tntsocialiconparent_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {

            $imageslider_info = $this->model_tnt_tntsocialicon->getsocialiconsingle($this->request->get['tntsocialiconparent_id']);

        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('tool/image');

            if (isset($this->request->post['tntsocialicon'])) {

                $data['tntsocialicon'] = $this->request->post['tntsocialicon'];

            } elseif (!empty($imageslider_info)) {

                $editdata = array();

                foreach ($imageslider_info as $key => $value) {

                    $editdata[$value['tntsocialiconchildlanguage_id']] = $value;

                }

                $data['tntsocialicon'] = $editdata;

            } else {

                $data['tntsocialicon'] = array();

            }



            if (isset($this->request->post['tntsocialicon_status'])) {

                $data['tntsocialicon_status'] = $this->request->post['tntsocialicon_status'];

            } elseif (!empty($imageslider_info)) {

                $data['tntsocialicon_status'] = $imageslider_info[0]['tntsocialicon_status'];

            } else {

                $data['tntsocialicon_status'] = array();

            }





            if (isset($this->request->post['tntsocialiconparent_link'])) {

                $data['tntsocialiconparent_link'] = $this->request->post['tntsocialiconparent_link'];

            } elseif (!empty($imageslider_info)) {

                $data['tntsocialiconparent_link'] = $imageslider_info[0]['tntsocialiconparent_link'];

            } else {

                $data['tntsocialiconparent_link'] = "";

            }

    

            if (isset($this->request->post['tntsocialiconparent_class_name'])) {

                $data['tntsocialiconparent_class_name'] = $this->request->post['tntsocialiconparent_class_name'];

            } elseif (!empty($imageslider_info)) {

                $data['tntsocialiconparent_class_name'] = $imageslider_info[0]['tntsocialiconparent_class_name'];

            } else {

                $data['tntsocialiconparent_class_name'] = "";

            }

        $data['header']         = $this->load->controller('common/header');

        $data['column_left']    = $this->load->controller('common/column_left');

        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/tntsocialicon_form', $data));

    }



    protected function validateDelete() {

        if (!$this->user->hasPermission('modify', 'extension/module/tntsocialicon')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }

        return !$this->error;

    }



    protected function validateCopy() {

        if (!$this->user->hasPermission('modify', 'extension/module/tntsocialicon')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }

        return !$this->error;

    }

    protected function validatesetting() {

        $this->load->language('extension/module/tntsocialicon');

        if (!$this->user->hasPermission('modify', 'extension/module/tntsocialicon')) {

            $this->error['warning'] = $this->language->get('error_permission');

        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {

            $this->error['name'] = $this->language->get('error_name');

        }

        return !$this->error;

    }

}