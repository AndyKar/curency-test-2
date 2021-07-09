<?php
class ControllerStartupRouter extends Controller {
	public function index() {
		
		// Route
		if (isset($this->request->get['route']) && $this->request->get['route'] != 'startup/router') {
			$route = $this->request->get['route'];
		} else {
			$route = $this->config->get('action_default');
		}
		
		// Sanitize the call
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);
		
		if ($this->config->get('developer_mode') && isset($this->session->data['user_token']) && substr($this->session->data['user_token'], 0, 3) === 'DEV') {

			$data['$route(preg)'] = $route;
		}
	
		if (!is_null($result)) {
			return $result;
		}

		// We dont want to use the loader class as it would make an controller callable.
		$action = new Action($route);

		// Any output needs to be another Action object.
		$output = $action->execute($this->registry); 

		if (!is_null($result)) {
			return $result;
		}
		
		return $output;
	}
}
