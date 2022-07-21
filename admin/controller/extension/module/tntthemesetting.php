<?php
class ControllerExtensionModuletntthemesetting extends Controller {
    private $error = array();
    public function index() {
        $this->session->data['module_id'] = $this->request->get['module_id'];
        $this->load->language('extension/module/tntthemesetting');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('localisation/language');
        $this->load->model('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatesetting()) {
            $this->model_setting_setting->editSetting('tntthemesetting', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/tntthemesetting', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->session->data['module_id'], true));
        }
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        if (isset($this->error['error_tab'])) {
            $data['error_tab'] = $this->error['error_tab'];
        } else {
            $data['error_tab'] = '';
        }
        if (isset($this->error['tntthemesetting_sliderimage'])) {
            $data['error_sliderimage']  = $this->error['tntthemesetting_sliderimage'];
        } else {
            $data['error_sliderimage']  = array();
        }
        if (isset($this->error['tntthemesetting_brandimage'])) {
            $data['error_brandimage']  = $this->error['tntthemesetting_brandimage'];
        } else {
            $data['error_brandimage']  = array();
        }
        if (isset($this->error['tntthemesetting_categoryimage'])) {
            $data['error_categoryimage']  = $this->error['tntthemesetting_categoryimage'];
        } else {
            $data['error_categoryimage']  = array();
        }
        if (isset($this->error['tntthemesetting_testimonial'])) {
            $data['error_testimonial']  = $this->error['tntthemesetting_testimonial'];
        } else {
            $data['error_testimonial']  = array();
        }
        if (isset($this->error['tntthemesetting_singleblock'])) {
            $data['error_singleblock']  = $this->error['tntthemesetting_singleblock'];
        } else {
            $data['error_singleblock']  = array();
        }
        if (isset($this->error['tntthemesetting_singleblock1'])) {
            $data['error_singleblock1']  = $this->error['tntthemesetting_singleblock1'];
        } else {
            $data['error_singleblock1']  = array();
        }
        if (isset($this->error['tntthemesetting_payemtnicon'])) {
            $data['error_payemtnicon']  = $this->error['tntthemesetting_payemtnicon'];
        } else {
            $data['error_payemtnicon']  = array();
        }
        if (isset($this->error['tntthemesetting_imagegallery'])) {
            $data['error_imagegallery']  = $this->error['tntthemesetting_imagegallery'];
        } else {
            $data['error_imagegallery']  = array();
        }
        if (isset($this->error['tntthemesetting_leftrightproduct'])) {
            $data['error_leftrightproduct']  = $this->error['tntthemesetting_leftrightproduct'];
        } else {
            $data['error_leftrightproduct']  = array();
        }
        if (isset($this->error['tntthemesetting_leftrighttestimoinal'])) {
            $data['error_leftrighttestimoinal']  = $this->error['tntthemesetting_leftrighttestimoinal'];
        } else {
            $data['error_leftrighttestimoinal']  = array();
        }
        if (isset($this->error['tntthemesetting_newsletterpopup'])) {
            $data['error_newsletterpopup']  = $this->error['tntthemesetting_newsletterpopup'];
        } else {
            $data['error_newsletterpopup']  = array();
        }
        if (isset($this->error['tntthemesetting_tabproduct'])) {
            $data['error_tabproduct']  = $this->error['tntthemesetting_tabproduct'];
        } else {
            $data['error_tabproduct']  = array();
        }
        if (isset($this->error['tntthemesetting_popupcart'])) {
            $data['error_popupcart']  = $this->error['tntthemesetting_popupcart'];
        } else {
            $data['error_popupcart']  = array();
        }
        if (isset($this->error['tntthemesetting_livesearch'])) {
            $data['error_livesearch']  = $this->error['tntthemesetting_livesearch'];
        } else {
            $data['error_livesearch']  = array();
        }
        if (isset($this->error['tntthemesetting_quickview'])) {
            $data['error_quickview']  = $this->error['tntthemesetting_quickview'];
        } else {
            $data['error_quickview']  = array();
        }
        if (isset($this->error['tntthemesetting_newproductbanner'])) {
            $data['error_newproductbanner']  = $this->error['tntthemesetting_newproductbanner'];
        } else {
            $data['error_newproductbanner']  = array();
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
                'href' => $this->url->link('extension/module/tntthemesetting', 'user_token=' . $this->session->data['user_token'] , true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/tntthemesetting', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }
        $data['languages']  = $this->model_localisation_language->getLanguages();
        $data['user_token'] = $this->session->data['user_token'];
        $data['action']     = $this->url->link('extension/module/tntthemesetting', 'user_token=' . $this->session->data['user_token'] , true);
        $data['cancel']     = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        if (isset($this->request->post['tntthemesetting_status'])) {
            $data['tntthemesetting_status'] = $this->request->post['tntthemesetting_status'];
        } else {
            $data['tntthemesetting_status'] = $this->config->get('tntthemesetting_status');
        }
        if (isset($this->request->post['tntthemesetting_genral'])) {
            $data['tntthemesetting_genral'] = $this->request->post['tntthemesetting_genral'];
        } else {
            $data['tntthemesetting_genral'] = $this->config->get('tntthemesetting_genral');
        }
        if (isset($this->request->post['tntthemesetting_minifycss'])) {
            $data['tntthemesetting_minifycss'] = $this->request->post['tntthemesetting_minifycss'];
        } else {
            $data['tntthemesetting_minifycss'] = $this->config->get('tntthemesetting_minifycss');
        }
        if (isset($this->request->post['tntthemesetting_minifyhtml'])) {
            $data['tntthemesetting_minifyhtml'] = $this->request->post['tntthemesetting_minifyhtml'];
        } else {
            $data['tntthemesetting_minifyhtml'] = $this->config->get('tntthemesetting_minifyhtml');
        }
        if (isset($this->request->post['tntthemesetting_minifyjs'])) {
            $data['tntthemesetting_minifyjs'] = $this->request->post['tntthemesetting_minifyjs'];
        } else {
            $data['tntthemesetting_minifyjs'] = $this->config->get('tntthemesetting_minifyjs');
        }
        
        if (isset($this->request->post['tntthemesetting_footer'])) {
            $data['tntthemesetting_footer'] = $this->request->post['tntthemesetting_footer'];
        } else {
            $data['tntthemesetting_footer'] = $this->config->get('tntthemesetting_footer');
        } 
        if (isset($this->request->post['tntthemesetting_header'])) {
            $data['tntthemesetting_header'] = $this->request->post['tntthemesetting_header'];
        } else {
            $data['tntthemesetting_header'] = $this->config->get('tntthemesetting_header');
        } 
        
        if (isset($this->request->post['tntthemesetting_sliderimage_width'])) {
            $data['tntthemesetting_sliderimage_width'] = $this->request->post['tntthemesetting_sliderimage_width'];
        } else {
            $data['tntthemesetting_sliderimage_width'] = $this->config->get('tntthemesetting_sliderimage_width');
        }
        if (isset($this->request->post['tntthemesetting_sliderimage_height'])) {
            $data['tntthemesetting_sliderimage_height'] = $this->request->post['tntthemesetting_sliderimage_height'];
        } else {
            $data['tntthemesetting_sliderimage_height'] = $this->config->get('tntthemesetting_sliderimage_height');
        }
        if (isset($this->request->post['tntthemesetting_brandimage_width'])) {
            $data['tntthemesetting_brandimage_width'] = $this->request->post['tntthemesetting_brandimage_width'];
        } else {
            $data['tntthemesetting_brandimage_width'] = $this->config->get('tntthemesetting_brandimage_width');
        }
        if (isset($this->request->post['tntthemesetting_categoryimage_width'])) {
            $data['tntthemesetting_categoryimage_width'] = $this->request->post['tntthemesetting_categoryimage_width'];
        } else {
            $data['tntthemesetting_categoryimage_width'] = $this->config->get('tntthemesetting_categoryimage_width');
        }
        if (isset($this->request->post['tntthemesetting_testimonial_width'])) {
            $data['tntthemesetting_testimonial_width'] = $this->request->post['tntthemesetting_testimonial_width'];
        } else {
            $data['tntthemesetting_testimonial_width'] = $this->config->get('tntthemesetting_testimonial_width');
        }
        if (isset($this->request->post['tntthemesetting_offerbanner_width'])) {
            $data['tntthemesetting_offerbanner_width'] = $this->request->post['tntthemesetting_offerbanner_width'];
        } else {
            $data['tntthemesetting_offerbanner_width'] = $this->config->get('tntthemesetting_offerbanner_width');
        }
        if (isset($this->request->post['tntthemesetting_testimonialbgimage_width'])) {
            $data['tntthemesetting_testimonialbgimage_width'] = $this->request->post['tntthemesetting_testimonialbgimage_width'];
        } else {
            $data['tntthemesetting_testimonialbgimage_width'] = $this->config->get('tntthemesetting_testimonialbgimage_width');
        }
        if (isset($this->request->post['tntthemesetting_payemtnicon_width'])) {
            $data['tntthemesetting_payemtnicon_width'] = $this->request->post['tntthemesetting_payemtnicon_width'];
        } else {
            $data['tntthemesetting_payemtnicon_width'] = $this->config->get('tntthemesetting_payemtnicon_width');
        }
        if (isset($this->request->post['tntthemesetting_brandimage_width'])) {
            $data['tntthemesetting_brandimage_width'] = $this->request->post['tntthemesetting_brandimage_width'];
        } else {
            $data['tntthemesetting_brandimage_width'] = $this->config->get('tntthemesetting_brandimage_width');
        }
        if (isset($this->request->post['tntthemesetting_imagegallery_width'])) {
            $data['tntthemesetting_imagegallery_width'] = $this->request->post['tntthemesetting_imagegallery_width'];
        } else {
            $data['tntthemesetting_imagegallery_width'] = $this->config->get('tntthemesetting_imagegallery_width');
        }
        if (isset($this->request->post['tntthemesetting_leftrightproduct_width'])) {
            $data['tntthemesetting_leftrightproduct_width'] = $this->request->post['tntthemesetting_leftrightproduct_width'];
        } else {
            $data['tntthemesetting_leftrightproduct_width'] = $this->config->get('tntthemesetting_leftrightproduct_width');
        }
        if (isset($this->request->post['tntthemesetting_leftrighttestimoinal_width'])) {
            $data['tntthemesetting_leftrighttestimoinal_width'] = $this->request->post['tntthemesetting_leftrighttestimoinal_width'];
        } else {
            $data['tntthemesetting_leftrighttestimoinal_width'] = $this->config->get('tntthemesetting_leftrighttestimoinal_width');
        }
        if (isset($this->request->post['tntthemesetting_newsletterpopup_width'])) {
            $data['tntthemesetting_newsletterpopup_width'] = $this->request->post['tntthemesetting_newsletterpopup_width'];
        } else {
            $data['tntthemesetting_newsletterpopup_width'] = $this->config->get('tntthemesetting_newsletterpopup_width');
        }
        if (isset($this->request->post['tntthemesetting_tabproduct_width'])) {
            $data['tntthemesetting_tabproduct_width'] = $this->request->post['tntthemesetting_tabproduct_width'];
        } else {
            $data['tntthemesetting_tabproduct_width'] = $this->config->get('tntthemesetting_tabproduct_width');
        }
        if (isset($this->request->post['tntthemesetting_popupcart_width'])) {
            $data['tntthemesetting_popupcart_width'] = $this->request->post['tntthemesetting_popupcart_width'];
        } else {
            $data['tntthemesetting_popupcart_width'] = $this->config->get('tntthemesetting_popupcart_width');
        }
        if (isset($this->request->post['tntthemesetting_livesearch_width'])) {
            $data['tntthemesetting_livesearch_width'] = $this->request->post['tntthemesetting_livesearch_width'];
        } else {
            $data['tntthemesetting_livesearch_width'] = $this->config->get('tntthemesetting_livesearch_width');
        }
        if (isset($this->request->post['tntthemesetting_quickview_width'])) {
            $data['tntthemesetting_quickview_width'] = $this->request->post['tntthemesetting_quickview_width'];
        } else {
            $data['tntthemesetting_quickview_width'] = $this->config->get('tntthemesetting_quickview_width');
        }
        if (isset($this->request->post['tntthemesetting_newproductbanner_width'])) {
            $data['tntthemesetting_newproductbanner_width'] = $this->request->post['tntthemesetting_newproductbanner_width'];
        } else {
            $data['tntthemesetting_newproductbanner_width'] = $this->config->get('tntthemesetting_newproductbanner_width');
        }

        if (isset($this->request->post['tntthemesetting_brandimage_height'])) {
            $data['tntthemesetting_brandimage_height'] = $this->request->post['tntthemesetting_brandimage_height'];
        } else {
            $data['tntthemesetting_brandimage_height'] = $this->config->get('tntthemesetting_brandimage_height');
        }
        if (isset($this->request->post['tntthemesetting_categoryimage_height'])) {
            $data['tntthemesetting_categoryimage_height'] = $this->request->post['tntthemesetting_categoryimage_height'];
        } else {
            $data['tntthemesetting_categoryimage_height'] = $this->config->get('tntthemesetting_categoryimage_height');
        }
        if (isset($this->request->post['tntthemesetting_testimonial_height'])) {
            $data['tntthemesetting_testimonial_height'] = $this->request->post['tntthemesetting_testimonial_height'];
        } else {
            $data['tntthemesetting_testimonial_height'] = $this->config->get('tntthemesetting_testimonial_height');
        }
        if (isset($this->request->post['tntthemesetting_offerbanner_height'])) {
            $data['tntthemesetting_offerbanner_height'] = $this->request->post['tntthemesetting_offerbanner_height'];
        } else {
            $data['tntthemesetting_offerbanner_height'] = $this->config->get('tntthemesetting_offerbanner_height');
        }
        if (isset($this->request->post['tntthemesetting_testimonialbgimage_height'])) {
            $data['tntthemesetting_testimonialbgimage_height'] = $this->request->post['tntthemesetting_testimonialbgimage_height'];
        } else {
            $data['tntthemesetting_testimonialbgimage_height'] = $this->config->get('tntthemesetting_testimonialbgimage_height');
        }
        if (isset($this->request->post['tntthemesetting_payemtnicon_height'])) {
            $data['tntthemesetting_payemtnicon_height'] = $this->request->post['tntthemesetting_payemtnicon_height'];
        } else {
            $data['tntthemesetting_payemtnicon_height'] = $this->config->get('tntthemesetting_payemtnicon_height');
        }
        if (isset($this->request->post['tntthemesetting_brandimage_height'])) {
            $data['tntthemesetting_brandimage_height'] = $this->request->post['tntthemesetting_brandimage_height'];
        } else {
            $data['tntthemesetting_brandimage_height'] = $this->config->get('tntthemesetting_brandimage_height');
        }
         if (isset($this->request->post['tntthemesetting_imagegallery_height'])) {
            $data['tntthemesetting_imagegallery_height'] = $this->request->post['tntthemesetting_imagegallery_height'];
        } else {
            $data['tntthemesetting_imagegallery_height'] = $this->config->get('tntthemesetting_imagegallery_height');
        }
        if (isset($this->request->post['tntthemesetting_leftrightproduct_height'])) {
            $data['tntthemesetting_leftrightproduct_height'] = $this->request->post['tntthemesetting_leftrightproduct_height'];
        } else {
            $data['tntthemesetting_leftrightproduct_height'] = $this->config->get('tntthemesetting_leftrightproduct_height');
        }
        if (isset($this->request->post['tntthemesetting_leftrighttestimoinal_height'])) {
            $data['tntthemesetting_leftrighttestimoinal_height'] = $this->request->post['tntthemesetting_leftrighttestimoinal_height'];
        } else {
            $data['tntthemesetting_leftrighttestimoinal_height'] = $this->config->get('tntthemesetting_leftrighttestimoinal_height');
        }
        if (isset($this->request->post['tntthemesetting_newsletterpopup_height'])) {
            $data['tntthemesetting_newsletterpopup_height'] = $this->request->post['tntthemesetting_newsletterpopup_height'];
        } else {
            $data['tntthemesetting_newsletterpopup_height'] = $this->config->get('tntthemesetting_newsletterpopup_height');
        }
        if (isset($this->request->post['tntthemesetting_tabproduct_height'])) {
            $data['tntthemesetting_tabproduct_height'] = $this->request->post['tntthemesetting_tabproduct_height'];
        } else {
            $data['tntthemesetting_tabproduct_height'] = $this->config->get('tntthemesetting_tabproduct_height');
        }
        if (isset($this->request->post['tntthemesetting_popupcart_height'])) {
            $data['tntthemesetting_popupcart_height'] = $this->request->post['tntthemesetting_popupcart_height'];
        } else {
            $data['tntthemesetting_popupcart_height'] = $this->config->get('tntthemesetting_popupcart_height');
        }
        if (isset($this->request->post['tntthemesetting_livesearch_height'])) {
            $data['tntthemesetting_livesearch_height'] = $this->request->post['tntthemesetting_livesearch_height'];
        } else {
            $data['tntthemesetting_livesearch_height'] = $this->config->get('tntthemesetting_livesearch_height');
        }
        if (isset($this->request->post['tntthemesetting_quickview_height'])) {
            $data['tntthemesetting_quickview_height'] = $this->request->post['tntthemesetting_quickview_height'];
        } else {
            $data['tntthemesetting_quickview_height'] = $this->config->get('tntthemesetting_quickview_height');
        }
        if (isset($this->request->post['tntthemesetting_newproductbanner_height'])) {
            $data['tntthemesetting_newproductbanner_height'] = $this->request->post['tntthemesetting_newproductbanner_height'];
        } else {
            $data['tntthemesetting_newproductbanner_height'] = $this->config->get('tntthemesetting_newproductbanner_height');
        }
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/tntthemesetting', $data));
       
    }
    public function install(){
        $this->load->model('localisation/language');
        $this->load->model('setting/setting');
        $parent                                                             = array();
        $parent['tntthemesetting_status']                                   = 1;

        $parent['tntthemesetting_minifycss']                                = 1;
        $parent['tntthemesetting_minifyhtml']                               = 1;

        $parent['tntthemesetting_genral']['pageloading']                    = 1;
        $parent['tntthemesetting_genral']['animation']                      = 1;
        $parent['tntthemesetting_genral']['producthover']                   = 1;
        $parent['tntthemesetting_genral']['menusticky']                     = 1;

        $parent['tntthemesetting_genral']['copyrighttextdisplay']           = 1;

        $parent['tntthemesetting_sliderimage_width']                        = 1920;
        $parent['tntthemesetting_sliderimage_height']                       = 685;
        $parent['tntthemesetting_brandimage_width']                         = 230;
        $parent['tntthemesetting_brandimage_height']                        = 96;
        $parent['tntthemesetting_categoryimage_width']                      = 370;
        $parent['tntthemesetting_categoryimage_height']                     = 370;
        $parent['tntthemesetting_testimonial_width']                        = 100;
        $parent['tntthemesetting_testimonial_height']                       = 100;
        $parent['tntthemesetting_offerbanner_width']                        = 1170;
        $parent['tntthemesetting_offerbanner_height']                       = 502;
        $parent['tntthemesetting_testimonialbgimage_width']                 = 1895;
        $parent['tntthemesetting_testimonialbgimage_height']                = 686;
        $parent['tntthemesetting_payemtnicon_width']                        = 286;
        $parent['tntthemesetting_payemtnicon_height']                       = 31;
        $parent['tntthemesetting_imagegallery_width']                       = 483;
        $parent['tntthemesetting_imagegallery_height']                      = 310;
        $parent['tntthemesetting_leftrightproduct_width']                   = 300;
        $parent['tntthemesetting_leftrightproduct_height']                  = 300;
        $parent['tntthemesetting_leftrighttestimoinal_width']               = 100;
        $parent['tntthemesetting_leftrighttestimoinal_height']              = 100;
        $parent['tntthemesetting_newsletterpopup_width']                    = 800;
        $parent['tntthemesetting_newsletterpopup_height']                   = 400;
        $parent['tntthemesetting_tabproduct_width']                         = 900;
        $parent['tntthemesetting_tabproduct_height']                        = 900;
        $parent['tntthemesetting_popupcart_width']                          = 253;
        $parent['tntthemesetting_popupcart_height']                         = 253;
        $parent['tntthemesetting_livesearch_width']                         = 900;
        $parent['tntthemesetting_livesearch_height']                        = 900;
        $parent['tntthemesetting_quickview_width']                          = 900;
        $parent['tntthemesetting_quickview_height']                         = 900;
        $parent['tntthemesetting_newproductbanner_width']                   = 360;
        $parent['tntthemesetting_newproductbanner_height']                  = 426;

        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $value) {
            $parent['tntthemesetting_footer']['language'][$value['language_id']] =  array('copyrighttext'=>"© 2019 Greenzone. All Rights Reserved",'copyrightlink'=>"#");
             $parent['tntthemesetting_header']['language'][$value['language_id']] =  array();
        }
        $this->model_setting_setting->editSetting('tntthemesetting', $parent);
    }
    protected function validatesetting() {
        $this->load->language('extension/module/tntthemesetting');
        if (!$this->user->hasPermission('modify', 'extension/module/tntthemesetting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (empty($this->request->post['tntthemesetting_sliderimage_width']) || !is_numeric($this->request->post['tntthemesetting_sliderimage_width']) || empty($this->request->post['tntthemesetting_sliderimage_height']) || !is_numeric($this->request->post['tntthemesetting_sliderimage_height'])) {
            $this->error['tntthemesetting_sliderimage']  = $this->language->get('error_sliderimage');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_sliderimage');
        }     
        if (empty($this->request->post['tntthemesetting_brandimage_width']) || !is_numeric($this->request->post['tntthemesetting_brandimage_width']) || empty($this->request->post['tntthemesetting_brandimage_height']) || !is_numeric($this->request->post['tntthemesetting_brandimage_height'])) {
            $this->error['tntthemesetting_brandimage']  = $this->language->get('error_brandimage');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_brandimage');
        }   
        if (empty($this->request->post['tntthemesetting_categoryimage_width']) || !is_numeric($this->request->post['tntthemesetting_categoryimage_width']) || empty($this->request->post['tntthemesetting_categoryimage_height']) || !is_numeric($this->request->post['tntthemesetting_categoryimage_height'])) {
            $this->error['tntthemesetting_categoryimage']  = $this->language->get('error_categoryimage');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_categoryimage');
        }   
        if (empty($this->request->post['tntthemesetting_testimonial_width']) || !is_numeric($this->request->post['tntthemesetting_testimonial_width']) || empty($this->request->post['tntthemesetting_testimonial_height']) || !is_numeric($this->request->post['tntthemesetting_testimonial_height'])) {
            $this->error['tntthemesetting_testimonial']  = $this->language->get('error_testimonial');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_testimonial');
        }   
        if (empty($this->request->post['tntthemesetting_offerbanner_width']) || !is_numeric($this->request->post['tntthemesetting_offerbanner_width']) || empty($this->request->post['tntthemesetting_offerbanner_height']) || !is_numeric($this->request->post['tntthemesetting_offerbanner_height'])) {
            $this->error['tntthemesetting_singleblock']  = $this->language->get('error_singleblock');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_singleblock');
        }
        if (empty($this->request->post['tntthemesetting_payemtnicon_width']) || !is_numeric($this->request->post['tntthemesetting_payemtnicon_width']) || empty($this->request->post['tntthemesetting_payemtnicon_height']) || !is_numeric($this->request->post['tntthemesetting_payemtnicon_height'])) {
            $this->error['tntthemesetting_payemtnicon']  = $this->language->get('error_payemtnicon');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_payemtnicon');
        }
        if (empty($this->request->post['tntthemesetting_imagegallery_width']) || !is_numeric($this->request->post['tntthemesetting_imagegallery_width']) || empty($this->request->post['tntthemesetting_imagegallery_height']) || !is_numeric($this->request->post['tntthemesetting_imagegallery_height'])) {
            $this->error['tntthemesetting_imagegallery']  = $this->language->get('error_imagegallery');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_imagegallery');
        }
        if (empty($this->request->post['tntthemesetting_leftrightproduct_width']) || !is_numeric($this->request->post['tntthemesetting_leftrightproduct_width']) || empty($this->request->post['tntthemesetting_leftrightproduct_height']) || !is_numeric($this->request->post['tntthemesetting_leftrightproduct_height'])) {
            $this->error['tntthemesetting_leftrightproduct']  = $this->language->get('error_leftrightproduct');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_leftrightproduct');
        }
        if (empty($this->request->post['tntthemesetting_leftrighttestimoinal_width']) || !is_numeric($this->request->post['tntthemesetting_leftrighttestimoinal_width']) || empty($this->request->post['tntthemesetting_leftrighttestimoinal_height']) || !is_numeric($this->request->post['tntthemesetting_leftrighttestimoinal_height'])) {
            $this->error['tntthemesetting_leftrighttestimoinal']  = $this->language->get('error_leftrighttestimoinal');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_leftrighttestimoinal');
        }
        if (empty($this->request->post['tntthemesetting_newsletterpopup_width']) || !is_numeric($this->request->post['tntthemesetting_newsletterpopup_width']) || empty($this->request->post['tntthemesetting_newsletterpopup_height']) || !is_numeric($this->request->post['tntthemesetting_newsletterpopup_height'])) {
            $this->error['tntthemesetting_newsletterpopup']  = $this->language->get('error_newsletterpopup');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_newsletterpopup');
        }
        if (empty($this->request->post['tntthemesetting_tabproduct_width']) || !is_numeric($this->request->post['tntthemesetting_tabproduct_width']) || empty($this->request->post['tntthemesetting_tabproduct_height']) || !is_numeric($this->request->post['tntthemesetting_tabproduct_height'])) {
            $this->error['tntthemesetting_tabproduct']  = $this->language->get('error_tabproduct');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_tabproduct');
        }
        if (empty($this->request->post['tntthemesetting_popupcart_width']) || !is_numeric($this->request->post['tntthemesetting_popupcart_width']) || empty($this->request->post['tntthemesetting_popupcart_height']) || !is_numeric($this->request->post['tntthemesetting_popupcart_height'])) {
            $this->error['tntthemesetting_popupcart']  = $this->language->get('error_popupcart');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_popupcart');
        }
        if (empty($this->request->post['tntthemesetting_livesearch_width']) || !is_numeric($this->request->post['tntthemesetting_livesearch_width']) || empty($this->request->post['tntthemesetting_livesearch_height']) || !is_numeric($this->request->post['tntthemesetting_livesearch_height'])) {
            $this->error['tntthemesetting_livesearch']  = $this->language->get('error_livesearch');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_livesearch');
        }
        if (empty($this->request->post['tntthemesetting_quickview_width']) || !is_numeric($this->request->post['tntthemesetting_quickview_width']) || empty($this->request->post['tntthemesetting_quickview_height']) || !is_numeric($this->request->post['tntthemesetting_quickview_height'])) {
            $this->error['tntthemesetting_quickview']  = $this->language->get('error_quickview');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_quickview');
        }
        if (empty($this->request->post['tntthemesetting_newproductbanner_width']) || !is_numeric($this->request->post['tntthemesetting_newproductbanner_width']) || empty($this->request->post['tntthemesetting_newproductbanner_height']) || !is_numeric($this->request->post['tntthemesetting_newproductbanner_height'])) {
            $this->error['tntthemesetting_newproductbanner']  = $this->language->get('error_newproductbanner');
            $this->error['error_tab']  = "Image Setting > ".$this->language->get('error_newproductbanner');
        }
        return !$this->error;
    }
    
}