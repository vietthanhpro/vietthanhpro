<?php
class ModelUserUserGroup extends Model {
	public function addUserGroup($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "user_group SET name = '" . $this->db->escape($data['name']) . "', permission = '" . (isset($data['permission']) ? $this->db->escape(serialize($data['permission'])) : '') . "'");
		$collection="mongo_user_group";
		$user_group_id=1+(int)$this->mongodb->getlastid($collection,'user_group_id');
		$where=array('user_group_id'=>(int)$user_group_id, 'name'=>$data['name'], 'permission'=>(isset($data['permission']) ? serialize($data['permission']) : ''));
		$this->mongodb->create($collection,$where); 
	}

	public function editUserGroup($user_group_id, $data) {
		//$this->db->query("UPDATE " . DB_PREFIX . "user_group SET name = '" . $this->db->escape($data['name']) . "', permission = '" . (isset($data['permission']) ? $this->db->escape(serialize($data['permission'])) : '') . "' WHERE user_group_id = '" . (int)$user_group_id . "'");
		$collection="mongo_user_group";
		$infoupdate=array('user_group_id'=>(int)$user_group_id, 'name'=>$data['name'], 'permission'=>(isset($data['permission']) ? serialize($data['permission']) : ''));
		$where=array('user_group_id'=>(int)$user_group_id);
		$this->mongodb->update($collection,$infoupdate,$where); 
	}

	public function deleteUserGroup($user_group_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_group_id . "'");
		$collection="mongo_user_group";
		$where=array('user_group_id'=>(int)$user_group_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getUserGroup($user_group_id) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_group_id . "'");
		$collection="mongo_user_group";
		$where=array('user_group_id'=>(int)$user_group_id);
		$user_group_list = $this->mongodb->getBy($collection,$where);
		/*
		$user_group = array(
			'name'       => $query->row['name'],
			'permission' => unserialize($query->row['permission'])
		);*/
		$user_group = array(
			'name'       => $user_group_list['name'],
			'permission' => unserialize($user_group_list['permission'])
		);

		return $user_group;
	}

	public function getUserGroups($data = array()) {
		$collection="mongo_user_group";
		if ($data) {
			/*
			$sql = "SELECT * FROM " . DB_PREFIX . "user_group";
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
			$orderby = "name";	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$where=array();
			$order=array('name'=> 1);
			return $this->mongodb->getall($collection,$where, $order);
		}
	}

	public function getTotalUserGroups() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user_group");
		//return $query->row['total'];
		$collection="mongo_user_group";
		$where=array();
		$user_group_data=$this->mongodb->gettotal($collection,$where);
		return $user_group_data;
	}

	public function addPermission($user_group_id, $type, $route) {
		//$user_group_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_group_id . "'");
		$collection="mongo_user_group";
		$where=array('user_group_id'=>(int)$user_group_id);
		$user_group_info = $this->mongodb->getBy($collection,$where);
		//if ($user_group_query->num_rows) {
		if ($user_group_info) {
			//$data = unserialize($user_group_query->row['permission']);
			$data = unserialize($user_group_info['permission']);
			$data[$type][] = $route;
			//$this->db->query("UPDATE " . DB_PREFIX . "user_group SET permission = '" . $this->db->escape(serialize($data)) . "' WHERE user_group_id = '" . (int)$user_group_id . "'");
			$infoupdate=array('permission'=>serialize($data));
			$where=array('user_group_id'=>(int)$user_group_id);
			$this->mongodb->update($collection,$infoupdate,$where); 
		}
	}

	public function removePermission($user_group_id, $type, $route) {
		//$user_group_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_group_id . "'");
		$collection="mongo_user_group";
		$where=array('user_group_id'=>(int)$user_group_id);
		$user_group_info = $this->mongodb->getBy($collection,$where);
		//if ($user_group_query->num_rows) {
		if ($user_group_info) {
			//$data = unserialize($user_group_query->row['permission']);
			$data = unserialize($user_group_info['permission']);
			$data[$type] = array_diff($data[$type], array($route));
			//$this->db->query("UPDATE " . DB_PREFIX . "user_group SET permission = '" . $this->db->escape(serialize($data)) . "' WHERE user_group_id = '" . (int)$user_group_id . "'");
			$infoupdate=array('permission'=>serialize($data));
			$where=array('user_group_id'=>(int)$user_group_id);
			$this->mongodb->update($collection,$infoupdate,$where); 
		}
	}
}