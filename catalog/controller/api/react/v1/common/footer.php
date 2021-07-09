<?php
class ControllerApiReactV1CommonFooter extends Controller {
	public function index() {

		//Copyright
		$data['text_copy'] = '© 2019';
		$data['text_owners'] = 'All trademarks are the property of their respective owners and used solely as product descriptions';
		
        // Contacts info
		$data['email'] = '';


		//$data['data_menu'] = $this->load->controller('common/menu', array('menu_type' => 'data', 'position' => 'bottom', 'type'=> ['news' => 0,'page' => 0,'post' => 0,'gallery' => 0,'download' => 0, 'information' => 0]));

        //$data['footer_contacts'] = $this->load->controller('api/react/v1/common/footer_contacts');

		return $data;
	}

}
