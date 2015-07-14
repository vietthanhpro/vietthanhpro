<?php
class ModelCatalogFilter extends Model {
	public function addFilter($data) {
		$this->event->trigger('pre.admin.filter.add', $data);

		//$this->db->query("INSERT INTO `" . DB_PREFIX . "filter_group` SET sort_order = '" . (int)$data['sort_order'] . "'");
		//$filter_group_id = $this->db->getLastId();
		$collection="mongo_filter_group";
		$filter_group_id=1+(int)$this->mongodb->getlastid($collection,'filter_group_id');
		$filter_group_description= array();

		foreach ($data['filter_group_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "filter_group_description SET filter_group_id = '" . (int)$filter_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");	
			$filter_group_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$newdocument=array('filter_group_id'=>(int)$filter_group_id, 'filter_group_description'=>$filter_group_description, 'sort_order'=>(int)$data['sort_order']);
		$this->mongodb->create($collection,$newdocument); 

		if (isset($data['filter'])) {			
			$collection="mongo_filter";
			foreach ($data['filter'] as $filter) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "filter SET filter_group_id = '" . (int)$filter_group_id . "', sort_order = '" . (int)$filter['sort_order'] . "'");
				//$filter_id = $this->db->getLastId();
				$filter_id=1+(int)$this->mongodb->getlastid($collection,'filter_id');
				$filter_description_= array();
				foreach ($filter['filter_description'] as $language_id => $filter_description) {
						//$this->db->query("INSERT INTO " . DB_PREFIX . "filter_description SET filter_id = '" . (int)$filter_id . "', language_id = '" . (int)$language_id . "', filter_group_id = '" . (int)$filter_group_id . "', name = '" . $this->db->escape($filter_description['name']) . "'");	
						$filter_description_[(int)$language_id]= array(
							'language_id'=>(int)$language_id,
							'name'=>$filter_description['name']
						);
				} 
				$where=array('filter_id'=>(int)$filter_id, 'filter_group_id'=>(int)$filter_group_id, 'filter_description'=>$filter_description_, 'sort_order'=>(int)$filter['sort_order']);
				$this->mongodb->create($collection,$where); 
			}
		}
		$this->event->trigger('post.admin.filter.add', $filter_group_id);
		return $filter_group_id;
	}

	public function editFilter($filter_group_id, $data) {
		$this->event->trigger('pre.admin.filter.edit', $data);
		//$this->db->query("UPDATE `" . DB_PREFIX . "filter_group` SET sort_order = '" . (int)$data['sort_order'] . "' WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "filter_group_description WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		$collection="mongo_filter_group";
		$filter_group_description= array();

		foreach ($data['filter_group_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "filter_group_description SET filter_group_id = '" . (int)$filter_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			$filter_group_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$infoupdate=array('filter_group_id'=>(int)$filter_group_id, 'filter_group_description'=>$filter_group_description, 'sort_order'=>(int)$data['sort_order']);
		$where=array('filter_group_id'=>(int)$filter_group_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "filter WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "filter_description WHERE filter_group_id = '" . (int)$filter_group_id . "'");		
		$collection="mongo_filter";
		$where=array('filter_group_id'=>(int)$filter_group_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['filter'])) {
			foreach ($data['filter'] as $filter) {
				$filter_description_= array();
				if ($filter['filter_id']) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "filter SET filter_id = '" . (int)$filter['filter_id'] . "', filter_group_id = '" . (int)$filter_group_id . "', sort_order = '" . (int)$filter['sort_order'] . "'");
					$filter_id=$filter['filter_id'];
				} else {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "filter SET filter_group_id = '" . (int)$filter_group_id . "', sort_order = '" . (int)$filter['sort_order'] . "'");
					$filter_id=1+(int)$this->mongodb->getlastid($collection,'filter_id');
				}
				//$filter_id = $this->db->getLastId();

				foreach ($filter['filter_description'] as $language_id => $filter_description) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "filter_description SET filter_id = '" . (int)$filter_id . "', language_id = '" . (int)$language_id . "', filter_group_id = '" . (int)$filter_group_id . "', name = '" . $this->db->escape($filter_description['name']) . "'");
						$filter_description_[(int)$language_id]= array(
							'language_id'=>(int)$language_id,
							'name'=>$filter_description['name']
						);
				}
				$where=array('filter_id'=>(int)$filter_id, 'filter_group_id'=>(int)$filter_group_id, 'filter_description'=>$filter_description_, 'sort_order'=>(int)$filter['sort_order']);
				$this->mongodb->create($collection,$where); 
			}
		}

		$this->event->trigger('post.admin.filter.edit', $filter_group_id);
	}

	public function deleteFilter($filter_group_id) {
		$this->event->trigger('pre.admin.filter.delete', $filter_group_id);

		//$this->db->query("DELETE FROM `" . DB_PREFIX . "filter_group` WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "filter_group_description` WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		$collection="mongo_filter_group";
		$where=array('filter_group_id'=>(int)$filter_group_id);
		$this->mongodb->delete($collection,$where); 
		
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "filter` WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "filter_description` WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		$collection="mongo_filter";
		$where=array('filter_group_id'=>(int)$filter_group_id);
		$this->mongodb->delete($collection,$where); 

		$this->event->trigger('post.admin.filter.delete', $filter_group_id);
	}

	public function getFilterGroup($filter_group_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "filter_group` fg LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE fg.filter_group_id = '" . (int)$filter_group_id . "' AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$collection="mongo_filter_group";
		$where=array('filter_group_id'=>(int)$filter_group_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getFilterGroups($data = array()) {
		$collection="mongo_filter_group";
		if ($data) {
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
				$orderby = 'filter_group_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'filter_group_description.'. (int)$this->config->get('config_language_id').'.name';	
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$filter_group_data = $this->cache->get('filter_group');
			if (!$filter_group_data) {
				$where=array();
				$order=array('filter_group_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
				$filter_group_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('filter_group', $filter_group_data);
			}
			return $filter_group_data;
		}
		/*
		$sql = "SELECT * FROM `" . DB_PREFIX . "filter_group` fg LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'fgd.name',
			'fg.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY fgd.name";
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

		return $query->rows;
		*/
	}
/*
	public function getFilterGroupDescriptions($filter_group_id) {
		$filter_group_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter_group_description WHERE filter_group_id = '" . (int)$filter_group_id . "'");

		foreach ($query->rows as $result) {
			$filter_group_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $filter_group_data;
	}*/

	public function getFilter($filter_id) {
		//$query = $this->db->query("SELECT *, (SELECT name FROM " . DB_PREFIX . "filter_group_description fgd WHERE f.filter_group_id = fgd.filter_group_id AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS `group` FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id = '" . (int)$filter_id . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$filter_data = array();$filter_group_data= array();
		$collection="mongo_filter";
		$where=array('filter_id'=>(int)$filter_id);
		$filter_data=$this->mongodb->getBy($collection,$where);
		if ($filter_data) {
			$filter_group_data=$this->getFilterGroup($filter_data['filter_group_id']);
			$filter_data['group']=$filter_group_data['filter_group_description'][$this->config->get('config_language_id')]['name'];
		} else {
			$filter_data['group']='';
		}
		return $filter_data;
	}

	public function getFilters($data) {
		$collection="mongo_filter";
		$data_filters = array();
		if (!empty($data['filter_name'])) {
			$where=array('filter_description.'. (int)$this->config->get('config_language_id').'.name'=>new MongoRegex('/^'.$data['filter_name'].'/'));
		} else {
			$where=array();
		}
		$order=array('sort_order'=> 1);
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
		$data_results = $this->mongodb->get($collection,$where, $order, $start, $limit);
		if ($data_results) {
			$results=$data_results['results'];
			foreach ($results as $result) {
				$filter_group_data=$this->getFilterGroup($result['filter_group_id']);
				$filter_data_group=$filter_group_data['filter_group_description'][$this->config->get('config_language_id')]['name'];
				$data_filters[] = array(
					'filter_id' => $result['filter_id'],
					'filter_group_id' => $result['filter_group_id'],
					'filter_description' => $result['filter_description'],
					'sort_order'      => $result['sort_order'],
					'group'            => $filter_data_group
				);
			} //print_r($data_filters); die();
		} 
			return $data_filters;
		/*
		$sql = "SELECT *, (SELECT name FROM " . DB_PREFIX . "filter_group_description fgd WHERE f.filter_group_id = fgd.filter_group_id AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS `group` FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		if (!empty($data['filter_name'])) {
			$sql .= " AND fd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		$sql .= " ORDER BY f.sort_order ASC";
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

	public function getFilterDescriptions($filter_group_id) {
		$filter_data = array();
		//$filter_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter WHERE filter_group_id = '" . (int)$filter_group_id . "'");
		$collection="mongo_filter";
		$where=array('filter_group_id'=>(int)$filter_group_id);
		$order=array();
		$filter_query_data = $this->mongodb->getall($collection,$where, $order);
		
		//foreach ($filter_query->rows as $filter) {
		foreach ($filter_query_data as $filter) {	
			/*
			$filter_description_data = array();
			//$filter_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter_description WHERE filter_id = '" . (int)$filter['filter_id'] . "'");
			foreach ($filter_description_query->rows as $filter_description) {
				$filter_description_data[$filter_description['language_id']] = array('name' => $filter_description['name']);
			}*/
			$filter_data[] = array(
				'filter_id'          => $filter['filter_id'],
				//'filter_description' => $filter_description_data,
				'filter_description' => $filter['filter_description'],
				'sort_order'         => $filter['sort_order']
			);		}
		return $filter_data;
	}

	public function getTotalFilterGroups() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "filter_group`");
		//return $query->row['total'];
		$collection="mongo_filter_group";
		$where=array();
		$filter_group_data=$this->mongodb->gettotal($collection,$where);
		return $filter_group_data;
	}
}
