<?php
class ModelLocalisationTaxClass extends Model {
	public function addTaxClass($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "tax_class SET title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "', date_added = NOW()");
		//$tax_class_id = $this->db->getLastId();
		$collection="mongo_tax_class";
		$tax_class_id=1+(int)$this->mongodb->getlastid($collection,'tax_class_id');
		$where=array('tax_class_id'=>(int)$tax_class_id, 'title'=>$data['title'], 'description'=>$data['description'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 
			
		$collection="mongo_tax_rule";
		if (isset($data['tax_rule'])) {
			foreach ($data['tax_rule'] as $tax_rule) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "tax_rule SET tax_class_id = '" . (int)$tax_class_id . "', tax_rate_id = '" . (int)$tax_rule['tax_rate_id'] . "', based = '" . $this->db->escape($tax_rule['based']) . "', priority = '" . (int)$tax_rule['priority'] . "'");
				$tax_rule_id=1+(int)$this->mongodb->getlastid($collection,'tax_rule_id');
				$where=array('tax_rule_id'=>(int)$tax_rule_id, 'tax_class_id'=>(int)$tax_class_id, 'tax_rate_id'=>(int)$data['tax_rate_id'], 'based'=>$tax_rule['based'], 'priority'=>(int)$tax_rule['priority']);
				$this->mongodb->create($collection,$where); 
			}
		}

		$this->cache->delete('tax_class');
	}

	public function editTaxClass($tax_class_id, $data) {
		//$this->db->query("UPDATE " . DB_PREFIX . "tax_class SET title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "', date_modified = NOW() WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		$collection="mongo_tax_class";
		$where=array('tax_class_id'=>(int)$tax_class_id, 'title'=>$data['title'], 'description'=>$data['description'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('tax_class_id'=>(int)$tax_class_id);
		$this->mongodb->update($collection,$infoupdate,$where); 

		//$this->db->query("DELETE FROM " . DB_PREFIX . "tax_rule WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		$collection="mongo_tax_rule";
		$where=array('tax_class_id'=>(int)$tax_class_id);
		$this->mongodb->delete($collection,$where); 

		$collection="mongo_tax_rule";
		if (isset($data['tax_rule'])) {
			foreach ($data['tax_rule'] as $tax_rule) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "tax_rule SET tax_class_id = '" . (int)$tax_class_id . "', tax_rate_id = '" . (int)$tax_rule['tax_rate_id'] . "', based = '" . $this->db->escape($tax_rule['based']) . "', priority = '" . (int)$tax_rule['priority'] . "'");
				$tax_rule_id=1+(int)$this->mongodb->getlastid($collection,'tax_rule_id');
				$where=array('tax_rule_id'=>(int)$tax_rule_id, 'tax_class_id'=>(int)$tax_class_id, 'tax_rate_id'=>(int)$data['tax_rate_id'], 'based'=>$tax_rule['based'], 'priority'=>(int)$tax_rule['priority']);
				$this->mongodb->create($collection,$where); 
			}
		}

		$this->cache->delete('tax_class');
	}

	public function deleteTaxClass($tax_class_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "tax_class WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		$collection="mongo_tax_class";
		$where=array('geo_zone_id'=>(int)$tax_class_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "tax_rule WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		$collection="mongo_tax_rule";
		$where=array('tax_class_id'=>(int)$tax_class_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('tax_class');
	}

	public function getTaxClass($tax_class_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_class WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		//return $query->row;
		$collection="mongo_tax_class";
		$where=array('tax_class_id'=>(int)$tax_class_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getTaxClasses($data = array()) {
		$collection="mongo_tax_class";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "tax_class";
			$sql .= " ORDER BY title";
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
			$orderby = "title";
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$tax_class_data = $this->cache->get('tax_class');
			if (!$tax_class_data) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_class");
				//$tax_class_data = $query->rows;
				$where=array();
				$order=array('title'=> 1);
				$tax_class_data=$this->mongodb->getall($collection,$where, $order);
				$this->cache->set('tax_class', $tax_class_data);
			}
			return $tax_class_data;
		}
	}

	public function getTotalTaxClasses() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tax_class");
		//return $query->row['total'];
		$collection="mongo_tax_class";
		$where=array();
		$tax_class_data=$this->mongodb->gettotal($collection,$where);
		return $tax_class_data;
	}

	public function getTaxRules($tax_class_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_rule WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		//return $query->rows;
		$collection="mongo_tax_rule";
		$where=array('tax_class_id'=>(int)$tax_class_id);
		$order=array();
		return $this->mongodb->getall($collection,$where, $order);
	}

	public function getTotalTaxRulesByTaxRateId($tax_rate_id) {
		//$query = $this->db->query("SELECT COUNT(DISTINCT tax_class_id) AS total FROM " . DB_PREFIX . "tax_rule WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
		//return $query->row['total'];
		$collection="mongo_tax_rule";
		$where=array('tax_class_id'=> array('$exists'=> true));
		$tax_class_data=$this->mongodb->gettotal($collection,$where);
		return $tax_class_data;
	}
}