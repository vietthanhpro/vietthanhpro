<?php
class ModelLocalisationCountry extends Model {
	public function addCountry($data) {
		$collection="mongo_country";
		$country_id=1+(int)$this->mongodb->getlastid($collection,'country_id');
		$where=array('country_id'=>(int)$country_id, 'name'=>$data['name'], 'iso_code_2'=>$data['iso_code_2'], 'iso_code_3'=>$data['iso_code_3'], 'address_format'=>$data['address_format'], 'postcode_required'=>(int)$data['postcode_required'], 'status'=>(int)$data['status']);
		$this->mongodb->create($collection,$where); 
		//$this->db->query("INSERT INTO " . DB_PREFIX . "country SET name = '" . $this->db->escape($data['name']) . "', iso_code_2 = '" . $this->db->escape($data['iso_code_2']) . "', iso_code_3 = '" . $this->db->escape($data['iso_code_3']) . "', address_format = '" . $this->db->escape($data['address_format']) . "', postcode_required = '" . (int)$data['postcode_required'] . "', status = '" . (int)$data['status'] . "'");

		$this->cache->delete('country');
	}

	public function editCountry($country_id, $data) {
		$collection="mongo_country";
		$infoupdate=array('country_id'=>(int)$country_id, 'name'=>$data['name'], 'iso_code_2'=>$data['iso_code_2'], 'iso_code_3'=>$data['iso_code_3'], 'address_format'=>$data['address_format'], 'postcode_required'=>(int)$data['postcode_required'], 'status'=>(int)$data['status']);
		$where=array('country_id'=>(int)$country_id);
		$this->mongodb->update($collection,$infoupdate,$where); 
		//$this->db->query("UPDATE " . DB_PREFIX . "country SET name = '" . $this->db->escape($data['name']) . "', iso_code_2 = '" . $this->db->escape($data['iso_code_2']) . "', iso_code_3 = '" . $this->db->escape($data['iso_code_3']) . "', address_format = '" . $this->db->escape($data['address_format']) . "', postcode_required = '" . (int)$data['postcode_required'] . "', status = '" . (int)$data['status'] . "' WHERE country_id = '" . (int)$country_id . "'");

		$this->cache->delete('country');
	}

	public function deleteCountry($country_id) {
		$collection="mongo_country";
		$where=array('country_id'=>(int)$country_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "'");

		$this->cache->delete('country');
	}

	public function getCountry($country_id) {
		$collection="mongo_country";
		$where=array('country_id'=>(int)$country_id);
		return $this->mongodb->getBy($collection,$where);
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "'");
		//return $query->row;
	}

	public function getCountries($data = array()) {
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "country";

			$sort_data = array(
				'name',
				'iso_code_2',
				'iso_code_3'
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
			$collection="mongo_country";
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
				'iso_code_2',
				'iso_code_3'
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
			$country_data = $this->cache->get('country');

			if (!$country_data) {
				/*$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country ORDER BY name ASC");
				$country_data = $query->rows;*/
				$collection="mongo_country";
				$where=array();
				$order=array('name'=> 1);
				$country_data=$this->mongodb->getall($collection,$where, $order);
				$this->cache->set('country', $country_data);
			}

			return $country_data;
		}
	}

	public function getTotalCountries() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "country");
		//return $query->row['total'];
		$collection="mongo_country";$country_data= 0;
		$where=array();
		$country_data=$this->mongodb->gettotal($collection,$where);
		return $country_data;
	}
}