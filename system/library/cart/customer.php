<?php
namespace Cart;
class Customer {
	private $customer_id;
	private $firstname;
	private $lastname;
	private $email;
	private $telephone;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

        // AUTH BY COOKIE TOKENS
        if(!isset($this->session->data['customer_id']) && isset($this->request->cookie['auth_token']) && isset($this->request->cookie['auth_jwt_token'])){
            $authtoken = $this->request->cookie['auth_token'];
            $jwttoken = $this->request->cookie['auth_jwt_token'];
            
            $customer_id = false;
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE authtoken = '" . $this->db->escape($authtoken) . "'");
            $customer_info = $query->row;

            if ($customer_info) {
                require DIR_SYSTEM . 'library/jwt/src/JWT.php';
                $payload_array = array( 'customer_id' => $customer_info['customer_id'] );
                $payload = json_encode($payload_array);
                $auth_jwttoken = \Firebase\JWT\JWT::encode($payload, $authtoken . $customer_info['password']);

                if($auth_jwttoken === $jwttoken){
                    $this->session->data['customer_id'] = $customer_info['customer_id'];
                    
                    // SET NEW TOKENS FOR NEXT AUTH BY COOKIE TOKENS
                    $authtoken_next = token(10);
                    $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET authtoken = '" . $authtoken_next . "' WHERE customer_id='" . (int)$customer_info['customer_id'] . "'");

                    $payload_array = array( 'customer_id' =>  $customer_info['customer_id'] );
                    $payload = json_encode($payload_array);
                    $jwttoken_next = \Firebase\JWT\JWT::encode($payload, $authtoken_next . $customer_info['password']);

                    SetCookie('auth_token', $authtoken_next, time() + 60 * 60 * 24 * 30, '/');
                    SetCookie('auth_jwt_token', $jwttoken_next, time() + 60 * 60 * 24 * 30, '/');
                }
            }
        }

		if (isset($this->session->data['customer_id'])) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");

			if ($customer_query->num_rows) {
				$this->customer_id = $customer_query->row['customer_id'];
				$this->firstname = $customer_query->row['firstname'];
				$this->lastname = $customer_query->row['lastname'];
				$this->email = $customer_query->row['email'];
				$this->telephone = $customer_query->row['telephone'];

			} else {
				$this->logout();
			}
		}
	}

  public function login($email, $password, $override = false) {
		if ($override) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND status = '1'");
		} else {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");
		}

		if ($customer_query->num_rows) {
			$this->session->data['customer_id'] = $customer_query->row['customer_id'];

			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['customer_id']);

		$this->customer_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->telephone = '';
	}

	public function isLogged() {
		return $this->customer_id;
	}

	public function getId() {
		return $this->customer_id;
	}

	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}

	public function getGroupId() {
		return $this->customer_group_id;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getTelephone() {
		return $this->telephone;
	}
}
