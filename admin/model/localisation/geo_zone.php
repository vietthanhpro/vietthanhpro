<?php
class ModelLocalisationGeoZone extends Model {
	public function addGeoZone($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_added = NOW()");
		//$geo_zone_id = $this->db->getLastId();	
		$collection="mongo_geo_zone";
		$geo_zone_id=1+(int)$this->mongodb->getlastid($collection,'geo_zone_id');
		$where=array('geo_zone_id'=>(int)$geo_zone_id, 'name'=>$data['name'], 'description'=>$data['description'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 
			
		$collection="mongo_zone_to_geo_zone";
		if (isset($data['zone_to_geo_zone'])) {
			foreach ($data['zone_to_geo_zone'] as $value) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '" . (int)$value['country_id'] . "', zone_id = '" . (int)$value['zone_id'] . "', geo_zone_id = '" . (int)$geo_zone_id . "', date_added = NOW()");
				$zone_to_geo_zone_id=1+(int)$this->mongodb->getlastid($collection,'zone_to_geo_zone_id');
				$where=array('zone_to_geo_zone_id'=>(int)$zone_to_geo_zone_id, 'country_id'=>(int)$value['country_id'], 'zone_id'=>(int)$value['zone_id'], 'geo_zone_id'=>(int)$geo_zone_id, 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
				$this->mongodb->create($collection,$where); 
			}
		}

		$this->cache->delete('geo_zone');
	}

	public function editGeoZone($geo_zone_id, $data) {
		//$this->db->query("UPDATE " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_modified = NOW() WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		$collection="mongo_geo_zone";
		$infoupdate=array('geo_zone_id'=>(int)$geo_zone_id, 'name'=>$data['name'], 'description'=>$data['description'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('geo_zone_id'=>(int)$geo_zone_id);
		$this->mongodb->update($collection,$infoupdate,$where); 

		//$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		$collection="mongo_zone_to_geo_zone";
		$where=array('geo_zone_id'=>(int)$geo_zone_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['zone_to_geo_zone'])) {
			foreach ($data['zone_to_geo_zone'] as $value) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '" . (int)$value['country_id'] . "', zone_id = '" . (int)$value['zone_id'] . "', geo_zone_id = '" . (int)$geo_zone_id . "', date_added = NOW()");
				$zone_to_geo_zone_id=1+(int)$this->mongodb->getlastid($collection,'zone_to_geo_zone_id');
				$where=array('zone_to_geo_zone_id'=>(int)$zone_to_geo_zone_id, 'country_id'=>(int)$value['country_id'], 'zone_id'=>(int)$value['zone_id'], 'geo_zone_id'=>(int)$geo_zone_id, 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
				$this->mongodb->create($collection,$where); 
			}
		}

		$this->cache->delete('geo_zone');
	}

	public function deleteGeoZone($geo_zone_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		$collection="mongo_geo_zone";
		$where=array('geo_zone_id'=>(int)$geo_zone_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		$collection="mongo_zone_to_geo_zone";
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('geo_zone');
	}

	public function getGeoZone($geo_zone_id) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		//return $query->row;
		$collection="mongo_geo_zone";
		$where=array('geo_zone_id'=>(int)$geo_zone_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getGeoZones($data = array()) {
		$collection="mongo_geo_zone";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "geo_zone";
			$sort_data = array(
				'name',
				'description'
			);
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
			}
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}
				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}
			//$query = $this->db->query($sql);
			return $query->rows;
			*/
			$where=array();
			$order=array();
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}	
				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}
				$start=$data['start'];
				$limit=$data['limit'];
			} else {
				$start=0;
				$limit=0;
			}	
			$sort_data = array(
				'name',
				'description'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = "name";
			}	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$geo_zone_data = $this->cache->get('geo_zone');
			if (!$geo_zone_data) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name ASC");
				//$geo_zone_data = $query->rows;
				$where=array();
				$order=array('name'=> 1);
				$geo_zone_data=$this->mongodb->getall($collection,$where, $order);
				$this->cache->set('geo_zone', $geo_zone_data);
			}
			return $geo_zone_data;
		}
	}

	public function getTotalGeoZones() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "geo_zone");
		//return $query->row['total'];
		$collection="mongo_geo_zone";
		$where=array();
		$geo_zone_data=$this->mongodb->gettotal($collection,$where);
		return $geo_zone_data;
	}

	public function getZoneToGeoZones($geo_zone_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		//return $query->rows;
		$collection="mongo_zone_to_geo_zone";
		$where=array('geo_zone_id' => (int)$geo_zone_id);
		$order=array();
		$zone_to_geo_zone_data=$this->mongodb->getall($collection,$where, $order);
		return $zone_to_geo_zone_data;
	}

	public function getTotalZoneToGeoZoneByGeoZoneId($geo_zone_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		//return $query->row['total'];
		$collection="mongo_zone_to_geo_zone";
		$where=array('geo_zone_id' => (int)$geo_zone_id);
		$zone_to_geo_zone_data=$this->mongodb->gettotal($collection,$where);
		return $zone_to_geo_zone_data;
	}

	public function getTotalZoneToGeoZoneByCountryId($country_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = '" . (int)$country_id . "'");
		//return $query->row['total'];
		$collection="mongo_zone_to_geo_zone";
		$where=array('country_id' => (int)$country_id);
		$zone_to_geo_zone_data=$this->mongodb->gettotal($collection,$where);
		return $zone_to_geo_zone_data;
	}

	public function getTotalZoneToGeoZoneByZoneId($zone_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE zone_id = '" . (int)$zone_id . "'");
		//return $query->row['total'];
		$collection="mongo_zone_to_geo_zone";
		$where=array('zone_id' => (int)$zone_id);
		$zone_to_geo_zone_data=$this->mongodb->gettotal($collection,$where);
		return $zone_to_geo_zone_data;
	}
}