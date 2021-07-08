<?php
class ControllerApiReactV1CommonLogout extends Controller {
	public function index() {
        
		$this->load->language('account/logout');
        
		$data['heading_title'] = $this->language->get('heading_title');
		$data['logout'] = $this->url->link('account/logout');
        $data['language']['text_message'] = $this->language->get('text_message');
        $data['language']['button_shopping'] = $this->language->get('button_shopping');
        $data['continue'] = $this->url->link('common/home');
		
		return $data;
	}
    
	public function logout() {
		
		if ($this->customer->isLogged()) {
			$this->customer->logout();

			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_address']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
            
            SetCookie('auth_token', '', -1, '/');
            SetCookie('auth_jwt_token', '', -1, '/');

			$data['redirect'] = $this->url->link('account/logout', '', true);
		}
        
        $data['success'] = 'sucess';
        
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
}
