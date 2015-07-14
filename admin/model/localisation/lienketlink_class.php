<?php
class ModelLocalisationLienketlinkClass extends Model {
	public function addLienketlinkClass($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "lienketlink_class SET value = '" . (float)$data['value'] . "'");
		//$lienketlink_class_id = $this->db->getLastId();
		$collection="mongo_lienketlink_class";
		$lienketlink_class_id=1+(int)$this->mongodb->getlastid($collection,'lienketlink_class_id');
		$lienketlink_class_description= array();

		foreach ($data['lienketlink_class_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "lienketlink_class_description SET lienketlink_class_id = '" . (int)$lienketlink_class_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', unit = '" . $this->db->escape($value['unit']) . "'");		
			$lienketlink_class_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'title'=>$value['title']
			);
		}
		$where=array('lienketlink_class_id'=>(int)$lienketlink_class_id, 'lienketlink_class_description'=>$lienketlink_class_description, 'link'=>$data['link'], 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status']);
		$this->mongodb->create($collection,$where); 

		$this->cache->delete('lienketlink_class');
	}

	public function editLienketlinkClass($lienketlink_class_id, $data) {
		//$this->db->query("UPDATE " . DB_PREFIX . "lienketlink_class SET value = '" . (float)$data['value'] . "' WHERE lienketlink_class_id = '" . (int)$lienketlink_class_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "lienketlink_class_description WHERE lienketlink_class_id = '" . (int)$lienketlink_class_id . "'");
		$collection="mongo_lienketlink_class";
		$lienketlink_class_description= array();

		foreach ($data['lienketlink_class_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "lienketlink_class_description SET lienketlink_class_id = '" . (int)$lienketlink_class_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', unit = '" . $this->db->escape($value['unit']) . "'");		
			$lienketlink_class_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'title'=>$value['title']
			);
		}
		$infoupdate=array('lienketlink_class_id'=>(int)$lienketlink_class_id, 'lienketlink_class_description'=>$lienketlink_class_description, 'link'=>$data['link'], 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status']);
		$where=array('lienketlink_class_id'=>(int)$lienketlink_class_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('lienketlink_class');
	}

	public function deleteLienketlinkClass($lienketlink_class_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "lienketlink_class WHERE lienketlink_class_id = '" . (int)$lienketlink_class_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "lienketlink_class_description WHERE lienketlink_class_id = '" . (int)$lienketlink_class_id . "'");
		$collection="mongo_lienketlink_class";
		$where=array('lienketlink_class_id'=>(int)$lienketlink_class_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('lienketlink_class');
	}

	public function getLienketlinkClasses($data = array()) {
		$collection="mongo_lienketlink_class";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "lienketlink_class lc LEFT JOIN " . DB_PREFIX . "lienketlink_class_description lcd ON (lc.lienketlink_class_id = lcd.lienketlink_class_id) WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
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
				'link',
				'sort_order'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'lienketlink_class_description.'. (int)$this->config->get('config_language_id').'.title';
			}
			if ($orderby == 'title') $orderby = 'lienketlink_class_description.'. (int)$this->config->get('config_language_id').'.title';	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$lienketlink_class_data = $this->cache->get('lienketlink_class');
			if (!$lienketlink_class_data) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "lienketlink_class lc LEFT JOIN " . DB_PREFIX . "lienketlink_class_description lcd ON (lc.lienketlink_class_id = lcd.lienketlink_class_id) WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				//$lienketlink_class_data = $query->rows;
				$where=array();
				$order=array('lienketlink_class_description.'. (int)$this->config->get('config_language_id').'.title'=> 1);
				$lienketlink_class_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('lienketlink_class', $lienketlink_class_data);
			}
			return $lienketlink_class_data;
		}
	}

	public function getLienketlinkClass($lienketlink_class_id) {
		$collection="mongo_lienketlink_class";
		$where=array('lienketlink_class_id'=>(int)$lienketlink_class_id);
		return $this->mongodb->getBy($collection,$where);
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "lienketlink_class lc LEFT JOIN " . DB_PREFIX . "lienketlink_class_description lcd ON (lc.lienketlink_class_id = lcd.lienketlink_class_id) WHERE lc.lienketlink_class_id = '" . (int)$lienketlink_class_id . "' AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
	}

	public function getTotalLienketlinkClasses() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "lienketlink_class");
		//return $query->row['total'];
		$collection="mongo_lienketlink_class";
		$where=array();
		$lienketlink_class_data=$this->mongodb->gettotal($collection,$where);
		return $lienketlink_class_data;
	}
}