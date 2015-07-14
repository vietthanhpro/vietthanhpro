<?php
class ModelSaleCustomerGroup extends Model {
	public function addCustomerGroup($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "customer_group SET approval = '" . (int)$data['approval'] . "', sort_order = '" . (int)$data['sort_order'] . "'");
		//$customer_group_id = $this->db->getLastId();
		$collection="mongo_customer_group";
		$customer_group_id=1+(int)$this->mongodb->getlastid($collection,'customer_group_id');
		$customer_group_description= array();
		foreach ($data['customer_group_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "customer_group_description SET customer_group_id = '" . (int)$customer_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");				
			$customer_group_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
				'description'=>$value['description']
			);
		}
		$where=array('customer_group_id'=>(int)$customer_group_id, 'customer_group_description'=>$customer_group_description, 'approval'=>(int)$data['approval'], 'sort_order'=>(int)$data['sort_order']);
		$this->mongodb->create($collection,$where); 
	}

	public function editCustomerGroup($customer_group_id, $data) {
		//$this->db->query("UPDATE " . DB_PREFIX . "customer_group SET approval = '" . (int)$data['approval'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer_group_description WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$collection="mongo_customer_group";
		$customer_group_description= array();
		foreach ($data['customer_group_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "customer_group_description SET customer_group_id = '" . (int)$customer_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");			
			$customer_group_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
				'description'=>$value['description']
			);
		}
		$infoupdate=array('customer_group_id'=>(int)$customer_group_id, 'customer_group_description'=>$customer_group_description, 'approval'=>(int)$data['approval'], 'sort_order'=>(int)$data['sort_order']);
		$where=array('customer_group_id'=>(int)$customer_group_id);
		$this->mongodb->update($collection,$infoupdate,$where);
	}

	public function deleteCustomerGroup($customer_group_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer_group_description WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		$collection="mongo_customer_group";
		$where=array('customer_group_id'=>(int)$customer_group_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getCustomerGroup($customer_group_id) {
		$collection="mongo_customer_group";
		$where=array('customer_group_id'=>(int)$customer_group_id);
		return $this->mongodb->getBy($collection,$where);
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cg.customer_group_id = '" . (int)$customer_group_id . "' AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
	}

	public function getCustomerGroups($data = array()) {
		$collection="mongo_customer_group";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			$sort_data = array(
				'cgd.name',
				'cg.sort_order'
			);
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY cgd.name";
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
				'sort_order',
			);
	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'customer_group_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'customer_group_description.'. (int)$this->config->get('config_language_id').'.name';	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
				$where=array();
				$order=array('customer_group_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
				return $this->mongodb->getall($collection,$where, $order);
		}
	}
/*
	public function getCustomerGroupDescriptions($customer_group_id) {
		$customer_group_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group_description WHERE customer_group_id = '" . (int)$customer_group_id . "'");

		foreach ($query->rows as $result) {
			$customer_group_data[$result['language_id']] = array(
				'name'        => $result['name'],
				'description' => $result['description']
			);
		}

		return $customer_group_data;
	}*/

	public function getTotalCustomerGroups() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_group");
		//return $query->row['total'];
		$collection="mongo_customer_group";
		$where=array();$customer_group_data= array();
		$customer_group_data=$this->mongodb->gettotal($collection,$where);
		return $customer_group_data;
	}
}