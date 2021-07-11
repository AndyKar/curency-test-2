<?php
class ControllerStartupStartup extends Controller {
	public function index() {

        $this->config->set('config_url', HTTP_SERVER);
        $this->config->set('config_ssl', HTTPS_SERVER);
		
		// Url
		$this->registry->set('url', new Url($this->config->get('config_url'), $this->config->get('config_ssl')));
		
        
		// Customer
		$customer = new Cart\Customer($this->registry);
		$this->registry->set('customer', $customer);
		
		// Customer Group
//		if (isset($this->session->data['customer']) && isset($this->session->data['customer']['customer_group_id'])) {
//			// For API calls
//			$this->config->set('config_customer_group_id', $this->session->data['customer']['customer_group_id']);
//		} elseif ($this->customer->isLogged()) {
//			// Logged in customers
//			$this->config->set('config_customer_group_id', $this->customer->getGroupId());
//		} elseif (isset($this->session->data['guest']) && isset($this->session->data['guest']['customer_group_id'])) {
//			$this->config->set('config_customer_group_id', $this->session->data['guest']['customer_group_id']);
//		}
		
		// Encryption
		$this->registry->set('encryption', new Encryption($this->config->get('config_encryption')));

	}
}
