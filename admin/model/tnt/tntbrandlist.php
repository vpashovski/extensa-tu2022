<?php

class ModelTnttntbrandlist extends Model {

    

    public function editposition($position){

        foreach ($position as $key => $value) {

            $pos = $key + 1;

            $this->db->query("UPDATE " . DB_PREFIX . "tntbrandlist SET tntbrandlist_position = '" . (int)$pos . "' WHERE tntbrandlist_id = '" . (int)$value . "'");

        }    

    }

    public function add($data) {

        if (isset($data)) {


            $this->db->query("TRUNCATE TABLE " . DB_PREFIX . "tntbrandlist");

            if(!empty($data)){

                foreach ($data as $datas) {

                    $query = "SELECT MAX(tntbrandlist_id) as id FROM `" . DB_PREFIX . "tntbrandlist`";

                    $query = $this->db->query($query);

                    $id    = $query->row['id'] + 1;

                    

                    $this->db->query("INSERT INTO " . DB_PREFIX . "tntbrandlist SET tntbrandlist_link = '" .  $this->db->escape($datas['tntbrandlist_link']) . "',tntbrandlist_position = '" . (int)$id . "', tntbrandlist_status = '" . (int)$datas['tntbrandlist_status'] . "',tntbrandlist_image = '" .  $this->db->escape($datas['tntbrandlist_image']) . "',tntbrandlist_text = '" . json_encode($datas['tntbrandlist_text']) . "'");

                }

            }

        }

    }

    public function exitsdata() {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tntbrandlist ORDER BY tntbrandlist_position");

        return $query;

    }

    

}