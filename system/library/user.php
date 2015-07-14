<?php
class User {
	private $user_id;
	private $username;
	private $permission = array();

	public function __construct($registry) {
		//$this->db = $registry->get('db');
		$this->mongodb = $registry->get('mongodb');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		
		if (isset($this->session->data['user_id'])) {
			//$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->session->data['user_id'] . "' AND status = '1'");
			$collection="mongo_user";
			$user_query_result = array();
			$where=array('user_id'=>(int)$this->session->data['user_id'], 'status'=>1);
			$user_query_result=$this->mongodb->getBy($collection,$where);
			//if ($user_query->num_rows) {
			if ($user_query_result) {
				//$this->user_id = $user_query->row['user_id'];
				//$this->username = $user_query->row['username'];
				//$this->user_group_id = $user_query->row['user_group_id'];
				$this->user_id = $user_query_result['user_id'];
				$this->username = $user_query_result['username'];
				$this->user_group_id = $user_query_result['user_group_id'];
				//$this->db->query("UPDATE " . DB_PREFIX . "user SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");
				$infoupdate=array('ip'=>(string)$this->request->server['REMOTE_ADDR']);
				$where=array('user_id'=>(int)$this->session->data['user_id']);
				$this->mongodb->update($collection,$infoupdate,$where); 

				//$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");
				$collection="mongo_user_group";
				$where=array('user_group_id'=>(int)$user_query_result['user_group_id']);
				$user_group_list = $this->mongodb->getBy($collection,$where);

				//$permissions = unserialize($user_group_query->row['permission']);
				$permissions = unserialize($user_group_list['permission']);
				
				if (is_array($permissions)) {
					foreach ($permissions as $key => $value) {
						$this->permission[$key] = $value;
					}
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($username, $password) {
		//$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");
		$collection="mongo_user";
		$user_query_result = array();
		$where=array('username'=>$username, 'status'=>1);
		$user_query_result=$this->mongodb->getBy($collection,$where);
		if ($user_query_result) {
			$salt_result=$user_query_result['salt'];
			$password_result=$user_query_result['password'];
			$password_test1=sha1($salt_result . sha1($salt_result . sha1($password)));
			$password_test2=md5($password);
		} else {
			return false;
		}
		//if ($user_query->num_rows) {
		if (($password_result==$password_test1) or ($password_result==$password_test2)) {
			//$this->session->data['user_id'] = $user_query->row['user_id'];
			//$this->user_id = $user_query->row['user_id'];
			//$this->username = $user_query->row['username'];
			//$this->user_group_id = $user_query->row['user_group_id'];
			$this->session->data['user_id'] = $user_query_result['user_id'];
			$this->user_id = $user_query_result['user_id'];
			$this->username = $user_query_result['username'];
			$this->user_group_id = $user_query_result['user_group_id'];

			//$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");
			$collection="mongo_user_group";
			$user_group_query_result = array();
			$where=array('user_group_id'=>(int)$user_query_result['user_group_id']);
			$user_group_query_result=$this->mongodb->getBy($collection,$where);
			
			//$permissions = unserialize($user_group_query->row['permission']);
			$permissions = unserialize($user_group_query_result['permission']);
			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['user_id']);

		$this->user_id = '';
		$this->username = '';
	}

	public function hasPermission($key, $value) {
		if (isset($this->permission[$key])) {
			return in_array($value, $this->permission[$key]);
		} else {
			return false;
		}
	}

	public function isLogged() {
		return $this->user_id;
	}

	public function getId() {
		return $this->user_id;
	}

	public function getUserName() {
		return $this->username;
	}
	
	public function getGroupId() {
		return $this->user_group_id;
	}	
}