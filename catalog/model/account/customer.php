<?php
class ModelAccountCustomer extends Model {
	public function addCustomer($data) {

		$sql = "INSERT INTO " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', status = '1', date_added = NOW()";

        $this->db->query($sql);
		$customer_id = $this->db->getLastId();
	
		return $customer_id;
	}

	public function editCustomer($customer_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . isset($data['telephone'])?$this->db->escape($data['telephone']):'' . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editPassword($email, $password) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function editCode($email, $code) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getCustomerByCode($code) {
		$query = $this->db->query("SELECT customer_id, firstname, lastname, email FROM `" . DB_PREFIX . "customer` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

		return $query->row;
	}

	public function getCustomerByToken($token) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE token = '" . $this->db->escape($token) . "' AND token != ''");

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = ''");

		return $query->row;
	}
	
	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}

	public function getIps($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->rows;
	}
    
    // New auth -----------------------------------------------------------------------------------------------------------------------------------
    
	public function getCustomerByPhone($phone) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE REPLACE(REPLACE(REPLACE(REPLACE(LOWER(telephone), ' ', '' ), '-', ''), '(', ''), '(', '') LIKE '%" . $this->db->escape(utf8_strtolower($phone)) . "'");

		return $query->row;
	}
    
    public function setPhoneCode($phone, $code) {
        $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET phonecode = '" . (int)$code . "' WHERE REPLACE(REPLACE(REPLACE(REPLACE(LOWER(telephone), ' ', '' ), '-', ''), '(', ''), '(', '') LIKE '%" . $this->db->escape(utf8_strtolower($phone)) . "'");
	}
    
	public function getPhoneCode($phone) {
		$query = $this->db->query("SELECT phonecode FROM `" . DB_PREFIX . "customer` WHERE REPLACE(REPLACE(REPLACE(REPLACE(LOWER(telephone), ' ', '' ), '-', ''), '(', ''), '(', '') LIKE '%" . $this->db->escape(utf8_strtolower($phone)) . "'");
	
        return $query->row;
    }
    
    public function setAuthToken($customer_id) {
        $tokens = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
        $customer_info = $query->row;
        
        if ($customer_info) {
            $authtoken = token(10);
            $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET authtoken = '" . $authtoken . "' WHERE customer_id='" . (int)$customer_id . "'");

            require 'system/library/jwt/src/JWT.php';
            
            $payload_array = array( 'customer_id' =>  $customer_info['customer_id'] );
            $payload = json_encode($payload_array);
            $jwttoken = Firebase\JWT\JWT::encode($payload, $authtoken . $customer_info['password']);
            $tokens = array(
                'authtoken' => $authtoken,
                'jwttoken' => $jwttoken
            );
        }
        
        return $tokens;
	}
    
	public function validateAuthTokens($authtoken, $jwttoken) {
        $customer_id = false;
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE authtoken = '" . $this->db->escape($authtoken) . "'");
        $customer_info = $query->row;
        
        if ($customer_info) {
            require 'system/library/jwt/src/JWT.php';
            
            $payload_array = array( 'customer_id' =>  $customer_info['customer_id'] );
            $payload = json_encode($payload_array);
            $auth_jwttoken = Firebase\JWT\JWT::encode($payload, $authtoken . $customer_info['password']);
           
            if($auth_jwttoken === $jwttoken){
                $customer_id = $customer_info['customer_id'];
            }
        }
        
        return $customer_id;
    }  
    //-----------------------------------------------------------------------------------------------------------------------------------------------------
}