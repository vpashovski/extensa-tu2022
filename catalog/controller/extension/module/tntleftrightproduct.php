<?php
class ControllerExtensionModuletntleftrightproduct extends Controller {
	public function index($setting) {

		$language_id = $this->config->get('config_language_id');
		
		$width 	= $this->config->get('tntthemesetting_leftrightproduct_width');
		$height = $this->config->get('tntthemesetting_leftrightproduct_height');
 
		if($setting['status']){

			$this->load->model('tool/image');
			$this->load->model('tnt/tntallquery');
			$this->load->model('catalog/product');
			$default = $this->model_tool_image->resize('placeholder.png', $width, $height);

			if(!empty($setting['productfeature']['status'])){
				$data['productfeature']['tabheading'] = $setting['productfeature']['parenttext'][$language_id]['tabheading'];
				$products = array_slice($setting['productfeature']['adminselectproduct'], 0, 50);

				foreach ($products as $product_id) {
					$product_info 			= $this->model_tnt_tntallquery->getproduct($product_id);
					if ($product_info) {
						if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
							$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						} else {
							$price = false;
						}
						if ($product_info['image']) {
							$image 			= $this->model_tool_image->resize($product_info['image'] ,$width, $height);
						} else {
							$image = $default;
						}
						$gethoverimage 	= $this->model_tnt_tntallquery->getproductimage($product_info['product_id']);
						if(!empty(current($gethoverimage))){
							$hoverimage = $this->model_tool_image->resize(current($gethoverimage)['image'], $width, $height);
						}else{
							$hoverimage = $image;
						}

						if ((float)$product_info['special']) {
							$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						} else {
							$special = false;
						}


						$date = $this->model_tnt_tntallquery->getproductspecialdate($product_id);
						
						if(isset($date['date_end'])){
							$date_end = $date['date_end'];
						}else{
							$date_end = null;
						}

						if ($this->config->get('config_tax')) {
							$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
						} else {
							$tax = false;
						}

						if ($this->config->get('config_review_status')) {
							$rating = $product_info['rating'];
						} else {
							$rating = false;
						}

						$categoryid 	= $this->model_tnt_tntallquery->getcategoryid($product_info['product_id']);
						$categoryname 	= $this->model_tnt_tntallquery->getcategoryname($categoryid);
						
						$data['productfeatures'][] = array(
							'product_id'  	=> $product_info['product_id'],
							'thumb'       	=> $image,
							'name'        	=> $product_info['name'],
							'description' 	=> utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
							'categoryname'  => $categoryname,
							'price'       	=> $price,
							'hoverimage'  	=> $hoverimage,
							'special'     	=> $special,
						    'date_end'    	=> $date_end,
							'tax'         	=> $tax,
							'rating'      	=> $rating,
							'href'        	=> $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
						);
					}
				}
			}
			if(!empty($setting['productspecial']['status'])){
				$data['productspecial']['tabheading'] = $setting['productspecial']['parenttext'][$language_id]['tabheading'];

				$specialproduct = $this->model_tnt_tntallquery->getProductSpecials(50);
				
				foreach ($specialproduct as $value) {
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($value['price'], $value['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ($value['image']) {
						$image = $this->model_tool_image->resize($value['image'], $width, $height);
					} else {
						$image = $default;
					}

					$gethoverimage 	= $this->model_tnt_tntallquery->getproductimage($value['product_id']);
					if(!empty(current($gethoverimage))){
						$hoverimage = $this->model_tool_image->resize(current($gethoverimage)['image'], $width, $height);
					}else{
						$hoverimage = $image;
					}


					if ((float)$value['special']) {
						$special = $this->currency->format($this->tax->calculate($value['special'], $value['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					$date = $this->model_tnt_tntallquery->getproductspecialdate($value['product_id']);
					if(isset($date['date_end'])){
						$date_end = $date['date_end'];
					}else{
						$date_end = null;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$value['special'] ? $value['special'] : $value['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					$categoryid 	= $this->model_tnt_tntallquery->getcategoryid($value['product_id']);
					$categoryname 	= $this->model_tnt_tntallquery->getcategoryname($categoryid);

					if ($this->config->get('config_review_status')) {
						$rating = $value['rating'];
					} else {
						$rating = false;
					}

					
	     			$data['productspecials'][] = array(
						'product_id'  	=> $value['product_id'],
						'thumb'      	=> $image,
						'name'        	=> $value['name'],
						'description' 	=> utf8_substr(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       	=> $price,
						'hoverimage'    => $hoverimage,
						'special'     	=> $special,
						'categoryname'  => $categoryname,
						'tax'         	=> $tax,
						'date_end'    	=> $date_end,
						'rating'      	=> $rating,
						'href'        	=> $this->url->link('product/product', 'product_id=' . $value['product_id'])
					);
				}
			}
			if(!empty($setting['productnew']['status'])){
				$data['productnew']['tabheading'] = $setting['productnew']['parenttext'][$language_id]['tabheading'];
				$productnew = $this->model_catalog_product->getLatestProducts(50);
				foreach ($productnew as $value) {
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($value['price'], $value['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}
					if ($value['image']) {
						$image 		= $this->model_tool_image->resize($value['image'], $width,$height);
					} else {
						$image 		= $default;
					}
					$gethoverimage 	= $this->model_tnt_tntallquery->getproductimage($value['product_id']);
					if(!empty(current($gethoverimage))){
						$hoverimage = $this->model_tool_image->resize(current($gethoverimage)['image'], $width, $height);
					}else{
						$hoverimage = $image;
					}

					if ((float)$value['special']) {
						$special = $this->currency->format($this->tax->calculate($value['special'], $value['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}
					$date = $this->model_tnt_tntallquery->getproductspecialdate($value['product_id']);
					
					if(isset($date['date_end'])){
						$date_end = $date['date_end'];
					}else{
						$date_end = null;
					}
					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$value['special'] ? $value['special'] : $value['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}
					$categoryid 	= $this->model_tnt_tntallquery->getcategoryid($value['product_id']);
					$categoryname 	= $this->model_tnt_tntallquery->getcategoryname($categoryid);
					if ($this->config->get('config_review_status')) {
						$rating = $value['rating'];
					} else {
						$rating = false;
					}
					
					$data['productnews'][] = array(
						'product_id'  	=> $value['product_id'],
						'categoryname'  => $categoryname,
						'thumb'      	=> $image,
						'hoverimage'    => $hoverimage,
						'name'       	=> $value['name'],
						'description'	=> utf8_substr(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       	=> $price,
						'special'     	=> $special,
						'tax'         	=> $tax,
						'date_end'   	=> $date_end,
						'rating'      	=> $rating,
						'href'        	=> $this->url->link('product/product', 'product_id=' . $value['product_id'])
					);
				}	
			}
			if(!empty($setting['productbest']['status'])){
				$data['productbest']['tabheading'] = $setting['productbest']['parenttext'][$language_id]['tabheading'];

				$productbest = $this->model_catalog_product->getBestSellerProducts(50);
				foreach ($productbest as $value) {
					
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($value['price'], $value['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ($value['image']) {
						$image 		= $this->model_tool_image->resize($value['image'], $width, $height);
					} else {
						$image 		= $default;
					}

					$gethoverimage 	= $this->model_tnt_tntallquery->getproductimage($value['product_id']);
					if(!empty(current($gethoverimage))){
						$hoverimage = $this->model_tool_image->resize(current($gethoverimage)['image'], $width, $height);
					}else{
						$hoverimage = $image;
					}

					if ((float)$value['special']) {
						$special = $this->currency->format($this->tax->calculate($value['special'], $value['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					$date = $this->model_tnt_tntallquery->getproductspecialdate($value['product_id']);
					
					if(isset($date['date_end'])){
						$date_end = $date['date_end'];
					}else{
						$date_end = null;
					} 

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$value['special'] ? $value['special'] : $value['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					$categoryid 	= $this->model_tnt_tntallquery->getcategoryid($value['product_id']);
					$categoryname 	= $this->model_tnt_tntallquery->getcategoryname($categoryid);

					if ($this->config->get('config_review_status')) {
						$rating = $value['rating'];
					} else {
						$rating = false;
					}

					   		
					

					$data['productbests'][] = array(
						'product_id'  	=> $value['product_id'],
						'thumb'       	=> $image,
						'name'        	=> $value['name'],
						'description' 	=> utf8_substr(strip_tags(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'categoryname'  => $categoryname,
						'price'       	=> $price,
						'special'     	=> $special,
						'tax'         	=> $tax,
						'hoverimage'  	=> $hoverimage,
						'rating'      	=> $rating,
						'date_end'    	=> $date_end,
						'href'        	=> $this->url->link('product/product', 'product_id=' . $value['product_id'])
					);
				}
			}
			
			return $this->load->view('extension/module/tntleftrightproduct', $data);
		}
	}
}