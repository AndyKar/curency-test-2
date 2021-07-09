<?php
class ControllerApiReactV1CommonHeader extends Controller {
	public function index() {

        
//        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
//			$data['logo'] = $this->model_tool_image->resize($this->config->get('config_logo'),'orig','orig');
//		} else {
//			$data['logo'] = '';
//		}
//		if (is_file(DIR_IMAGE . $this->config->get('config_image'))) {
//			$data['image'] = $this->model_tool_image->resize($this->config->get('config_image'),'orig','orig');
//		} else {
//			$data['image'] = '';
//		}
//        if($this->config->get('config_svg_logo')){
//            $data['svg_logo'] = html_entity_decode($this->config->get('config_svg_logo'), ENT_QUOTES, 'UTF-8');
//        }
//		
//		$data['data_menu'] = $this->load->controller('common/menu', array('menu_type' => 'data', 'position' => 'top'));

        
        if(isset($this->request->cookie['colortheme']) && $this->request->cookie['colortheme'] === 'dark'){ 
            $data['colortheme'] = 'dark'; 
        } else { 
            $data['colortheme'] = 'sun';
        }

        $data['theme'] = $this->load->controller('api/react/v1/common/theme');
        $data['account'] = $this->load->controller('api/react/v1/common/account');

        return $data;
	}
}
