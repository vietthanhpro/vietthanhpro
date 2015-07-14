<?php
class ModelUserUser extends Model {
	public function addUser($data) {
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($data['username']) . "', user_group_id = '" . (int)$data['user_group_id'] . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', image = '" . $this->db->escape($data['image']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
		$collection="mongo_user";
		$user_id=1+(int)$this->mongodb->getlastid($collection,'user_id');
		$where=array('user_id'=>(int)$user_id, 'username'=>$data['username'], 'user_group_id'=>(int)$data['user_group_id'], 'salt'=>$salt = substr(md5(uniqid(rand(), true)), 0, 9), 'password'=>sha1($salt . sha1($salt . sha1($data['password']))), 'firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'email'=>$data['email'], 'image'=>$data['image'], 'status'=>(int)$data['status'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 
	}

	public function editUser($user_id, $data) {
		//$this->db->query("UPDATE `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($data['username']) . "', user_group_id = '" . (int)$data['user_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', image = '" . $this->db->escape($data['image']) . "', status = '" . (int)$data['status'] . "' WHERE user_id = '" . (int)$user_id . "'");
		$collection="mongo_user";
		$infoupdate=array('user_id'=>(int)$user_id, 'username'=>$data['username'], 'user_group_id'=>(int)$data['user_group_id'], 'salt'=>$salt = substr(md5(uniqid(rand(), true)), 0, 9), 'password'=>sha1($salt . sha1($salt . sha1($data['password']))), 'firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'email'=>$data['email'], 'image'=>$data['image'], 'status'=>(int)$data['status'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('user_id'=>(int)$user_id);
		$this->mongodb->update($collection,$infoupdate,$where); 

		if ($data['password']) {
			//$this->db->query("UPDATE `" . DB_PREFIX . "user` SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE user_id = '" . (int)$user_id . "'");
			$infoupdate=array('salt'=>$salt = substr(md5(uniqid(rand(), true)), 0, 9), 'password'=>sha1($salt . sha1($salt . sha1($data['password']))));
			$where=array('user_id'=>(int)$user_id);
			$this->mongodb->update($collection,$infoupdate,$where); 
		}
	}

	public function editPassword($user_id, $password) {
		//$this->db->query("UPDATE `" . DB_PREFIX . "user` SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE user_id = '" . (int)$user_id . "'");
		$collection="mongo_user";
		$infoupdate=array('salt'=>$salt = substr(md5(uniqid(rand(), true)), 0, 9), 'password'=>sha1($salt . sha1($salt . sha1($password))), 'code'=>'');
		$where=array('user_id'=>(int)$user_id);
		$this->mongodb->update($collection,$infoupdate,$where); 
	}

	public function editCode($email, $code) {
		//$this->db->query("UPDATE `" . DB_PREFIX . "user` SET code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		$collection="mongo_user";
		$infoupdate=array('code'=>$code);
		$where=array('email'=>utf8_strtolower($email));
		$this->mongodb->update($collection,$infoupdate,$where); 
	}

	public function deleteUser($user_id) {
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$user_id . "'");
		$collection="mongo_user";
		$where=array('user_id'=>(int)$user_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getUser($user_id) {
		//$query = $this->db->query("SELECT *, (SELECT ug.name FROM `" . DB_PREFIX . "user_group` ug WHERE ug.user_group_id = u.user_group_id) AS user_group FROM `" . DB_PREFIX . "user` u WHERE u.user_id = '" . (int)$user_id . "'");
		//return $query->row;
		$data_result = array();
		$collection="mongo_user";
		$where=array('user_id'=>(int)$user_id);
		$data_result=$this->mongodb->getBy($collection,$where);
		if ($data_result) {
			$collection="mongo_user_group";
			$where=array('user_group_id'=>(int)$data_result['user_group_id']);
			$user_group_info=$this->mongodb->getBy($collection,$where);
			$data_result['user_group']=$user_group_info['name'];
		} else {
			$data_result['user_group']='';
		}
		return $data_result;
	}

	public function getUserByUsername($username) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE username = '" . $this->db->escape($username) . "'");
		//return $query->row;
		$collection="mongo_user";
		$where=array('username'=>$username);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getUserByCode($code) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");
		//return $query->row; 
		$collection="mongo_user";
		$where=array('code'=>array('$ne'=>''),'code'=>$code);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getUsers($data = array()) {
		$collection="mongo_user";
		if ($data) {/*
			$sql = "SELECT * FROM `" . DB_PREFIX . "user`";
			$sort_data = array(
				'username',
				'status',
				'date_added'
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
				'date_added'
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

	public function getTotalUsers() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user`");
		//return $query->row['total'];
		$collection="mongo_user";
		$where=array();
		return $this->mongodb->gettotal($collection,$where);
	}

	public function getTotalUsersByGroupId($user_group_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE user_group_id = '" . (int)$user_group_id . "'");
		//return $query->row['total'];
		$collection="mongo_user";
		$where=array('user_group_id'=> (int)$user_group_id);
		return $this->mongodb->gettotal($collection,$where);
	}

	public function getTotalUsersByEmail($email) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		//return $query->row['total'];
		$collection="mongo_user";
		$where=array('email'=>utf8_strtolower($email));
		return $this->mongodb->gettotal($collection,$where);
	}
}