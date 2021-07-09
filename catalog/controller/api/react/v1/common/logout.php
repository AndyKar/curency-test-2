<?php
class ControllerApiReactV1CommonLogout extends Controller {
	public function index() {
        
		$data['heading_title'] = 'Выход';
		$data['logout'] = $this->url->link('account/logout');
        $data['language']['text_message'] = 'somemessage';
        $data['language']['button_shopping'] = 'Продолжить';
        //$data['continue'] = $this->url->link('common/home');
		
		return $data;
	}
    
	public function logout() {
		
		if ($this->customer->isLogged()) {
			$this->customer->logout();
            
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
