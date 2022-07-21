<?php
class Tntspeedup{
    public $document;
    public function __construct($registry){
        $this->document = $registry->get('document');
    }
    public function minify_css($input, $comment = 2, $quote = 2){
        include_once DIR_SYSTEM.'/library/minify/tntCSSMins.php';
        $result = Minify_CSS_Compressors::process($input);
        return $result;
    }
    public function minify_js($input, $comment = 2, $quote = 2){
        include_once DIR_SYSTEM.'/library/minify/tntJSMins.php';
        $jz = new JSqueezes();
        return $jz->squeeze($input,true,true,false);
    }
    public function minify_html($txt){
        $place_holders = array('<!-->' => '_!--_','<pre>' => '_pre_','</pre>' => '_/pre_','<code>' => '_code_','</code>' => '_/code_','javascript"><!--'=>'javascript">_!--','--></script>'=>'--_</script>');
        $txt = strtr($txt, $place_holders);
        $txt = str_replace(array("\t",'   ','  '," \n",",\n",),array('',' ',' ',"\n",','), $txt);
        $txt = preg_replace("`>\s+<`", "><", $txt); 
        $txt = preg_replace('/<!--[^(\[|(<!))](.*)-->/Uis', '', $txt);
        $txt = strtr($txt, array_flip($place_holders));
        return $txt;
    }
    public function changeCssPath($txt,$f){
        $f_info = pathinfo($f);
        $f_dir = $f_info['dirname'];
        $src = array();
        chdir(realpath($f_dir));
        $imgdir = str_replace('\\','/',str_replace('image/', '', DIR_IMAGE));
        preg_match_all('/(url|src)\(([^\)]*)\)/i', $txt.$txt, $result);
        foreach ($result[0] as $k => $v){
            $src[$v] = $result[2][$k];
        }
        foreach ($src as $k => $info){
            $info = str_replace(array('"',"'"),'',$info);
            $basename = basename($info);
            $arg = substr($info,strpos($info,'?'));
            $fullpath = rtrim(realpath(str_replace($arg,'',$info)),'/').'/';
            $imgfile =  str_replace($imgdir,'../',str_replace('\\','/',$fullpath)).$arg;
            $imgfile = str_replace('/?','?',$imgfile);
            $imgfile = str_replace($info,$imgfile,$k);
            $txt = str_replace($k, $imgfile, $txt);
        }
        chdir(str_replace('catalog/','',DIR_APPLICATION));
        return $txt;
    }
    public function ganratefile($file){
        if($file == 'js'){
            $files = $this->document->getScripts();
        }else if($file == 'css'){
            $files = $this->document->getStyles();
        }
        //$files = array_merge($default,$files);
        $sort_files = array_map(function($a){
            if(is_array($a)) $a = $a['href']; return $a;
        },$files);
        sort($sort_files);
        $filename = md5(json_encode($sort_files)).'.'.$file;
        $path = rtrim(str_replace('catalog','',DIR_APPLICATION),'/').'/';
        if(!file_exists($path.'cache/'.$filename)){
            $content = '';
            foreach ($files as $f){
                if(is_array($f)) $f = $f['href'];
                if(file_exists($path.$f)) $f = $path.$f;
                else if(substr($f,0,1) == '//') $f = 'http://'.substr($f,2);
                if($file == 'css') $tmpcontent = $this->changeCssPath(@file_get_contents($f),$f);
                else{
                    $tmpcontent = rtrim(@file_get_contents($f),';').';';
                    $tmpcontent = str_replace('+ +','+/\+',$tmpcontent);
                }
                $content .= $tmpcontent;
            }
            if($file == 'css') $content = $this->minify_css($content);
            else if($file == 'js')$content = $this->minify_js($content);
            $content = str_replace('+/\+','+ +',$content);
            if(!is_dir($path.'cache/')) mkdir($path.'cache',0777,true);
            file_put_contents($path.'cache/'.$filename,$content);
        }
        if($file == 'css')
            return array('cache/'.$filename => array( 
                'href' => 'cache/'.$filename,
                'rel' => 'stylesheet',
                'media' => 'screen'
            ));
        return array('cache/'.$filename);
    }
    public function compresscssjs($file){
        $dir = str_replace('system/', '', DIR_SYSTEM);
        $file = str_replace('//','/',$file);
        $file = str_replace($dir.'/','',$file);
        $ex = pathinfo($file, PATHINFO_EXTENSION);

        $content = '';
        if($ex == 'css') $tmpcontent = file_get_contents($file);
        else $tmpcontent = file_get_contents($file);
        $content .= $tmpcontent;
        
        if($ex == 'css') $content = $this->minify_css($content);
        else if($ex == 'js') $content = $this->minify_js($content);
        $success = file_put_contents($file,$content);
        if($success) $success = TRUE;
        else $success = FALSE;

        return $success;
    }
}