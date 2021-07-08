<?php
class ControllerApiReactV1CommonMain extends Controller {

	public function index() {
        $start = microtime(true);
        $data = array();
        $data['main']['footer'] = $this->load->controller('api/react/v1/common/footer');
		$data['main']['header'] = $this->load->controller('api/react/v1/common/header');
        
        $this->load->language('error/not_found');
        $data['main']['language']['text_empty'] = $this->language->get('text_error');
        $data['main']['language']['button_shopping'] = $this->language->get('button_shopping');
        $data['main']['language']['button_back'] = $this->language->get('button_back');
        $data['main']['language']['button_delete'] = $this->language->get('button_delete');
        $data['main']['language']['button_edit'] = $this->language->get('button_edit');
        //$data['main']['language'][''] = $this->language->get('');
        
//        $data['main']['language']['wish'] = $this->load->language('account/wishlist');
//        $data['main']['language']['account'] = $this->load->language('account/account');
//        
//        $data['main']['language']['coupon'] = $this->load->language('extension/total/coupon');
//        $data['main']['language']['checkout'] = $this->load->language('checkout/checkout');;
//        $data['main']['language']['checkout'] = array_merge($data['main']['language']['checkout'], $this->load->language('extension/quickcheckout/checkout'));
        
        
        $data['auth'] = $this->auth();
        $data['auth']['logoutInfo'] = $this->load->controller('api/react/v1/common/logout');
        if(!$this->customer->getId()){
            $data['auth']['loginInfo'] = $this->load->controller('api/react/v1/common/login');
            $data['auth']['registerInfo'] = $this->load->controller('api/react/v1/common/register');
            $data['auth']['forgottenInfo'] = $this->load->controller('api/react/v1/common/forgotten');
        }
        
        $data['session'] = $this->session->data;
        $data['cookie'] = $this->request->cookie;
        
        $data['time'] = round((microtime(true) - $start) * 1000, 2);
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
    
    public function auth() {
		$data = array();
        $data['logged'] = false;
        if ($this->customer->isLogged()) {
            $data['logged'] = true;
            $data['customer_id'] = $this->customer->getId();
            $this->load->model('account/customer');           
            $data['customer_info'] = $this->model_account_customer->getCustomer($this->customer->getId());
        }
		return $data;
	}
}