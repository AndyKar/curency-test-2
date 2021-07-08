<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Document class
*/
class Document {
	
	
	private $title;
	private $description;
	private $keywords;
	private $nofollow;
	private $links = array();
	private $styles = array();
	private $scripts = array(
		'header' => array(),
		'footer' => array()
	);
	
	private $exexute_time = array(
		'seo_rewrite' => 0.000
	);
	
	private $dev_info = array(
		'ERROR'			=> array(),
		'Header'		=> array(),
		'content'		=> array(),
		'modules'		=> array(),
		'extensions'	=> array(),
		'Footer'		=> array(),
		'controllers'	=> array(
				'account'		=> array(),
				'affiliate'		=> array(),
				'api'			=> array(),
				'checkout'		=> array(),
				'developer'		=> array(),
				'error'			=> array(),
				'event'			=> array(),
				'extension'		=> array(),
				'information'	=> array(),
				'mail'			=> array(),
				'product'		=> array(),
				'site'			=> array(),
				'startup'		=> array(),
				'tool'			=> array(),
				'trigger'		=> array()
			)
	);
	
	
	
	
	public function __construct($registry){
		$this->compress = $registry->get('compress');
		$this->config = $registry->get('config');
		
	}
	
	/**
     * 
     *
     * @param	boolean	$nofollow
     */
	public function setNofollow($nofollow) {
		$this->nofollow = $nofollow;
	}

	/**
     * 
	 * 
	 * @return	boolean
     */
	public function getNofollow() {
		return $this->nofollow;
	}
	
	/**
     * 
     *
     * @param	array	$ex_time
     */
	public function setExTime($extime = 0.000) {
		foreach ($extime as $key => $value) {
			$this->exexute_time[$key] += (float)$value;
		}
	}
	
	/**
     * 
	 * 
	 * @return	array
     */
	public function getExTime() {
		return $this->exexute_time;
	}
	
	/**
     * 
     *
     * @param	array $dev_info
     */
	
//	public function setDevInfo($postion, $data = array()) {
//		foreach ($data as $key => $value) {
//			$this->dev_info[$postion][$key] = $value;
//		}
//		
//	}
	public function setDevInfo($postion, $props) {

		if($postion === 'error'){
			static $err_count = 1;
			$this->dev_info['ERROR']['err ' . $err_count++] = '<span class="err" style="color: #f1447e">' . $props['message'] . ' <small><span class="dev-time">(Timeline: ' . $props['timeline'] . 'ms)</span></small>  in </span> ' . $props['file'] . ' <span class="err" style="color: #f1447e"> on line </span>' . $props['line'] ;
			
		} else if(is_object($props) || !isset($props['view'])){
			
			foreach ($props as $key => $value) {
				$this->dev_info[$postion][$key] = $value;
			}
			
		} else {

			
			if(isset($props['controller'])){
				$controller = '<small>Controller</small> ';
				$controller_name = '<b class="route" style="color: #5898cc">' . $props['controller'] . '</b> ';
			} else {
				$controller = '';
				$controller_name = '';
			}

			if(isset($props['view'])){
				$view = '<small>View: </small><b class="route" style="color: #5898cc">' . $props['view'] . ' </b>';
			} else {
				$view = '';
			}

			if(isset($props['type'])){
				$type = '<small>Type:</small> <b>' . $props['tipe'] . ' </b>';
			} else {
				$type = '';
			}

			if(isset($props['time'])){
				if(!isset($props['timeline'])){
					$props['timeline'] = 'unknown';
				}
				
				$time = '<small><span class="dev-time">(Execute: ' . $props['time'] . ' ms, Timeline: ' . $props['timeline'] . ')</span></small> ';
			} else {
				$time = '';
				
			}
		
			
			$dev_title = $controller?$controller:'<big><span class="dev-info-title">Page Information<span></big>';
			$dev_title .= $time;
			$dev_title .= $controller_name;
			$dev_title .= $view;
			$dev_title .= $type;
			
			$pos = explode('/', $postion);
			
			if(!isset($props['data']) && isset($props['action'])){
				$props['data'] = array(
					'Action' => $props['action']
				);
				
				foreach ($props['data'] as $key => $value) {
					$this->dev_info['controllers'][$pos[1]][$pos[2]][$dev_title][$key] = $value;
				}	
			
			} else if($pos[0] === 'controllers' && count($pos) === 2 ){
				foreach ($props['data'] as $key => $value) {
					$this->dev_info['controllers'][$pos[1]][$dev_title][$key][] = $value;
				}	
			} else if($pos[0] === 'controllers' && count($pos) === 3 ){
				
				if(isset($props['data']) && is_array($props['data'])){
					foreach ($props['data'] as $key => $value) {
						$this->dev_info['controllers'][$pos[1]][$pos[2]][$dev_title][$key] = $value;
					} 
				}
				
			} else if($pos[0] === 'controllers' && count($pos) === 4 ){
				static $n = 1;
				foreach ($props['data'] as $key => $value) {

					if(explode(' ',$key)[0] === 'param'){

						$this->dev_info['controllers'][$pos[1]][$pos[2]][$dev_title][$key] = $value;
					} else {
						$this->dev_info['controllers'][$pos[1]][$pos[2]][$pos[3]][$dev_title][$key] = $value;
					}
				}	
			} else {
				foreach ($props['data'] as $key => $value) {
					$this->dev_info[$pos[0]][$dev_title][$key] = $value;
				}
			}
		}	
	}
	

	/**
     * 
	 * 
	 * @return	array
     */
	public function getDevInfo() {
		return $this->dev_info;
	}

	/**
     * 
     *
     * @param	string	$title
     */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
     * 
	 * 
	 * @return	string
     */
	public function getTitle() {
		return $this->title;
	}

	/**
     * 
     *
     * @param	string	$description
     */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
     * 
     *
     * @param	string	$description
	 * 
	 * @return	string
     */
	public function getDescription() {
		return $this->description;
	}

	/**
     * 
     *
     * @param	string	$keywords
     */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	/**
     *
	 * 
	 * @return	string
     */
	public function getKeywords() {
		return $this->keywords;
	}
	
	/**
     * 
     *
     * @param	string	$href
	 * @param	string	$rel
     */
	public function addLink($href, $rel) {
		$this->links[$href] = array(
			'href' => $href,
			'rel'  => $rel
		);
	}

	/**
     * 
	 * 
	 * @return	array
     */
	public function getLinks() {
		return $this->links;
	}

	/**
     * 
     *
     * @param	string	$href
	 * @param	string	$rel
	 * @param	string	$media
     */
	public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
		$this->styles[$href] = array(
			'href'  => $href,
			'rel'   => $rel,
			'media' => $media
		);
	}

	/**
     * 
	 * 
	 * @return	array
     */
	public function getStyles() {

		if($this->config->get('developer_compress')){
			
			$md5 = md5(serialize(array_reverse($this->styles)));
			$file = DIR_COMPRESS . 'cache/' . $md5 . '.css';

			if (file_exists($file))
			{
				$style[$file] = array(
					'href' => PATH_COMPRESS . 'cache/' . $md5 . '.css',
					'rel' => 'stylesheet',
					'media' => 'screen'
				);
				
				return $style;

			} else {

				$resource = PATH_COMPRESS . 'cache/' . $this->compress->merge_css($this->styles);

				$array[$resource] = array(
					'href' => $resource,
					'rel' => 'stylesheet',
					'media' => 'screen'
				);
				
				return $array;
			}
			
		} else {
			
			return $this->styles;
		}
			
	}

	/**
     * 
     *
     * @param	string	$href
	 * @param	string	$postion
     */
	public function addScript($href, $postion = 'header') {
		$this->scripts[$postion][$href] = $href;
	}

	/**
     * 
     *
     * @param	string	$postion
	 * 
	 * @return	array
     */
	public function getScripts($postion = 'header') {
	
		if($this->config->get('developer_compress')){
			
			if (isset($this->scripts[$postion])) {
				
				$md5 = md5(serialize(array_reverse($this->scripts[$postion])));
				$file = DIR_COMPRESS . 'cache/' . $md5 . '.js';

				if (file_exists($file))
				{
					$scripts[PATH_COMPRESS . 'cache/' . $md5 . '.js'] = PATH_COMPRESS . 'cache/' . $md5 . '.js';

					return $scripts;

				} else {

					$resource = PATH_COMPRESS . 'cache/' . $this->compress->merge_js($this->scripts[$postion]);

					$array[$resource] = $resource;

					return $array;

				}
			} else {
				return array();
			}
		} else {
			
			return $this->scripts[$postion];
		}
	}
}