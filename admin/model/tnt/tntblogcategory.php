<?php
class ModelTnttntblogcategory extends Model {
    
    public function sortdata($position){
        foreach ($position as $key => $value) {
            $pos = $key + 1;
            $this->db->query("UPDATE " . DB_PREFIX . "tntblogcategory_parent SET tntblogcategory_position = '" . (int)$pos . "' WHERE tntblogcategory_id = '" . (int)$value . "'");
        }    
    }

    public function copyblogdata($tntblogcategory_id) {

        $query      = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntblogcategory_parent WHERE tntblogcategory_id = '" . (int)$tntblogcategory_id . "'");
        $miandata   = $query->row;
        $numcheck   = $query->num_rows;

        if ($numcheck) {
            $dataa['tntblogcategory_featureimage']       = $miandata['tntblogcategory_featureimage'];
            $dataa['tntblogcategory_urlrewrite']         = $miandata['tntblogcategory_urlrewrite'];
            $dataa['tntblogcategory_deafultcategory']    = $miandata['tntblogcategory_deafultcategory'];
            $dataa['tntblogcategory_status']              = $miandata['tntblogcategory_status'];
            $dataa['tntblogcategoryform']                     = $this->getcopyblogdata($tntblogcategory_id);
            $this->addrecord($dataa);
        }
    }

    public function getcopyblogdata($tntblogcategory_id) {
        $image_slider_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntblogcategory_sub WHERE tntblogcategory_id = '" . (int)$tntblogcategory_id . "'");
        foreach ($query->rows as $result) {
            $image_slider_data[$result['tntblogcategory_sublang_id']] = array(
                'tntblogcategory_sublang_id'       => $result['tntblogcategory_sublang_id'],
                'tntblogcategory_sub_title'        => $result['tntblogcategory_sub_title'],
                'tntblogcategory_sub_categorydes'      => $result['tntblogcategory_sub_categorydes'],
                'tntblogcategory_sub_metatitle'    => $result['tntblogcategory_sub_metatitle'],
                'tntblogcategory_sub_metades'      => $result['tntblogcategory_sub_metades'],
                'tntblogcategory_sub_metakeyword'  => $result['tntblogcategory_sub_metakeyword']
            );
        }
        return $image_slider_data;
    }

    public function addrecord($data) {
        $query = "SELECT MAX(tntblogcategory_id) as id FROM `" . DB_PREFIX . "tntblogcategory_parent`";
        $query = $this->db->query($query);
        $data['id'] = $query->row['id'] + 1;

        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblogcategory_parent`
            SET 
                        tntblogcategory_id                = '.$data["id"].',
                        tntblogcategory_position               = '.$data["id"].',
                        tntblogcategory_status            = '.$data["tntblogcategory_status"].',
                        tntblogcategory_featureimage  = "'.$data["tntblogcategory_featureimage"].'",
                        tntblogcategory_deafultcategory   = '.$data["tntblogcategory_deafultcategory"].',
                        tntblogcategory_urlrewrite        = "'.$data["tntblogcategory_urlrewrite"].'"');

        foreach ($data['tntblogcategoryform'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblogcategory_sub`
                        SET 
                            tntblogcategory_id            = '.$data["id"].',
                            tntblogcategory_sub_title         = "'.$value['tntblogcategory_sub_title'].'",
                            tntblogcategory_sub_categorydes       = "'.$value['tntblogcategory_sub_categorydes'].'",
                            tntblogcategory_sub_metatitle     = "'.$value['tntblogcategory_sub_metatitle'].'",
                            tntblogcategory_sub_metades       = "'.$value['tntblogcategory_sub_metades'].'",
                            tntblogcategory_sub_metakeyword   = "'.$value['tntblogcategory_sub_metakeyword'].'",
                            tntblogcategory_sublang_id        = '.$value['tntblogcategory_sublang_id'].'');
        }
        
    }

    public function gettotalrecord() {
        $sql  = "SELECT COUNT(DISTINCT tntblogcategory_id) AS total FROM `" . DB_PREFIX . "tntblogcategory_parent`";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function editdatablog($tntblogcategory_id, $data) {
        
        $this->db->query('UPDATE `' . DB_PREFIX . 'tntblogcategory_parent`
            SET 
                        tntblogcategory_position               = '.$tntblogcategory_id.',
                        tntblogcategory_status            = '.$data["tntblogcategory_status"].',
                        tntblogcategory_featureimage      = "'.$data["tntblogcategory_featureimage"].'",
                        tntblogcategory_deafultcategory   = '.$data["tntblogcategory_deafultcategory"].',
                        tntblogcategory_urlrewrite        = "'.$data["tntblogcategory_urlrewrite"].'"
            WHERE
                        tntblogcategory_id                = '.$tntblogcategory_id.'');
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntblogcategory_sub WHERE tntblogcategory_id = '" . (int)$tntblogcategory_id . "'");
        foreach ($data['tntblogcategoryform'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblogcategory_sub`
                        SET 
                            tntblogcategory_id                = '.$tntblogcategory_id.',
                            tntblogcategory_sub_title     = "'.$value['tntblogcategory_sub_title'].'",
                            tntblogcategory_sub_categorydes   = "'.$value['tntblogcategory_sub_categorydes'].'",
                            tntblogcategory_sub_metatitle     = "'.$value['tntblogcategory_sub_metatitle'].'",
                            tntblogcategory_sub_metades       = "'.$value['tntblogcategory_sub_metades'].'",
                            tntblogcategory_sub_metakeyword   = "'.$value['tntblogcategory_sub_metakeyword'].'",
                            tntblogcategory_sublang_id        = '.$value['tntblogcategory_sublang_id'].'');
        }
        
    }
    
    public function blogdatadelete($tntblogcategory_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntblogcategory_parent WHERE tntblogcategory_id = '" . (int)$tntblogcategory_id . "' ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntblogcategory_sub  WHERE tntblogcategory_id  = '" . (int)$tntblogcategory_id . "' ");
    

        $this->cache->delete('tntblogcategory_parent');
        $this->cache->delete('tntblogcategory_sub');
    }

    public function getsingleblog($tntblogcategory_id) {
        
        $query = $this->db->query("SELECT  " . DB_PREFIX . "tntblogcategory_sub.*, " . DB_PREFIX . "tntblogcategory_parent.* FROM  " . DB_PREFIX . "tntblogcategory_sub
            INNER JOIN " . DB_PREFIX . "tntblogcategory_parent ON  
            " . DB_PREFIX . "tntblogcategory_sub.tntblogcategory_id = " . DB_PREFIX . "tntblogcategory_parent.tntblogcategory_id
            WHERE " . DB_PREFIX . "tntblogcategory_sub.tntblogcategory_id = '" . (int)$tntblogcategory_id . "'");

        return  $query->rows;
    }

    public function getblogdatarecord($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "tntblogcategory_parent p LEFT JOIN " . DB_PREFIX . "tntblogcategory_sub pd ON (p.tntblogcategory_id = pd.tntblogcategory_id) WHERE pd.tntblogcategory_sublang_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntblogcategory_sub_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tntblogcategory_status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.tntblogcategory_id";

        $sort_data = array(
            'pd.tntblogcategory_sub_title',
            'pd.tntblogcategory_sub_categorydes',
            'p.tntblogcategory_urlrewrite',
            'pd.tntblogcategory_des_sub'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.tntblogcategory_position";
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

    public function getblogrecordtotal($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "tntblogcategory_parent p LEFT JOIN " . DB_PREFIX . "tntblogcategory_sub pd ON (p.tntblogcategory_id = pd.tntblogcategory_id) WHERE pd.tntblogcategory_sublang_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntblogcategory_sub_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tntblogcategory_status = '" . (int)$data['filter_status'] . "'";
        }
        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    

}