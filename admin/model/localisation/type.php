<?php
class ModelLocalisationType extends Model {
	public function addType($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "type SET value = '" . (float)$data['value'] . "'");
		//$type_id = $this->db->getLastId();
		$collection="mongo_type";
		$type_id=1+(int)$this->mongodb->getlastid($collection,'type_id');
		$type_description= array();

		foreach ($data['type_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "type_description SET type_id = '" . (int)$type_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['name']) . "', unit = '" . $this->db->escape($value['unit']) . "'");		
			$type_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$where=array('type_id'=>(int)$type_id, 'type_description'=>$type_description, 'code'=>$data['code'], 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status']);
		$this->mongodb->create($collection,$where); 

		$this->cache->delete('type');
	}

	public function editType($type_id, $data) {
		//$this->db->query("UPDATE " . DB_PREFIX . "type SET value = '" . (float)$data['value'] . "' WHERE type_id = '" . (int)$type_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "type_description WHERE type_id = '" . (int)$type_id . "'");
		$collection="mongo_type";
		$type_description= array();

		foreach ($data['type_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "type_description SET type_id = '" . (int)$type_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['name']) . "', unit = '" . $this->db->escape($value['unit']) . "'");		
			$type_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$infoupdate=array('type_id'=>(int)$type_id, 'type_description'=>$type_description, 'code'=>$data['code'], 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status']);
		$where=array('type_id'=>(int)$type_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('type');
	}

	public function deleteType($type_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "type WHERE type_id = '" . (int)$type_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "type_description WHERE type_id = '" . (int)$type_id . "'");
		$collection="mongo_type";
		$where=array('type_id'=>(int)$type_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('type');
	}

	public function getTypes($data = array()) {
		$collection="mongo_type";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "type lc LEFT JOIN " . DB_PREFIX . "type_description lcd ON (lc.type_id = lcd.type_id) WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			$sort_data = array(
				'name',
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
				'name',
				'code',
				'sort_order'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'type_description.'. (int)$this->config->get('config_language_id').'.title';
			}
			if ($orderby == 'name') $orderby = 'type_description.'. (int)$this->config->get('config_language_id').'.title';	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$type_data = $this->cache->get('type');
			if (!$type_data) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "type lc LEFT JOIN " . DB_PREFIX . "type_description lcd ON (lc.type_id = lcd.type_id) WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				//$type_data = $query->rows;
				$where=array();
				$order=array('type_description.'. (int)$this->config->get('config_language_id').'.title'=> 1);
				$type_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('type', $type_data);
			}
			return $type_data;
		}
	}

	public function getType($type_id) {
		$collection="mongo_type";
		$where=array('type_id'=>(int)$type_id);
		return $this->mongodb->getBy($collection,$where);
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "type lc LEFT JOIN " . DB_PREFIX . "type_description lcd ON (lc.type_id = lcd.type_id) WHERE lc.type_id = '" . (int)$type_id . "' AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
	}

	public function getTotalTypes() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "type");
		//return $query->row['total'];
		$collection="mongo_type";
		$where=array();
		$type_data=$this->mongodb->gettotal($collection,$where);
		return $type_data;
	}
}