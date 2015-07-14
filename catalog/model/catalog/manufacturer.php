<?php
class ModelCatalogManufacturer extends Model {
	public function getManufacturer($manufacturer_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		//return $query->row;
		$manufacturer_info = array();
		$collection="mongo_manufacturer";
		$where=array('manufacturer_id'=>(int)$manufacturer_id, 'manufacturer_to_store'=>(int)$this->config->get('config_store_id'));
		$manufacturer_info=$this->mongodb->getBy($collection,$where);
		return $manufacturer_info;
	}

	public function getManufacturers($data = array()) {
		$collection="mongo_manufacturer";
		if ($data) {
			$where=array('manufacturer_to_store'=>(int)$this->config->get('config_store_id'));
			$order=array();
			if (isset($data['start']) || isset($data['limit'])) {if ($data['start'] < 0) {$data['start'] = 0;}if ($data['limit'] < 1) {$data['limit'] = 20;}$start=$data['start'];$limit=$data['limit'];} else {$start=0;$limit=0;}	
			$sort_data = array('name','sort_order');	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {$orderby = $data['sort'];} else {$orderby = "name";}if (isset($data['order']) && ($data['order'] == 'DESC')) {$order[$orderby] = -1;} else {$order[$orderby]= 1;} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$manufacturer_data = $this->cache->get('manufacturer.' . (int)$this->config->get('config_store_id'));
			if (!$manufacturer_data) {
				$where=array('manufacturer_to_store'=>(int)$this->config->get('config_store_id'));
				$order=array('name'=> 1);
				$manufacturer_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('manufacturer.' . (int)$this->config->get('config_store_id'), $manufacturer_data);
			}
			return $manufacturer_data;
		}
	}
}