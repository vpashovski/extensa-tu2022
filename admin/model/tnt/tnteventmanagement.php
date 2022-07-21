<?php

class ModelTnttnteventmanagement extends Model {
    public function sortingdata($position){
        foreach ($position as $key => $value) {
            $pos = $key + 1;
            $this->db->query("UPDATE " . DB_PREFIX . "tnteventmanagementparent SET tnteventmanagementparent_position   = '" . (int)$position . "' WHERE tnteventmanagementparent_id = '" . (int)$value . "'");
        }    
    }

    public function copysocialicon($tnteventmanagementparent_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "tnteventmanagementparent p WHERE p.tnteventmanagementparent_id = '" . (int)$tnteventmanagementparent_id . "'");

        if ($query->num_rows) {
            $data                               = $query->row;
            $data['tnteventmanagement']            = $this->getimageslidercopy($tnteventmanagementparent_id);
            $data['tnteventmanagementparent_end_date']   = $data['tnteventmanagementparent_end_date'];

            $this->insertrecord($data);
        }
    }

    public function getimageslidercopy($tnteventmanagementparent_id) {
        $image_slider_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tnteventmanagementchild WHERE tnteventmanagementparent_id = '" . (int)$tnteventmanagementparent_id . "'");

        foreach ($query->rows as $result) {
            $image_slider_data[$result['tnteventmanagementchildlanguage_id']] = array(
                'tnteventmanagementchild_title'          => $result['tnteventmanagementchild_title'],
                'tnteventmanagementchild_description'          => $result['tnteventmanagementchild_description'],
                'tnteventmanagementchild_designation'   => $result['tnteventmanagementchild_designation'],
                'tnteventmanagementchild_description'    => $result['tnteventmanagementchild_description'],
                'lang'                              => $result['tnteventmanagementchildlanguage_id']
            );
        }

        return $image_slider_data;
    }

    public function insertrecord($data) {

        $query = "SELECT MAX(tnteventmanagementparent_id) as id FROM `" . DB_PREFIX . "tnteventmanagementparent`";
        $query = $this->db->query($query);

        $data['id'] = $query->row['id'] + 1;

        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tnteventmanagementparent`
            SET
                        tnteventmanagementparent_id          = '.$data["id"].',
                        tnteventmanagementparent_end_date        = "'.$data['tnteventmanagementparent_end_date'].'" ,
                        tnteventmanagementparent_start_date  = "'.$data['tnteventmanagementparent_start_date'].'" ,
                        tnteventmanagement_status          = "'.$data['tnteventmanagement_status'].'" ,
                        tnteventmanagementparent_position         = '.$data['id'].';');
        foreach ($data['tnteventmanagement'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tnteventmanagementchild`
                        SET
                            tnteventmanagementparent_id                     = '.$data["id"].',
                            tnteventmanagementchildlanguage_id              = '.$language_id.',
                            tnteventmanagementchild_description             = "'.$value['tnteventmanagementchild_description'].',"tnteventmanagementchild_title            = "'.$value['tnteventmanagementchild_title'].'"
                            ');
        }

    }

    public function gettottaldata(){
        $sql  = "SELECT COUNT(DISTINCT tnteventmanagementparent_id) AS total FROM `" . DB_PREFIX . "tnteventmanagementparent`";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function editsocialicon($tnteventmanagementparent_id, $data) {
    
        $this->db->query('UPDATE `' . DB_PREFIX . 'tnteventmanagementparent`
            SET 
            tnteventmanagement_status          = '.$data['tnteventmanagement_status'].',
            tnteventmanagementparent_start_date  = "'.$data['tnteventmanagementparent_start_date'].'",
            tnteventmanagementparent_end_date        = "'.$data['tnteventmanagementparent_end_date'].'" WHERE tnteventmanagementparent_id = ' . (int)$tnteventmanagementparent_id . '');

        $this->db->query("DELETE FROM " . DB_PREFIX . "tnteventmanagementchild WHERE tnteventmanagementparent_id = '" . (int)$tnteventmanagementparent_id . "'");
        
        foreach ($data['tnteventmanagement'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tnteventmanagementchild`
                        SET 
                            tnteventmanagementparent_id                 = '.$tnteventmanagementparent_id.',
                            tnteventmanagementchild_title               = "'.$value['tnteventmanagementchild_title'].'",
                            tnteventmanagementchild_description         = "'.$value['tnteventmanagementchild_description'].'",
                            tnteventmanagementchildlanguage_id          = '.$value['lang'].'');
        }
    }
    
    public function deletesocialicon($tnteventmanagementparent_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tnteventmanagementparent WHERE tnteventmanagementparent_id = '" . (int)$tnteventmanagementparent_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "tnteventmanagementchild WHERE tnteventmanagementparent_id = '" . (int)$tnteventmanagementparent_id . "'");
    
        $this->cache->delete('tnteventmanagementparent');
        $this->cache->delete('tnteventmanagementchild');
    }

    public function getsocialiconsingle($tnteventmanagementparent_id) {
        
        
        $query = $this->db->query("SELECT  " . DB_PREFIX . "tnteventmanagementchild.*, " . DB_PREFIX . "tnteventmanagementparent.* FROM  " . DB_PREFIX . "tnteventmanagementchild
            INNER JOIN " . DB_PREFIX . "tnteventmanagementparent ON  
            " . DB_PREFIX . "tnteventmanagementchild.tnteventmanagementparent_id = " . DB_PREFIX . "tnteventmanagementparent.tnteventmanagementparent_id
            WHERE " . DB_PREFIX . "tnteventmanagementchild.tnteventmanagementparent_id = '" . (int)$tnteventmanagementparent_id . "'");
        return  $query->rows;
    }

    public function getsocialicon($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "tnteventmanagementparent p LEFT JOIN " . DB_PREFIX . "tnteventmanagementchild pd ON (p.tnteventmanagementparent_id = pd.tnteventmanagementparent_id) WHERE pd.tnteventmanagementchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tnteventmanagementchild_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_description'])) {
            $sql .= " AND pd.tnteventmanagementchild_description LIKE '" . $this->db->escape($data['filter_description']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tnteventmanagement_status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.tnteventmanagementparent_id";

        $sort_data = array(
            'pd.tnteventmanagementchild_title',
            'pd.tnteventmanagementchild_description',
            'p.tnteventmanagementparent_end_date',
            'pd.tnteventmanagementchild_designation',
            'pd.tnteventmanagementchild_description',
            'p.tnteventmanagement_status'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.tnteventmanagementparent_position";
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

        $sql = "SELECT COUNT(DISTINCT tnteventmanagementchild_id) AS total FROM " . DB_PREFIX . "tnteventmanagementparent p LEFT JOIN " . DB_PREFIX . "tnteventmanagementchild pd ON (p.tnteventmanagementparent_id = pd.tnteventmanagementparent_id) WHERE pd.tnteventmanagementchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tnteventmanagementchild_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_description'])) {
            $sql .= " AND pd.tnteventmanagementchild_description LIKE '" . $this->db->escape($data['filter_description']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tnteventmanagement_status = '" . (int)$data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }
}