<?php
class ModelLocalisationZone extends Model {
	public function getZone($zone_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");
		//return $query->row;
		$collection="mongo_zone";
		$where=array('zone_id'=>(int)$zone_id, 'status'=>1);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getZonesByCountryId($country_id) {
		$zone_data = $this->cache->get('zone.' . (int)$country_id);
		if (!$zone_data) {
			//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");
			//$zone_data = $query->rows;
			$collection="mongo_zone";
			$where=array('country_id'=> (int)$country_id,'status'=> 1);
			$order=array('name'=> 1);
			$zone_zone=$this->mongodb->getall($collection,$where, $order);
			foreach ($zone_zone as $result) {
				$zone_data[] = array(
					'zone_id' => $result['zone_id'],
					'name'        => $result['name'],
					'country_id'        => $result['country_id'],
					'code'      => $result['code'],
					'status'      => $result['status']
				);
			}
			$this->cache->set('zone.' . (int)$country_id, $zone_data);
		}
		return $zone_data;
	}
}