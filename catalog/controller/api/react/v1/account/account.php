<?php
class ControllerApiReactV1AccountAccount extends Controller {
	public function index() {
       
		$data['heading_title'] = 'Личный кабинет';
		
        $data['language'] = $this->account_language();
        
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
  
    private function account_language() {
         // Text
        $data['text_account']        = 'Личный Кабинет';
        $data['text_my_account']     = 'Моя учетная запись';
        $data['text_edit']           = 'Изменить информацию';
        $data['text_password']       = 'Изменить пароль';
        $data['text_logout']         = 'Выйти';
        
        return $data;
    }

}
