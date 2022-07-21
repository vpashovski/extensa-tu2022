<?php

class ModelTnttntsocialicon extends Model {
    public function sortingdata($position){
        foreach ($position as $key => $value) {
            $pos = $key + 1;
            $this->db->query("UPDATE " . DB_PREFIX . "tntsocialiconparent SET tntsocialiconparent_position   = '" . (int)$position . "' WHERE tntsocialiconparent_id = '" . (int)$value . "'");
        }    
    }

    public function copysocialicon($tntsocialiconparent_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "tntsocialiconparent p WHERE p.tntsocialiconparent_id = '" . (int)$tntsocialiconparent_id . "'");

        if ($query->num_rows) {
            $data                               = $query->row;
            $data['tntsocialicon']            = $this->getimageslidercopy($tntsocialiconparent_id);
            $data['tntsocialiconparent_link']   = $data['tntsocialiconparent_link'];

            $this->insertrecord($data);
        }
    }

    public function getimageslidercopy($tntsocialiconparent_id) {
        $image_slider_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntsocialiconchild WHERE tntsocialiconparent_id = '" . (int)$tntsocialiconparent_id . "'");

        foreach ($query->rows as $result) {
            $image_slider_data[$result['tntsocialiconchildlanguage_id']] = array(
                'tntsocialiconchild_title'          => $result['tntsocialiconchild_title'],
                'tntsocialiconchild_designation'   => $result['tntsocialiconchild_designation'],
                'tntsocialiconchild_description'    => $result['tntsocialiconchild_description'],
                'lang'                              => $result['tntsocialiconchildlanguage_id']
            );
        }

        return $image_slider_data;
    }

    public function insertrecord($data) {

        $query = "SELECT MAX(tntsocialiconparent_id) as id FROM `" . DB_PREFIX . "tntsocialiconparent`";
        $query = $this->db->query($query);

        $data['id'] = $query->row['id'] + 1;

        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntsocialiconparent`
            SET
                tntsocialiconparent_id          = '.$data["id"].',
                tntsocialiconparent_link        = "'.$data['tntsocialiconparent_link'].'" ,
                tntsocialiconparent_class_name  = "'.$data['tntsocialiconparent_class_name'].'" ,
                tntsocialicon_status          = "'.$data['tntsocialicon_status'].'" ,
                tntsocialiconparent_position         = '.$data['id'].';');
        foreach ($data['tntsocialicon'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntsocialiconchild`
                SET
                    tntsocialiconparent_id         = '.$data["id"].',
                    tntsocialiconchildlanguage_id  = '.$language_id.',
                    tntsocialiconchild_title       = "'.$value['tntsocialiconchild_title'].'"');
        }

    }

    public function gettottaldata(){
        $sql  = "SELECT COUNT(DISTINCT tntsocialiconparent_id) AS total FROM `" . DB_PREFIX . "tntsocialiconparent`";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function editsocialicon($tntsocialiconparent_id, $data) {
    
        $this->db->query('UPDATE `' . DB_PREFIX . 'tntsocialiconparent`
            SET 
            tntsocialicon_status          = '.$data['tntsocialicon_status'].',
            tntsocialiconparent_class_name  = "'.$data['tntsocialiconparent_class_name'].'",
            tntsocialiconparent_link        = "'.$data['tntsocialiconparent_link'].'" WHERE tntsocialiconparent_id = ' . (int)$tntsocialiconparent_id . '');

        $this->db->query("DELETE FROM " . DB_PREFIX . "tntsocialiconchild WHERE tntsocialiconparent_id = '" . (int)$tntsocialiconparent_id . "'");
        
        foreach ($data['tntsocialicon'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntsocialiconchild`
                        SET 
                            tntsocialiconparent_id          = '.$tntsocialiconparent_id.',
                            tntsocialiconchild_title            = "'.$value['tntsocialiconchild_title'].'",
                            tntsocialiconchildlanguage_id           = '.$value['lang'].'');
        }
    }
    
    public function deletesocialicon($tntsocialiconparent_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntsocialiconparent WHERE tntsocialiconparent_id = '" . (int)$tntsocialiconparent_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntsocialiconchild WHERE tntsocialiconparent_id = '" . (int)$tntsocialiconparent_id . "'");
    
        $this->cache->delete('tntsocialiconparent');
        $this->cache->delete('tntsocialiconchild');
    }

    public function getsocialiconsingle($tntsocialiconparent_id) {
        
        
        $query = $this->db->query("SELECT  " . DB_PREFIX . "tntsocialiconchild.*, " . DB_PREFIX . "tntsocialiconparent.* FROM  " . DB_PREFIX . "tntsocialiconchild
            INNER JOIN " . DB_PREFIX . "tntsocialiconparent ON  
            " . DB_PREFIX . "tntsocialiconchild.tntsocialiconparent_id = " . DB_PREFIX . "tntsocialiconparent.tntsocialiconparent_id
            WHERE " . DB_PREFIX . "tntsocialiconchild.tntsocialiconparent_id = '" . (int)$tntsocialiconparent_id . "'");
        return  $query->rows;
    }

    public function getsocialicon($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "tntsocialiconparent p LEFT JOIN " . DB_PREFIX . "tntsocialiconchild pd ON (p.tntsocialiconparent_id = pd.tntsocialiconparent_id) WHERE pd.tntsocialiconchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntsocialiconchild_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tntsocialicon_status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.tntsocialiconparent_id";

        $sort_data = array(
            'pd.tntsocialiconchild_title',
            'p.tntsocialiconparent_link',
            'pd.tntsocialiconchild_designation',
            'pd.tntsocialiconchild_description',
            'p.tntsocialicon_status'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.tntsocialiconparent_position";
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

    public function getTotalsocialicon($data = array()) {

        $sql = "SELECT COUNT(DISTINCT tntsocialiconchild_id) AS total FROM " . DB_PREFIX . "tntsocialiconparent p LEFT JOIN " . DB_PREFIX . "tntsocialiconchild pd ON (p.tntsocialiconparent_id = pd.tntsocialiconparent_id) WHERE pd.tntsocialiconchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntsocialiconchild_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tntsocialicon_status = '" . (int)$data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }
}