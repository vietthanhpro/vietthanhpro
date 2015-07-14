<?php
class ModelSaleCustomField extends Model {
	public function addCustomField($data) {
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field` SET type = '" . $this->db->escape($data['type']) . "', value = '" . $this->db->escape($data['value']) . "', location = '" . $this->db->escape($data['location']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "'");
		//$custom_field_id = $this->db->getLastId();
		$collection="mongo_custom_field";
		$custom_field_id=1+(int)$this->mongodb->getlastid($collection,'custom_field_id');
		$custom_field_description= array();

		foreach ($data['custom_field_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_description SET custom_field_id = '" . (int)$custom_field_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");			
			$custom_field_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$where=array('custom_field_id'=>(int)$custom_field_id, 'custom_field_description'=>$custom_field_description, 'type'=>$data['type'], 'value'=>$data['value'], 'location'=>$data['location'], 'status'=>(int)$data['status'], 'sort_order'=>(int)$data['sort_order']);
		$this->mongodb->create($collection,$where); 

		if (isset($data['custom_field_customer_group'])) {
			$collection="mongo_custom_field_customer_group";
			foreach ($data['custom_field_customer_group'] as $custom_field_customer_group) {
				if (isset($custom_field_customer_group['customer_group_id'])) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_customer_group SET custom_field_id = '" . (int)$custom_field_id . "', customer_group_id = '" . (int)$custom_field_customer_group['customer_group_id'] . "', required = '" . (int)(isset($custom_field_customer_group['required']) ? 1 : 0) . "'");
					$where=array('custom_field_id'=>(int)$custom_field_id, 'customer_group_id'=>(int)$custom_field_customer_group['customer_group_id'], 'required'=>(int)(isset($custom_field_customer_group['required']) ? 1 : 0));
					$this->mongodb->create($collection,$where); 
				}
			}
		}

		if (isset($data['custom_field_value'])) {
			$collection="mongo_custom_field_value";
			foreach ($data['custom_field_value'] as $custom_field_value) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_value SET custom_field_id = '" . (int)$custom_field_id . "', sort_order = '" . (int)$custom_field_value['sort_order'] . "'");
				//$custom_field_value_id = $this->db->getLastId();
				$custom_field_value_id=1+(int)$this->mongodb->getlastid($collection,'custom_field_value_id');
				$custom_field_value_description_array= array();

				foreach ($custom_field_value['custom_field_value_description'] as $language_id => $custom_field_value_description) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_value_description SET custom_field_value_id = '" . (int)$custom_field_value_id . "', language_id = '" . (int)$language_id . "', custom_field_id = '" . (int)$custom_field_id . "', name = '" . $this->db->escape($custom_field_value_description['name']) . "'");		
					$custom_field_value_description_array[$language_id]= array(
						'language_id'=>(int)$language_id,
						'name'=>$custom_field_value_description['name']
					);
				}
				$where=array('custom_field_value_id'=>(int)$custom_field_value_id, 'custom_field_id'=>(int)$custom_field_id, 'custom_field_value_description'=>$custom_field_value_description_array, 'sort_order'=>(int)$custom_field_value['sort_order']);
				$this->mongodb->create($collection,$where); 
			}
		}
	}

	public function editCustomField($custom_field_id, $data) {
		//$this->db->query("UPDATE `" . DB_PREFIX . "custom_field` SET type = '" . $this->db->escape($data['type']) . "', value = '" . $this->db->escape($data['value']) . "', location = '" . $this->db->escape($data['location']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "custom_field_description WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$collection="mongo_custom_field";
		$custom_field_description= array();

		foreach ($data['custom_field_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_description SET custom_field_id = '" . (int)$custom_field_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			$custom_field_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$infoupdate=array('custom_field_description'=>$custom_field_description, 'type'=>$data['type'], 'value'=>$data['value'], 'location'=>$data['location'], 'status'=>(int)$data['status'], 'sort_order'=>(int)$data['sort_order']);
		$where=array('custom_field_id'=>(int)$custom_field_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "custom_field_customer_group WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$collection="mongo_custom_field_customer_group";
		$where=array('custom_field_id'=>(int)$custom_field_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['custom_field_customer_group'])) {
			$collection="mongo_custom_field_customer_group";
			foreach ($data['custom_field_customer_group'] as $custom_field_customer_group) {
				if (isset($custom_field_customer_group['customer_group_id'])) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_customer_group SET custom_field_id = '" . (int)$custom_field_id . "', customer_group_id = '" . (int)$custom_field_customer_group['customer_group_id'] . "', required = '" . (int)(isset($custom_field_customer_group['required']) ? 1 : 0) . "'");
					$where=array('custom_field_id'=>(int)$custom_field_id, 'customer_group_id'=>(int)$custom_field_customer_group['customer_group_id'], 'required'=>(int)(isset($custom_field_customer_group['required']) ? 1 : 0));
					$this->mongodb->create($collection,$where); 
				}
			}
		}

		//$this->db->query("DELETE FROM " . DB_PREFIX . "custom_field_value WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "custom_field_value_description WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$collection="mongo_custom_field_value";
		$where=array('custom_field_id'=>(int)$custom_field_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['custom_field_value'])) {
			$collection="mongo_custom_field_value";
			foreach ($data['custom_field_value'] as $custom_field_value) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_value SET custom_field_id = '" . (int)$custom_field_id . "', sort_order = '" . (int)$custom_field_value['sort_order'] . "'");
				//$custom_field_value_id = $this->db->getLastId();
				$custom_field_value_id=1+(int)$this->mongodb->getlastid($collection,'custom_field_value_id');
				$custom_field_value_description_array= array();

				foreach ($custom_field_value['custom_field_value_description'] as $language_id => $custom_field_value_description) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_value_description SET custom_field_value_id = '" . (int)$custom_field_value_id . "', language_id = '" . (int)$language_id . "', custom_field_id = '" . (int)$custom_field_id . "', name = '" . $this->db->escape($custom_field_value_description['name']) . "'");		
					$custom_field_value_description_array[$language_id]= array(
						'language_id'=>(int)$language_id,
						'name'=>$custom_field_value_description['name']
					);
				}
				$where=array('custom_field_value_id'=>(int)$custom_field_value_id, 'custom_field_id'=>(int)$custom_field_id, 'custom_field_value_description'=>$custom_field_value_description_array, 'sort_order'=>(int)$custom_field_value['sort_order']);
				$this->mongodb->create($collection,$where); 
			}
		}
	}

	public function deleteCustomField($custom_field_id) {
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_description` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$collection="mongo_custom_field";
		$where=array('custom_field_id'=>(int)$custom_field_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_customer_group` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$collection="mongo_custom_field_customer_group";
		$where=array('custom_field_id'=>(int)$custom_field_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_value` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_value_description` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$collection="mongo_custom_field_value";
		$where=array('custom_field_id'=>(int)$custom_field_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getCustomField($custom_field_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cf.custom_field_id = '" . (int)$custom_field_id . "' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$collection="mongo_custom_field";
		$where=array('custom_field_id'=>(int)$custom_field_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getCustomFields($data = array()) {/*
		if (empty($data['filter_customer_group_id'])) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		} else {
			$sql = "SELECT * FROM " . DB_PREFIX . "custom_field_customer_group cfcg LEFT JOIN `" . DB_PREFIX . "custom_field` cf ON (cfcg.custom_field_id = cf.custom_field_id) LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		}
		if (!empty($data['filter_name'])) {
			$sql .= " AND cfd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_customer_group_id'])) {
			$sql .= " AND cfcg.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}
		$sort_data = array(
			'cfd.name',
			'cf.type',
			'cf.location',
			'cf.status',
			'cf.sort_order'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cfd.name";
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
		$customer_group_data = array();
		if (isset($data['filter_customer_group_id'])) {
			$collection="mongo_custom_field_customer_group";
			$where=array('customer_group_id'=>(int)$data['filter_customer_group_id']);
			$order=array();
			$customer_group_list = $this->mongodb->getall($collection,$where, $order);
			foreach ($customer_group_list as $customer_group_list_info) {
				$customer_group_data[]= (int)$customer_group_list_info['custom_field_id'];
			}
		}
		$collection="mongo_custom_field";
		$where=array();
		if (!empty($data['filter_name'])) {
			$where['custom_field_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
		}
		if (!empty($data['filter_customer_group_id'])) {
			$where['custom_field_id']=array('$in'=>$customer_group_data);
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
				'location',
				'status',
				'sort_order'
			);
	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'custom_field_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'custom_field_description.'. (int)$this->config->get('config_language_id').'.name';	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
	}
/*
	public function getCustomFieldDescriptions($custom_field_id) {
		$custom_field_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_description WHERE custom_field_id = '" . (int)$custom_field_id . "'");

		foreach ($query->rows as $result) {
			$custom_field_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $custom_field_data;
	}*/
	
	public function getCustomFieldValue($custom_field_value_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value cfv LEFT JOIN " . DB_PREFIX . "custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) WHERE cfv.custom_field_value_id = '" . (int)$custom_field_value_id . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$collection="mongo_custom_field_value";
		$where=array('custom_field_value_id'=>(int)$custom_field_value_id);
		return $this->mongodb->getBy($collection,$where);
	}
	
	public function getCustomFieldValues($custom_field_id) {
		$custom_field_value_data = array();
		//$custom_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value cfv LEFT JOIN " . DB_PREFIX . "custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) WHERE cfv.custom_field_id = '" . (int)$custom_field_id . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cfv.sort_order ASC");
		$collection="mongo_custom_field_value";
		$where=array('custom_field_id'=>(int)$custom_field_id);
		$order=array();$custom_field_value_query_list=array();
		$custom_field_value_query_list= $this->mongodb->getall($collection,$where, $order);
		
		//foreach ($custom_field_value_query->rows as $custom_field_value) {
		foreach ($custom_field_value_query_list as $custom_field_value) {
			$custom_field_value_data[$custom_field_value['custom_field_value_id']] = array(
				'custom_field_value_id' => $custom_field_value['custom_field_value_id'],
				'name'                  => $custom_field_value['custom_field_value_description'][(int)$this->config->get('config_language_id')]['name']
			);
		}
		return $custom_field_value_data;
	}
	
	public function getCustomFieldCustomerGroups($custom_field_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_customer_group` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		//return $query->rows;
		$collection="mongo_custom_field_customer_group";
		$where=array('custom_field_id'=>(int)$custom_field_id);
		$order=array();
		return $this->mongodb->getall($collection,$where, $order);
	}

	public function getCustomFieldValueDescriptions($custom_field_id) {
		$custom_field_value_data = array();
		//$custom_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$collection="mongo_custom_field_value";
		$where=array('custom_field_id'=>(int)$custom_field_id);
		$order=array();$custom_field_value_query_list=array();
		$custom_field_value_query_list= $this->mongodb->getall($collection,$where, $order);
		
		//foreach ($custom_field_value_query->rows as $custom_field_value) {
		foreach ($custom_field_value_query_list as $custom_field_value) {
			$custom_field_value_description_data = array();
			$custom_field_value_description_data=$custom_field_value['custom_field_value_description'];
			/*
			//$custom_field_value_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value_description WHERE custom_field_value_id = '" . (int)$custom_field_value['custom_field_value_id'] . "'");
			foreach ($custom_field_value_description_query->rows as $custom_field_value_description) {
				$custom_field_value_description_data[$custom_field_value_description['language_id']] = array('name' => $custom_field_value_description['name']);
			}*/
			$custom_field_value_data[] = array(
				'custom_field_value_id'          => $custom_field_value['custom_field_value_id'],
				'custom_field_value_description' => $custom_field_value_description_data,
				'sort_order'                     => $custom_field_value['sort_order']
			);
		}
		return $custom_field_value_data;
	}

	public function getTotalCustomFields() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "custom_field`");
		//return $query->row['total'];
		$collection="mongo_custom_field";
		$where=array();$customer_group_data= array();
		$customer_group_data=$this->mongodb->gettotal($collection,$where);
		return $customer_group_data;
	}
}