<?php
class ModelLocalisationReturnReason extends Model {
	public function addReturnReason($data) {
		$collection="mongo_return_reason";
		foreach ($data['return_reason'] as $language_id => $value) {
			if (isset($return_reason_id)) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "return_reason SET return_reason_id = '" . (int)$return_reason_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
				$where=array('return_reason_id'=>(int)$return_reason_id, 'name'=>$value['name'], 'language_id'=>(int)$language_id);
			} else {
				$return_reason_id=1+(int)$this->mongodb->getlastid($collection,'return_reason_id');		
				$where=array('return_reason_id'=>(int)$return_reason_id, 'name'=>$value['name'], 'language_id'=>(int)$language_id);
				//$this->db->query("INSERT INTO " . DB_PREFIX . "return_reason SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				//$return_reason_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('return_reason');
	}

	public function editReturnReason($return_reason_id, $data) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "return_reason WHERE return_reason_id = '" . (int)$return_reason_id . "'");
		$collection="mongo_return_reason";
		$where=array('return_reason_id'=>(int)$return_reason_id);

		foreach ($data['return_reason'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "return_reason SET return_reason_id = '" . (int)$return_reason_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			$where=array('return_reason_id'=>(int)$return_reason_id, 'name'=>$value['name'], 'language_id'=>(int)$language_id);
			$this->mongodb->create($collection,$where); 
		}

		$this->cache->delete('return_reason');
	}

	public function deleteReturnReason($return_reason_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "return_reason WHERE return_reason_id = '" . (int)$return_reason_id . "'");
		$collection="mongo_return_reason";
		$where=array('return_reason_id'=>(int)$return_reason_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('return_reason');
	}

	public function getReturnReason($return_reason_id) {
		/*$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "return_reason WHERE return_reason_id = '" . (int)$return_reason_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");*/
		$collection="mongo_return_reason";
		$where=array('language_id'=>(int)$this->config->get('config_language_id'), 'return_reason_id'=>(int)$return_reason_id);
		return $this->mongodb->getBy($collection,$where);

		return $query->row;
	}

	public function getReturnReasons($data = array()) {
		$collection="mongo_return_reason";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "return_reason WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sql .= " ORDER BY name";

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
			$where=array('language_id' => (int)$this->config->get('config_language_id'));
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
	
			$orderby = 'name';
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$return_reason_data = $this->cache->get('return_reason.' . (int)$this->config->get('config_language_id'));

			if (!$return_reason_data) {/*
				//$query = $this->db->query("SELECT return_reason_id, name FROM " . DB_PREFIX . "return_reason WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
				$return_reason_data = $query->rows;*/
				$where=array('language_id' => (int)$this->config->get('config_language_id'));
				$order=array('name'=> 1);
				$return_reason_data=$this->mongodb->getall($collection,$where, $order);

				$this->cache->set('return_reason.' . (int)$this->config->get('config_language_id'), $return_reason_data);
			}

			return $return_reason_data;
		}
	}

	public function getReturnReasonDescriptions($return_reason_id) {
		$return_reason_data = array();
		
		$collection="mongo_return_reason";
		$where=array('return_reason_id' => (int)$return_reason_id);
		$order=array();
		$return_reason_list=$this->mongodb->getall($collection,$where, $order);
		
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "return_reason WHERE return_reason_id = '" . (int)$return_reason_id . "'");

		//foreach ($query->rows as $result) {
		foreach ($return_reason_list as $result) {
			$return_reason_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $return_reason_data;
	}

	public function getTotalReturnReasons() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_reason WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row['total'];
		$collection="mongo_return_reason";
		$where=array('language_id'=>(int)$this->config->get('config_language_id'));
		$filter_group_data=$this->mongodb->gettotal($collection,$where);
		return $filter_group_data;
	}
}