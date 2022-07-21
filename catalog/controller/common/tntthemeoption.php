<?php
class ControllerCommontntthemeoption extends Controller {
	public function index() {
		$data  = array();
        $limit = 55; 

        for ($i=1; $i < $limit ; $i++) { 
            $data["patterimages"][] = HTTP_SERVER ."image/catalog/themefactory/pattern/".$i.".png";
        }
        $data['bgpatter'] = HTTP_SERVER ."image/catalog/themefactory/pattern/pattern.png";
        $data['font_list'] = $this->getFontInfo();
        
        return $this->load->view('helpfile/themeoption', $data);
	}
     public function getFontInfo()
    {
        $font_list = array(
            'Abril Fatface' => 'https://fonts.googleapis.com/css?family=Abril+Fatface&display=swap',
            'Alfa Slab One' => 'https://fonts.googleapis.com/css?family=Alfa+Slab+One&display=swap',
            'Allerta Stencil' => 'https://fonts.googleapis.com/css?family=Allerta+Stencil&display=swap',
            'Bangers' => 'https://fonts.googleapis.com/css?family=Bangers&display=swap',
            'Chivo' => 'https://fonts.googleapis.com/css?family=Chivo:300,300i,400,400i,700,700i,900,900i&display=swap',
            'Dosis' => 'https://fonts.googleapis.com/css?family=Dosis:200,300,400,500,600,700,800&display=swap',
            'Fjalla One' => 'https://fonts.googleapis.com/css?family=Fjalla+One&display=swap',
            'Great Vibes' => 'https://fonts.googleapis.com/css?family=Great+Vibes&display=swap',
            'Indie Flower' => 'https://fonts.googleapis.com/css?family=Indie+Flower&display=swap',
            'Lato' => 'https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,'
            .'700i,900,900i&display=swap',
            'Lobster' => 'https://fonts.googleapis.com/css?family=Lobster&display=swap',
            'Monoton' => 'https://fonts.googleapis.com/css?family=Monoton&'
            .'display=swap',
            'Montserrat' => 'https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,'
            .'400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap',
            'Notable' => 'https://fonts.googleapis.com/css?family=Notable'
            .'&display=swap',
            'Noto Sans' => 'https://fonts.googleapis.com/css?family=Noto+Sans:400,400i,700,700i&display=swap',
            'Nunito' => 'https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,'
            .'700,700i,800,800i,900,900i&display=swap',
            'Nunito Sans' => 'https://fonts.googleapis.com/css?family=Nunito+Sans:200,200i,300,300i,400,400i,600,600i,'
            .'700,700i,800,800i,900,900i&display=swap',
            'Open Sans' => 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,'
            .'600i,700,700i,800,800i&display=swap',
            'Oswald' => 'https://fonts.googleapis.com/css?family=Oswald:200,300,400,500,600,700&display=swap',
            'Pacifico' => 'https://fonts.googleapis.com/css?family=Pacifico&display=swap',
            'Permanent Marker' => 'https://fonts.googleapis.com/css?family=Permanent+Marker&display=swap',
            'Philosopher' => 'https://fonts.googleapis.com/css?family=Philosopher:400,'
            .'400i,700,700i&display=swap',
            'Playfair Display' => 'https://fonts.googleapis.com/css?family=Playfair+Display:400,'
            .'400i,700,700i,900,900i&display=swap',
            'Poppin' => 'https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,'
            .'500,500i,600,600i,700,700i,800,800i,900,900i&display=swap',
            'PT Sans' => 'https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap',
            'Raleway' => 'https://fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,'
            .'500,500i,600,600i,700,700i,800,800i,900,900i&display=swap',
            'Righteous' => 'https://fonts.googleapis.com/css?family=Righteous&display=swap',
            'Robot' => 'https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,'
            .'400i,500,500i,700,700i,900,900i&display=swap',
            'Roboto Condensed' => 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,'
            .'400,400i,700,700i&display=swap',
            'Roboto Slab' => 'https://fonts.googleapis.com/css?family=Roboto+Slab:100,300,400,700&display=swap',
            'Rubik' => 'https://fonts.googleapis.com/css?family=Rubik:300,300i,400,400i,500,500i,700,'
            .'700i,900,900i&display=swap',
            'Squada One' => 'https://fonts.googleapis.com/css?family=Squada+One&display=swap',
            'Tangerine' => 'https://fonts.googleapis.com/css?family=Tangerine:400,700&display=swap',
            'Titillium Web' => 'https://fonts.googleapis.com/css?family=Titillium+Web:200,200i,300,300i,400,'
            .'400i,600,600i,700,700i,900&display=swap',
            'Work Sans' => 'https://fonts.googleapis.com/css?family=Work+Sans:100,200,300,'
            .'400,500,600,700,800,900&display=swap',
            'Yellowta' => 'https://fonts.googleapis.com/css?family=Yellowtail&display=swap'
        );
        // $this->context->smarty->assign('font_list', $font_list);
        return $font_list;
    }
	
}