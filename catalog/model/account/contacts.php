<?php
class ModelAccountContacts extends Model {
	public function addContact($data) {

		$sql = "INSERT INTO " . DB_PREFIX . "contacts SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', customer_id = '" . (int)$this->customer->getId() . "', date_added = NOW()";

        $this->db->query($sql);
		$contact_id = $this->db->getLastId();
	
		return $contact_id;
	}
    
	public function addContactToFavorite($contact_id) {
        
        $exist_query = $this->db->query("SELECT contact_id FROM " . DB_PREFIX . "favorite_contacts WHERE customer_id = '" . (int)$this->customer->getId() . "' AND contact_id = '" . (int)$contact_id . "'");

        if(!$exist_query->row['contact_id']){
            $this->db->query("INSERT INTO " . DB_PREFIX . "favorite_contacts SET contact_id = '" . (int)$contact_id . "', customer_id = '" . (int)$this->customer->getId() . "'");
        }
	}

	public function editContact($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "contacts SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . isset($data['telephone'])?$this->db->escape($data['telephone']):'' . "', custom_field = '" . "' WHERE contact_id = '" . (int)$data['contact_id'] . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	}
    
	public function deleteContact($contact_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "contacts WHERE contact_id = '" . (int)$contact_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	}
    
	public function deleteFavoriteContact($contact_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "favorite_contacts WHERE contact_id = '" . (int)$contact_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function getContactsByCustomerId($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "contacts WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->rows;
	}
    
	public function getFavoriteContacts() {
		$sql = "SELECT * FROM " . DB_PREFIX . "favorite_contacts fc LEFT JOIN " . DB_PREFIX . "contacts c ON (c.contact_id = fc.contact_id) WHERE fc.customer_id = '" . (int)$this->customer->getId() . "'";
		$query = $this->db->query($sql);

		return $query->rows;
	}
    
	public function getContacts() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "contacts c WHERE c.customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->rows;
	}
    
	public function getCorpContacts() {
		$query = $this->db->query("SELECT * , (SELECT fc.contact_id FROM " . DB_PREFIX . "favorite_contacts fc WHERE fc.customer_id = '" . (int)$this->customer->getId() . "' AND fc.contact_id = c.contact_id) as favorite FROM " . DB_PREFIX . "contacts c WHERE c.customer_id = '1'");

		return $query->rows;
	}

	public function getContactById($contact_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE contact_id = '" . (int)$contact_id . "'");

		return $query->row;
	}

	public function getTotalCustomerContactById($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "contacts WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}
}