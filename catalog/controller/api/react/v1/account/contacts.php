<?php
class ControllerApiReactV1AccountContacts extends Controller {
    
	public function contact() {
        $data['heading_title'] = 'Добавить контакт';
        
        $data['form'] = array(
            array(
                'name'  => 'lastname',
                'type'  => 'text',
                'entry' => 'Фамилия'
            ),
            array(
                'name'  => 'firstname',
                'type'  => 'text',
                'entry' => 'Имя'
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
            )
        );
        
        $data['button'] = 'Добавить';
		
		return $data;
  	}
    
     public function getContacts() {
        $data['contacts']['heading_title'] = 'Контакты';
        $data['contacts']['contacts'] = array();
        $data['contacts']['customer_contacts'] = array();
        $this->load->model('account/contacts');
        $data['contacts']['contacts'] = $this->model_account_contacts->getCorpContacts();
        $data['contacts']['favorite_contacts'] = $this->model_account_contacts->getFavoriteContacts();
        $data['contacts']['customer_contacts'] = $this->model_account_contacts->getContacts();
        $data['contacts']['language'] = $this->contacts_language();
        $data['contacts']['contact'] = $this->contact();
            
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
    
    private function contacts_language() {
         // Text
        $data['heading_title']          = 'Контакты';
        $data['text_contacts']          = 'Корпоративные контакты';
        $data['text_favorite']          = 'Избранные';
        $data['text_customer_contacts'] = 'Мои контакты';
        $data['text_empty']             = 'Список пуст';
       
        return $data;
    }

	public function add() {

		$this->load->model('account/contacts');

		if ($this->customer->isLogged()) {	
			$contact_id = $this->model_account_contacts->addContact($this->request->post);        
        }
       
        $json['contact_id'] = $contact_id;
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
    
	public function addFavorite() {

		$this->load->model('account/contacts');

		if ($this->customer->isLogged()) {	
			$this->model_account_contacts->addContactToFavorite($this->request->get['contact_id']);        
        }
       
        $json['contact_id'] = $this->request->get['contact_id'];
            
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
    
	public function delete() {

		$this->load->model('account/contacts');

		if ($this->customer->isLogged() && isset($this->request->get['contact_id'])) {	
			$this->model_account_contacts->deleteContact($this->request->get['contact_id']);        
        }
       
        $json['customer_id'] = (int)$this->customer->getId();
        $json['contact_id'] = $this->request->get['contact_id'];
            
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
    
	public function deleteFavorite() {

		$this->load->model('account/contacts');

		if ($this->customer->isLogged() && isset($this->request->get['contact_id'])) {	
			$this->model_account_contacts->deleteFavoriteContact($this->request->get['contact_id']);        
        }
       
        $json['customer_id'] = (int)$this->customer->getId();
        $json['contact_id'] = $this->request->get['contact_id'];
            
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