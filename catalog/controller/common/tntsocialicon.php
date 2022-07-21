<?php
class ControllerCommontntsocialicon extends Controller {
	public function index() {
		$this->load->model('tnt/tntallquery');
		$name		 				= "tntsocialicon";
		$detail		 				= $this->model_tnt_tntallquery->getcommonmoduledetail($name);
		$commonmoduledetail   		= json_decode($detail['setting'],1);
		if(isset($commonmoduledetail['status'])){
			$socialllist 			= $this->model_tnt_tntallquery->getsocialiconlist();
			$data['socialicons'] 	= array(); 		
			foreach ($socialllist as $key => $value) {
				if(isset($value['tntsocialicon_status'])){
					$data['socialicons'][] = array(
						'tntsocialiconparent_class_name'	=> $value['tntsocialiconparent_class_name'],
						'tntsocialiconparent_link'			=> $value['tntsocialiconparent_link'],
						'tntsocialiconchild_title'			=> $value['tntsocialiconchild_title']
					);
				}
			}
        	return $this->load->view('extension/module/tntsocialicon', $data);
		}
	}
	public function autocomplete() {
        $width  = $this->config->get('tntthemesetting_livesearch_width');
        $height = $this->config->get('tntthemesetting_livesearch_height');
        if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
            $this->load->model('tool/image');
            $this->load->model('tnt/tntallquery');
            $filter_data = array(
                'filter_name'  => $filter_name,
                'start'        => 0
            );
            $results       			   = $this->model_tnt_tntallquery->getproducts($filter_data);
            $data['producttotal']      = $results->num_rows;
            $data['products']   	   = array();
            if (!empty($data['producttotal'])) {
                $i = 1 ;
                foreach ($results->rows as $result) { 
                    if($i < 4){
                        if(!empty($result['price'])){
                        	$price      = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                            $price = "";
                        }  
                        if ((float)$result['special']) {
                            $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                            $special = "";
                        }
                        $data['products'][] = array(
                            'special'     => $special,
                            'image'       => $this->model_tool_image->resize($result['image'],$width,$height),
                            'link'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
                            'price'       => $price,
                            'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                        );
                    }
                    $i++; 
                }
            }else{
                $data['noproduct']     = "No More Have Products";
            }
        }
       return $this->response->setOutput($this->load->view('helpfile/livesearch', $data));
    }
    public function productquickview(){
        $product_id = $this->request->get['product_id'];
        $width  = $this->config->get('tntthemesetting_quickview_width');
        $height = $this->config->get('tntthemesetting_quickview_height');
        $this->load->model('catalog/product');
        $product_info = $this->model_catalog_product->getProduct($product_id);
        if ($product_info) {
            $this->load->language('product/product');
            $data['heading_title'] = $product_info['name'];
            $data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
            
            $this->load->model('catalog/review');
            $data['product_id'] = (int)$product_id;
            $data['manufacturer'] = $product_info['manufacturer'];
            $data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
            $data['model']  = $product_info['model'];
            $data['reward'] = $product_info['reward'];
            if ($product_info['quantity'] <= 0) {
                $data['stock'] = $product_info['stock_status'];
            } elseif ($this->config->get('config_stock_display')) {
                $data['stock'] = $product_info['quantity'];
            } else {
                $data['stock'] = $this->language->get('text_instock');
            }
            $this->load->model('tool/image');
            if ($product_info['image']) {
                $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $width, $height);
            } else {
                $data['thumb'] = '';
            }
            /*$data['images'] = array();
            $results = $this->model_catalog_product->getProductImages($product_id);
            foreach ($results as $result) {
                $data['images'][] = array(
                    'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
                );
            }*/
            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $data['price'] = false;
            }
            if ((float)$product_info['special']) {
                $data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $data['special'] = false;
            }
            if ($this->config->get('config_tax')) {
                $data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
            } else {
                $data['tax'] = false;
            }
            $discounts = $this->model_catalog_product->getProductDiscounts($product_id);
            $data['discounts'] = array();
            foreach ($discounts as $discount) {
                $data['discounts'][] = array(
                    'quantity' => $discount['quantity'],
                    'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
                );
            }
            $data['options'] = array();
            foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
                $product_option_value_data = array();
                foreach ($option['product_option_value'] as $option_value) {
                    if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                        if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                            $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                        } else {
                            $price = false;
                        }
                        $product_option_value_data[] = array(
                            'product_option_value_id' => $option_value['product_option_value_id'],
                            'option_value_id'         => $option_value['option_value_id'],
                            'name'                    => $option_value['name'],
                            'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
                            'price'                   => $price,
                            'price_prefix'            => $option_value['price_prefix']
                        );
                    }
                }
                $data['options'][] = array(
                    'product_option_id'    => $option['product_option_id'],
                    'product_option_value' => $product_option_value_data,
                    'option_id'            => $option['option_id'],
                    'name'                 => $option['name'],
                    'type'                 => $option['type'],
                    'value'                => $option['value'],
                    'required'             => $option['required']
                );
            }
            if ($product_info['minimum']) {
                $data['minimum'] = $product_info['minimum'];
            } else {
                $data['minimum'] = 1;
            }
            
            $data['link']           = $this->url->link('product/product', 'product_id=' . $product_info['product_id']);
            $data['review_status']  = $this->config->get('config_review_status');
            $data['reviews']        = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
            $data['rating']         = (int)$product_info['rating'];
            return $this->response->setOutput($this->load->view('helpfile/quickviewproduct', $data));
        }
    }
}