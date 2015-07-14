<?php
class ModelMarketingAffiliate extends Model {
	public function addAffiliate($data) {
		$this->event->trigger('pre.admin.affiliate.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "affiliate SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', code = '" . $this->db->escape($data['code']) . "', commission = '" . (float)$data['commission'] . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', cheque = '" . $this->db->escape($data['cheque']) . "', paypal = '" . $this->db->escape($data['paypal']) . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
		//$affiliate_id = $this->db->getLastId();
		$collection="mongo_affiliate";
		$affiliate_id=1+(int)$this->mongodb->getlastid($collection,'affiliate_id');
		$newdocument=array('affiliate_id'=>(int)$affiliate_id, 'firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'email'=>$data['email'], 'telephone'=>$data['telephone'], 'fax'=>$data['fax'], 'salt'=>$salt = substr(md5(uniqid(rand(), true)), 0, 9), 'password'=>sha1($salt . sha1($salt . sha1($data['password']))), 'company'=>$data['company'], 'website'=>$data['website'], 'address_1'=>$data['address_1'], 'address_2'=>$data['address_2'], 'city'=>$data['city'], 'postcode'=>$data['postcode'], 'country_id'=>(int)$data['country_id'], 'zone_id'=>(int)$data['zone_id'], 'code'=>$data['code'], 'commission'=>(float)$data['commission'], 'tax'=>$data['tax'], 'payment'=>$data['payment'], 'cheque'=>$data['cheque'], 'paypal'=>$data['paypal'], 'bank_name'=>$data['bank_name'], 'bank_branch_number'=>$data['bank_branch_number'], 'bank_swift_code'=>$data['bank_swift_code'], 'bank_account_name'=>$data['bank_account_name'], 'bank_account_number'=>$data['bank_account_number'], 'status'=>(int)$data['status'], 'approved'=>(int)!$this->config->get('config_affiliate_approval'),'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 

		$this->event->trigger('post.admin.affiliate.add', $affiliate_id);

		return $affiliate_id;
	}

	public function editAffiliate($affiliate_id, $data) {
		$this->event->trigger('pre.admin.affiliate.edit', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', code = '" . $this->db->escape($data['code']) . "', commission = '" . (float)$data['commission'] . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', cheque = '" . $this->db->escape($data['cheque']) . "', paypal = '" . $this->db->escape($data['paypal']) . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "', status = '" . (int)$data['status'] . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		$collection="mongo_affiliate";
		$newdocument=array('firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'email'=>$data['email'], 'telephone'=>$data['telephone'], 'fax'=>$data['fax'], 'company'=>$data['company'], 'website'=>$data['website'], 'address_1'=>$data['address_1'], 'address_2'=>$data['address_2'], 'city'=>$data['city'], 'postcode'=>$data['postcode'], 'country_id'=>(int)$data['country_id'], 'zone_id'=>(int)$data['zone_id'], 'code'=>$data['code'], 'commission'=>(float)$data['commission'], 'tax'=>$data['tax'], 'payment'=>$data['payment'], 'cheque'=>$data['cheque'], 'paypal'=>$data['paypal'], 'bank_name'=>$data['bank_name'], 'bank_branch_number'=>$data['bank_branch_number'], 'bank_swift_code'=>$data['bank_swift_code'], 'bank_account_name'=>$data['bank_account_name'], 'bank_account_number'=>$data['bank_account_number'], 'status'=>(int)$data['status']);
		$where=array('affiliate_id'=>(int)$affiliate_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		if ($data['password']) {
			//$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
			$infoupdate=array('salt'=>$salt = substr(md5(uniqid(rand(), true)), 0, 9), 'password'=>sha1($salt . sha1($salt . sha1($data['password']))));
			$where=array('affiliate_id'=>(int)$affiliate_id);
			$this->mongodb->update($collection,$infoupdate,$where);
		}

		$this->event->trigger('post.admin.affiliate.edit', $affiliate_id);
	}

	public function deleteAffiliate($affiliate_id) {
		$this->event->trigger('pre.admin.affiliate.delete', $affiliate_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "affiliate WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		$collection="mongo_affiliate";
		$where=array('affiliate_id'=>(int)$affiliate_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_activity WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		$collection="mongo_affiliate_activity";
		$where=array('affiliate_id'=>(int)$affiliate_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_transaction WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		$collection="mongo_affiliate_transaction";
		$where=array('affiliate_id'=>(int)$affiliate_id);
		$this->mongodb->delete($collection,$where); 

		$this->event->trigger('post.admin.affiliate.delete', $affiliate_id);
	}

	public function getAffiliate($affiliate_id) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "affiliate WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		//return $query->row;
		$affiliate_info = array();
		$collection="mongo_affiliate";
		$where=array('affiliate_id'=>(int)$affiliate_id);
		$affiliate_info=$this->mongodb->getBy($collection,$where);
		return $affiliate_info;
	}

	public function getAffiliateByEmail($email) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "affiliate WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		//return $query->row;
		$affiliate_info = array();
		$collection="mongo_affiliate";
		$where=array('email'=>utf8_strtolower($email));
		$affiliate_info=$this->mongodb->getBy($collection,$where);
		return $affiliate_info;
	}
	
	public function getSumAffiliatesTransaction($affiliate_id) {
		$collection="mongo_affiliate_transaction";
		$match=array('$match'=> array('affiliate_id'=>(int)$affiliate_id));
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$amount')));		
		$affiliate_transaction_data=$this->mongodb->getaggregate($collection, $match, $group);
		return $affiliate_transaction_data;
	}
	
	public function getAffiliates($data = array()) {
		//$sql = "SELECT *, CONCAT(a.firstname, ' ', a.lastname) AS name, (SELECT SUM(at.amount) FROM " . DB_PREFIX . "affiliate_transaction at WHERE at.affiliate_id = a.affiliate_id GROUP BY at.affiliate_id) AS balance FROM " . DB_PREFIX . "affiliate a";
		//$implode = array();
		$collection="mongo_affiliate";$affiliate_query_data= array();
		$where=array();
		$order=array();
		if (!empty($data['filter_name'])) {
			//$implode[] = "CONCAT(a.firstname, ' ', a.lastname) LIKE '" . $this->db->escape($data['filter_name']) . "%'";
			$where['firstname']=new MongoRegex('/'.$data['filter_name'].'/');
		}
		if (!empty($data['filter_email'])) {
			//$implode[] = "LCASE(a.email) = '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "'";	
			$where['email']=utf8_strtolower($data['filter_email']);
		}
		if (!empty($data['filter_code'])) {
			//$implode[] = "a.code = '" . $this->db->escape($data['filter_code']) . "'";	
			$where['code']=$data['filter_code'];
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			//$implode[] = "a.status = '" . (int)$data['filter_status'] . "'";		
			$where['status']=(int)$data['filter_status'];
		}
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			//$implode[] = "a.approved = '" . (int)$data['filter_approved'] . "'";		
			$where['approved']=(int)$data['filter_approved'];
		}
		if (!empty($data['filter_date_added'])) {
			//$implode[] = "DATE(a.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		//if ($implode) {
			//$sql .= " WHERE " . implode(" AND ", $implode);
		//}
		$sort_data = array(
			'firstname',
			'email',
			'code',
			'status',
			'approved',
			'date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			//$sql .= " ORDER BY " . $data['sort'];
			$orderby = $data['sort'];
		} else {
			//$sql .= " ORDER BY name";
			$orderby = 'firstname';
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			//$sql .= " DESC";
			$order[$orderby] = -1;
		} else {
			//$sql .= " ASC";
			$order[$orderby] = 1;
		}
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			//$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			$start=(int)$data['start'];
			$limit=(int)$data['limit'];
		}
		$affiliate_query_data = $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		return $affiliate_query_data;
		//$query = $this->db->query($sql);
		//return $query->rows;
	}

	public function approve($affiliate_id) {

		$affiliate_info = $this->getAffiliate($affiliate_id);

		if ($affiliate_info) {
			$this->event->trigger('pre.admin.affiliate.approve', $affiliate_id);

			//$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET approved = '1' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
			$collection="mongo_affiliate";
			$infoupdate=array('approved'=>1);
			$where=array('affiliate_id'=>(int)$affiliate_id);
			$this->mongodb->update($collection,$infoupdate,$where);

			$this->load->language('mail/affiliate');

			$message  = sprintf($this->language->get('text_approve_welcome'), $this->config->get('config_name')) . "\n\n";
			$message .= $this->language->get('text_approve_login') . "\n";
			$message .= HTTP_CATALOG . 'index.php?route=affiliate/login' . "\n\n";
			$message .= $this->language->get('text_approve_services') . "\n\n";
			$message .= $this->language->get('text_approve_thanks') . "\n";
			$message .= $this->config->get('config_name');

			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($affiliate_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject(sprintf($this->language->get('text_approve_subject'), $this->config->get('config_name')));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();

			$this->event->trigger('post.admin.affiliate.approve', $affiliate_id);
		}
	}

	public function getAffiliatesByNewsletter() {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE newsletter = '1' ORDER BY firstname, lastname, email");
		//return $query->rows;
		$collection="mongo_affiliate";$affiliate_data=array();
		$where=array('newsletter'=> 1);
		$order=array('firstname'=> 1, 'lastname'=> 1, 'email'=> 1);
		$affiliate_data = $this->mongodb->getall($collection,$where, $order);
		return $affiliate_data;
	}

	public function getTotalAffiliates($data = array()) {
		$affiliate_data= array();
		$collection="mongo_affiliate";
		//$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate";
		//$implode = array();
		$where=array();
		if (!empty($data['filter_name'])) {
			//$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			$where['firstname']=new MongoRegex('/'.$data['filter_name'].'/');
		}
		if (!empty($data['filter_email'])) {
			//$implode[] = "LCASE(email) = '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "'";
			$where['email']=utf8_strtolower($data['filter_email']);
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			//$implode[] = "status = '" . (int)$data['filter_status'] . "'";		
			$where['status']=(int)$data['filter_status'];
		}
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			//$implode[] = "approved = '" . (int)$data['filter_approved'] . "'";		
			$where['approved']=(int)$data['filter_approved'];
		}
		if (!empty($data['filter_date_added'])) {
			//$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		//if ($implode) {
			//$sql .= " WHERE " . implode(" AND ", $implode);
		//}
		//$query = $this->db->query($sql);
		//return $query->row['total'];
		$affiliate_data=$this->mongodb->gettotal($collection,$where);
		return $affiliate_data;
	}

	public function getTotalAffiliatesAwaitingApproval() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate WHERE status = '0' OR approved = '0'");
		//return $query->row['total'];
		$affiliate_data= array();
		$collection="mongo_affiliate";
		$where=array('status'=>0,'approved'=>0);
		$affiliate_data=$this->mongodb->gettotal($collection,$where);
		return $affiliate_data;
	}

	public function getTotalAffiliatesByCountryId($country_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate WHERE country_id = '" . (int)$country_id . "'");
		//return $query->row['total'];
		$affiliate_data= array();
		$collection="mongo_affiliate";
		$where=array('country_id'=>(int)$country_id);
		$affiliate_data=$this->mongodb->gettotal($collection,$where);
		return $affiliate_data;
	}

	public function getTotalAffiliatesByZoneId($zone_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate WHERE zone_id = '" . (int)$zone_id . "'");
		//return $query->row['total'];
		$affiliate_data= array();
		$collection="mongo_affiliate";
		$where=array('zone_id'=>(int)$zone_id);
		$affiliate_data=$this->mongodb->gettotal($collection,$where);
		return $affiliate_data;
	}

	public function addTransaction($affiliate_id, $description = '', $amount = '', $order_id = 0) {
		$affiliate_info = $this->getAffiliate($affiliate_id);
		if ($affiliate_info) {
			$this->event->trigger('pre.admin.affiliate.transaction.add', $affiliate_id);
			//$this->db->query("INSERT INTO " . DB_PREFIX . "affiliate_transaction SET affiliate_id = '" . (int)$affiliate_id . "', order_id = '" . (float)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");
			$collection="mongo_affiliate_transaction";
			$affiliate_transaction_id=1+(int)$this->mongodb->getlastid($collection,'affiliate_transaction_id');
			$newdocument=array('affiliate_transaction_id'=>(int)$affiliate_transaction_id, 'affiliate_id'=>(int)$data['affiliate_id'], 'order_id'=>(float)$data['order_id'], 'description'=>$data['description'], 'amount'=>(float)$data['amount'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
			$this->mongodb->create($collection,$newdocument); 
		
			$affiliate_transaction_id = $this->db->getLastId();
			$this->load->language('mail/affiliate');
			$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
			$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($affiliate_id), $this->config->get('config_currency')));
			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($affiliate_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject(sprintf($this->language->get('text_transaction_subject'), $this->config->get('config_name')));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
			$this->event->trigger('post.admin.affiliate.transaction.add', $affiliate_transaction_id);
			return $affiliate_transaction_id;
		}
	}

	public function deleteTransaction($order_id) {
		$this->event->trigger('pre.admin.affiliate.transaction.delete', $order_id);
		//$this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_transaction WHERE order_id = '" . (int)$order_id . "'");
		$collection="mongo_affiliate_transaction";
		$where=array('order_id'=>(int)$order_id);
		$this->mongodb->delete($collection,$where); 
		
		$this->event->trigger('post.admin.affiliate.transaction.delete', $order_id);
	}

	public function getTransactions($affiliate_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 10;
		}
		$affiliate_transaction= array();
		$collection="mongo_affiliate_transaction";
		$where=array('affiliate_id'=>(int)$affiliate_id);
		$order=array('date_added'=>-1);
		$affiliate_transaction=$this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		return $affiliate_transaction;
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate_transaction WHERE affiliate_id = '" . (int)$affiliate_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
		//return $query->rows;
	}

	public function getTotalTransactions($affiliate_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "affiliate_transaction WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		//return $query->row['total'];
		$affiliate_transaction_data= array();
		$collection="mongo_affiliate_transaction";
		$where=array('affiliate_id'=>(int)$affiliate_id);
		$affiliate_transaction_data=$this->mongodb->gettotal($collection,$where);
		return $affiliate_transaction_data;
	}

	public function getTransactionTotal($affiliate_id) {
		//$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "affiliate_transaction WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		//return $query->row['total'];
		$collection="mongo_affiliate_transaction";
		$match=array('$match'=> array('affiliate_id'=>(int)$affiliate_id));
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$amount')));		
		$affiliate_transaction_data=$this->mongodb->getaggregate($collection, $match, $group);
		return $affiliate_transaction_data;
	}

	public function getTotalTransactionsByOrderId($order_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate_transaction WHERE order_id = '" . (int)$order_id . "'");
		//return $query->row['total'];
		$collection="mongo_affiliate_transaction";
		$where=array('order_id'=>(int)$order_id);
		$affiliate_transaction_data=$this->mongodb->gettotal($collection,$where);
		return $affiliate_transaction_data;
	}
	
	public function getTotalLoginAttempts($email) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_login` WHERE `email` = '" . $this->db->escape($email) . "'");
		//return $query->row;
		$collection="mongo_affiliate_login";
		$where=array('email'=>$email);
		$affiliate_login_info=$this->mongodb->getBy($collection,$where);
		return $affiliate_login_info;
	}	

	public function deleteLoginAttempts($email) {
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_login` WHERE `email` = '" . $this->db->escape($email) . "'");
		$collection="mongo_affiliate_login";
		$where=array('email'=>$email);
		$this->mongodb->delete($collection,$where); 
	}	
}