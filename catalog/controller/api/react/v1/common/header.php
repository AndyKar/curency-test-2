<?php
class ControllerApiReactV1CommonHeader extends Controller {
	public function index() {

        $this->load->language('common/header');
        $data['language']['text_categories'] = $this->language->get('text_categories');
        $data['language']['text_search'] = $this->language->get('text_search');

		$this->load->model('common/home');
		$home_id = $this->model_common_home->getMainHomeId();
		$home_info = $this->model_common_home->getHome($home_id);
        
		$data['name'] = $home_info['title'];
		$data['descriptor'] = $home_info['descriptor'];
		$data['e_mail'] = $this->config->get('config_main_email');
		$data['logged'] = $this->customer->isLogged();
		$data['telephone'] = $this->config->get('config_telephone');
		$data['email'] = $this->config->get('config_main_email');

        $phone_numbers = explode(',', $this->config->get('config_telephone'));
		foreach($phone_numbers as $phone_number){
            if(!isset(explode(':', $phone_number)[1])){
                $data['telephones'][] = trim($phone_number);
            } else {
                $messeger = explode(':', $phone_number);
                switch ($messeger[0]){
                    case 'telegram':
                        $data['telegram'] = 'tg://resolve?domain=' . $messeger[1];
                        break;
                    case 'viber':
                        $data['viber'] = 'viber://chat?number=' . $messeger[1];
                        break;
                }
            }
		}
		
		if ($this->config->get('config_social')) {
			$socials_info = explode(',', $this->config->get('config_social'));
			
			foreach ($socials_info as $social_info){
                $social = explode(':', $social_info);
                switch ($social[0]){
                    case 'instagram':
                        $data['instagram'] = 'https://instagram.com/' . $social[1];
                        break;
                }
			}
		} else {
			$data['social'] = false;
		}
        
        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $this->model_tool_image->resize($this->config->get('config_logo'),'orig','orig');
		} else {
			$data['logo'] = '';
		}
		if (is_file(DIR_IMAGE . $this->config->get('config_image'))) {
			$data['image'] = $this->model_tool_image->resize($this->config->get('config_image'),'orig','orig');
		} else {
			$data['image'] = '';
		}
        if($this->config->get('config_svg_logo')){
            $data['svg_logo'] = html_entity_decode($this->config->get('config_svg_logo'), ENT_QUOTES, 'UTF-8');
        }
		
		$data['data_menu'] = $this->load->controller('common/menu', array('menu_type' => 'data', 'position' => 'top'));
		$data['data_category_menu'] = $this->load->controller('product/category_menu', array('menu_type' => 'data', 'position' => 'top'));
		//$data['base'] = $server;
        
        if(isset($this->request->cookie['colortheme']) && $this->request->cookie['colortheme'] === 'dark'){ 
            $data['colortheme'] = 'dark'; 
        } else { 
            $data['colortheme'] = 'sun';
        }
 
        //$data['session'] = $this->session->data;
        
		$data['languages'] = $this->load->controller('api/react/v1/common/language');
		$data['currencies'] = $this->load->controller('api/react/v1/common/currency');
        $data['theme'] = $this->load->controller('api/react/v1/common/theme');
        $data['account'] = $this->load->controller('api/react/v1/common/account');
        $data['wishlist'] = $this->load->controller('api/react/v1/common/wishlist');
        $data['cart'] = $this->load->controller('api/react/v1/common/cart');
        
//		$data['search'] = $this->load->controller('api/react/v1/common/search');
//		//$data['notification'] = $this->load->controller('api/react/v1/common/notification');
//		
//		
//		$data['downloads'] = $this->load->controller('api/react/v1/common/download');
//		$data['backcall'] = $this->load->controller('api/react/v1/common/backcall');
//		$data['email'] = $this->load->controller('api/react/v1/common/email');

        return $data;
	}
}
