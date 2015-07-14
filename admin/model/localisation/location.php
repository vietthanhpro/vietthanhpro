<?php
class ModelLocalisationLocation extends Model {
	public function addLocation($data) {
		$collection="mongo_location";
		$location_id=1+(int)$this->mongodb->getlastid($collection,'location_id');
		$where=array('location_id'=>(int)$location_id, 'name'=>$data['name'], 'address'=>$data['address'], 'geocode'=>$data['geocode'], 'telephone'=>$data['telephone'], 'fax'=>$data['fax'], 'image'=>$data['image'], 'open'=>$data['open'], 'comment'=>$data['comment']);
		$this->mongodb->create($collection,$where); 
		//$this->db->query("INSERT INTO " . DB_PREFIX . "location SET name = '" . $this->db->escape($data['name']) . "', address = '" . $this->db->escape($data['address']) . "', geocode = '" . $this->db->escape($data['geocode']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', image = '" . $this->db->escape($data['image']) . "', open = '" . $this->db->escape($data['open']) . "', comment = '" . $this->db->escape($data['comment']) . "'");
	}

	public function editLocation($location_id, $data) {
		$collection="mongo_location";
		$infoupdate=array('location_id'=>(int)$location_id, 'name'=>$data['name'], 'address'=>$data['address'], 'geocode'=>$data['geocode'], 'telephone'=>$data['telephone'], 'fax'=>$data['fax'], 'image'=>$data['image'], 'open'=>$data['open'], 'comment'=>$data['comment']);
		$where=array('location_id'=>(int)$location_id);
		$this->mongodb->update($collection,$infoupdate,$where); 
		//$this->db->query("UPDATE " . DB_PREFIX . "location SET name = '" . $this->db->escape($data['name']) . "', address = '" . $this->db->escape($data['address']) . "', geocode = '" . $this->db->escape($data['geocode']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', image = '" . $this->db->escape($data['image']) . "', open = '" . $this->db->escape($data['open']) . "', comment = '" . $this->db->escape($data['comment']) . "' WHERE location_id = '" . (int)$location_id . "'");
	}

	public function deleteLocation($location_id) {
		$collection="mongo_location";
		$where=array('location_id'=>(int)$location_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "location WHERE location_id = " . (int)$location_id);
	}

	public function getLocation($location_id) {
		$collection="mongo_location";
		$where=array('location_id'=>(int)$location_id);
		return $this->mongodb->getBy($collection,$where);
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "location WHERE location_id = '" . (int)$location_id . "'");
		//return $query->row;
	}

	public function getLocations($data = array()) {
		$collection="mongo_location";
		if ($data) {
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
				'address',
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
				$where=array();
				$order=array('name'=> 1);
				return $this->mongodb->getall($collection,$where, $order);
		}
		/*
		$sql = "SELECT location_id, name, address FROM " . DB_PREFIX . "location";

		$sort_data = array(
			'name',
			'address',
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

		return $query->rows;*/
	}

	public function getTotalLocations() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "location");
		//return $query->row['total'];
		$collection="mongo_location";$location_data= array();
		$where=array();
		$location_data=$this->mongodb->gettotal($collection,$where);
		return $location_data;
	}
}
