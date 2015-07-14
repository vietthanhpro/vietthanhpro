<?php
class ModelLocalisationLengthClass extends Model {
	public function addLengthClass($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "length_class SET value = '" . (float)$data['value'] . "'");
		//$length_class_id = $this->db->getLastId();
		$collection="mongo_length_class";
		$length_class_id=1+(int)$this->mongodb->getlastid($collection,'length_class_id');
		$length_class_description= array();

		foreach ($data['length_class_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "length_class_description SET length_class_id = '" . (int)$length_class_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', unit = '" . $this->db->escape($value['unit']) . "'");		
			$length_class_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'title'=>$value['title'],
				'unit'=>$value['unit']
			);
		}
		$where=array('length_class_id'=>(int)$length_class_id, 'length_class_description'=>$length_class_description, 'value'=>(float)$data['value']);
		$this->mongodb->create($collection,$where); 

		$this->cache->delete('length_class');
	}

	public function editLengthClass($length_class_id, $data) {
		//$this->db->query("UPDATE " . DB_PREFIX . "length_class SET value = '" . (float)$data['value'] . "' WHERE length_class_id = '" . (int)$length_class_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "length_class_description WHERE length_class_id = '" . (int)$length_class_id . "'");
		$collection="mongo_length_class";
		$length_class_description= array();

		foreach ($data['length_class_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "length_class_description SET length_class_id = '" . (int)$length_class_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', unit = '" . $this->db->escape($value['unit']) . "'");		
			$length_class_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'title'=>$value['title'],
				'unit'=>$value['unit']
			);
		}
		$infoupdate=array('length_class_id'=>(int)$length_class_id, 'length_class_description'=>$length_class_description, 'value'=>(float)$data['value']);
		$where=array('length_class_id'=>(int)$length_class_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('length_class');
	}

	public function deleteLengthClass($length_class_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "length_class WHERE length_class_id = '" . (int)$length_class_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "length_class_description WHERE length_class_id = '" . (int)$length_class_id . "'");
		$collection="mongo_length_class";
		$where=array('length_class_id'=>(int)$length_class_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('length_class');
	}

	public function getLengthClasses($data = array()) {
		$collection="mongo_length_class";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "length_class lc LEFT JOIN " . DB_PREFIX . "length_class_description lcd ON (lc.length_class_id = lcd.length_class_id) WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
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
				$orderby = 'length_class_description.'. (int)$this->config->get('config_language_id').'.title';
			}
			if ($orderby == 'title') $orderby = 'length_class_description.'. (int)$this->config->get('config_language_id').'.title';	
			if ($orderby == 'unit') $orderby = 'length_class_description.'. (int)$this->config->get('config_language_id').'.unit';	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$length_class_data = $this->cache->get('length_class');
			if (!$length_class_data) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class lc LEFT JOIN " . DB_PREFIX . "length_class_description lcd ON (lc.length_class_id = lcd.length_class_id) WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				//$length_class_data = $query->rows;
				$where=array();
				$order=array('length_class_description.'. (int)$this->config->get('config_language_id').'.title'=> 1);
				$length_class_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('length_class', $length_class_data);
			}
			return $length_class_data;
		}
	}

	public function getLengthClass($length_class_id) {
		$collection="mongo_length_class";
		$where=array('length_class_id'=>(int)$length_class_id);
		return $this->mongodb->getBy($collection,$where);
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class lc LEFT JOIN " . DB_PREFIX . "length_class_description lcd ON (lc.length_class_id = lcd.length_class_id) WHERE lc.length_class_id = '" . (int)$length_class_id . "' AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
	}

	public function getLengthClassDescriptionByUnit($unit) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class_description WHERE unit = '" . $this->db->escape($unit) . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$collection="mongo_length_class";
		$where=array('length_class_description.'. (int)$this->config->get('config_language_id').'.unit'=>$unit);
		return $this->mongodb->getBy($collection,$where);
	}
/*
	public function getLengthClassDescriptions($length_class_id) {
		$length_class_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class_description WHERE length_class_id = '" . (int)$length_class_id . "'");

		foreach ($query->rows as $result) {
			$length_class_data[$result['language_id']] = array(
				'title' => $result['title'],
				'unit'  => $result['unit']
			);
		}

		return $length_class_data;
	}*/

	public function getTotalLengthClasses() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "length_class");
		//return $query->row['total'];
		$collection="mongo_length_class";
		$where=array();
		$length_class_data=$this->mongodb->gettotal($collection,$where);
		return $length_class_data;
	}
}