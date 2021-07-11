<?php
class ControllerApiReactV1CommonRegister extends Controller {
    
	public function index() {

        
        $data['heading_title'] = 'Регистрация';
        
        $data['form'] = array(
            array(
                'name'  => 'firstname',
                'type'  => 'text',
                'entry' => 'Имя'
            ),
            array(
                'name'  => 'lastname',
                'type'  => 'text',
                'entry' => 'Фамилия'
            ),
            array(
                'name'  => 'email',
                'type'  => 'text',
                'entry' => 'Email'
            ),
            array(
                'name'  => 'telephone',
                'type'  => 'text',
                'entry' => 'Телефон'
            ),
            array(
                'name'  => 'password',
                'type'  => 'password',
                'entry' => 'Пароль'
            ),
            array(
                'name'  => 'confirm',
                'type'  => 'password',
                'entry' => 'Подтверждение пароля'
            )
        );
        
        $data['button'] = 'Зарегистрироваться';
		
		return $data;
  	}

	public function register() {

		$this->load->model('account/customer');

		$json = array();
		if (!$this->customer->isLogged() && isset($this->request->post['email'])) {
		
			if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error_warning'] = 'Пользователь с таким email уже существует';
            }
            if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)){
                $json['error_warning'] = 'Некорректный email ' . $this->request->post['email'];
            }    
            $mail_domen = explode('.',explode('@', $customer_info['email'])[1])[1];
            $legal_domen = ['com','org','net','gov','mil','biz','info','mobi','name','aero','jobs','museum'];
            if(strlen($mail_domen) > 2 && !in_array($mail_domen , $legal_domen)){
                 $json['error_warning'] = 'Некорректный почтовый домен';
			}
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				$json['error_warning'] = 'Пароль должен быть от 4 до 20 символов';
			}
			if ($this->request->post['confirm'] != $this->request->post['password']) {
				$json['error_warning'] = 'Пароли не совпадают';
			}
            
           

            if (!$json) {
                $customer_id = $this->model_account_customer->addCustomer($this->request->post);

                $this->customer->login($this->request->post['email'], $this->request->post['password']);
                $tokens = $this->model_account_customer->setAuthToken($this->customer->getId());
                $json['$this->customer->getId()'] = $this->customer->getId();
                SetCookie('auth_token', $tokens['authtoken'], time() + 60 * 60 * 24 * 30, '/');
                SetCookie('auth_jwt_token', $tokens['jwttoken'], time() + 60 * 60 * 24 * 30, '/');

                $json['success'] = 'success';
//
//                unset($this->session->data['guest']);
                // Add to activity log
//                if ($this->config->get('config_customer_activity')) {
//                    $this->load->model('account/activity');
//                    $activity_data = array(
//                        'customer_id' => $customer_id,
//                        'name'        => $this->request->post['firstname'] . ' ' . $this->request->post['lastname']
//                    );
//                    $this->model_account_activity->addActivity('register', $activity_data);
//                }
            }
        }
        
        $json['customer_id'] = $customer_id;
        $json['post'] = $this->request->post;
            
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