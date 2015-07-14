<?php
class ModelUserApi extends Model {
	public function addApi($data) {
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "api` SET username = '" . $this->db->escape($data['username']) . "', `password` = '" . $this->db->escape($data['password']) . "', status = '" . (int)$data['status'] . "', date_added = NOW(), date_modified = NOW()");
		$collection="mongo_api";
		$api_id=1+(int)$this->mongodb->getlastid($collection,'api_id');
		$where=array('api_id'=>(int)$api_id, 'username'=>$data['username'], 'password'=>$data['password'], 'status'=>(int)$data['status'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 
	}

	public function editApi($api_id, $data) {
		//$this->db->query("UPDATE `" . DB_PREFIX . "api` SET username = '" . $this->db->escape($data['username']) . "', `password` = '" . $this->db->escape($data['password']) . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE api_id = '" . (int)$api_id . "'");
		$collection="mongo_api";
		$infoupdate=array('api_id'=>(int)$api_id, 'username'=>$data['username'], 'password'=>$data['password'], 'status'=>(int)$data['status'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('api_id'=>(int)$api_id);
		$this->mongodb->update($collection,$infoupdate,$where); 
	}

	public function deleteApi($api_id) {
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "api` WHERE api_id = '" . (int)$api_id . "'");
		$collection="mongo_api";
		$where=array('api_id'=>(int)$api_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getApi($api_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api` WHERE api_id = '" . (int)$api_id . "'");
		//return $query->row;
		$collection="mongo_api";
		$where=array('api_id'=>(int)$api_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getApis($data = array()) {
		$collection="mongo_api";
		if ($data) {/*
			$sql = "SELECT * FROM `" . DB_PREFIX . "api`";
			$sort_data = array(
				'username',
				'status',
				'date_added',
				'date_modified'
			);
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY username";
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
				'username',
				'status',
				'date_added',
				'date_modified'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = "username";
			}	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$where=array();
			$order=array('username'=> 1);
			return $this->mongodb->getall($collection,$where, $order);
		}
	}

	public function getTotalApis() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "api`");
		//return $query->row['total'];
		$collection="mongo_api";
		$where=array();
		return $this->mongodb->gettotal($collection,$where);
	}
}