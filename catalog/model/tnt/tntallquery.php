<?php
class ModelTnttntallquery extends Model {
	public function getsliderlist() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntsliderparent p LEFT JOIN " . DB_PREFIX . "tntsliderchild pd ON (p.tntsliderparent_id = pd.tntsliderparent_id) WHERE pd.tntsliderchildlang_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tntsliderparent_position ");
		return $query->rows;
	}
	public function getbrandlist() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntbrandlist ORDER BY tntbrandlist_position");
		return $query->rows;
	}
	public function gettestimoniallist() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tnttestimonialparent p LEFT JOIN " . DB_PREFIX . "tnttestimonialchild pd ON (p.tnttestimonialparent_id = pd.tnttestimonialparent_id) WHERE pd.tnttestimonialchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tnttestimonialparent_position");
		return $query->rows;
	}
	public function getcommonmoduledetail($name) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module WHERE code = '" . $name . "' ");
		return $query->row;
	}
	public function getsocialiconlist() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntsocialiconparent p LEFT JOIN " . DB_PREFIX . "tntsocialiconchild pd ON (p.tntsocialiconparent_id = pd.tntsocialiconparent_id) WHERE pd.tntsocialiconchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tntsocialiconparent_position ");
		return $query->rows;
	}
	public function geteventlist() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tnteventmanagementparent p LEFT JOIN " . DB_PREFIX . "tnteventmanagementchild pd ON (p.tnteventmanagementparent_id = pd.tnteventmanagementparent_id) WHERE pd.tnteventmanagementchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tnteventmanagementparent_position ");
		return $query->rows;
	}
	public function getProductSpecials($data = array()) {
		$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'rating',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getproduct($result['product_id']);
		}

		return $product_data;
	}
   
    public function getproductspecialdate($product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '".$product_id."' ");
        return $query->row;
    }
    public function getcategoryid($product_id) {
        $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
        if(!empty($query->row['category_id'])){
        	return $query->row['category_id'];
        }else{
        	return 1;
        }
    }
    public function getcategoryname($category_id) {
        $query = $this->db->query("SELECT name FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");
         if(!empty($query->row['name'])){
        	return $query->row['name'];
        }else{
        	return "null";
        }
    }
    public function getproductimage($product_id) {
        $query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product_image WHERE product_id =  '" . (int)$product_id . "'");
            return $query->rows;
    }
    public function getproductvideo($product_id) {
        $query = $this->db->query("SELECT video FROM " . DB_PREFIX . "product_video WHERE product_id =  '" . (int)$product_id . "'");
            return $query->rows;
    }
    public function getproduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return false;
		}
	}	
	public function categoryslider() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntcategorysliderparent p LEFT JOIN " . DB_PREFIX . "tntcategorysliderchild pd ON (p.tntcategorysliderparent_id = pd.tntcategorysliderparent_id) WHERE pd.tntcategorysliderchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tntcategorysliderparent_position");
		return $query->rows;
	}
	public function getpaymentlist() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntpaymenticonparent p LEFT JOIN " . DB_PREFIX . "tntpaymenticonchild pd ON (p.tntpaymenticonparent_id = pd.tntpaymenticonparent_id) WHERE pd.tntpaymenticonchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tntpaymenticonparent_position ");
		return $query->rows;
	}

	public function getimagegallerylist() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntimagegalleryparent p LEFT JOIN " . DB_PREFIX . "tntimagegallerychild pd ON (p.tntimagegalleryparent_id = pd.tntimagegalleryparent_id) WHERE pd.tntimagegallerychildlanguage_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tntimagegalleryparent_position ");
		return $query->rows;
	}
	
	public function getproducts($data = array()) {
		if (!empty($data['filter_name'])) {
			$sql = "SELECT ps.price as special,p.*,pd.* FROM 
				" . DB_PREFIX . "product p 
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
				LEFT JOIN " . DB_PREFIX . "product_special ps ON (p.product_id = ps.product_id)
				WHERE 
				pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sql .= "AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";

			$sql .= "GROUP BY p.product_id";

			$sort_data = array(
				'pd.name',
				'p.sort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY pd.name";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}
			}

			$query = $this->db->query($sql);
			return $query;
		}
	}
	public function checknewsletter($data){
        
        $query = $this->db->query("SELECT * FROM  " . DB_PREFIX . "tntnewsletter  WHERE tntnewsletter_email = '" . $data . "'");
        return  $query->num_rows;
    }
    public function insertnewsletter($data){
        
        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntnewsletter`
            SET 
                        tntnewsletter_email           = "'.$data.'",
                        tntnewsletter_adddate         = NOW()');
    }
    public function getblogdatarecordlist($limit) {
        if(!empty($limit)){
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntblog_parent p LEFT JOIN " . DB_PREFIX . "tntblog_child pd ON (p.tntblog_parent_id = pd.tntblog_parent_id) WHERE pd.tntblog_child_languages_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tntblog_parent_position limit ".$limit."");
        }else{
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntblog_parent p LEFT JOIN " . DB_PREFIX . "tntblog_child pd ON (p.tntblog_parent_id = pd.tntblog_parent_id) WHERE pd.tntblog_child_languages_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tntblog_parent_position");
        }
        return $query->rows;
    }
    public function getblogdatarecordlistpage($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "tntblog_parent p LEFT JOIN " . DB_PREFIX . "tntblog_child pd ON (p.tntblog_parent_id = pd.tntblog_parent_id) WHERE pd.tntblog_child_languages_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tntblog_parent_position";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        $query = $this->db->query($sql);

        return $query->rows;
    }
    public function getTotalgetblogdatarecordlistpage() {
        
        $sql = "SELECT * FROM " . DB_PREFIX . "tntblog_parent p LEFT JOIN " . DB_PREFIX . "tntblog_child pd ON (p.tntblog_parent_id = pd.tntblog_parent_id) WHERE pd.tntblog_child_languages_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY tntblog_parent_position";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);


        return $query->num_rows;
    }
    public function getblogdatarecordsingle($tntblog_parent_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntblog_parent p LEFT JOIN " . DB_PREFIX . "tntblog_child pd ON (p.tntblog_parent_id = pd.tntblog_parent_id) WHERE pd.tntblog_child_languages_id = '" . (int)$this->config->get('config_language_id') . "' and p.tntblog_parent_id = '" . (int)$tntblog_parent_id . "'");

        return $query->row;
    }
    public function getblogdatarecordcategorysigle($tntblogcategory_id) {
        
        $query = $this->db->query("SELECT  " . DB_PREFIX . "tntblogcategory_sub.*, " . DB_PREFIX . "tntblogcategory_parent.* FROM  " . DB_PREFIX . "tntblogcategory_sub
            INNER JOIN " . DB_PREFIX . "tntblogcategory_parent ON  
            " . DB_PREFIX . "tntblogcategory_sub.tntblogcategory_id = " . DB_PREFIX . "tntblogcategory_parent.tntblogcategory_id
            WHERE " . DB_PREFIX . "tntblogcategory_sub.tntblogcategory_id = '" . (int)$tntblogcategory_id . "'");

        return  $query->row;
    }
    public function getblogdatarecordgallery($tntblogcategory_id) {
        $query = $this->db->query("SELECT * FROM  " . DB_PREFIX . "tntblog_gallery    WHERE tntblog_id = '" . (int)$tntblogcategory_id . "'");
        return  $query;
    }
    public function getblogdatarecordcomment($tntblogcategory_id) {
        $query = $this->db->query("SELECT * FROM  " . DB_PREFIX . "tntblog_comment    WHERE tntblog_id = '" . (int)$tntblogcategory_id . "'");
        return  $query->rows;
    }
    public function getblogdatarecordaddcomment($data){
        $query = "SELECT MAX(tntblog_comment_id) as id FROM `" . DB_PREFIX . "tntblog_comment`";
        $query = $this->db->query($query);
        $data['id'] = $query->row['id'] + 1;

        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblog_comment`
            SET 
                        tntblog_comment_id            = '.$data["id"].',
                        tntblog_id                    = '.$data["tntblog_parent_id"].',
                        tntblog_comment_name          = "'.$data["tntblog_comment_name"].'",
                        tntblog_comment_email         = "'.$data["tntblog_comment_email"].'",
                        tntblog_coment_url   = "'.$data["tntblog_coment_url"].'",
                        tntblog_comment_subject       = "'.$data["tntblog_comment_subject"].'",
                        tntblog_comment_comment       = "'.$data["tntblog_comment_comment"].'",
                        tntblog_comment_position           = "'.$data["id"].'",
                        tntblog_comment_createdate = NOW()');
    }
}