<?php
class ModelLocalisationStockStatus extends Model {
	public function addStockStatus($data) {
		$collection="mongo_stock_status";
		foreach ($data['stock_status'] as $language_id => $value) {
			if (isset($stock_status_id)) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET stock_status_id = '" . (int)$stock_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
				$where=array('stock_status_id'=>(int)$stock_status_id, 'name'=>$value['name'], 'language_id'=>(int)$language_id);
			} else {
				$stock_status_id=1+(int)$this->mongodb->getlastid($collection,'stock_status_id');		
				$where=array('stock_status_id'=>(int)$stock_status_id, 'name'=>$value['name'], 'language_id'=>(int)$language_id);
				//$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				//$stock_status_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('stock_status');
	}

	public function editStockStatus($stock_status_id, $data) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "'");
		$collection="mongo_stock_status";
		$where=array('stock_status_id'=>(int)$stock_status_id);
		$this->mongodb->delete($collection,$where); 

		foreach ($data['stock_status'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "stock_status SET stock_status_id = '" . (int)$stock_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			$where=array('stock_status_id'=>(int)$stock_status_id, 'name'=>$value['name'], 'language_id'=>(int)$language_id);
			$this->mongodb->create($collection,$where); 
		}

		$this->cache->delete('stock_status');
	}

	public function deleteStockStatus($stock_status_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "'");
		$collection="mongo_stock_status";
		$where=array('stock_status_id'=>(int)$stock_status_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('stock_status');
	}

	public function getStockStatus($stock_status_id) {/*
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		return $query->row;*/
		$collection="mongo_stock_status";
		$where=array('language_id'=>(int)$this->config->get('config_language_id'), 'stock_status_id'=>(int)$stock_status_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getStockStatuses($data = array()) {
		$collection="mongo_stock_status";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

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
			$stock_status_data = $this->cache->get('stock_status.' . (int)$this->config->get('config_language_id'));

			if (!$stock_status_data) {/*
				//$query = $this->db->query("SELECT stock_status_id, name FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
				$stock_status_data = $query->rows;*/
				$where=array('language_id' => (int)$this->config->get('config_language_id'));
				$order=array('name'=> 1);
				$stock_status_data=(array)$this->mongodb->getall($collection,$where, $order);

				$this->cache->set('stock_status.' . (int)$this->config->get('config_language_id'), $stock_status_data);
			}

			return $stock_status_data;
		}
	}

	public function getStockStatusDescriptions($stock_status_id) {
		$stock_status_data = array();
		
		$collection="mongo_stock_status";
		$where=array('stock_status_id' => (int)$stock_status_id);
		$order=array();
		$stock_status_list=$this->mongodb->getall($collection,$where, $order);

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		//foreach ($query->rows as $result) {
		foreach ($stock_status_list as $result) {
			$stock_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $stock_status_data;
	}

	public function getTotalStockStatuses() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row['total'];
		$collection="mongo_stock_status";$stock_status_data= array();
		$where=array('language_id'=>(int)$this->config->get('config_language_id'));
		$stock_status_data=$this->mongodb->gettotal($collection,$where);
		return $stock_status_data;
	}
}