<?php
class ControllerApiReactV1CommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		//Copyright
		$data['text_copy'] = '© 2019';
		$data['text_owners'] = 'All trademarks are the property of their respective owners and used solely as product descriptions';
		
        // Contacts info
		$data['email'] = $this->config->get('config_main_email');
		$addresses = explode(';', $this->config->get('config_address'));
        foreach($addresses as $address){
            $data['addresses'][] = explode(':', $address);
        }
        
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
		} 

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['german'] = false;
		if($this->session->data['language'] === 'de-de'){
			$data['german'] = true;
		}

		$data['data_menu'] = $this->load->controller('common/menu', array('menu_type' => 'data', 'position' => 'bottom', 'type'=> ['news' => 0,'page' => 0,'post' => 0,'gallery' => 0,'download' => 0, 'information' => 0]));
		$data['data_category_menu'] = $this->load->controller('product/category_menu', array('menu_type' => 'data', 'position' => 'bottom', 'type' => ['news' => 0,'page' => 0,'post' => 0,'gallery' => 0,'download' => 0, 'information' => 0]));

		if(isset($this->request->cookie['over18'])){
			$data['age_check'] = false;
		} else {
			$data['age_check'] = true;
		}
        $data['footer_contacts'] = $this->load->controller('api/react/v1/common/footer_contacts');

		return $data;
	}

}
