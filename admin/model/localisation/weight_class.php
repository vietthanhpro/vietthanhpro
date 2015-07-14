<?php
class ModelLocalisationWeightClass extends Model {
	public function addWeightClass($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class SET value = '" . (float)$data['value'] . "'");
		//$weight_class_id = $this->db->getLastId();
		$collection="mongo_weight_class";
		$weight_class_id=1+(int)$this->mongodb->getlastid($collection,'weight_class_id');
		$weight_class_description= array();

		foreach ($data['weight_class_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class_description SET weight_class_id = '" . (int)$weight_class_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', unit = '" . $this->db->escape($value['unit']) . "'");			
			$weight_class_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'title'=>$value['title'],
				'unit'=>$value['unit']
			);
		}
		$where=array('weight_class_id'=>(int)$weight_class_id, 'weight_class_description'=>$weight_class_description, 'value'=>(float)$data['value']);
		$this->mongodb->create($collection,$where); 

		$this->cache->delete('weight_class');
	}

	public function editWeightClass($weight_class_id, $data) {
		//$this->db->query("UPDATE " . DB_PREFIX . "weight_class SET value = '" . (float)$data['value'] . "' WHERE weight_class_id = '" . (int)$weight_class_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "weight_class_description WHERE weight_class_id = '" . (int)$weight_class_id . "'");
		$collection="mongo_weight_class";
		$weight_class_description= array();

		foreach ($data['weight_class_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "weight_class_description SET weight_class_id = '" . (int)$weight_class_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', unit = '" . $this->db->escape($value['unit']) . "'");		
			$weight_class_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'title'=>$value['title'],
				'unit'=>$value['unit']
			);
		}
		$infoupdate=array('weight_class_id'=>(int)$weight_class_id, 'weight_class_description'=>$weight_class_description, 'value'=>(float)$data['value']);
		$where=array('weight_class_id'=>(int)$weight_class_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('weight_class');
	}

	public function deleteWeightClass($weight_class_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "weight_class WHERE weight_class_id = '" . (int)$weight_class_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "weight_class_description WHERE weight_class_id = '" . (int)$weight_class_id . "'");
		$collection="mongo_weight_class";
		$where=array('weight_class_id'=>(int)$weight_class_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('weight_class');
	}

	public function getWeightClasses($data = array()) {
		$collection="mongo_weight_class";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			$sort_data = array(
				'title',
				'unit',
				'value'
			);
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY title";
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
				'title',
				'unit',
				'value'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'weight_class_description.'. (int)$this->config->get('config_language_id').'.title';
			}
			if ($orderby == 'title') $orderby = 'weight_class_description.'. (int)$this->config->get('config_language_id').'.title';	
			if ($orderby == 'unit') $orderby = 'weight_class_description.'. (int)$this->config->get('config_language_id').'.unit';	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$weight_class_data = $this->cache->get('weight_class');
			if (!$weight_class_data) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				//$weight_class_data = $query->rows;
				$where=array();
				$order=array('weight_class_description.'. (int)$this->config->get('config_language_id').'.title'=> 1);
				$weight_class_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('weight_class', $weight_class_data);
			}
			return $weight_class_data;
		}
	}

	public function getWeightClass($weight_class_id) {
		$collection="mongo_weight_class";
		$where=array('weight_class_id'=>(int)$weight_class_id);
		return $this->mongodb->getBy($collection,$where);
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wc.weight_class_id = '" . (int)$weight_class_id . "' AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
	}

	public function getWeightClassDescriptionByUnit($unit) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class_description WHERE unit = '" . $this->db->escape($unit) . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$collection="mongo_weight_class";
		$where=array('weight_class_description.'. (int)$this->config->get('config_language_id').'.unit'=>$unit);
		return $this->mongodb->getBy($collection,$where);
	}
/*
	public function getWeightClassDescriptions($weight_class_id) {
		$weight_class_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class_description WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		foreach ($query->rows as $result) {
			$weight_class_data[$result['language_id']] = array(
				'title' => $result['title'],
				'unit'  => $result['unit']
			);
		}

		return $weight_class_data;
	}*/

	public function getTotalWeightClasses() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "weight_class");
		//return $query->row['total'];
		$collection="mongo_weight_class";$weight_class_data= array();
		$where=array();
		$weight_class_data=$this->mongodb->gettotal($collection,$where);
		return $weight_class_data;
	}
}