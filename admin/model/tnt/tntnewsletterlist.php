<?php

class ModelTnttntnewsletterlist extends Model {

   public function getlist($data = array()) {
        $sql = "SELECT * FROM ". DB_PREFIX . "tntnewsletter";
        $sort_data = array(
            'tntnewsletter_email'
        );
        if(isset($data['sort']) && in_array($data['sort'],$sort_data)){
            $sql .= " ORDER BY " . $data['sort'];
        }else{
            $sql .= " ORDER BY tntnewsletter_id";
        }
        if(isset($data['order']) && ($data['order'] == 'DESC')){
            $sql .= " DESC";
        }else{
            $sql .= " ASC";
        }
        if(isset($data['start']) || isset($data['limit'])){
            if($data['start'] < 0){
                $data['start'] = 0;
            }
            if($data['limit'] < 1){
                $data['limit'] = 20;
            }
            $sql .= " LIMIT ". (int)$data['start'].",".(int)$data['limit'];
        }
        
        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function getTotallist($data = array()) {

        $sql = "SELECT * FROM ". DB_PREFIX. "tntnewsletter";
        $query = $this->db->query($sql);
        return $query->num_rows;
    }
}