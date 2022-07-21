<?php

class ModelTnttntimagegallery extends Model {



    public function updatePosition($position){

        foreach ($position as $key => $value) {

            $pos = $key + 1;

            $this->db->query("UPDATE " . DB_PREFIX . "tntimagegalleryparent SET tntimagegalleryparent_position = '" . (int)$pos . "' WHERE tntimagegalleryparent_id = '" . (int)$value . "'");

        }    

    }



    public function copyimagegallery($tntimagegalleryparent_id) {

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "tntimagegalleryparent p WHERE p.tntimagegalleryparent_id = '" . (int)$tntimagegalleryparent_id . "'");

        if ($query->num_rows) {

            

            $data = $query->row;

            $dataa['tntimagegalleryparent_image']         = $data['tntimagegalleryparent_image'];

            $dataa['tntimagegalleryparent_link']   = $data['tntimagegalleryparent_link'];

            $dataa['tntimagegalleryparent_status']        = $data['tntimagegalleryparent_status'];

            $dataa['tntimagegallery']                     = $this->getimagegallerycopy($tntimagegalleryparent_id);



            $this->add($dataa);

        }

    }



    public function getcategoryname($cate_id) {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$cate_id . "'");



        return $query->row;

    }



    public function getimagegallerycopy($tntimagegalleryparent_id) {

        $category_slider_data = array();



        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntimagegallerychild WHERE tntimagegalleryparent_id = '" . (int)$tntimagegalleryparent_id . "'");



        foreach ($query->rows as $result) {

            $category_slider_data[$result['tntimagegallerychildlanguage_id']] = array(

                'tntimagegallerychild_name'             => $result['tntimagegallerychild_name'],

                'lang'                                  => $result['tntimagegallerychildlanguage_id']

            );

        }



        return $category_slider_data;

    }



    public function add($data) {

        $query = "SELECT MAX(tntimagegalleryparent_id) as id FROM `" . DB_PREFIX . "tntimagegalleryparent`";

        $query = $this->db->query($query);

        $data['id'] = $query->row['id'] + 1;

        

        $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntimagegalleryparent`

            SET 

                        tntimagegalleryparent_id          = '.$data["id"].',

                        tntimagegalleryparent_link = "'.$data['tntimagegalleryparent_link'].'" ,

                        tntimagegalleryparent_image       = "'.$data['tntimagegalleryparent_image'].'",

                        tntimagegalleryparent_position         = '.$data["id"].',

                        tntimagegalleryparent_status      = '.$data['tntimagegalleryparent_status'].';');



        foreach ($data['tntimagegallery'] as $language_id => $value) {

            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntimagegallerychild`

                        SET 

                            tntimagegalleryparent_id      = '.$data["id"].',

                            tntimagegallerychild_name     = "'.$value['tntimagegallerychild_name'].'",

                            tntimagegallerychildlanguage_id   = '.$value['lang'].'');

        }

    }



    public function gettottaldata(){

        $sql  = "SELECT COUNT(DISTINCT tntimagegalleryparent_id) AS total FROM `" . DB_PREFIX . "tntimagegalleryparent`";

        $query = $this->db->query($sql);

        return $query->row['total'];

    }



    public function editimagegallery($tntimagegalleryparent_id, $data) {



        $this->db->query('UPDATE `' . DB_PREFIX . 'tntimagegalleryparent`

            SET 

                        tntimagegalleryparent_link = "'.$data['tntimagegalleryparent_link'].'",

                        tntimagegalleryparent_image       = "'.$data['tntimagegalleryparent_image'].'",

                        tntimagegalleryparent_status      = '.$data['tntimagegalleryparent_status'].'

                        WHERE tntimagegalleryparent_id = "' . (int)$tntimagegalleryparent_id . '" ');

        



        $this->db->query("DELETE FROM " . DB_PREFIX . "tntimagegallerychild WHERE tntimagegalleryparent_id = '" . (int)$tntimagegalleryparent_id . "'");      

        foreach ($data['tntimagegallery'] as $language_id => $value) {

           
            $this->db->query('INSERT INTO `' . DB_PREFIX . 'tntimagegallerychild`

                        SET 

                            tntimagegalleryparent_id      = '.$tntimagegalleryparent_id.',

                            tntimagegallerychild_name     = "'.$value['tntimagegallerychild_name'].'",

                            tntimagegallerychildlanguage_id   = '.$value['lang'].'');

        } 
    }

    

    public function deleteimagegallery($tntimagegalleryparent_id) {

        $this->db->query("DELETE FROM " . DB_PREFIX . "tntimagegalleryparent WHERE tntimagegalleryparent_id = '" . (int)$tntimagegalleryparent_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "tntimagegallerychild WHERE tntimagegalleryparent_id = '" . (int)$tntimagegalleryparent_id . "'");

    

        $this->cache->delete('tntimagegalleryparent');

        $this->cache->delete('tntimagegallerychild');

    }



    public function getcateimageslidesigle($tntimagegalleryparent_id) {

        

        $query = $this->db->query("SELECT  " . DB_PREFIX . "tntimagegallerychild.*, " . DB_PREFIX . "tntimagegalleryparent.* FROM  " . DB_PREFIX . "tntimagegallerychild

            INNER JOIN " . DB_PREFIX . "tntimagegalleryparent ON  

            " . DB_PREFIX . "tntimagegallerychild.tntimagegalleryparent_id = " . DB_PREFIX . "tntimagegalleryparent.tntimagegalleryparent_id

            WHERE " . DB_PREFIX . "tntimagegallerychild.tntimagegalleryparent_id = '" . (int)$tntimagegalleryparent_id . "'");



        return  $query->rows;

    }



    public function getimagegallery($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "tntimagegalleryparent p LEFT JOIN " . DB_PREFIX . "tntimagegallerychild pd ON (p.tntimagegalleryparent_id = pd.tntimagegalleryparent_id) WHERE pd.tntimagegallerychildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";





        if (!empty($data['filter_name'])) {

            $sql .= " AND pd.tntimagegallerychild_name LIKE '" . $this->db->escape($data['filter_name']) . "%'";

        }



        if (isset($data['filter_status']) && $data['filter_status'] !== '') {

            $sql .= " AND p.tntimagegalleryparent_status = '" . (int)$data['filter_status'] . "'";

        }



        $sql .= " GROUP BY p.tntimagegalleryparent_id";



        $sort_data = array(

            'pd.tntimagegallerychild_name',

            'p.tntimagegalleryparent_status'          

        );



        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {

            $sql .= " ORDER BY " . $data['sort'];

        } else {

            $sql .= " ORDER BY p.tntimagegalleryparent_position";

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



    public function gettotalimagegallerys($data = array()) {

        $sql = "SELECT COUNT(DISTINCT tntimagegallerychild_id) AS total FROM " . DB_PREFIX . "tntimagegalleryparent p LEFT JOIN " . DB_PREFIX . "tntimagegallerychild pd ON (p.tntimagegalleryparent_id = pd.tntimagegalleryparent_id) WHERE pd.tntimagegallerychildlanguage_id = '" . (int)$this->config->get('config_language_id') . "'";





        if (!empty($data['filter_name'])) {

            $sql .= " AND pd.tntimagegallerychild_name LIKE '" . $this->db->escape($data['filter_name']) . "%'";

        }



        if (isset($data['filter_status']) && $data['filter_status'] !== '') {

            $sql .= " AND p.tntimagegalleryparent_status = '" . (int)$data['filter_status'] . "'";

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