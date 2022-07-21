<?php
class ControllerExtensionModuletnttabproduct extends Controller {
    private $error = array();

    public function index() {
        $this->session->data['module_id'] = $this->request->get['module_id'];
        $this->load->language('extension/module/tnttabproduct');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('localisation/language');
        $this->load->model('setting/module');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('catalog/category');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatesetting()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('tnttabproduct', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/tnttabproduct', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
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
                'href' => $this->url->link('extension/module/tnttabproduct', 'user_token=' . $this->session->data['user_token'] , true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tnttabproduct', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }
        $data['languages']   = $this->model_localisation_language->getLanguages();
        $data['user_token']  = $this->session->data['user_token'];
        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/tnttabproduct', 'user_token=' . $this->session->data['user_token'] , true);
        } else {
            $data['action'] = $this->url->link('extension/module/tnttabproduct', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
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
        $data['products']   = array();
        if (!empty($this->request->post['productfeature']['adminselectproduct'])) {
            $products = $this->request->post['productfeature']['adminselectproduct'];
        } elseif (!empty($module_info['productfeature']['adminselectproduct'])) {
            $products = $module_info['productfeature']['adminselectproduct'];
        } else {
            $products = array();
        }

        foreach ($products as $product_id) {
            $product_info =  $this->model_catalog_product->getProduct($product_id);
            if ($product_info) {
                $data['products'][] = array(
                    'product_id' => $product_info['product_id'],
                    'name'       => $product_info['name']
                );
            }
        }
         $data['placeholder'] = 'no_image.png';
        $default_image = $this->model_tool_image->resize('no_image.png', 100, 100);
        if(!empty($this->request->post['customtext'])) {
            $data['customtext']   = $this->request->post['customtext'];
        } elseif (!empty($module_info)) {
            $data['customtext']   = $module_info['customtext'];
        } else {
            $data['customtext']   = array();
        }
        $default_image = $this->model_tool_image->resize('no_image.png', 100, 100);
        if (isset($this->request->post['productfeature'])) {
            $data['productfeature']   = $this->request->post['productfeature'];
            $data['productfeature_thumb'] = $this->model_tool_image->resize($this->request->post['productfeature']['image'],100,100);
        } elseif (!empty($module_info)) {
            $data['productfeature']   = $module_info['productfeature'];
            $data['productfeature_thumb'] = $this->model_tool_image->resize($module_info['productfeature']['image'], 100, 100);
        } else {
            $data['productfeature']   = array();
            $data['productfeature_thumb'] =  $default_image;
        }

        if (isset($this->request->post['productnew'])) {
            $data['productnew']   = $this->request->post['productnew'];
            $data['productnew_thumb'] = $this->model_tool_image->resize($this->request->post['productnew']['image'],100,100);
        } elseif (!empty($module_info)) {
            $data['productnew']   = $module_info['productnew'];
            $data['productnew_thumb'] = $this->model_tool_image->resize($module_info['productnew']['image'], 100, 100);
        } else {
            $data['productnew']   = array();
            $data['productnew_thumb'] =  $default_image;
        }
        


        /*if (isset($this->request->post['productnew'])) {
            $data['productnew']   = $this->request->post['productnew'];
            foreach ($this->request->post['productnew']['parenttext'] as $key => $value) {
                if(!empty($value['image'])){
                    $data['newproductimage'][$key] = $this->model_tool_image->resize($value['image'], 100, 100);                
                }else{
                    $data['newproductimage'][$key] = $default_image;              
                }
            }
        } elseif (!empty($module_info)) {
            $data['productnew']   = $module_info['productnew'];
            foreach ($module_info['productnew']['parenttext'] as $key => $value) {
                if(!empty($value['image'])){
                    $data['newproductimage'][$key] = $this->model_tool_image->resize($value['image'], 100, 100);                
                }else{
                    $data['newproductimage'][$key] = $default_image;              
                }
            }
        } else {
            foreach ($data['languages'] as $key => $value) {
                $data['newproductimage'][$value['language_id']] = $default_image;
            }
            $data['productnew']   = array();
        }*/


        if (isset($this->request->post['productbest'])) {
            $data['productbest']   = $this->request->post['productbest'];
            $data['productbest_thumb'] = $this->model_tool_image->resize($this->request->post['productbest']['image'],100,100);
        } elseif (!empty($module_info)) {
            $data['productbest']   = $module_info['productbest'];
            $data['productbest_thumb'] = $this->model_tool_image->resize($module_info['productbest']['image'], 100, 100);
        } else {
            $data['productbest']   = array();
            $data['productbest_thumb'] =  $default_image;
        }

        /*if (isset($this->request->post['productbest'])) {
            $data['productbest']  = $this->request->post['productbest'];
        } elseif (!empty($module_info)) {
            $data['productbest']  = $module_info['productbest'];
        } else {
            $data['productbest']  = array();
        }*/

         if (isset($this->request->post['productspecial'])) {
            $data['productspecial']   = $this->request->post['productspecial'];
            $data['productspecial_thumb'] = $this->model_tool_image->resize($this->request->post['productspecial']['image'],100,100);
        } elseif (!empty($module_info)) {
            $data['productspecial']   = $module_info['productspecial'];
            $data['productspecial_thumb'] = $this->model_tool_image->resize($module_info['productspecial']['image'], 100, 100);
        } else {
            $data['productspecial']   = array();
            $data['productspecial_thumb'] =  $default_image;
        }

       /* if (isset($this->request->post['productspecial'])) {
            $data['productspecial'] = $this->request->post['productspecial'];
        } elseif (!empty($module_info)) {
            $data['productspecial'] = $module_info['productspecial'];
        } else {
            $data['productspecial'] = array();
        }*/
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/tnttabproduct', $data));
    }

    public function install(){
        $this->load->model('setting/module');
        $this->load->model('localisation/language');
        $data                                               = array();
        $data['name']                                       = "Tab Product";
        $data['status']                                     = 1;
        $data['productfeature']['adminselectproduct']      = array('42','30','47','28','41','40','48','36','34');
        $data['productfeature']['status']                   = 1;
        $data['productnew']['status']                       = 1;
        $data['productbest']['status']                      = 1;
        $data['productspecial']['status']                   = 1;

        $data['productfeature']['image']                   = "catalog/themefactory/tabproduct/1.png";
        $data['productnew']['image']                       = "catalog/themefactory/tabproduct/2.png";
        $data['productbest']['image']                      = "catalog/themefactory/tabproduct/3.png";
        $data['productspecial']['image']                   = "catalog/themefactory/tabproduct/4.png";


        $data['customtext']['status']                       = 1;
        $data['productfeature']['limit']                    = 8;
        $data['productnew']['limit']                        = 8;
        $data['productbest']['limit']                       = 8;
        $data['productspecial']['limit']                    = 8;

        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $value) {
            $data['productfeature']['parenttext'][$value['language_id']] =  array('tabheading'=>"Featured Product",'title'=>"Organic Vegetable",'subtitle'=>"FRESH & SILKY DAILY");
            $data['productnew']['parenttext'][$value['language_id']] =  array('tabheading'=>"Organic Fruits",'title'=>"New Product",'subtitle'=>"FRESH & SILKY DAILY",'image'=>"catalog/themefactory/tabproduct/latest.jpg");
            $data['productbest']['parenttext'][$value['language_id']] =  array('tabheading'=>"SeaFood",'title'=>"Best Sellers",'subtitle'=>"FRESH & SILKY DAILY");
            $data['productspecial']['parenttext'][$value['language_id']] =  array('tabheading'=>"Wine & Beer",'title'=>"Deal Of The Day",'subtitle'=>"FRESH & SILKY DAILY");
            $data['customtext']['parenttext'][$value['language_id']] =  array('tabproductheading'=>"New Arrivals",'subtitle'=>"FRESH & SILKY DAILY");
        }
        $this->model_setting_module->addModule('tnttabproduct', $data);
    }

      
    public function autocomplete() {
        $json = array();
        if (isset($this->request->get['filter_name'])) {
            $this->load->model('catalog/category');
            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'sort'        => 'name',
                'order'       => 'ASC',
                'start'       => 0,
                'limit'       => 5
            );
            $results = $this->model_catalog_category->getcustomcategories($filter_data);
            foreach ($results as $result) {
                $json[] = array(
                    'category_id' => $result['category_id'],
                    'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }
        $sort_order = array();
        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }
        array_multisort($sort_order, SORT_ASC, $json);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    protected function validatesetting() {
        $this->load->language('extension/module/mrfimageslider');
        if (!$this->user->hasPermission('modify', 'extension/module/tnttabproduct')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        return !$this->error;
    }
}