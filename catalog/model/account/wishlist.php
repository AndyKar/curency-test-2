<?php
class ModelAccountWishlist extends Model {
	public function addWishlist($product_id, $total = 1) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_wishlist SET customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', total = '" . (int)$total . "', date_added = NOW()");
	}

	public function deleteWishlist($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "'");
	}

	public function getWishlist() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");
        $wishlist = array();
		foreach ($query->rows as $row){
            $wishlist[] = array (
                'product_id' => $row['product_id'],
                'quantity' => $row['total']
            );
        }
        return $wishlist;
	}

	public function getTotalWishlist() {
		$query = $this->db->query("SELECT COUNT(*) AS totallist FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['totallist'];
	}
	
}
