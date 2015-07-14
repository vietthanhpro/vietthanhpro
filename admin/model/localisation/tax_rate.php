<?php
class ModelLocalisationTaxRate extends Model {
	public function addTaxRate($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "tax_rate SET name = '" . $this->db->escape($data['name']) . "', rate = '" . (float)$data['rate'] . "', `type` = '" . $this->db->escape($data['type']) . "', geo_zone_id = '" . (int)$data['geo_zone_id'] . "', date_added = NOW(), date_modified = NOW()");
		//$tax_rate_id = $this->db->getLastId();
		$collection="mongo_tax_rate";
		$tax_rate_id=1+(int)$this->mongodb->getlastid($collection,'tax_rate_id');
		$where=array('tax_rate_id'=>(int)$tax_rate_id, 'name'=>$data['name'], 'rate'=>(float)$data['rate'], 'type'=>$data['type'], 'geo_zone_id'=>(int)$data['geo_zone_id'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 
		
		$collection="mongo_tax_rate_to_customer_group";
		if (isset($data['tax_rate_customer_group'])) {
			foreach ($data['tax_rate_customer_group'] as $customer_group_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "tax_rate_to_customer_group SET tax_rate_id = '" . (int)$tax_rate_id . "', customer_group_id = '" . (int)$customer_group_id . "'");
				$where=array('tax_rate_id'=>(int)$tax_rate_id, 'customer_group_id'=>(int)$customer_group_id);
				$this->mongodb->create($collection,$where); 
			}
		}
	}

	public function editTaxRate($tax_rate_id, $data) {
		//$this->db->query("UPDATE " . DB_PREFIX . "tax_rate SET name = '" . $this->db->escape($data['name']) . "', rate = '" . (float)$data['rate'] . "', `type` = '" . $this->db->escape($data['type']) . "', geo_zone_id = '" . (int)$data['geo_zone_id'] . "', date_modified = NOW() WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		$collection="mongo_tax_rate";
		$where=array('tax_rate_id'=>(int)$tax_rate_id, 'name'=>$data['name'], 'rate'=>(float)$data['rate'], 'type'=>$data['type'], 'geo_zone_id'=>(int)$data['geo_zone_id'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('tax_rate_id'=>(int)$tax_rate_id);
		$this->mongodb->update($collection,$infoupdate,$where); 

		//$this->db->query("DELETE FROM " . DB_PREFIX . "tax_rate_to_customer_group WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		$collection="mongo_tax_rate_to_customer_group";
		$where=array('tax_rate_id'=>(int)$tax_rate_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['tax_rate_customer_group'])) {
			foreach ($data['tax_rate_customer_group'] as $customer_group_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "tax_rate_to_customer_group SET tax_rate_id = '" . (int)$tax_rate_id . "', customer_group_id = '" . (int)$customer_group_id . "'");
				$where=array('tax_rate_id'=>(int)$tax_rate_id, 'customer_group_id'=>(int)$customer_group_id);
				$this->mongodb->create($collection,$where); 
			}
		}
	}

	public function deleteTaxRate($tax_rate_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "tax_rate WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		$collection="mongo_tax_rate";
		$where=array('tax_rate_id'=>(int)$tax_rate_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "tax_rate_to_customer_group WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		$collection="mongo_tax_rate_to_customer_group";
		$where=array('tax_rate_id'=>(int)$tax_rate_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getTaxRate($tax_rate_id) {
		//$query = $this->db->query("SELECT tr.tax_rate_id, tr.name AS name, tr.rate, tr.type, tr.geo_zone_id, gz.name AS geo_zone, tr.date_added, tr.date_modified FROM " . DB_PREFIX . "tax_rate tr LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr.geo_zone_id = gz.geo_zone_id) WHERE tr.tax_rate_id = '" . (int)$tax_rate_id . "'");
		//return $query->row;
		$data_result = array();
		$data_result1 = array();
		$collection="mongo_tax_rate";
		$where=array('tax_rate_id'=>(int)$tax_rate_id);
		$data_result= $this->mongodb->getBy($collection,$where);
		if ($data_result) {
			$collection="mongo_geo_zone";
			$where=array('geo_zone_id'=>(int)$data_result['geo_zone_id']);
			$data_result1= $this->mongodb->getBy($collection,$where);
			if ($data_result1) {$data_result['geo_zone']=$data_result1['name'];}
			else {$data_result['geo_zone']='';}
		} 
		return $data_result;
	}

	public function getTaxRates($data = array()) {
		$collection="mongo_tax_rate";
		if ($data) { /*
			$sql = "SELECT tr.tax_rate_id, tr.name AS name, tr.rate, tr.type, gz.name AS geo_zone, tr.date_added, tr.date_modified FROM " . DB_PREFIX . "tax_rate tr LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr.geo_zone_id = gz.geo_zone_id)";
			$sort_data = array(
				'tr.name',
				'tr.rate',
				'tr.type',
				'gz.name',
				'tr.date_added',
				'tr.date_modified'
			);
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY tr.name";
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
				'rate',
				'type',
				'geo_zone_id',
				'date_added',
				'date_modified'
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
			$tax_rate_data = $this->cache->get('tax_rate');
			if (!$tax_rate_data) {
				$where=array();
				$order=array('name'=> 1);
				$tax_rate_data=$this->mongodb->getall($collection,$where, $order);
				$this->cache->set('tax_rate', $tax_rate_data);
			}
			return $tax_rate_data;
		}
	}

	public function getTaxRateCustomerGroups($tax_rate_id) {
		$collection="mongo_tax_rate_to_customer_group";
		$tax_customer_group_data = array();
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_rate_to_customer_group WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		$where=array('tax_rate_id'=>(int)$tax_rate_id);
		$order=array('name'=> 1);
		$tax_customer_group_list=$this->mongodb->getall($collection,$where, $order);
		
		//foreach ($query->rows as $result) {
		foreach ($tax_customer_group_list as $result) {
			$tax_customer_group_data[] = $result['customer_group_id'];
		}
		return $tax_customer_group_data;
	}

	public function getTotalTaxRates() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tax_rate");
		//return $query->row['total'];
		$collection="mongo_tax_rate";
		$where=array();
		$geo_zone_data=$this->mongodb->gettotal($collection,$where);
		return $geo_zone_data;
	}

	public function getTotalTaxRatesByGeoZoneId($geo_zone_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tax_rate WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		//return $query->row['total'];
		$collection="mongo_tax_rate";
		$where=array('geo_zone_id'=>(int)$geo_zone_id);
		$geo_zone_data=$this->mongodb->gettotal($collection,$where);
		return $geo_zone_data;
	}
}