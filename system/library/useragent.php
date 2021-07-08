<?php
/**
 * @package		OpenCart
 * @author		Inversum ltd.
 * @copyright	Copyright (c) 2017 - 2018, Inversum, Ltd. (http://www.inversum.by/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		http://www.inversum.by.com
*/

/**
* Text class
*/
class Useragent{

	private $request; 
	private $user_agent;
	
	public function __construct() {
		
		$this->request = new Request();
		$this->user_agent = $this->request->server['HTTP_USER_AGENT'];

	}

	public function getUseragent() {
		
		$useragent = array();
		$bot = 0;
		$tablet_browser = 0;
		$mobile_browser = 0;

		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($this->user_agent))) {
			$tablet_browser++;
		}

		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($this->user_agent))) {
			$mobile_browser++;
		}

		if ((strpos(strtolower($this->request->server['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($this->request->server['HTTP_X_WAP_PROFILE']) or isset($this->request->server['HTTP_PROFILE'])))) {
			$mobile_browser++;
		}

		$mobile_ua = strtolower(substr($this->user_agent, 0, 4));
		$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki','palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda ','xda-');

		if (in_array($mobile_ua,$mobile_agents)) {
			$mobile_browser++;
		}

		if (strpos(strtolower($this->user_agent),'opera mini') > 0) {
			$mobile_browser++;
			//Check for tablets on opera mini alternative headers
			$stock_ua = strtolower(isset($this->request->server['HTTP_X_OPERAMINI_PHONE_UA'])?$this->request->server['HTTP_X_OPERAMINI_PHONE_UA']:(isset($this->request->server['HTTP_DEVICE_STOCK_UA'])?$this->request->server['HTTP_DEVICE_STOCK_UA']:''));
			if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
			  $tablet_browser++;
			}
		}
		if (preg_match('/bot|crawl|curl|dataprovider|search|get|spider|find|java|majesticsEO|google|yahoo|teoma|contaxe|yandex|libwww-perl|facebookexternalhit/i', $this->user_agent)) {
			$bot++;
		}

		if ($bot > 0) {
		   $useragent['agent'] = 'bot';
		}
		else if ($tablet_browser > 0) {
		   $useragent['agent'] = 'tablet';
		}
		else if ($mobile_browser > 0) {
		   $useragent['agent'] = 'mobile';
		}
		else {
		  $useragent['agent'] = 'desktop';
		}  
		
		$useragent['user_agent'] = $this->user_agent;

		return $useragent;
		
	}

}