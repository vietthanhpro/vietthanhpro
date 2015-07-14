<?php
class Affiliate {
	private $affiliate_id;
	private $firstname;
	private $lastname;
	private $email;
	private $telephone;
	private $fax;
	private $code;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		//$this->db = $registry->get('db');
		$this->mongodb = $registry->get('mongodb');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['affiliate_id'])) {
			//$affiliate_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE affiliate_id = '" . (int)$this->session->data['affiliate_id'] . "' AND status = '1'");
			$affiliate_query_data = array();
			$collection="mongo_affiliate";
			$where=array('affiliate_id'=>(int)$this->session->data['affiliate_id'], 'status'=>1);
			$affiliate_query_data=$this->mongodb->getBy($collection,$where);
			//if ($affiliate_query->num_rows) {
			if ($affiliate_query->num_rows) {/*
				$this->affiliate_id = $affiliate_query->row['affiliate_id'];
				$this->firstname = $affiliate_query->row['firstname'];
				$this->lastname = $affiliate_query->row['lastname'];
				$this->email = $affiliate_query->row['email'];
				$this->telephone = $affiliate_query->row['telephone'];
				$this->fax = $affiliate_query->row['fax'];
				$this->code = $affiliate_query->row['code'];*/
				$this->affiliate_id = $affiliate_query_data['customer_id'];
				$this->firstname = $affiliate_query_data['firstname'];
				$this->lastname = $affiliate_query_data['lastname'];
				$this->email = $affiliate_query_data['email'];
				$this->telephone = $affiliate_query_data['telephone'];
				$this->fax = $affiliate_query_data['fax'];
				$this->code = $affiliate_query_data['code'];

				//$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE affiliate_id = '" . (int)$this->session->data['affiliate_id'] . "'");
				$newdocument=array( 'ip'=>$this->request->server['REMOTE_ADDR']);
				$where=array('affiliate_id'=>(int)$this->session->data['affiliate_id']);
				$this->mongodb->update($collection,$infoupdate,$where);
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password) {
		//$affiliate_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1' AND approved = '1'");
		$collection="mongo_user";
		$affiliate_query_result = array();
		$where=array('email'=>utf8_strtolower($email), 'status'=>1, 'approved'=>1);
		$affiliate_query_result=$this->mongodb->getBy($collection,$where);
		if ($affiliate_query_result) {
			$salt_result=$affiliate_query_result['salt'];
			$password_result=$affiliate_query_result['password'];
			$password_test1=sha1($salt_result . sha1($salt_result . sha1($password)));
			$password_test2=md5($password);
		} else {
			return false;
		}
		//if ($affiliate_query->num_rows) {
		if ($affiliate_query_result) {
			//$this->session->data['affiliate_id'] = $affiliate_query->row['affiliate_id'];
			$this->session->data['affiliate_id'] = $affiliate_query_result['affiliate_id'];
			/*
			$this->affiliate_id = $affiliate_query->row['affiliate_id'];
			$this->firstname = $affiliate_query->row['firstname'];
			$this->lastname = $affiliate_query->row['lastname'];
			$this->email = $affiliate_query->row['email'];
			$this->telephone = $affiliate_query->row['telephone'];
			$this->fax = $affiliate_query->row['fax'];
			$this->code = $affiliate_query->row['code'];*/
			$this->affiliate_id = $affiliate_query_data['customer_id'];
			$this->firstname = $affiliate_query_data['firstname'];
			$this->lastname = $affiliate_query_data['lastname'];
			$this->email = $affiliate_query_data['email'];
			$this->telephone = $affiliate_query_data['telephone'];
			$this->fax = $affiliate_query_data['fax'];
			$this->code = $affiliate_query_data['code'];

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['affiliate_id']);

		$this->affiliate_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->telephone = '';
		$this->fax = '';
	}

	public function isLogged() {
		return $this->affiliate_id;
	}

	public function getId() {
		return $this->affiliate_id;
	}

	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getTelephone() {
		return $this->telephone;
	}

	public function getFax() {
		return $this->fax;
	}

	public function getCode() {
		return $this->code;
	}
}