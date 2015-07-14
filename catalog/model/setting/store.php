<?php
class ModelSettingStore extends Model {
	public function getStores($data = array()) {
		$store_data = $this->cache->get('store');

		if (!$store_data) {
			//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");
			//$store_data = $query->rows;
			$layout_route_info = array();
			$collection="mongo_store";
			$where=array('store_id'=>0);
			$order=array('url'=>1);
			$layout_route_info=$this->mongodb->getall($collection,$where,$order);

			$this->cache->set('store', $store_data);
		}

		return $store_data;
	}
}