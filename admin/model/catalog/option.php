<?php
class ModelCatalogOption extends Model {
	public function addOption($data) {
		$this->event->trigger('pre.admin.option.add', $data);

		//$this->db->query("INSERT INTO `" . DB_PREFIX . "option` SET type = '" . $this->db->escape($data['type']) . "', sort_order = '" . (int)$data['sort_order'] . "'");
		//$option_id = $this->db->getLastId();
		$collection="mongo_option";
		$option_id=1+(int)$this->mongodb->getlastid($collection,'option_id');
		$option_description= array();

		foreach ($data['option_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");	
			$option_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$newdocument=array('option_id'=>(int)$option_id, 'type'=>$data['type'], 'option_description'=>$option_description, 'sort_order'=>(int)$data['sort_order']);
		$this->mongodb->create($collection,$newdocument); 

		if (isset($data['option_value'])) {		
			$collection="mongo_option_value";
			foreach ($data['option_value'] as $option_value) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$option_id . "', image = '" . $this->db->escape(html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$option_value['sort_order'] . "'");
				//$option_value_id = $this->db->getLastId();
				$option_value_id=1+(int)$this->mongodb->getlastid($collection,'option_value_id');
				$option_value_description_= array();

				foreach ($option_value['option_value_description'] as $language_id => $option_value_description) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$option_value_id . "', language_id = '" . (int)$language_id . "', option_id = '" . (int)$option_id . "', name = '" . $this->db->escape($option_value_description['name']) . "'");
					$option_value_description_[(int)$language_id]= array(
						'language_id'=>(int)$language_id,
						'name'=>$option_value_description['name']
					);
				}
				$newdocument=array('option_value_id'=>(int)$option_value_id, 'option_id'=>(int)$option_id, 'option_value_description'=>$option_value_description_, 'image'=>html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8'), 'sort_order'=>(int)$option_value['sort_order']);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		$this->event->trigger('post.admin.option.add', $option_id);

		return $option_id;
	}

	public function editOption($option_id, $data) {
		$this->event->trigger('pre.admin.option.edit', $data);

		//$this->db->query("UPDATE `" . DB_PREFIX . "option` SET type = '" . $this->db->escape($data['type']) . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE option_id = '" . (int)$option_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int)$option_id . "'");
		$collection="mongo_option";
		$option_description= array();

		foreach ($data['option_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");	
			$option_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$infoupdate=array('option_id'=>(int)$option_id, 'type'=>$data['type'], 'option_description'=>$option_description, 'sort_order'=>(int)$data['sort_order']);
		$where=array('option_id'=>(int)$option_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int)$option_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int)$option_id . "'");
		$collection="mongo_option_value";
		$where=array('option_id'=>(int)$option_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['option_value'])) {
			foreach ($data['option_value'] as $option_value) {
				$filter_description_= array();
				if ($option_value['option_value_id']) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_value_id = '" . (int)$option_value['option_value_id'] . "', option_id = '" . (int)$option_id . "', image = '" . $this->db->escape(html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$option_value['sort_order'] . "'");
					$option_value_id=$filter['option_value_id'];
				} else {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$option_id . "', image = '" . $this->db->escape(html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$option_value['sort_order'] . "'");
					$option_value_id=1+(int)$this->mongodb->getlastid($collection,'option_value_id');
				}

				//$option_value_id = $this->db->getLastId();

				foreach ($option_value['option_value_description'] as $language_id => $option_value_description) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$option_value_id . "', language_id = '" . (int)$language_id . "', option_id = '" . (int)$option_id . "', name = '" . $this->db->escape($option_value_description['name']) . "'");
						$option_value_description_[(int)$language_id]= array(
							'language_id'=>(int)$language_id,
							'name'=>$option_value_description['name']
						);
				}
				$newdocument=array('option_value_id'=>(int)$option_value_id, 'option_id'=>(int)$option_id, 'option_value_description'=>$option_value_description_, 'image'=>html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8'), 'sort_order'=>(int)$option_value['sort_order']);
				$this->mongodb->create($collection,$newdocument); 
			}

		}

		$this->event->trigger('post.admin.option.edit', $option_id);
	}

	public function deleteOption($option_id) {
		$this->event->trigger('pre.admin.option.delete', $option_id);

		//$this->db->query("DELETE FROM `" . DB_PREFIX . "option` WHERE option_id = '" . (int)$option_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int)$option_id . "'");
		$collection="mongo_option";
		$where=array('option_id'=>(int)$option_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int)$option_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int)$option_id . "'");
		$collection="mongo_option_value";
		$where=array('option_id'=>(int)$option_id);
		$this->mongodb->delete($collection,$where); 

		$this->event->trigger('post.admin.option.delete', $option_id);
	}

	public function getOption($option_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE o.option_id = '" . (int)$option_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$collection="mongo_option";
		$where=array('option_id'=>(int)$option_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getOptions($data = array()) {
		$collection="mongo_option";
		if ($data) {
			$where=array();
			if (!empty($data['filter_name'])) {
				$where['option_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
			} 
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
				'type',
				'sort_order'
			);
	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = "name";
			}
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'option_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'option_description.'. (int)$this->config->get('config_language_id').'.name';	
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$option_data = $this->cache->get('option');
			if (!$option_data) {
				$where=array();
				$order=array('option_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
				$option_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('option', $option_data);
			}
			return $option_data;
		}
		/*
		$sql = "SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND od.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'od.name',
			'o.type',
			'o.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY od.name";
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
	/*
	public function getOptionDescriptions($option_id) {
		$option_data = array();
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int)$option_id . "'");
		foreach ($query->rows as $result) {
			$option_data[$result['language_id']] = array('name' => $result['name']);
		}
		return $option_data;
	}*/

	public function getOptionValue($option_value_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_value_id = '" . (int)$option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$collection="mongo_option_value";
		$filter_data = array();
		$where=array('option_value_id'=>(int)$option_value_id);
		$filter_data=$this->mongodb->getBy($collection,$where);
		return $filter_data;
	}

	public function getOptionValues($option_id) {
		$option_value_data = array();
		//$option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id = '" . (int)$option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order, ovd.name");
		$collection="mongo_option_value";
		$where=array('option_id'=>(int)$option_id);
		$order=array('sort_order'=>1,'option_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
		$option_value_data_query = $this->mongodb->getall($collection,$where, $order);

		//foreach ($option_value_query->rows as $option_value) {
		foreach ($option_value_data_query as $option_value) {
			$option_value_data[] = array(
				'option_value_id' => $option_value['option_value_id'],
				'option_value_description'           => $option_value['option_value_description'],
				'image'           => $option_value['image'],
				'sort_order'      => $option_value['sort_order']
			);
		}

		return $option_value_data;
	}
	/*
	public function getOptionValueDescriptions($option_id) {
		$option_value_data = array();

		$option_value_query = //$this->db->query("SELECT * FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int)$option_id . "' ORDER BY sort_order");

		foreach ($option_value_query->rows as $option_value) {
			$option_value_description_data = array();

			$option_value_description_query = //$this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '" . (int)$option_value['option_value_id'] . "'");

			foreach ($option_value_description_query->rows as $option_value_description) {
				$option_value_description_data[$option_value_description['language_id']] = array('name' => $option_value_description['name']);
			}

			$option_value_data[] = array(
				'option_value_id'          => $option_value['option_value_id'],
				'option_value_description' => $option_value_description_data,
				'image'                    => $option_value['image'],
				'sort_order'               => $option_value['sort_order']
			);
		}

		return $option_value_data;
	}
*/
	public function getTotalOptions() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "option`");
		//return $query->row['total'];
		$collection="mongo_option";
		$where=array();
		$option_data=$this->mongodb->gettotal($collection,$where);
		return $option_data;
	}
}