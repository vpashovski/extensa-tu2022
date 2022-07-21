<?php

class ModelTnttntslider extends Model {
    
    public function sortingdata($position){
        foreach ($position as $key => $value) {
            $pos = $key + 1;
            $this->db->query("UPDATE " . DB_PREFIX . "tntsliderparent SET tntsliderparent_position = '" . (int)$pos . "' WHERE tntsliderparent_id = '" . (int)$value . "'");
        }    
    }

    public function copyimageslider($tntsliderparent_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "tntsliderparent p WHERE p.tntsliderparent_id = '" . (int)$tntsliderparent_id . "'");
        if ($query->num_rows) {
            $data                    = $query->row;
            $data['tntslider']       = $this->getimageslidercopy($tntsliderparent_id);
            $this->insertrecord($data);
        }
    }

    public function getimageslidercopy($tntsliderparent_id) {
        $image_slider_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntsliderchild WHERE tntsliderparent_id = '" . (int)$tntsliderparent_id . "'");

        foreach ($query->rows as $result) {
            $image_slider_data[$result['tntsliderchildlang_id']] = array(
                'tntsliderchild_link'              => $result['tntsliderchild_link'],
                'tntsliderchild_image'             => $result['tntsliderchild_image'],
                'tntsliderchild_subtitle'          => $result['tntsliderchild_subtitle'],
                'tntsliderchild_title'             => $result['tntsliderchild_title'],
                'tntsliderchild_textaligment'      => $result['tntsliderchild_textaligment'],
                'tntsliderchild_buttontext'        => $result['tntsliderchild_buttontext'],
                'tntsliderchild_description'       => $result['tntsliderchild_description'],
                'tntsliderchild_enable'            => $result['tntsliderchild_enable'],
                'lang'                             => $result['tntsliderchildlang_id']
            );
        }
        return $image_slider_data;
    }

    public function insertrecord($data) {

        $query = "SELECT MAX(tntsliderparent_id) as id FROM `" . DB_PREFIX . "tntsliderparent`";
        $query = $this->db->query($query);
        $data['id'] = $query->row['id'] + 1;
        
        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntsliderparent`
            SET 
                        tntsliderparent_id = '.$data["id"].',
                        tntsliderparent_position = '.$data['id'].';');

        foreach ($data['tntslider'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntsliderchild`
                        SET 
                            tntsliderparent_id = '.$data["id"].',
                            tntsliderchild_link = "'.$value['tntsliderchild_link'].'",
                            tntsliderchild_image = "'.$value['tntsliderchild_image'].'",
                            tntsliderchild_title = "'.$value['tntsliderchild_title'].'",
                            tntsliderchild_subtitle = "'.$value['tntsliderchild_subtitle'].'",
                            tntsliderchild_textaligment = "'.$value['tntsliderchild_textaligment'].'",
                            tntsliderchild_buttontext = "'.$value['tntsliderchild_buttontext'].'",
                            tntsliderchild_shortdescription = "'.$value['tntsliderchild_shortdescription'].'",
                            tntsliderchild_description = "'.$value['tntsliderchild_description'].'",
                            tntsliderchild_enable = '.$value['tntsliderchild_enable'].',
                            tntsliderchildlang_id = '.$value['lang'].'');
        }
    }

    public function gettottaldata(){
        $sql  = "SELECT COUNT(DISTINCT tntsliderparent_id) AS total FROM `" . DB_PREFIX . "tntsliderparent`";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function editimageslider($tntsliderparent_id, $data) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntsliderchild WHERE tntsliderparent_id = '" . (int)$tntsliderparent_id . "'");
        foreach ($data['tntslider'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntsliderchild`
                        SET 
                            tntsliderparent_id = '.$tntsliderparent_id.',
                            tntsliderchild_image = "'.$value['tntsliderchild_image'].'",
                            tntsliderchild_link = "'.$value['tntsliderchild_link'].'",
                            tntsliderchild_title = "'.$value['tntsliderchild_title'].'",
                            tntsliderchild_subtitle = "'.$value['tntsliderchild_subtitle'].'",
                            tntsliderchild_textaligment = "'.$value['tntsliderchild_textaligment'].'",
                            tntsliderchild_buttontext = "'.$value['tntsliderchild_buttontext'].'",
                            tntsliderchild_description = "'.$value['tntsliderchild_description'].'",
                            tntsliderchild_enable = '.$value['tntsliderchild_enable'].',
                            tntsliderchildlang_id = '.$value['lang'].'');
        }
    }
    
    public function deleteimageslider($tntsliderparent_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntsliderparent WHERE tntsliderparent_id = '" . (int)$tntsliderparent_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntsliderchild WHERE tntsliderparent_id = '" . (int)$tntsliderparent_id . "'");
        $this->cache->delete('tntsliderparent');
        $this->cache->delete('tntsliderchild');
    }

    public function getimageslidesingle($tntsliderparent_id) {
        
        $query = $this->db->query("SELECT  " . DB_PREFIX . "tntsliderchild.*,  " . DB_PREFIX . "tntsliderparent.* FROM   " . DB_PREFIX . "tntsliderchild
            INNER JOIN  " . DB_PREFIX . "tntsliderparent ON  
             " . DB_PREFIX . "tntsliderchild.tntsliderparent_id =  " . DB_PREFIX . "tntsliderparent.tntsliderparent_id
            WHERE  " . DB_PREFIX . "tntsliderchild.tntsliderparent_id = '" . (int)$tntsliderparent_id . "'");

        return  $query->rows;
    }

    public function getsliderimage($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "tntsliderparent p LEFT JOIN " . DB_PREFIX . "tntsliderchild pd ON (p.tntsliderparent_id = pd.tntsliderparent_id) WHERE pd.tntsliderchildlang_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntsliderchild_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND pd.tntsliderchild_enable = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.tntsliderparent_id";

        $sort_data = array(
            'pd.tntsliderchild_title',
            'pd.tntsliderchild_subtitle',
            'pd.tntsliderchild_link',
            'pd.tntsliderchild_description',
            'pd.tntsliderchild_textaligment',
            'pd.tntsliderchild_buttontext',
            'pd.tntsliderchild_enable'         
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.tntsliderparent_position";
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

    public function gettotalsliderimage($data = array()) {
        $sql  = "SELECT COUNT(DISTINCT tntsliderchild_id) AS total FROM `" . DB_PREFIX . "tntsliderchild`";

        $sql .= " WHERE tntsliderchildlang_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND tntsliderchild_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND tntsliderchild_enable = '" . (int)$data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }
    public function getmodulename() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` like 'tnt%'");
        return $query->rows;
    }
}