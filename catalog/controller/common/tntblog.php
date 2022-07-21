<?php
class ControllerCommontntblog extends Controller {
    public function index() {
       
        $this->load->model('tnt/tntallquery');
        $this->load->model('tool/image');
        $languages_id                   = $this->config->get('config_language_id');
        $data['owner']             = $this->config->get('config_owner');
        $data['main_title']             = "LATEST BLOG";
        $data['allbloglink']            = $this->url->link('common/tntallblogpage',  true);
        $limit                          = 4;
        $blogpost_info                  = $this->model_tnt_tntallquery->getblogdatarecordlist($limit);
        $data['blogpost_data']          = array();
        foreach ($blogpost_info as $key => $value) {
            if(!empty($value['tntblog_parent_status'])){

                if($value['tntblog_parent_positionttype'] == "gallery"){
                    $gallery    = $this->model_tnt_tntallquery->getblogdatarecordgallery($value['tntblog_parent_id']);
                    if(!empty($gallery->num_rows)){
                        foreach ($gallery->rows as $key => $vv) {
                            if(!empty($vv['image'])){
                                $vvv = $vv['image'];
                            }else{
                                $vvv = 'no_image.png';
                            }
                            $gallery_info[] = $this->model_tool_image->resize($vvv, 20, 30);
                        }
                        $gallery_img = current($gallery_info);
                    }
                }
                if(empty($gallery_img)){
                    $gallery_img = 'no_image.png';
                }
                 if($value['tntblog_parent_positionttype'] == "video"){
                        $youtube = preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $value['tntblog_parent_video'], $match);
                        $youtube_id = $match[1];
                    }else{
                        $youtube_id = 1;
                    }
                if(!empty($value['tntblog_parent_featureimage'])){
                    $tntblog_parent_featureimage = $value['tntblog_parent_featureimage'];
                }else{
                    $tntblog_parent_featureimage = 'no_image.png';
                }

                $data['blogpost_data'][] = array(
                    'comment_count'                     => count($this->model_tnt_tntallquery->getblogdatarecordcomment($value['tntblog_parent_id'])),
                    'gallery'                           => $gallery_img,
                    'tntblog_parent_link'               => $this->url->link('common/tntallblogpage/singleblog','&tntblog_parent_id='.$value['tntblog_parent_id'].''),
                    'tntblog_parent_id'                 => $value['tntblog_parent_id'],
                    'youtube_id'                        => $youtube_id,
                    'tntblog_parent_positionttype'           => $value['tntblog_parent_positionttype'],
                    'tntblog_parent_deafultcategory'    => $value['tntblog_parent_deafultcategory'],
                    'tntblog_parent_url'         => $value['tntblog_parent_url'],
                    'tntblog_parent_video'              => $value['tntblog_parent_video'],
                    'tntblog_parent_createdate_month'          => date('M', strtotime($value['tntblog_parent_createdate'])),
                    'tntblog_parent_createdate_day'            => date('d', strtotime($value['tntblog_parent_createdate'])),
                    'tntblog_parent_featureimage'       => $this->model_tool_image->resize($tntblog_parent_featureimage, 202, 123),
                    'tntblog_child_title'               => $value['tntblog_child_title'],
                    'tntblog_child_excerpt'             => $value['tntblog_child_excerpt'],
                    'tntblog_child_content'             => $value['tntblog_child_content'],
                    'tntblog_child_metatitle'           => $value['tntblog_child_metatitle'],
                    'tntblog_child_metatag'             => $value['tntblog_child_metatag'],
                    'tntblog_child_meta_description'             => $value['tntblog_child_meta_description'],
                    'tntblog_child_metakeyword'         => $value['tntblog_child_metakeyword'],
                );
                
            }
        }
      // echo "<pre>"; print_r($data); die;

        return $this->load->view('extension/module/tntblogpost', $data);
    }
}