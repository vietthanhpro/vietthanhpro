<?php
class ModelCheckoutMarketing extends Model {
	public function getMarketingByCode($code) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "marketing WHERE code = '" . $this->db->escape($code) . "'");
		//return $query->row;
		$marketing_query_data = array();
		$collection="mongo_marketing";
		$where=array('code'=>$code);
		$marketing_query_data = $this->mongodb->getBy($collection,$where);
		return $marketing_query_data;
	}
}