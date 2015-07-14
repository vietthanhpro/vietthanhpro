<?php
class ModelLocalisationZone extends Model {
	public function addZone($data) {
		$collection="mongo_zone";
		$zone_id=1+(int)$this->mongodb->getlastid($collection,'zone_id');
		$where=array('zone_id'=>(int)$zone_id, 'name'=>$data['name'], 'code'=>$data['code'], 'country_id'=>(int)$data['country_id'], 'status'=>(int)$data['status']);
		$this->mongodb->create($collection,$where); 
		//$this->db->query("INSERT INTO " . DB_PREFIX . "zone SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', country_id = '" . (int)$data['country_id'] . "'");

		$this->cache->delete('zone');
	}

	public function editZone($zone_id, $data) {
		$collection="mongo_zone";
		$infoupdate=array('zone_id'=>(int)$zone_id, 'name'=>$data['name'], 'code'=>$data['code'], 'country_id'=>(int)$data['country_id'], 'status'=>(int)$data['status']);
		$where=array('zone_id'=>(int)$zone_id);
		$this->mongodb->update($collection,$infoupdate,$where); 
		//$this->db->query("UPDATE " . DB_PREFIX . "zone SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', country_id = '" . (int)$data['country_id'] . "' WHERE zone_id = '" . (int)$zone_id . "'");
		$this->cache->delete('zone');
	}

	public function deleteZone($zone_id) {
		$collection="mongo_zone";
		$where=array('zone_id'=>(int)$zone_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "'");
		$this->cache->delete('zone');
	}

	public function getZone($zone_id) {
		$collection="mongo_zone";
		$where=array('zone_id'=>(int)$zone_id);
		return $this->mongodb->getBy($collection,$where);
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "'");
		//return $query->row;
	}

	public function getZones($data = array()) {
		$collection="mongo_zone";
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
		switch ($data['sort']) {
			case "c.name":
				$data['sort']='country_id';
				break;
			case "z.name":
				$data['sort']='name';
				break;
			case "z.code":
				$data['sort']='code';
				break;
		}
		$sort_data = array(
			'name',
			'country_id',
			'code'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$orderby = $data['sort'];
		} else {
			$orderby = "country_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order[$orderby] = -1;
		} else {
			$order[$orderby]= 1;
		} //print_r($order); die();
		return $this->mongodb->get($collection,$where, $order, $start, $limit);
		/*
		$sql = "SELECT *, z.name, c.name AS country FROM " . DB_PREFIX . "zone z LEFT JOIN " . DB_PREFIX . "country c ON (z.country_id = c.country_id)";

		$sort_data = array(
			'c.name',
			'z.name',
			'z.code'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY c.name";
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

		return $query->rows;*/
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

	public function getTotalZones() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone");
		//return $query->row['total'];	
		$collection="mongo_zone";$zone_data= array();
		$where=array();
		$zone_data=$this->mongodb->gettotal($collection,$where);
		return $zone_data;	
	}

	public function getTotalZonesByCountryId($country_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "'");
		//return $query->row['total'];
			$collection="mongo_zone";
			$where=array('country_id'=> (int)$country_id);
			return $this->mongodb->gettotal($collection,$where);
	}
}