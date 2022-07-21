<?php

class ModelTnttnttestimonial extends Model {
    public function sortingdata($position){
        foreach ($position as $key => $value) {
            $pos = $key + 1;
            $this->db->query("UPDATE " . DB_PREFIX . "tnttestimonialparent SET tnttestimonialparent_position = '" . (int)$pos . "' WHERE tnttestimonialparent_id = '" . (int)$value . "'");
        }    
    }

    public function copytestimonial($tnttestimonialparent_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "tnttestimonialparent p WHERE p.tnttestimonialparent_id = '" . (int)$tnttestimonialparent_id . "'");

        if ($query->num_rows) {
            $data                               = $query->row;
            $data['tnttestimonial']           = $this->getimageslidercopy($tnttestimonialparent_id);
            $data['tnttestimonialparent_link']  = $data['tnttestimonialparent_link'];

            $this->insertrecord($data);
        }
    }

    public function getimageslidercopy($tnttestimonialparent_id) {
        $image_slider_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tnttestimonialchild WHERE tnttestimonialparent_id = '" . (int)$tnttestimonialparent_id . "'");

        foreach ($query->rows as $result) {
            $image_slider_data[$result['tnttestimonialchildlanguage_id']] = array(
                'tnttestimonialchild_name'         => $result['tnttestimonialchild_name'],
                'tnttestimonialchild_designation'         => $result['tnttestimonialchild_designation'],
                'tnttestimonialchild_description'   => $result['tnttestimonialchild_description'],
                'lang'                              => $result['tnttestimonialchildlanguage_id']
            );
        }

        return $image_slider_data;
    }

    public function insertrecord($data) {
        $query = "SELECT MAX(tnttestimonialparent_id) as id FROM `" . DB_PREFIX . "tnttestimonialparent`";
        $query = $this->db->query($query);

        $data['id'] = $query->row['id'] + 1;

        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tnttestimonialparent`
            SET
                        tnttestimonialparent_id         = '.$data["id"].',
                        tnttestimonialparent_image         = "'.$data['tnttestimonialparent_image'].'" ,
                        tnttestimonialparent_link         = "'.$data['tnttestimonialparent_link'].'" ,
                        tnttestimonialparent_status         = "'.$data['tnttestimonialparent_status'].'" ,
                        tnttestimonialparent_position        = '.$data['id'].';');

        foreach ($data['tnttestimonial'] as $language_id => $value) {
            
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tnttestimonialchild`
                        SET
                            tnttestimonialparent_id             = '.$data["id"].',
                            tnttestimonialchild_name           = "'.$value['tnttestimonialchild_name'].'",
                            tnttestimonialchild_designation           = "'.$value['tnttestimonialchild_designation'].'",
                            tnttestimonialchild_description     = "'.$value['tnttestimonialchild_description'].'",
                            tnttestimonialchildlanguage_id          = '.$value['lang'].'');
        }

    }

    public function gettottaldata(){
        $sql  = "SELECT COUNT(DISTINCT tnttestimonialparent_id) AS total FROM `" . DB_PREFIX . "tnttestimonialparent`";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function edittestimonial($tnttestimonialparent_id, $data) {
        
        $this->db->query('UPDATE `' . DB_PREFIX . 'tnttestimonialparent`
            SET 
            tnttestimonialparent_image = "'.$data['tnttestimonialparent_image'].'",
            tnttestimonialparent_link  = "'.$data['tnttestimonialparent_link'].'",
            tnttestimonialparent_status = "'.$data['tnttestimonialparent_status'].'"
             WHERE tnttestimonialparent_id = "' . (int)$tnttestimonialparent_id . '" ');
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "tnttestimonialchild WHERE tnttestimonialparent_id = '" . (int)$tnttestimonialparent_id . "'");
        
        foreach ($data['tnttestimonial'] as $language_id => $value) {
            
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tnttestimonialchild`
                SET 
                    tnttestimonialparent_id = '.$tnttestimonialparent_id.',
                    tnttestimonialchild_name = "'.$value['tnttestimonialchild_name'].'",
                    tnttestimonialchild_designation = "'.$value['tnttestimonialchild_designation'].'",
                    tnttestimonialchild_description = "'.$value['tnttestimonialchild_description'].'",
                    tnttestimonialchildlanguage_id = '.$value['lang'].'');
        }
    }
    
    public function deletetestimonial($tnttestimonialparent_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tnttestimonialparent WHERE tnttestimonialparent_id = '" . (int)$tnttestimonialparent_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "tnttestimonialchild WHERE tnttestimonialparent_id = '" . (int)$tnttestimonialparent_id . "'");
    
        $this->cache->delete('tnttestimonialparent');
        $this->cache->delete('tnttestimonialchild');
    }

    public function gettestimonialsingle($tnttestimonialparent_id) {
        
        $query = $this->db->query("SELECT  " . DB_PREFIX . "tnttestimonialchild.*, " . DB_PREFIX . "tnttestimonialparent.* FROM  " . DB_PREFIX . "tnttestimonialchild
            INNER JOIN " . DB_PREFIX . "tnttestimonialparent ON  
            " . DB_PREFIX . "tnttestimonialchild.tnttestimonialparent_id = " . DB_PREFIX . "tnttestimonialparent.tnttestimonialparent_id
            WHERE " . DB_PREFIX . "tnttestimonialchild.tnttestimonialparent_id = '" . (int)$tnttestimonialparent_id . "'");

        return  $query->rows;
    }

    public function gettestimonial($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "tnttestimonialparent p LEFT JOIN " . DB_PREFIX . "tnttestimonialchild pd ON (p.tnttestimonialparent_id = pd.tnttestimonialparent_id) WHERE pd.tnttestimonialchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tnttestimonialchild_name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tnttestimonialparent_status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.tnttestimonialparent_id";

        $sort_data = array(
            'pd.tnttestimonialchild_name',
            'pd.tnttestimonialchild_description',
            'pd.tnttestimonialchild_designation',
            'p.tnttestimonialparent_status'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.tnttestimonialparent_position";
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

    public function getTotaltestimonial($data = array()) {

        $sql = "SELECT COUNT(DISTINCT tnttestimonialchild_id) AS total FROM " . DB_PREFIX . "tnttestimonialparent p LEFT JOIN " . DB_PREFIX . "tnttestimonialchild pd ON (p.tnttestimonialparent_id = pd.tnttestimonialparent_id) WHERE pd.tnttestimonialchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tnttestimonialchild_name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tnttestimonialparent_status = '" . (int)$data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }
}