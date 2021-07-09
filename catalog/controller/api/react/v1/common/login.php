<?php
class ControllerApiReactV1CommonLogin extends Controller {
	private $error = array();

	public function index() {
        

		$data['heading_title'] = 'Аутентификация';

		
        $data['form'] = array(
            array(
                'name'  => 'email',
                'type'  => 'text',
                'entry' => 'Email'
            ),
            array(
                'name'  => 'password',
                'type'  => 'password',
                'entry' => 'Пароль'
            )
        );
        
        $data['button'] = 'Войти';

		return $data;
	}
    
    public function login() {

        $this->load->model('account/customer');
        
        if (!empty($this->request->get['token'])) {

			$customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);

			if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
			
				$this->response->redirect($this->url->link('account/account', '', true));
			}
		}
//        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            
            $tokens = $this->model_account_customer->setAuthToken($this->customer->getId());
            SetCookie('auth_token', $tokens['authtoken'], time() + 60 * 60 * 24 * 30, '/');
            SetCookie('auth_jwt_token', $tokens['jwttoken'], time() + 60 * 60 * 24 * 30, '/');
            

            $this->session->data['account'] = 'register';
            
//            if ($this->config->get('config_customer_activity')) {
//                $this->load->model('account/activity');
//
//                $activity_data = array(
//                    'customer_id' => $customer_info['customer_id'],
//                    'name'        => $customer_info['firstname'] . ' ' . $customer_info['lastname']
//                );
//
//                $this->model_account_activity->addActivity('reset', $activity_data);
//            }
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		if (isset($this->request->post['email'])) {
			$data['form']['email'] = $this->request->post['email'];
		} else {
			$data['form']['email'] = '';
		}
		if (isset($this->request->post['password'])) {
			$data['form']['password'] = $this->request->post['password'];
		} else {
			$data['form']['password'] = '';
		}
        
		if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Credentials: true');
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers:  Access-Control-Allow-Origin, Access-Control-Allow-Credentials, Content-Type, Authorization, X-Requested-With');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
        
    }

	protected function validate() {
		
		if(isset($this->request->post['email']) && $this->request->post['email']){
			
			// Check if customer has been approved.
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

			if ($customer_info && !$customer_info['status']) {
				$this->error['warning'] = 'error_approved';
			}

			if (!$this->error) {
				if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
					$this->error['warning'] = 'Ошибка аутентификации';
				} 
			}
			
		}

		return !$this->error;
	}
}
