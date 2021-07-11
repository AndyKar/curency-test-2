<?php
class ModelAccountCurrency extends Model {
	public function addValute($data) {

		$sql = "INSERT INTO " . DB_PREFIX . "valutes SET valuteID = '" . $this->db->escape($data['valuteID']) 
                . "', numCode = '" . $this->db->escape($data['numCode']) 
                . "', charCode = '" . $this->db->escape($data['charCode']) 
                . "', nominal = '" . (int)$data['nominal']
                . "', name = '" . $this->db->escape($data['name']) 
                . "', date_added = NOW()";

        $this->db->query($sql);
		$valute_id = $this->db->getLastId();
	
		return $valute_id;
	}
    
    public function getValuteByValuteID($valuteID) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "valutes v WHERE v.valuteID = '" . $this->db->escape($valuteID) . "'");

		return $query->row;
	}
    
    public function getAllValutes() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "valutes v ORDER BY LCASE(v.name) ASC");
		return $query->rows;
	}

    public function getCurrencyByValuteIdDateRange($valuteID, $date1, $date2) {
		$sql = "SELECT * FROM " . DB_PREFIX . "currencies c LEFT JOIN " 
                . DB_PREFIX . "valutes V ON(c.valute_id = v.valute_id) WHERE v.valuteID = '" . $this->db->escape($valuteID) 
                . "' AND c.date > " . (int)$date1 . " AND c.date < " . (int)$date2;

        $query = $this->db->query($sql);
		return $query->rows;
	}
    
    public function getLastCurrenciesDate() {
		$query = $this->db->query("SELECT MAX(c.date) as max FROM " . DB_PREFIX . "currencies c");

		return $query->row;
	}
    
    public function addCurrency($data) {
		$sql = "INSERT INTO " . DB_PREFIX . "currencies SET valute_id = '" . (int)$data['valute_id']
                . "', value = '" . (float)$data['value'] 
                . "', date = '" . (int)$data['date'] . "'";
               
        $this->db->query($sql);
		$valute_id = $this->db->getLastId();
	
		return $valute_id;
	}

}