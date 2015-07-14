<?php
class ModelSaleCustomer extends Model {
	public function addCustomer($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "', newsletter = '" . (int)$data['newsletter'] . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', status = '" . (int)$data['status'] . "', approved = '" . (int)$data['approved'] . "', safe = '" . (int)$data['safe'] . "', date_added = NOW()");
		//$customer_id = $this->db->getLastId();
		$collection="mongo_customer";
		$customer_id=1+(int)$this->mongodb->getlastid($collection,'customer_id');
		$newdocument=array('customer_id'=>(int)$customer_id, 'customer_group_id'=>(int)$data['customer_group_id'], 'firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'fullname'=>trim($data['lastname'].' '.$data['lastname']), 'email'=>$data['email'], 'telephone'=>$data['telephone'], 'fax'=>$data['fax'], 'custom_field'=>isset($data['custom_field']) ? serialize($data['custom_field']) : '','ip'=>'', 'newsletter'=>(int)$data['newsletter'],'address_id'=>0, 'salt'=>$salt = substr(md5(uniqid(rand(), true)), 0, 9), 'password'=>sha1($salt . sha1($salt . sha1($data['password']))), 'status'=>(int)$data['status'], 'approved'=>(int)$data['approved'], 'safe'=>(int)$data['safe'],'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'store_id'=>0, 'cart'=>'', 'wishlist'=>'', 'token'=>'');

		if (isset($data['address'])) {
			foreach ($data['address'] as $address) {
				$collection="mongo_address";
				$address_id=1+(int)$this->mongodb->getlastid($collection,'address_id');
				//$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "', custom_field = '" . $this->db->escape(isset($address['custom_field']) ? serialize($address['custom_field']) : '') . "'");
				$newdocument=array('address_id'=>(int)$address_id, 'customer_id'=>(int)$customer_id, 'firstname'=>$address['firstname'], 'lastname'=>$address['lastname'], 'company'=>$address['company'], 'address_1'=>$address['address_1'], 'address_2'=>$address['address_2'], 'city'=>$address['city'], 'postcode'=>$address['postcode'], 'country_id'=>(int)$address['country_id'], 'zone_id'=>(int)$address['zone_id'], 'custom_field'=>isset($address['custom_field']) ? serialize($address['custom_field']) : '');
				$this->mongodb->create($collection,$newdocument); 

				if (isset($address['default'])) {
					//$address_id = $this->db->getLastId();
					//$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
					$collection="mongo_customer";
					$infoupdate=array('address_id'=>(int)$address_id);
					$where=array('customer_id'=>(int)$customer_id);
					$this->mongodb->update($collection,$infoupdate,$where);
				}
			}
		}
	}

	public function editCustomer($customer_id, $data) {
		if (!isset($data['custom_field'])) {
			$data['custom_field'] = array();
		}
		//$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "', newsletter = '" . (int)$data['newsletter'] . "', status = '" . (int)$data['status'] . "', approved = '" . (int)$data['approved'] . "', safe = '" . (int)$data['safe'] . "' WHERE customer_id = '" . (int)$customer_id . "'");
		$collection="mongo_customer";
		$infoupdate=array('customer_group_id'=>(int)$data['customer_group_id'], 'firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'fullname'=>trim($data['lastname'].' '.$data['lastname']), 'email'=>$data['email'], 'telephone'=>$data['telephone'], 'fax'=>$data['fax'], 'custom_field'=>isset($data['custom_field']) ? serialize($data['custom_field']) : '', 'newsletter'=>(int)$data['newsletter'], 'status'=>(int)$data['status'], 'approved'=>(int)$data['approved'], 'safe'=>(int)$data['safe']);
		$where=array('customer_id'=>(int)$customer_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		if ($data['password']) {
			//$this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE customer_id = '" . (int)$customer_id . "'");
			$infoupdate=array('salt'=>$salt = substr(md5(uniqid(rand(), true)), 0, 9), 'password'=>sha1($salt . sha1($salt . sha1($data['password']))));
			$where=array('customer_id'=>(int)$customer_id);
			$this->mongodb->update($collection,$infoupdate,$where);
		}

		//$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
		$collection="mongo_address";
		$where=array('customer_id'=>(int)$customer_id);
		$this->mongodb->delete($collection,$where); 
		
		if (isset($data['address'])) {
			foreach ($data['address'] as $address) {
				if (!isset($address['custom_field'])) {
					$address['custom_field'] = array();
				}
				$collection="mongo_address";
				$address_id=1+(int)$this->mongodb->getlastid($collection,'address_id');
				//$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "', custom_field = '" . $this->db->escape(isset($address['custom_field']) ? serialize($address['custom_field']) : '') . "'");
				$newdocument=array('address_id'=>(int)$address_id, 'customer_id'=>(int)$customer_id, 'firstname'=>$address['firstname'], 'lastname'=>$address['lastname'], 'company'=>$address['company'], 'address_1'=>$address['address_1'], 'address_2'=>$address['address_2'], 'city'=>$address['city'], 'postcode'=>$address['postcode'], 'country_id'=>(int)$address['country_id'], 'zone_id'=>(int)$address['zone_id'], 'custom_field'=>isset($address['custom_field']) ? serialize($address['custom_field']) : '');
				$this->mongodb->create($collection,$newdocument); 

				if (isset($address['default'])) {
					//$address_id = $this->db->getLastId();
					//$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
					$collection="mongo_customer";
					$infoupdate=array('address_id'=>(int)$address_id);
					$where=array('customer_id'=>(int)$customer_id);
					$this->mongodb->update($collection,$infoupdate,$where);
				}
			}
		}
	}

	public function editToken($customer_id, $token) {
		//$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
		$collection="mongo_customer";
		$infoupdate=array('token'=>$token);
		$where=array('customer_id'=>(int)$customer_id);
		$this->mongodb->update($collection,$infoupdate,$where);
	}

	public function deleteCustomer($customer_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		$collection="mongo_customer";
		$where=array('customer_id'=>(int)$customer_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
		$collection="mongo_customer_reward";
		$where=array('customer_id'=>(int)$customer_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
		$collection="mongo_customer_transaction";
		$where=array('customer_id'=>(int)$customer_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");
		$collection="mongo_customer_ip";
		$where=array('customer_id'=>(int)$customer_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
		$collection="mongo_address";
		$where=array('customer_id'=>(int)$customer_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getCustomer($customer_id) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		//return $query->row;
		$customer_info = array();
		$collection="mongo_customer";
		$where=array('customer_id'=>(int)$customer_id);
		$customer_info=$this->mongodb->getBy($collection,$where);
		return $customer_info;
	}

	public function getCustomerByEmail($email) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		//return $query->row;
		$customer_info = array();
		$collection="mongo_customer";
		$where=array('email'=>utf8_strtolower($email));
		$customer_info=$this->mongodb->getBy($collection,$where);
		return $customer_info;
	}

	public function getCustomers($data = array()) {	
		$customer_ip_data = array();
		if (isset($data['filter_ip'])) {
			$collection="mongo_customer_ip";
			$where=array('filter_ip'=>$data['filter_ip']);
			$order=array();
			$customer_ip_list = $this->mongodb->getall($collection,$where, $order);
			foreach ($customer_ip_list as $customer_ip_list_info) {
				$customer_ip_data[]= (int)$customer_ip_list_info['customer_id'];
			}
		}
		///	
		$customer_data = array();
		$collection="mongo_customer";
		$where=array();
		if (!empty($data['filter_name'])) {
			$where['fullname']=new MongoRegex('/'.$data['filter_name'].'/');
		}
		if (!empty($data['filter_email'])) {
			$where['email']=new MongoRegex('/^'.$data['filter_email'].'/');
		}
		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$where['newsletter']=(int)$data['filter_newsletter'];
		}
		if (!empty($data['filter_customer_group_id'])) {
			$where['customer_group_id']=(int)$data['filter_customer_group_id'];
		}
		if (!empty($data['filter_ip'])) {
			//$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
			$where['customer_id']=array('$in'=>$customer_ip_data);
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {			
			$where['status']=(int)$data['filter_status'];
		}
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$where['approved']=(int)$data['filter_approved'];
		}
		if (!empty($data['filter_date_added'])) {
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
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
			'fullname',
			'email',
			'customer_group_id',
			'status',
			'approved',
			'ip',
			'date_added'
		);	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$orderby = $data['sort'];
		} else {
			$orderby = 'fullname';
		}	
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order[$orderby] = -1;
		} else {
			$order[$orderby]= 1;
		} 
		$customer_data = $this->mongodb->get($collection,$where, $order, $start, $limit);
		return $customer_data;
		/*
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}
		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}
		if (!empty($data['filter_ip'])) {
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}
		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.approved',
			'c.ip',
			'c.date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
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

	public function approve($customer_id) {
		$customer_info = $this->getCustomer($customer_id);
		if ($customer_info) {
			//$this->db->query("UPDATE " . DB_PREFIX . "customer SET approved = '1' WHERE customer_id = '" . (int)$customer_id . "'");
			$collection="mongo_customer";
			$infoupdate=array('approved'=>1);
			$where=array('customer_id'=>(int)$customer_id);
			$this->mongodb->update($collection,$infoupdate,$where);
			
			$this->load->language('mail/customer');
			$this->load->model('setting/store');
			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);
			if ($store_info) {
				$store_name = $store_info['name'];
				$store_url = $store_info['url'] . 'index.php?route=account/login';
			} else {
				$store_name = $this->config->get('config_name');
				$store_url = HTTP_CATALOG . 'index.php?route=account/login';
			}
			$message  = sprintf($this->language->get('text_approve_welcome'), $store_name) . "\n\n";
			$message .= $this->language->get('text_approve_login') . "\n";
			$message .= $store_url . "\n\n";
			$message .= $this->language->get('text_approve_services') . "\n\n";
			$message .= $this->language->get('text_approve_thanks') . "\n";
			$message .= $store_name;
			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($store_name);
			$mail->setSubject(sprintf($this->language->get('text_approve_subject'), $store_name));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function getAddress($address_id) {
		//$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");		
		$address_query_info = array();
		$collection="mongo_address";
		$where=array('address_id'=>(int)$address_id);
		$address_query_info=$this->mongodb->getBy($collection,$where);
		
		//if ($address_query->num_rows) {
		if ($address_query_info) {
			//$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");		
			$country_query_info = array();
			$collection="mongo_country";
			$where=array('country_id'=>(int)$address_query_info['country_id']);
			$country_query_info=$this->mongodb->getBy($collection,$where);
		
			//if ($country_query->num_rows) {
			if ($country_query_info) {
				//$country = $country_query->row['name'];
				//$iso_code_2 = $country_query->row['iso_code_2'];
				//$iso_code_3 = $country_query->row['iso_code_3'];
				//$address_format = $country_query->row['address_format'];
				$country = $country_query_info['name'];
				$iso_code_2 = $country_query_info['iso_code_2'];
				$iso_code_3 = $country_query_info['iso_code_3'];
				$address_format = $country_query_info['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}
			//$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");
			$zone_query_info = array();
			$collection="mongo_zone";
			$where=array('zone_id'=>(int)$address_query_info['zone_id']);
			$zone_query_info=$this->mongodb->getBy($collection,$where);
			
			//if ($zone_query->num_rows) {
			if ($zone_query_info) {
				//$zone = $zone_query->row['name'];
				//$zone_code = $zone_query->row['code'];
				$zone = $zone_query_info['name'];
				$zone_code = $zone_query_info['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}
			return array(/*
				'address_id'     => $address_query->row['address_id'],
				'customer_id'    => $address_query->row['customer_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],*/
				'address_id'     => $address_query_info['address_id'],
				'customer_id'    => $address_query_info['customer_id'],
				'firstname'      => $address_query_info['firstname'],
				'lastname'       => $address_query_info['lastname'],
				'company'        => $address_query_info['company'],
				'address_1'      => $address_query_info['address_1'],
				'address_2'      => $address_query_info['address_2'],
				'postcode'       => $address_query_info['postcode'],
				'city'           => $address_query_info['city'],
				'zone_id'        => $address_query_info['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				//'country_id'     => $address_query->row['country_id'],
				'country_id'     => $address_query_info['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format,
				//'custom_field'   => unserialize($address_query->row['custom_field'])
				'custom_field'   => unserialize($address_query_info['custom_field'])
			);
		}
	}

	public function getAddresses($customer_id) {
		$address_data = array();
		$query_data = array();
		$collection="mongo_address";
		//$query = $this->db->query("SELECT address_id FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
		$where=array('customer_id'=>(int)$customer_id);
		$order=array();
		$query_data = $this->mongodb->getall($collection,$where, $order);
		
		//foreach ($query->rows as $result) {
		foreach ($query_data as $result) {
			$address_info = $this->getAddress($result['address_id']);
			if ($address_info) {
				$address_data[$result['address_id']] = $address_info;
			}
		}
		return $address_data;
	}

	public function getTotalCustomers($data = array()) {
		/*
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$implode[] = "newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}
		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}
		if (!empty($data['filter_ip'])) {
			$implode[] = "customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "approved = '" . (int)$data['filter_approved'] . "'";
		}
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];
		*/
		$collection="mongo_customer";
		$where=array();
		if (!empty($data['filter_name'])) {
			$where['fullname']=new MongoRegex('/'.$data['filter_name'].'/');
		}
		if (!empty($data['filter_email'])) {
			$where['email']=new MongoRegex('/^'.$data['filter_email'].'/');
		}
		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$where['newsletter']=(int)$data['filter_newsletter'];
		}
		if (!empty($data['filter_customer_group_id'])) {
			$where['customer_group_id']=(int)$data['filter_customer_group_id'];
		}
		if (!empty($data['filter_ip'])) {
			//$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {			
			$where['status']=(int)$data['filter_status'];
		}
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$where['approved']=(int)$data['filter_approved'];
		}
		if (!empty($data['filter_date_added'])) {
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function getTotalCustomersAwaitingApproval() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE status = '0' OR approved = '0'");
		//return $query->row['total'];
		$customer_data = array();
		$collection="mongo_customer";//{ $or: [ { status: "A" } , { age: 50 } ] }
		$where=array('$or'=>array(array('status'=>0),array('approved'=>0)));
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function getTotalAddressesByCustomerId($customer_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
		//return $query->row['total'];
		$customer_data = array();
		$collection="mongo_address";
		$where=array('customer_id'=>(int)$customer_id);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function getTotalAddressesByCountryId($country_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE country_id = '" . (int)$country_id . "'");
		//return $query->row['total'];
		$customer_data = array();
		$collection="mongo_address";
		$where=array('country_id'=>(int)$country_id);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function getTotalAddressesByZoneId($zone_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE zone_id = '" . (int)$zone_id . "'");
		//return $query->row['total'];
		$customer_data = array();
		$collection="mongo_address";
		$where=array('zone_id'=>(int)$zone_id);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function getTotalCustomersByCustomerGroupId($customer_group_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		//return $query->row['total'];
		$customer_data = array();
		$collection="mongo_customer";
		$where=array('customer_group_id'=>(int)$customer_group_id);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function addHistory($customer_id, $comment) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "customer_history SET customer_id = '" . (int)$customer_id . "', comment = '" . $this->db->escape(strip_tags($comment)) . "', date_added = NOW()");
		$collection="mongo_customer_history";
		$customer_history_id=1+(int)$this->mongodb->getlastid($collection,'customer_history_id');
		$newdocument=array('customer_history_id'=>(int)$customer_history_id, 'customer_id'=>(int)$customer_id, 'comment'=>strip_tags($comment), 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 
	}

	public function getHistories($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 10;
		}
		//$query = $this->db->query("SELECT comment, date_added FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
		//return $query->rows;
		$collection="mongo_customer_history";
		$where=array('customer_id'=>(int)$customer_id);
		$order=array('date_added'=>-1);
		$ketquatrave= $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		return $ketquatrave;
	}

	public function getTotalHistories($customer_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "'");
		//return $query->row['total'];
		$customer_data = array();
		$collection="mongo_customer_history";
		$where=array('customer_id'=>(int)$customer_id);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function addTransaction($customer_id, $description = '', $amount = '', $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);
		if ($customer_info) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");
			$collection="mongo_customer_transaction";
			$customer_transaction_id=1+(int)$this->mongodb->getlastid($collection,'customer_transaction_id');
			$newdocument=array('customer_transaction_id'=>(int)$customer_transaction_id, 'customer_id'=>(int)$customer_id, 'order_id'=>(int)$order_id, 'description'=>$description, 'amount'=>(float)$amount, 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
			$this->mongodb->create($collection,$newdocument); 
			
			$this->load->language('mail/customer');
			$this->load->model('setting/store');
			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);
			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}
			$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
			$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($customer_id)));
			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($store_name);
			$mail->setSubject(sprintf($this->language->get('text_transaction_subject'), $this->config->get('config_name')));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function deleteTransaction($order_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
		$collection="mongo_customer_transaction";
		$where=array('order_id'=>(int)$order_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getTransactions($customer_id, $start = 0, $limit = 10) {
		$ketquatrave= array();
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 10;
		}
		/*
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;*/
		$collection="mongo_customer_transaction";
		$where=array('customer_id'=>(int)$customer_id);
		$order=array('date_added'=>-1);
		$ketquatrave= $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		return $ketquatrave;
	}

	public function getTotalTransactions($customer_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
		//return $query->row['total'];
		$collection="mongo_customer_transaction";
		$where=array('customer_id'=>(int)$customer_id);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function getTransactionTotal($customer_id) {
		//$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
		//return $query->row['total'];
		$collection="mongo_customer_transaction";
		$match=array('$match'=> array('customer_id'=>(int)$customer_id));
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$amount')));		
		$customer_data=$this->mongodb->getaggregate($collection, $match, $group);
		return $customer_data;
	}

	public function getTotalTransactionsByOrderId($order_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
		//return $query->row['total'];
		$collection="mongo_customer_transaction";
		$where=array('order_id'=>(int)$order_id);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function addReward($customer_id, $description = '', $points = '', $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);
		if ($customer_info) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', points = '" . (int)$points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");
			$collection="mongo_customer_reward";
			$customer_reward_id=1+(int)$this->mongodb->getlastid($collection,'customer_reward_id');
			$newdocument=array('customer_reward_id'=>(int)$customer_reward_id, 'customer_id'=>(int)$customer_id, 'order_id'=>(int)$order_id, 'points'=>(int)$points, 'description'=>$description, 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
			$this->mongodb->create($collection,$newdocument); 
			
			$this->load->language('mail/customer');
			$this->load->model('setting/store');
			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);
			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}
			$message  = sprintf($this->language->get('text_reward_received'), $points) . "\n\n";
			$message .= sprintf($this->language->get('text_reward_total'), $this->getRewardTotal($customer_id));
			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($store_name);
			$mail->setSubject(sprintf($this->language->get('text_reward_subject'), $store_name));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function deleteReward($order_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "' AND points > 0");
		$collection="mongo_customer_reward";
		$where=array('order_id'=>(int)$order_id, 'points'=>array('$gt'=>0));
		$this->mongodb->delete($collection,$where); 
	}

	public function getRewards($customer_id, $start = 0, $limit = 10) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
		//return $query->rows;
		$ketquatrave= array();
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 10;
		}
		$collection="mongo_customer_reward";
		$where=array('customer_id'=>(int)$customer_id);
		$order=array('date_added'=>-1);
		$ketquatrave= $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		return $ketquatrave;
	}

	public function getTotalRewards($customer_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
		//return $query->row['total'];
		$collection="mongo_customer_reward";
		$where=array('customer_id'=>(int)$customer_id);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function getRewardTotal($customer_id) {
		//$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
		//return $query->row['total'];
		$collection="mongo_customer_reward";
		$match=array('$match'=> array('customer_id'=>(int)$customer_id));
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$points')));		
		$customer_data=$this->mongodb->getaggregate($collection, $match, $group);
		return $customer_data;
	}

	public function getTotalCustomerRewardsByOrderId($order_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");
		//return $query->row['total'];
		$collection="mongo_customer_reward";
		$where=array('order_id'=>(int)$order_id);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function getIps($customer_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");
		//return $query->rows;
		$customer_data = array();
		$collection="mongo_customer_ip";
		$where=array('customer_id'=>(int)$customer_id);
		$order=array();
		$customer_data = $this->mongodb->getall($collection,$where, $order);
		return $customer_data;
	}

	public function getTotalIps($customer_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");
		//return $query->row['total'];
		$collection="mongo_customer_ip";
		$where=array('customer_id'=>(int)$customer_id);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function getTotalCustomersByIp($ip) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($ip) . "'");
		//return $query->row['total'];
		$collection="mongo_customer_ip";
		$where=array('ip'=>$ip);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}

	public function addBanIp($ip) {
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_ban_ip` SET `ip` = '" . $this->db->escape($ip) . "'");
		$collection="mongo_customer_ban_ip";
		$customer_ban_ip_id=1+(int)$this->mongodb->getlastid($collection,'customer_ban_ip_id');
		$newdocument=array('customer_ban_ip_id'=>(int)$customer_ban_ip_id, 'ip'=>$ip);
		$this->mongodb->create($collection,$newdocument); 
	}

	public function removeBanIp($ip) {
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_ban_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");
		$collection="mongo_customer_ban_ip";
		$where=array('ip'=>$ip);
		$this->mongodb->delete($collection,$where); 
	}

	public function getTotalBanIpsByIp($ip) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_ban_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");
		//return $query->row['total'];
		$collection="mongo_customer_ban_ip";
		$where=array('ip'=>$ip);
		$customer_data=$this->mongodb->gettotal($collection,$where);
		return $customer_data;
	}
	
	public function getTotalLoginAttempts($email) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");
		//return $query->row;
		$customer_info = array();
		$collection="mongo_customer_login";
		$where=array('email'=>$email);
		$customer_info=$this->mongodb->getBy($collection,$where);
		return $customer_info;
	}	

	public function deleteLoginAttempts($email) {
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");
		$collection="mongo_customer_login";
		$where=array('email'=>$email);
		$this->mongodb->delete($collection,$where); 
	}		
}