<?php
class ControllerApiReactV1CommonTheme extends Controller {

	public function index() {
		//$data['language'] = $this->load->language('common/theme');
		$data['theme'] = isset($this->request->cookie['colortheme'])?$this->request->cookie['colortheme']:'dark';
        
		return $data;
	}
    
    public function theme() {
        $data['success'] = false;
        
		if (isset($this->request->post['code'])) {
			$this->session->data['theme'] = $this->request->post['code'];
            $data['success'] = true;
            $data['theme'] = $this->session->data['theme'];
        }
            
        if (isset($this->request->server['HTTP_ORIGIN'])) {
            //$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers:  Access-Control-Allow-Origin, Access-Control-Allow-Credentials, Content-Type, Authorization, X-Requested-With');//$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
		
	}
}
