<?php

class ModelTnttntpaymenticon extends Model {
    public function updatePosition($position){
        foreach ($position as $key => $value) {
            $pos = $key + 1;
            $this->db->query("UPDATE " . DB_PREFIX . "tntpaymenticonparent SET tntpaymenticonparent_position = '" . (int)$pos . "' WHERE tntpaymenticonparent_id = '" . (int)$value . "'");
        }    
    }

    public function copypaymenticon($tntpaymenticonparent_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "tntpaymenticonparent p WHERE p.tntpaymenticonparent_id = '" . (int)$tntpaymenticonparent_id . "'");
        if ($query->num_rows) {
            $data                                 = $query->row;
            $datas['tntpaymenticon']              = $this->getpaymenticoncopy($tntpaymenticonparent_id);
            $datas['tntpaymenticonparent_link']   = $data['tntpaymenticonparent_link'];
            $datas['tntpaymenticonparent_image']  = $query->row['tntpaymenticonparent_image'];
            $datas['tntpaymenticonparent_status'] = $query->row['tntpaymenticonparent_status'];
            $this->insertrecord($datas);
        }
    }

    public function getpaymenticoncopy($tntpaymenticonparent_id) {
        $paymenticon = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntpaymenticonchild WHERE tntpaymenticonparent_id = '" . (int)$tntpaymenticonparent_id . "'");

        foreach ($query->rows as $result) {
            $paymenticon[$result['tntpaymenticonchildlanguage_id']] = array(
                'tntpaymenticonchild_title'         => $result['tntpaymenticonchild_title'],
                'tntpaymenticonchild_designation'   => $result['tntpaymenticonchild_designation'],
                'tntpaymenticonchild_description'   => $result['tntpaymenticonchild_description'],
                'language'                          => $result['tntpaymenticonchildlanguage_id']
            );
        }

        return $paymenticon;
    }

    public function insertrecord($data) {
        $query          = "SELECT MAX(tntpaymenticonparent_id) as id FROM `" . DB_PREFIX . "tntpaymenticonparent`";
        $query          = $this->db->query($query);
        $data['id']     = $query->row['id'] + 1;
        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntpaymenticonparent`
            SET
                tntpaymenticonparent_id             = '.$data["id"].',
                tntpaymenticonparent_link           = "'.$data['tntpaymenticonparent_link'].'" ,
                tntpaymenticonparent_image          = "'.$data['tntpaymenticonparent_image'].'" ,
                tntpaymenticonparent_status         = "'.$data['tntpaymenticonparent_status'].'" ,
                tntpaymenticonparent_position       = '.$data['id'].';');
        foreach ($data['tntpaymenticon'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntpaymenticonchild`
                SET
                    tntpaymenticonparent_id             = '.$data["id"].',
                    `tntpaymenticonchildlanguage_id`    = '.$language_id.',
                    tntpaymenticonchild_title           = "'.$value['tntpaymenticonchild_title'].'"');
        }
    }

    public function gettottaldata(){
        $sql  = "SELECT COUNT(DISTINCT tntpaymenticonparent_id) AS total FROM `" . DB_PREFIX . "tntpaymenticonparent`";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function editpaymenticon($tntpaymenticonparent_id, $data) {
        $this->db->query('UPDATE `' . DB_PREFIX . 'tntpaymenticonparent`
            SET 
            tntpaymenticonparent_status             = '.$data['tntpaymenticonparent_status'].',
            tntpaymenticonparent_link       = "'.$data['tntpaymenticonparent_link'].'" WHERE tntpaymenticonparent_id = ' . (int)$tntpaymenticonparent_id . '');

        $this->db->query("DELETE FROM " . DB_PREFIX . "tntpaymenticonchild WHERE tntpaymenticonparent_id = '" . (int)$tntpaymenticonparent_id . "'");
        
        foreach ($data['tntpaymenticon'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntpaymenticonchild`
                SET 
                    tntpaymenticonparent_id         = '.$tntpaymenticonparent_id.',
                    tntpaymenticonchild_title       = "'.$value['tntpaymenticonchild_title'].'",
                    tntpaymenticonchildlanguage_id  = '.$value['language'].'');
        }
    }
    
    public function deletepaymenticon($tntpaymenticonparent_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntpaymenticonparent WHERE tntpaymenticonparent_id = '" . (int)$tntpaymenticonparent_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntpaymenticonchild WHERE tntpaymenticonparent_id = '" . (int)$tntpaymenticonparent_id . "'");
    
        $this->cache->delete('tntpaymenticonparent');
        $this->cache->delete('tntpaymenticonchild');
    }

    public function getpaymenticonsingle($tntpaymenticonparent_id) {
        
        $query = $this->db->query("SELECT  " . DB_PREFIX . "tntpaymenticonchild.*, " . DB_PREFIX . "tntpaymenticonparent.* FROM  " . DB_PREFIX . "tntpaymenticonchild
            INNER JOIN " . DB_PREFIX . "tntpaymenticonparent ON  
            " . DB_PREFIX . "tntpaymenticonchild.tntpaymenticonparent_id = " . DB_PREFIX . "tntpaymenticonparent.tntpaymenticonparent_id
            WHERE " . DB_PREFIX . "tntpaymenticonchild.tntpaymenticonparent_id = '" . (int)$tntpaymenticonparent_id . "'");

        return  $query->rows;
    }

    public function getpaymenticon($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "tntpaymenticonparent p LEFT JOIN " . DB_PREFIX . "tntpaymenticonchild pd ON (p.tntpaymenticonparent_id = pd.tntpaymenticonparent_id) WHERE pd.tntpaymenticonchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntpaymenticonchild_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tntpaymenticonparent_status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.tntpaymenticonparent_id";

        $sort_data = array(
            'pd.tntpaymenticonchild_title',
            'p.tntpaymenticonparent_link',
            'pd.tntpaymenticonchild_designation',
            'pd.tntpaymenticonchild_description',
            'p.tntpaymenticonparent_status'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.tntpaymenticonparent_position";
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

    public function gettotalpaymenticon($data = array()) {

        $sql = "SELECT COUNT(DISTINCT tntpaymenticonchild_id) AS total FROM " . DB_PREFIX . "tntpaymenticonparent p LEFT JOIN " . DB_PREFIX . "tntpaymenticonchild pd ON (p.tntpaymenticonparent_id = pd.tntpaymenticonparent_id) WHERE pd.tntpaymenticonchildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntpaymenticonchild_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tntpaymenticonparent_status = '" . (int)$data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }
}