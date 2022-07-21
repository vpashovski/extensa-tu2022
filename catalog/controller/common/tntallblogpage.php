<?php
class ControllerCommontntallblogpage extends Controller {
    private $error = array();

    public function index() {

            $this->load->language('extension/module/tntcustomtext');
            $this->load->model('tnt/tntallquery');
            $this->load->model('tool/image');

            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else {
                $page = 1;
            }

            if (isset($this->request->get['limit'])) {
                $limit = (int)$this->request->get['limit'];
            } else {
                $limit = 3;
            }
            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_blogspagetitle'),
                'href' => $this->url->link('common/tntallblogpage')
            );


            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }
            $filter_data = array(
                'start'               => ($page - 1) * $limit,
                'limit'               => $limit
            );

            

            $this->load->model('catalog/category');
            $data['store_owner']    = $this->config->get('config_owner');
            $blog_total             = $this->model_tnt_tntallquery->getTotalgetblogdatarecordlistpage($filter_data);
            $blogpost_info          = $this->model_tnt_tntallquery->getblogdatarecordlistpage($filter_data);
            $data['blogpost_data']  = array();
            foreach ($blogpost_info as $key => $value) {
                if(!empty($value['tntblog_parent_status'])){
                    $gallery    = $this->model_tnt_tntallquery->getblogdatarecordgallery($value['tntblog_parent_id']);
                     if(!empty($gallery->num_rows)){
                        foreach ($gallery->rows as $key => $vv) {
                            if(!empty($vv['image'])){
                                $vvv = $vv['image'];
                            }else{
                                $vvv = 'no_image.png';
                            }
                            $gallery_info[] = $this->model_tool_image->resize($vvv, $this->config->get('tntcustomsetting_tntbloggallall_img_width'), $this->config->get('tntcustomsetting_tntbloggallhome_img_height'));
                        }
                     }else{
                        $gallery_info = array();
                     }
                    $category_info  = $this->model_tnt_tntallquery->getblogdatarecordcategorysigle($value['tntblog_parent_id']);

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
                        'tntblog_parent_link'               => $this->url->link('common/tntallblogpage/singleblog','&tntblog_parent_id='.$value['tntblog_parent_id'].''),
                        'gallery'                           => $gallery_info,
                        'category_title'                    => $category_info['tntblogcategory_sub_title'],
                        'tntblog_parent_id'                 => $value['tntblog_parent_id'],
                        'tntblog_parent_createdate'            => date('M d, Y', strtotime($value['tntblog_parent_createdate'])),
                        'tntblog_parent_positionttype'           => $value['tntblog_parent_positionttype'],
                        'tntblog_parent_deafultcategory'    => $value['tntblog_parent_deafultcategory'],
                        'tntblog_parent_url'         => $value['tntblog_parent_url'],
                        'tntblog_parent_video'              => $value['tntblog_parent_video'],
                        'youtube_id'                        => $youtube_id,
                        'tntblog_parent_featureimage'       => $this->model_tool_image->resize($tntblog_parent_featureimage, $this->config->get('tntcustomsetting_tntblogfeaall_img_width'), $this->config->get('tntcustomsetting_tntblogfeaall_img_height')),
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
            $pagination         = new tntpagination();
            $pagination->total  = $blog_total;
            $pagination->page   = $page;
            $pagination->limit  = $limit;
            $pagination->url    = $this->url->link('common/tntallblogpage', '&page={page}');
            $data['pagination'] = $pagination->render();

            $data['results']    = sprintf($this->language->get('text_pagination'), ($blog_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($blog_total - $limit)) ? $blog_total : ((($page - 1) * $limit) + $limit), $blog_total, ceil($blog_total / $limit));


            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');
            $this->response->setOutput($this->load->view('common/tntallblogpage', $data));
    }
    
    protected function validate() {

        if ((utf8_strlen($this->request->post['tntblog_comment_name']) < 3) || (utf8_strlen($this->request->post['tntblog_comment_name']) > 32)) {
            $this->error['name'] = "Enter Name";

        }

        if (!filter_var($this->request->post['tntblog_comment_email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = "Enter Email";
        }

        if ((utf8_strlen($this->request->post['tntblog_coment_url']) < 10) || (utf8_strlen($this->request->post['tntblog_coment_url']) > 3000)) {
            $this->error['enquiry'] = "Enter Comment";
        }

        return !$this->error;
    }

    public function singleblog(){
        if (isset($this->request->get['tntblog_parent_id'])) {
            $tntblog_parent_id = (int)$this->request->get['tntblog_parent_id'];
        } else if (!empty($this->request->post['tntblog_parent_id'])) {
            $tntblog_parent_id = (int)$this->request->post['tntblog_parent_id'];
        } else {
            $tntblog_parent_id = 1;
        }
        $this->load->language('extension/module/tntcustomtext');
        $this->load->model('tnt/tntallquery');
        $this->load->model('tool/image');
        $this->load->model('catalog/category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_tnt_tntallquery->getblogdatarecordaddcomment($this->request->post);
            $this->session->data['success'] = "successfully add comment";
        }
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        } 
        $data['store_owner'] = $this->config->get('config_owner');
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );
       /* $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_blogspagetitle'),
            'href' => $this->url->link('common/tntallblogpage')
        );
*/
        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }

        if (isset($this->error['enquiry'])) {
            $data['error_enquiry'] = $this->error['enquiry'];
        } else {
            $data['error_enquiry'] = '';
        }

        if (isset($this->request->post['tntblog_comment_name'])) {
            $data['tntblog_comment_name'] = $this->request->post['tntblog_comment_name'];
        } else {
            $data['tntblog_comment_name'] = '';
        }
        if (isset($this->request->post['tntblog_comment_email'])) {
            $data['tntblog_comment_email'] = $this->request->post['tntblog_comment_email'];
        } else {
            $data['tntblog_comment_email'] = '';
        }
        if (isset($this->request->post['tntblog_coment_url'])) {
            $data['tntblog_coment_url'] = $this->request->post['tntblog_coment_url'];
        } else {
            $data['tntblog_coment_url'] = '';
        }
        if (isset($this->request->post['tntblog_comment_subject'])) {
            $data['tntblog_comment_subject'] = $this->request->post['tntblog_comment_subject'];
        } else {
            $data['tntblog_comment_subject'] = '';
        }
        if (isset($this->request->post['tntblog_coment_url'])) {
            $data['tntblog_coment_url'] = $this->request->post['tntblog_coment_url'];
        } else {
            $data['tntblog_coment_url'] = '';
        }

        $data['action'] = $this->url->link('common/tntallblogpage/singleblog',  true);
        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }
        $data['comment_image'] = $server."image/catalog/themefactory/blog/comment.png";
        $data['comment_info']   = $this->model_tnt_tntallquery->getblogdatarecordcomment($tntblog_parent_id);
      
        $blogpost_info          = $this->model_tnt_tntallquery->getblogdatarecordsingle($tntblog_parent_id);
        $category_info          = $this->model_tnt_tntallquery->getblogdatarecordcategorysigle($blogpost_info['tntblog_parent_id']);
        if ($blogpost_info) {           

            $data['breadcrumbs'][] = array(
                'text' => $blogpost_info['tntblog_child_title'],
                'href' => $this->url->link('common/tntallblogpage/singleblog','&tntblog_parent_id=' . $tntblog_parent_id)
            );
            $this->document->setTitle($blogpost_info['tntblog_child_metatitle']);
            $this->document->setDescription($blogpost_info['tntblog_child_meta_description']);
            $this->document->setKeywords($blogpost_info['tntblog_child_metakeyword']);
            $this->document->addLink($this->url->link('common/tntallblogpage/singleblog', 'tntblog_parent_id=' . $tntblog_parent_id), 'canonical');
            $gallery = $this->model_tnt_tntallquery->getblogdatarecordgallery($tntblog_parent_id);
            $gallery_info = array();
            foreach ($gallery->rows as $key => $vv) {
                if(!empty($vv['image'])){
                    $vv1 = $vv['image'];
                }else{
                    $vv1 = 'no_image.png';
                }
                $gallery_info[] = $this->model_tool_image->resize($vv1, $this->config->get('tntcustomsetting_tntbloggallsin_img_width'), $this->config->get('tntcustomsetting_tntbloggallsin_img_height'));
            }
            $data['gallery']                            = $gallery_info;

            $data['tntblog_parent_id']                  = $tntblog_parent_id;
            if($blogpost_info['tntblog_parent_positionttype'] == "video"){
                    $youtube = preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $blogpost_info['tntblog_parent_video'], $match);
                    $youtube_id = $match[1];
            }else{
                    $youtube_id = 1;
            }
            if(!empty($blogpost_info['tntblog_parent_featureimage'])){
                $tntblog_parent_featureimage = $blogpost_info['tntblog_parent_featureimage'];
            }else{
                $tntblog_parent_featureimage = 'no_image.png';
            }
            $data['youtube_id']                         = $youtube_id;
            $data['category_title']                     = $category_info['tntblogcategory_sub_title'];
            $data['heading_title']                      = $blogpost_info['tntblog_child_title'];
            $data['tntblog_parent_link']                = $this->url->link('common/tntallblogpage/singleblog','&tntblog_parent_id='.$blogpost_info['tntblog_parent_id'].'');
            $data['tntblog_parent_id']                  = $blogpost_info['tntblog_parent_id'];
            $data['tntblog_parent_createdate']             = date('M d, Y', strtotime($blogpost_info['tntblog_parent_createdate']));
            $data['tntblog_parent_positionttype']            = $blogpost_info['tntblog_parent_positionttype'];
            $data['tntblog_parent_deafultcategory']     = $blogpost_info['tntblog_parent_deafultcategory'];
            $data['tntblog_parent_url']          = $blogpost_info['tntblog_parent_url'];
            $data['tntblog_parent_video']               = $blogpost_info['tntblog_parent_video'];
            $data['tntblog_parent_featureimage']        = $this->model_tool_image->resize($tntblog_parent_featureimage, 1315, 800);
            $data['tntblog_child_title']                = $blogpost_info['tntblog_child_title'];
            $data['tntblog_child_excerpt']              = $blogpost_info['tntblog_child_excerpt'];
            $data['tntblog_child_content']              = $blogpost_info['tntblog_child_content'];
            $data['tntblog_child_metatitle']            = $blogpost_info['tntblog_child_metatitle'];
            $data['tntblog_child_metatag']              = $blogpost_info['tntblog_child_metatag'];
            $data['tntblog_child_meta_description']              = $blogpost_info['tntblog_child_meta_description'];
            $data['tntblog_child_metakeyword']          = $blogpost_info['tntblog_child_metakeyword'];
            $data['tntblog_parent_commentstatus']       = $blogpost_info['tntblog_parent_commentstatus'];
            $data['footer']                             = $this->load->controller('common/footer');
            $data['header']                             = $this->load->controller('common/header');
            //echo "<pre>"; print_r($data); die;
            $this->response->setOutput($this->load->view('common/tntsingleblog', $data));
        } else {
            
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('common/tntallblogpage/singleblog', $url . '&tntblog_parent_id=' . $tntblog_parent_id)
            );

            $this->document->setTitle($this->language->get('text_error'));

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }
}