<?php
class ModelAccountCustomerGroup extends Model {
	public function getCustomerGroup($customer_group_id) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cg.customer_group_id = '" . (int)$customer_group_id . "' AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$collection="mongo_customer_group";
		$where=array('customer_group_id'=>(int)$customer_group_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getCustomerGroups() {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cg.sort_order ASC, cgd.name ASC");
		//return $query->rows;
		$collection="mongo_customer_group";
		$where=array();
		$order=array('sort_order'=>1, 'customer_group_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
		return $this->mongodb->getall($collection,$where, $order);
	}
}