<?php
class ControllerApiReactV1CommonLogin extends Controller {
	private $error = array();

	public function index() {
        
		$this->load->language('account/login');
		$data['heading_title'] = $this->language->get('heading_title');

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_login'),
			'href' => $this->url->link('account/login', '', true)
		);
        
        $data['form'] = array(
            array(
                'name'  => 'email',
                'type'  => 'text',
                'entry' => $this->language->get('entry_email')
            ),
            array(
                'name'  => 'password',
                'type'  => 'password',
                'entry' => $this->language->get('entry_password')
            )
        );
        
        $data['button'] = $this->language->get('button_login');

		return $data;
	}
    
    public function login() {
        $this->load->language('account/login');
        $this->load->model('account/customer');
        
        // Login override for admin users
		if (!empty($this->request->get['token'])) {
			$this->customer->logout();
			$this->cart->clear();

			unset($this->session->data['order_id']);
			unset($this->session->data['payment_address']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);

			$customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);

			if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
				// Default Addresses
				$this->load->model('account/address');

				if ($this->config->get('config_tax_customer') == 'payment') {
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				if ($this->config->get('config_tax_customer') == 'shipping') {
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				$this->response->redirect($this->url->link('account/account', '', true));
			}
		}
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            
            $tokens = $this->model_account_customer->setAuthToken($this->customer->getId());
            SetCookie('auth_token', $tokens['authtoken'], time() + 60 * 60 * 24 * 30, '/');
            SetCookie('auth_jwt_token', $tokens['jwttoken'], time() + 60 * 60 * 24 * 30, '/');
            
			// Unset guest
			unset($this->session->data['guest']);
            $this->session->data['account'] = 'register';
            
			// Default Shipping Address
			$this->load->model('account/address');
			if ($this->config->get('config_tax_customer') == 'payment') {
				$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}
			if ($this->config->get('config_tax_customer') == 'shipping') {
				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}
			// Wishlist
            if (isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist'])) {
                $this->load->model('account/wishlist');
                $data_wish = $this->session->data['wish'];
                foreach ($this->session->data['wishlist'] as $key => $product_id) {
                    $quantity = isset($data_wish[$value])?$data_wish[$value]:1;
                    $this->model_account_wishlist->addWishlist($product_id, $quantity);
                    unset($this->session->data['wishlist'][$key]);
                    unset($this->session->data['wish'][$product_id]);
                }
            }
            
            if ($this->config->get('config_customer_activity')) {
                $this->load->model('account/activity');

                $activity_data = array(
                    'customer_id' => $customer_info['customer_id'],
                    'name'        => $customer_info['firstname'] . ' ' . $customer_info['lastname']
                );

                $this->model_account_activity->addActivity('reset', $activity_data);
            }
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
			
			// Check how many login attempts have been made.
			$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

			if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
				$this->error['warning'] = $this->language->get('error_attempts');
			}
		
			// Check if customer has been approved.
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

			if ($customer_info && !$customer_info['status']) {
				$this->error['warning'] = $this->language->get('error_approved');
			}

			if (!$this->error) {
				if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
					$this->error['warning'] = $this->language->get('error_login');

					$this->model_account_customer->addLoginAttempt($this->request->post['email']);
				} else {
					$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
                    
				}
			}
			
		}

		return !$this->error;
	}
}
