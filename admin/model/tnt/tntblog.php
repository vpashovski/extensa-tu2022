<?php
class ModelTnttntblog extends Model {
    
    public function sortdata($position){
        foreach ($position as $key => $value) {
            $pos = $key + 1;
            $this->db->query("UPDATE " . DB_PREFIX . "tntblog_parent SET tntblog_parent_position = '" . (int)$pos . "' WHERE tntblog_parent_id = '" . (int)$value . "'");
        }    
    }

    public function copyblogdata($tntblog_parent_id) {

        $query      = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntblog_parent WHERE tntblog_parent_id = '" . (int)$tntblog_parent_id . "'");
        $miandata   = $query->row;
        $numcheck   = $query->num_rows;

        if ($numcheck) {
            $dataa['tntblog_parent_positionttype']           = $miandata['tntblog_parent_positionttype'];
            $dataa['tntblog_parent_featureimage']       = $miandata['tntblog_parent_featureimage'];
            $dataa['tntblog_parent_url']         = $miandata['tntblog_parent_url'];
            $dataa['tntblog_parent_deafultcategory']    = $miandata['tntblog_parent_deafultcategory'];
            $dataa['tntblog_parent_video']              = $miandata['tntblog_parent_video'];
            $dataa['tntblog_parent_commentstatus']      = $miandata['tntblog_parent_commentstatus'];
            $dataa['tntblog_parent_createdate']            = $miandata['tntblog_parent_createdate'];
            $dataa['tntblog_parent_status']             = $miandata['tntblog_parent_status'];
            $dataa['tntblogform']                     = $this->getcopyblogdata($tntblog_parent_id);
            $dataa['gallery']                           = $this->getgalleryImages($tntblog_parent_id);
            $this->addrecord($dataa);
        }
    }

    public function getcopyblogdata($tntblog_parent_id) {
        $image_slider_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntblog_child WHERE tntblog_parent_id = '" . (int)$tntblog_parent_id . "'");
        foreach ($query->rows as $result) {
            $image_slider_data[$result['tntblog_child_languages_id']] = array(
                'tntblog_child_languages_id'       => $result['tntblog_child_languages_id'],
                'tntblog_child_title'        => $result['tntblog_child_title'],
                'tntblog_child_excerpt'      => $result['tntblog_child_excerpt'],
                'tntblog_child_content'      => $result['tntblog_child_content'],
                'tntblog_child_metatitle'    => $result['tntblog_child_metatitle'],
                'tntblog_child_metatag'      => $result['tntblog_child_metatag'],
                'tntblog_child_meta_description'      => $result['tntblog_child_meta_description'],
                'tntblog_child_metakeyword'  => $result['tntblog_child_metakeyword']
            );
        }
        return $image_slider_data;
    }

    public function addrecord($data) {
        $query = "SELECT MAX(tntblog_parent_id) as id FROM `" . DB_PREFIX . "tntblog_parent`";
        $query = $this->db->query($query);
        $data['id'] = $query->row['id'] + 1;

        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblog_parent`
            SET 
                        tntblog_parent_id               = '.$data["id"].',
                        tntblog_parent_position              = '.$data["id"].',
                        tntblog_parent_status           = '.$data["tntblog_parent_status"].',
                        tntblog_parent_positionttype         = "'.$data["tntblog_parent_positionttype"].'",
                        tntblog_parent_featureimage     = "'.$data["tntblog_parent_featureimage"].'",
                        tntblog_parent_deafultcategory  = '.$data["tntblog_parent_deafultcategory"].',
                        tntblog_parent_url       = "'.$data["tntblog_parent_url"].'",
                        tntblog_parent_video            = "'.$data["tntblog_parent_video"].'",
                        tntblog_parent_createdate          = NOW(),
                        tntblog_parent_commentstatus    = '.$data["tntblog_parent_commentstatus"].'');

        foreach ($data['tntblogform'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblog_child`
                        SET 
                            tntblog_parent_id           = '.$data["id"].',
                            tntblog_child_title         = "'.$value['tntblog_child_title'].'",
                            tntblog_child_excerpt       = "'.$value['tntblog_child_excerpt'].'",
                            tntblog_child_content       = "'.$value['tntblog_child_content'].'",
                            tntblog_child_metatitle     = "'.$value['tntblog_child_metatitle'].'",
                            tntblog_child_metatag       = "'.$value['tntblog_child_metatag'].'",
                            tntblog_child_meta_description       = "'.$value['tntblog_child_meta_description'].'",
                            tntblog_child_metakeyword   = "'.$value['tntblog_child_metakeyword'].'",
                            tntblog_child_languages_id        = '.$value['tntblog_child_languages_id'].'');
        }
        foreach ($data['gallery'] as $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblog_gallery`
                SET 
                    tntblog_id            = '.$data["id"].',
                    image = "'.$value['image'].'"');
        }
    }

    public function gettotalrecord() {
        $sql  = "SELECT COUNT(DISTINCT tntblog_parent_id) AS total FROM `" . DB_PREFIX . "tntblog_parent`";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function editdatablog($tntblog_parent_id, $data) {
        
        $this->db->query('UPDATE `' . DB_PREFIX . 'tntblog_parent`
            SET 
                        tntblog_parent_position              = '.$tntblog_parent_id.',
                        tntblog_parent_status           = '.$data["tntblog_parent_status"].',
                        tntblog_parent_positionttype         = "'.$data["tntblog_parent_positionttype"].'",
                        tntblog_parent_featureimage     = "'.$data["tntblog_parent_featureimage"].'",
                        tntblog_parent_deafultcategory  = '.$data["tntblog_parent_deafultcategory"].',
                        tntblog_parent_url       = "'.$data["tntblog_parent_url"].'",
                        tntblog_parent_video            = "'.$data["tntblog_parent_video"].'",
                        tntblog_parent_commentstatus    = '.$data["tntblog_parent_commentstatus"].'
            WHERE
                        tntblog_parent_id               = '.$tntblog_parent_id.'');
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntblog_child WHERE tntblog_parent_id = '" . (int)$tntblog_parent_id . "'");
        foreach ($data['tntblogform'] as $language_id => $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblog_child`
                        SET 
                            tntblog_parent_id       = '.$tntblog_parent_id.',
                            tntblog_child_title         = "'.$value['tntblog_child_title'].'",
                            tntblog_child_excerpt       = "'.$value['tntblog_child_excerpt'].'",
                            tntblog_child_content       = "'.$value['tntblog_child_content'].'",
                            tntblog_child_metatitle     = "'.$value['tntblog_child_metatitle'].'",
                            tntblog_child_metatag       = "'.$value['tntblog_child_metatag'].'",
                            tntblog_child_meta_description       = "'.$value['tntblog_child_meta_description'].'",
                            tntblog_child_metakeyword   = "'.$value['tntblog_child_metakeyword'].'",
                            tntblog_child_languages_id        = '.$value['tntblog_child_languages_id'].'');
        }
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntblog_gallery WHERE tntblog_id = '" . (int)$tntblog_parent_id . "'");
        foreach ($data['gallery'] as $value) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntblog_gallery`
                SET 
                    tntblog_id    = '.$tntblog_parent_id.',
                    image           = "'.$value['image'].'"');
        }   
    }
    
    public function blogdatadelete($tntblog_parent_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntblog_parent WHERE tntblog_parent_id = '" . (int)$tntblog_parent_id . "' ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "tntblog_child  WHERE tntblog_parent_id  = '" . (int)$tntblog_parent_id . "' ");
    

        $this->cache->delete('tntblog_parent');
        $this->cache->delete('tntblog_child');
    }

    public function getsingleblog($tntblog_parent_id) {
        
        $query = $this->db->query("SELECT  " . DB_PREFIX . "tntblog_child.*, " . DB_PREFIX . "tntblog_parent.* FROM  " . DB_PREFIX . "tntblog_child
            INNER JOIN " . DB_PREFIX . "tntblog_parent ON  
            " . DB_PREFIX . "tntblog_child.tntblog_parent_id = " . DB_PREFIX . "tntblog_parent.tntblog_parent_id
            WHERE " . DB_PREFIX . "tntblog_child.tntblog_parent_id = '" . (int)$tntblog_parent_id . "'");

        return  $query->rows;
    }

    public function getblogdatarecord($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "tntblog_parent p LEFT JOIN " . DB_PREFIX . "tntblog_child pd ON (p.tntblog_parent_id = pd.tntblog_parent_id) WHERE pd.tntblog_child_languages_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntblog_child_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tntblog_parent_status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.tntblog_parent_id";

        $sort_data = array(
            'pd.tntblog_child_title',
            'pd.tntblog_child_excerpt',
            'p.tntblog_parent_url',
            'pd.tntblog_des_sub'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.tntblog_parent_position";
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

        $sql = "SELECT * FROM " . DB_PREFIX . "tntblog_parent p LEFT JOIN " . DB_PREFIX . "tntblog_child pd ON (p.tntblog_parent_id = pd.tntblog_parent_id) WHERE pd.tntblog_child_languages_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.tntblog_child_title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.tntblog_parent_status = '" . (int)$data['filter_status'] . "'";
        }
        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    public function getgalleryImages($tntblog_parent_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntblog_gallery WHERE tntblog_id = '" . (int)$tntblog_parent_id . "'");

        return $query->rows;
    }
    public function getblogdatarecordcategory($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "tntblogcategory_parent p LEFT JOIN " . DB_PREFIX . "tntblogcategory_sub pd ON (p.tntblogcategory_id = pd.tntblogcategory_id) WHERE pd.tntblogcategory_sublang_id = '" . (int)$this->config->get('config_language_id') . "'";


        $sql .= " GROUP BY p.tntblogcategory_id";



        $query = $this->db->query($sql);
        return $query->rows;
    }

}