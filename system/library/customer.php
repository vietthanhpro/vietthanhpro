<?php
class Customer {
	private $customer_id;
	private $firstname;
	private $lastname;
	private $email;
	private $telephone;
	private $fax;
	private $newsletter;
	private $customer_group_id;
	private $address_id;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		//$this->db = $registry->get('db');
		$this->mongodb = $registry->get('mongodb');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['customer_id'])) {
			//$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");
			$customer_query_data = array();
			$collection="mongo_customer";
			$where=array('customer_id'=>(int)$this->session->data['customer_id'], 'status'=>1);
			$customer_query_data=$this->mongodb->getBy($collection,$where);
			//if ($customer_query->num_rows) {
			if ($customer_query_data) {/*
				$this->customer_id = $customer_query->row['customer_id'];
				$this->firstname = $customer_query->row['firstname'];
				$this->lastname = $customer_query->row['lastname'];
				$this->email = $customer_query->row['email'];
				$this->telephone = $customer_query->row['telephone'];
				$this->fax = $customer_query->row['fax'];
				$this->newsletter = $customer_query->row['newsletter'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->address_id = $customer_query->row['address_id'];*/
				$this->customer_id = $customer_query_data['customer_id'];
				$this->firstname = $customer_query_data['firstname'];
				$this->lastname = $customer_query_data['lastname'];
				$this->email = $customer_query_data['email'];
				$this->telephone = $customer_query_data['telephone'];
				$this->fax = $customer_query_data['fax'];
				$this->newsletter = $customer_query_data['newsletter'];
				$this->customer_group_id = $customer_query_data['customer_group_id'];
				$this->address_id = $customer_query_data['address_id'];

				//$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
				$infoupdate=array('cart'=>isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '', 'wishlist'=>isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '', 'ip'=>$this->request->server['REMOTE_ADDR']);
				$where=array('customer_id'=>(int)$this->customer_id);
				$this->mongodb->update($collection,$infoupdate,$where);

				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");
				$query_data = array();
				$collection="mongo_customer_ip";
				$where=array('customer_id'=>(int)$this->session->data['customer_id'],'ip'=>$this->request->server['REMOTE_ADDR']);
				$query_data=$this->mongodb->getBy($collection,$where);
				//if (!$query->num_rows) {
				if (!$query_data) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . (int)$this->session->data['customer_id'] . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()");
					$customer_ip_id=1+(int)$this->mongodb->getlastid($collection,'customer_ip_id');
					$newdocument=array('customer_ip_id'=>(int)$customer_ip_id, 'customer_id'=>(int)$this->session->data['customer_id'], 'ip'=>$this->request->server['REMOTE_ADDR'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
					$this->mongodb->create($collection,$newdocument); 
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password, $override = false) {
		$collection="mongo_customer";
		if ($override) {
			//$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND status = '1'");
			$customer_query_data = array();
			$where=array('email'=>utf8_strtolower($email), 'status'=>1);
			$customer_query_data=$this->mongodb->getBy($collection,$where);
		} else {
			//$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1' AND approved = '1'");
			$customer_query_data = array();$customer_query_data_ = array();
			$where=array('email'=>utf8_strtolower($email), 'status'=>1);
			$customer_query_data_=$this->mongodb->getBy($collection,$where);
			$salt_result=$customer_query_data_['salt'];
			$password_result=$customer_query_data_['password'];
			$password_test1=sha1($salt_result . sha1($salt_result . sha1($password)));
			$password_test2=md5($password);
			if (($password_result==$password_test1) or ($password_result==$password_test2)) {
				$customer_query_data =$customer_query_data_;
			}
		}
		//if ($customer_query->num_rows) {
		if ($customer_query_data) {
			//$this->session->data['customer_id'] = $customer_query->row['customer_id'];
			$this->session->data['customer_id'] = $customer_query_data['customer_id'];
			//if ($customer_query->row['cart'] && is_string($customer_query->row['cart'])) {
			if ($customer_query_data['cart'] && is_string($customer_query_data['cart'])) {
				//$cart = unserialize($customer_query->row['cart']);
				$cart = unserialize($customer_query_data['cart']);
				foreach ($cart as $key => $value) {
					if (!array_key_exists($key, $this->session->data['cart'])) {
						$this->session->data['cart'][$key] = $value;
					} else {
						$this->session->data['cart'][$key] += $value;
					}
				}
			}

			//if ($customer_query->row['wishlist'] && is_string($customer_query->row['wishlist'])) {
			if ($customer_query_data['wishlist'] && is_string($customer_query_data['wishlist'])) {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}

				$wishlist = unserialize($customer_query->row['wishlist']);

				foreach ($wishlist as $product_id) {
					if (!in_array($product_id, $this->session->data['wishlist'])) {
						$this->session->data['wishlist'][] = $product_id;
					}
				}
			}
			/*
			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];
			$this->fax = $customer_query->row['fax'];
			$this->newsletter = $customer_query->row['newsletter'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->address_id = $customer_query->row['address_id'];*/
			$this->customer_id = $customer_query_data['customer_id'];
			$this->firstname = $customer_query_data['firstname'];
			$this->lastname = $customer_query_data['lastname'];
			$this->email = $customer_query_data['email'];
			$this->telephone = $customer_query_data['telephone'];
			$this->fax = $customer_query_data['fax'];
			$this->newsletter = $customer_query_data['newsletter'];
			$this->customer_group_id = $customer_query_data['customer_group_id'];
			$this->address_id = $customer_query_data['address_id'];

			//$this->db->query("UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
			$infoupdate=array('ip'=>$this->request->server['REMOTE_ADDR']);
			$where=array('customer_id'=>(int)$this->customer_id);
			$this->mongodb->update($collection,$infoupdate,$where);

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		//$this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', wishlist = '" . $this->db->escape(isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '') . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
		$collection="mongo_customer";
		$infoupdate=array('cart'=>isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '', 'wishlist'=>isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '');
		$where=array('customer_id'=>(int)$this->customer_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		unset($this->session->data['customer_id']);

		$this->customer_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->telephone = '';
		$this->fax = '';
		$this->newsletter = '';
		$this->customer_group_id = '';
		$this->address_id = '';
	}

	public function isLogged() {
		return $this->customer_id;
	}

	public function getId() {
		return $this->customer_id;
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

	public function getNewsletter() {
		return $this->newsletter;
	}

	public function getGroupId() {
		return $this->customer_group_id;
	}

	public function getAddressId() {
		return $this->address_id;
	}

	public function getBalance() {
		//$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");
		$collection="mongo_customer_transaction";
		$match=array('$match'=> array('customer_id'=>(int)$this->customer_id));
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$amount')));		
		$customer_transaction_data=$this->mongodb->getaggregate($collection, $match, $group);
		return $customer_transaction_data;
		//return $query->row['total'];
	}

	public function getRewardPoints() {
		//$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");
		$collection="mongo_customer_reward";
		$match=array('$match'=> array('customer_id'=>(int)$this->customer_id));
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$points')));		
		$customer_reward_data=$this->mongodb->getaggregate($collection, $match, $group);
		return $customer_reward_data;
		//return $query->row['total'];
	}
}