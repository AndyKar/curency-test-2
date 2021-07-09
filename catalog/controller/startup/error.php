<?php
class ControllerStartupError extends Controller {
	public function index() {
		
		$this->registry->set('log', new Log('error.log'));
		
		set_error_handler(array($this, 'handler'));	
	
	}
	
	public function handler($code, $message, $file, $line) {
		
		// error suppressed with @
		if (error_reporting() === 0) {
			return false;
		}
	
		switch ($code) {
			case E_NOTICE:
			case E_USER_NOTICE:
				$error = 'Notice';
				$ecode = 'E_NOTICE, E_USER_NOTICE';
				break;
			case E_WARNING:
			case E_USER_WARNING:
				$error = 'Warning';
				$ecode = 'E_WARNING, E_USER_WARNING';
				break;
			case E_ERROR:
			case E_USER_ERROR:
				$error = 'Fatal Error';
				$ecode = 'E_ERROR, E_USER_ERROR';
				break;
			default:
				$error = 'Unknown';
				$ecode = 'Unknown ' . $code;
				break;
		}
	
		if ($this->config->get('config_error_display')) {
			echo '<b>' . $error . '</b>: ' . $message . ' in <b>' . $file . '</b> on line <b>' . $line . '</b>';
		}
		
		if ($this->config->get('config_error_log')) {
			$this->log->write('PHP ' . $error . ':  ' . $message . ' in ' . $file . ' on line ' . $line);
		}
	
		return true;
	} 
} 