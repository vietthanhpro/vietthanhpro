<?php
class ModelLocalisationCountry extends Model {
	public function getCountry($country_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");
		//return $query->row;
		$collection="mongo_country";
		$where=array('country_id'=>(int)$country_id, 'status'=>1);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getCountries() {
		$country_data = $this->cache->get('country.status');

		if (!$country_data) {
			//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE status = '1' ORDER BY name ASC");
			//$country_data = $query->rows;
			$collection="mongo_country";
			$where=array();
			$order=array('name'=> 1);
			$country_data=$this->mongodb->getall($collection,$where, $order);
			$this->cache->set('country.status', $country_data);
		}

		return $country_data;
	}
}