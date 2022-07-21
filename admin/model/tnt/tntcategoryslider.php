<?php
class ModelTnttntcategoryslider extends Model {

    public function updatePosition($position){
        foreach ($position as $key => $value) {
            $pos = $key + 1;
            $this->db->query("UPDATE " . DB_PREFIX . "tntcategorysliderparent SET tntcategorysliderparent_position = '" . (int)$pos . "' WHERE tntcategorysliderparent_id = '" . (int)$value . "'");
        }    
    }

    public function copycategoryslider($tntcategorysliderparent_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "tntcategorysliderparent p WHERE p.tntcategorysliderparent_id = '" . (int)$tntcategorysliderparent_id . "'");
        if ($query->num_rows) {
            
            $data = $query->row;
            $dataa['tntcategorysliderparent_image']         = $data['tntcategorysliderparent_image'];
            $dataa['tntcategorysliderparent_category_id']   = $data['tntcategorysliderparent_category_id'];
            $dataa['tntcategorysliderparent_status']        = $data['tntcategorysliderparent_status'];
            $dataa['tntcategoryslider']                     = $this->getcategoryslidercopy($tntcategorysliderparent_id);

            $this->add($dataa);
        }
    }

    public function getcategoryname($cate_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$cate_id . "'");

        return $query->row;
    }

    public function getcategoryslidercopy($tntcategorysliderparent_id) {
        $category_slider_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntcategorysliderchild WHERE tntcategorysliderparent_id = '" . (int)$tntcategorysliderparent_id . "'");

        foreach ($query->rows as $result) {
            $category_slider_data[$result['tntcategorysliderchildlanguage_id']] = array(
                'tntcategorysliderchild_name'             => $result['tntcategorysliderchild_name'],
                'lang'                                  => $result['tntcategorysliderchildlanguage_id'],
                'tntcategorysliderchild_description'             => $result['tntcategorysliderchild_description']
            );
        }

        return $category_slider_data;
    }

    public function add($data) {
        $query = "SELECT MAX(tntcategorysliderparent_id) as id FROM `" . DB_PREFIX . "tntcategorysliderparent`";
        $query = $this->db->query($query);
        $data['id'] = $query->row['id'] + 1;
        
        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntcategorysliderparent`
            SET 
                        tntcategorysliderparent_id          = '.$data["id"].',
                        tntcategorysliderparent_category_id = "'.$data['tntcategorysliderparent_category_id'].'" ,
                        tntcategorysliderparent_image       = "'.$data['tntcategorysliderparent_image'].'",
                        tntcategorysliderparent_position         = '.$data["id"].',
                        tntcategorysliderparent_status      = '.$data['tntcategorysliderparent_status'].';');

        foreach ($data['tntcategoryslider'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntcategorysliderchild`
                        SET 
                            tntcategorysliderparent_id      = '.$data["id"].',
                            tntcategorysliderchild_name     = "'.$value['tntcategorysliderchild_name'].'",
                            tntcategorysliderchild_description      = "'.$value['tntcategorysliderchild_description'].'",
                            tntcategorysliderchildlanguage_id   = '.$value['lang'].'');
        }
    }

    public function gettottaldata(){
        $sql  = "SELECT COUNT(DISTINCT tntcategorysliderparent_id) AS total FROM `" . DB_PREFIX . "tntcategorysliderparent`";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function editcategoryslider($tntcategorysliderparent_id, $data) {

        $this->db->query('UPDATE `' . DB_PREFIX . 'tntcategorysliderparent`
            SET 
                        tntcategorysliderparent_category_id = "'.$data['tntcategorysliderparent_category_id'].'",
                        tntcategorysliderparent_image       = "'.$data['tntcategorysliderparent_image'].'",
                        tntcategorysliderparent_status      = '.$data['tntcategorysliderparent_status'].'
                        WHERE tntcategorysliderparent_id = "' . (int)$tntcategorysliderparent_id . '" ');
        

        $this->db->query("DELETE FROM " . DB_PREFIX . "tntcategorysliderchild WHERE tntcategorysliderparent_id = '" . (int)$tntcategorysliderparent_id . "'");      
        foreach ($data['tntcategoryslider'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntcategorysliderchild`
                        SET 
                            tntcategorysliderparent_id      = '.$tntcategorysliderparent_id.',
                            tntcategorysliderchild_name     = "'.$value['tntcategorysliderchild_name'].'",
                            tntcategorysliderchild_description      = "'.$value['tntcategorysliderchild_description'].'",
                            tntcategorysliderchildlanguage_id   = '.$value['lang'].'');
        }
    }
    
    public function deletecategoryslider($tntcategorysliderparent_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntcategorysliderparent WHERE tntcategorysliderparent_id = '" . (int)$tntcategorysliderparent_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntcategorysliderchild WHERE tntcategorysliderparent_id = '" . (int)$tntcategorysliderparent_id . "'");
    
        $this->cache->delete('tntcategorysliderparent');
        $this->cache->delete('tntcategorysliderchild');
    }

    public function getcateimageslidesigle($tntcategorysliderparent_id) {
        
        $query = $this->db->query("SELECT  " . DB_PREFIX . "tntcategorysliderchild.*, " . DB_PREFIX . "tntcategorysliderparent.* FROM  " . DB_PREFIX . "tntcategorysliderchild
            INNER JOIN " . DB_PREFIX . "tntcategorysliderparent ON  
            " . DB_PREFIX . "tntcategorysliderchild.tntcategorysliderparent_id = " . DB_PREFIX . "tntcategorysliderparent.tntcategorysliderparent_id
            WHERE " . DB_PREFIX . "tntcategorysliderchild.tntcategorysliderparent_id = '" . (int)$tntcategorysliderparent_id . "'");

        return  $query->rows;
    }

    public function getcategoryslider($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "tntcategorysliderparent p LEFT JOIN " . DB_PREFIX . "tntcategorysliderchild pd ON (p.tntcategorysliderparent_id = pd.tntcategorysliderparent_id) WHERE pd.tntcategorysliderchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";


        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntcategorysliderchild_name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tntcategorysliderparent_status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.tntcategorysliderparent_id";

        $sort_data = array(
            'pd.tntcategorysliderchild_name',
            'pd.tntcategorysliderchild_description',
            'p.tntcategorysliderparent_status'          
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.tntcategorysliderparent_position";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
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
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function gettotalcategorysliders($data = array()) {
        $sql = "SELECT COUNT(DISTINCT tntcategorysliderchild_id) AS total FROM " . DB_PREFIX . "tntcategorysliderparent p LEFT JOIN " . DB_PREFIX . "tntcategorysliderchild pd ON (p.tntcategorysliderparent_id = pd.tntcategorysliderparent_id) WHERE pd.tntcategorysliderchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";


        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntcategorysliderchild_name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tntcategorysliderparent_status = '" . (int)$data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getCategories($data = array()) {
        $sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c1.status = '1'";
        
        $sql .= " GROUP BY cp.category_id";

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
}