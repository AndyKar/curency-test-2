<?php
class ControllerApiReactV1CommonRegister extends Controller {
    
	public function index() {
//		$this->load->language('checkout/checkout');
//		$this->load->language('extension/quickcheckout/checkout');
		$this->load->language('account/register');
        
        $data['heading_title'] = $this->language->get('heading_title');
		$data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter'), $this->config->get('config_name'));

		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		// All variables
		$data['field_newsletter'] = $this->config->get('quickcheckout_field_newsletter');
        
        $data['form'] = array(
            array(
                'name'  => 'firstname',
                'type'  => 'text',
                'entry' => $this->language->get('entry_firstname')
            ),
            array(
                'name'  => 'lastname',
                'type'  => 'text',
                'entry' => $this->language->get('entry_lastname')
            ),
            array(
                'name'  => 'email',
                'type'  => 'text',
                'entry' => $this->language->get('entry_email')
            ),
            array(
                'name'  => 'telephone',
                'type'  => 'text',
                'entry' => $this->language->get('entry_telephone')
            ),
            array(
                'name'  => 'password',
                'type'  => 'password',
                'entry' => $this->language->get('entry_password')
            ),
            array(
                'name'  => 'confirm',
                'type'  => 'password',
                'entry' => $this->language->get('entry_confirm')
            )
        );
        
        $data['button'] = $this->language->get('button_login');
		
		return $data;
  	}

	public function register() {
		$this->load->language('checkout/checkout');
		$this->load->language('extension/quickcheckout/checkout');
		$this->load->model('account/customer');

		$json = array();
		if (!$this->customer->isLogged() && isset($this->request->post['email'])) {
			 // Customer Group
			if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->post['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}			
			if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error_warning'] = $this->language->get('error_exists');
            }
            if (!filter_var($customer_info['email'], FILTER_VALIDATE_EMAIL)){
                $json['error_warning'] = $this->language->get('error_email');
            }    
            $mail_domen = explode('.',explode('@', $customer_info['email'])[1])[1];
            $legal_domen = ['com','org','net','gov','mil','biz','info','mobi','name','aero','jobs','museum'];
            if(strlen($mail_domen) > 2 && !in_array($mail_domen , $legal_domen)){
                 $json['error_warning'] = $this->language->get('error_email');
			}
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				$json['error_warning'] = $this->language->get('error_password');
			}
			if ($this->request->post['confirm'] != $this->request->post['password']) {
				$json['error_warning'] = $this->language->get('error_confirm');
			}

			if ($this->config->get('config_account_id')) {
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
				if ($information_info && !isset($this->request->post['agree'])) {
					$json['error_warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}

            if (!$json) {
                $customer_id = $this->model_account_customer->addCustomer($this->request->post);
                
                // Clear any previous login attempts for unregistered accounts.
                $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
                $this->load->model('account/customer_group');

                $customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

                if ($customer_group_info && !$customer_group_info['approval']) {
                    $this->customer->login($this->request->post['email'], $this->request->post['password']);
                    $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
                    $tokens = $this->model_account_customer->setAuthToken($this->customer->getId());
                    $json['$this->customer->getId()'] = $this->customer->getId();
                    SetCookie('auth_token', $tokens['authtoken'], time() + 60 * 60 * 24 * 30, '/');
                    SetCookie('auth_jwt_token', $tokens['jwttoken'], time() + 60 * 60 * 24 * 30, '/');
                } else {
                     $json['success'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'), $this->url->link('information/contact'));
                }

                unset($this->session->data['guest']);
                // Add to activity log
                if ($this->config->get('config_customer_activity')) {
                    $this->load->model('account/activity');
                    $activity_data = array(
                        'customer_id' => $customer_id,
                        'name'        => $this->request->post['firstname'] . ' ' . $this->request->post['lastname']
                    );
                    $this->model_account_activity->addActivity('register', $activity_data);
                }
            }
        } 
            
        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Credentials: true');
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers:  Access-Control-Allow-Origin, Access-Control-Allow-Credentials, Content-Type, Authorization, X-Requested-With');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        
	}
}