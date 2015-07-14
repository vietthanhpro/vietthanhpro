<?php
class ModelSaleFraud extends Model {
	public function getFraud($order_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_fraud` WHERE order_id = '" . (int)$order_id . "'");
		//return $query->row;
		$collection="mongo_order_fraud";
		$where=array('order_id'=>(int)$order_id);
		return $this->mongodb->getBy($collection,$where);
	}
}