<?php
class ModelAffiliateAffiliate extends Model {
	public function addAffiliate($data) {
		$this->event->trigger('pre.affiliate.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "affiliate SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', code = '" . $this->db->escape(uniqid()) . "', commission = '" . (float)$this->config->get('config_affiliate_commission') . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', cheque = '" . $this->db->escape($data['cheque']) . "', paypal = '" . $this->db->escape($data['paypal']) . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "', status = '1', approved = '" . (int)!$this->config->get('config_affiliate_approval') . "', date_added = NOW()");
		//$affiliate_id = $this->db->getLastId();
		$collection="mongo_affiliate";
		$affiliate_id=1+(int)$this->mongodb->getlastid($collection,'affiliate_id');
		$newdocument=array('affiliate_id'=>(int)$affiliate_id, 'firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'email'=>$data['email'], 'telephone'=>$data['telephone'], 'fax'=>$data['fax'], 'salt'=>$salt = substr(md5(uniqid(rand(), true)), 0, 9), 'password'=>sha1($salt . sha1($salt . sha1($data['password']))), 'company'=>$data['company'], 'website'=>$data['website'], 'address_1'=>$data['address_1'], 'address_2'=>$data['address_2'], 'city'=>$data['city'], 'postcode'=>$data['postcode'], 'country_id'=>(int)$data['country_id'], 'zone_id'=>(int)$data['zone_id'], 'code'=>uniqid(), 'commission'=>(float)$this->config->get('config_affiliate_commission'), 'tax'=>$data['tax'], 'payment'=>$data['payment'], 'cheque'=>$data['cheque'], 'paypal'=>$data['paypal'], 'bank_name'=>$data['bank_name'], 'bank_branch_number'=>$data['bank_branch_number'], 'bank_swift_code'=>$data['bank_swift_code'], 'bank_account_name'=>$data['bank_account_name'], 'bank_account_number'=>$data['bank_account_number'], 'status'=>1, 'approved'=>(int)!$this->config->get('config_affiliate_approval'),'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 

		$this->load->language('mail/affiliate');

		$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

		$message  = sprintf($this->language->get('text_welcome'), $this->config->get('config_name')) . "\n\n";
		$message .= $this->language->get('text_approval') . "\n";

		if (!$this->config->get('config_affiliate_approval')) {
			$message .= $this->language->get('text_login') . "\n";
		} else {
			$message .= $this->language->get('text_approval') . "\n";
		}

		$message .= $this->url->link('affiliate/login', '', 'SSL') . "\n\n";
		$message .= $this->language->get('text_services') . "\n\n";
		$message .= $this->language->get('text_thanks') . "\n";
		$message .= $this->config->get('config_name');

		$mail = new Mail($this->config->get('config_mail'));
		$mail->setTo($this->request->post['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject($subject);
		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		$mail->send();

		// Send to main admin email if new affiliate email is enabled
		if ($this->config->get('config_affiliate_mail')) {
			$message  = $this->language->get('text_signup') . "\n\n";
			$message .= $this->language->get('text_store') . ' ' . $this->config->get('config_name') . "\n";
			$message .= $this->language->get('text_firstname') . ' ' . $data['firstname'] . "\n";
			$message .= $this->language->get('text_lastname') . ' ' . $data['lastname'] . "\n";

			if ($data['website']) {
				$message .= $this->language->get('text_website') . ' ' . $data['website'] . "\n";
			}

			if ($data['company']) {
				$message .= $this->language->get('text_company') . ' '  . $data['company'] . "\n";
			}

			$message .= $this->language->get('text_email') . ' '  .  $data['email'] . "\n";
			$message .= $this->language->get('text_telephone') . ' ' . $data['telephone'] . "\n";

			$mail->setTo($this->config->get('config_email'));
			$mail->setSubject(html_entity_decode($this->language->get('text_new_affiliate'), ENT_QUOTES, 'UTF-8'));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();

			// Send to additional alert emails if new affiliate email is enabled
			$emails = explode(',', $this->config->get('config_mail_alert'));

			foreach ($emails as $email) {
				if (utf8_strlen($email) > 0 && preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}

		$this->event->trigger('post.affiliate.add', $affiliate_id);

		return $affiliate_id;
	}

	public function editAffiliate($data) {
		$this->event->trigger('pre.affiliate.edit', $data);

		$affiliate_id = $this->affiliate->getId();
		//$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		$collection="mongo_affiliate";
		$newdocument=array('firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'email'=>$data['email'], 'telephone'=>$data['telephone'], 'fax'=>$data['fax'], 'company'=>$data['company'], 'website'=>$data['website'], 'address_1'=>$data['address_1'], 'address_2'=>$data['address_2'], 'city'=>$data['city'], 'postcode'=>$data['postcode'], 'country_id'=>(int)$data['country_id'], 'zone_id'=>(int)$data['zone_id']);
		$where=array('affiliate_id'=>(int)$affiliate_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->event->trigger('post.affiliate.edit', $affiliate_id);
	}

	public function editPayment($data) {
		$this->event->trigger('pre.affiliate.edit.payment', $data);

		$affiliate_id = $this->affiliate->getId();
		//$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', cheque = '" . $this->db->escape($data['cheque']) . "', paypal = '" . $this->db->escape($data['paypal']) . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', bank_branch_number = '" . $this->db->escape($data['bank_branch_number']) . "', bank_swift_code = '" . $this->db->escape($data['bank_swift_code']) . "', bank_account_name = '" . $this->db->escape($data['bank_account_name']) . "', bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		$collection="mongo_affiliate";
		$newdocument=array('tax'=>$data['tax'], 'payment'=>$data['payment'], 'cheque'=>$data['cheque'], 'paypal'=>$data['paypal'], 'bank_name'=>$data['bank_name'], 'bank_branch_number'=>$data['bank_branch_number'], 'bank_swift_code'=>$data['bank_swift_code'], 'bank_account_name'=>$data['bank_account_name'], 'bank_account_number'=>$data['bank_account_number']);
		$where=array('affiliate_id'=>(int)$affiliate_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->event->trigger('post.affiliate.edit.payment', $affiliate_id);
	}

	public function editPassword($email, $password) {
		$affiliate_id = $this->affiliate->getId();

		$this->event->trigger('pre.affiliate.edit.password', $affiliate_id);

		//$this->db->query("UPDATE " . DB_PREFIX . "affiliate SET salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		$collection="mongo_affiliate";
		$newdocument=array('salt'=>$salt = substr(md5(uniqid(rand(), true)), 0, 9), 'password'=>sha1($salt . sha1($salt . sha1($password))));
		$where=array('email'=>utf8_strtolower($email));
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->event->trigger('post.affiliate.edit.password', $affiliate_id);
	}

	public function getAffiliate($affiliate_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		//return $query->row;
		$affiliate_info = array();
		$collection="mongo_affiliate";
		$where=array('affiliate_id'=>(int)$affiliate_id);
		$affiliate_info=$this->mongodb->getBy($collection,$where);
		return $affiliate_info;
	}

	public function getAffiliateByEmail($email) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		//return $query->row;
		$affiliate_info = array();
		$collection="mongo_affiliate";
		$where=array('email'=>utf8_strtolower($email));
		$affiliate_info=$this->mongodb->getBy($collection,$where);
		return $affiliate_info;
	}

	public function getAffiliateByCode($code) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate WHERE code = '" . $this->db->escape($code) . "'");
		//return $query->row;
		$affiliate_info = array();
		$collection="mongo_affiliate";
		$where=array('code'=>$code);
		$affiliate_info=$this->mongodb->getBy($collection,$where);
		return $affiliate_info;
	}

	public function getTotalAffiliatesByEmail($email) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		//return $query->row['total'];
		$affiliate_data= array();
		$collection="mongo_affiliate";
		$where=array('email'=>utf8_strtolower($email));
		$affiliate_data=$this->mongodb->gettotal($collection,$where);
		return $affiliate_data;
	}

	public function addTransaction($affiliate_id, $amount = '', $order_id = 0) {
		$affiliate_info = $this->getAffiliate($affiliate_id);

		if ($affiliate_info) {
			$this->event->trigger('pre.affiliate.add.transaction');

			$this->load->language('mail/affiliate');

			//$this->db->query("INSERT INTO " . DB_PREFIX . "affiliate_transaction SET affiliate_id = '" . (int)$affiliate_id . "', order_id = '" . (float)$order_id . "', description = '" . $this->db->escape($this->language->get('text_order_id') . ' #' . $order_id) . "', amount = '" . (float)$amount . "', date_added = NOW()");
			//$affiliate_transaction_id = $this->db->getLastId();
			$collection="mongo_affiliate_transaction";
			$affiliate_transaction_id=1+(int)$this->mongodb->getlastid($collection,'affiliate_transaction_id');
			$newdocument=array('affiliate_transaction_id'=>(int)$affiliate_transaction_id, 'affiliate_id'=>(int)$data['affiliate_id'], 'order_id'=>(float)$order_id, 'description'=>$this->language->get('text_order_id'). ' #' . $order_id, 'amount'=>(float)$amount, 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
			$this->mongodb->create($collection,$newdocument); 

			$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
			$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($affiliate_id), $this->config->get('config_currency')));

			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($affiliate_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject(sprintf($this->language->get('text_transaction_subject'), $this->config->get('config_name')));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();

			$this->event->trigger('post.affiliate.add.transaction', $affiliate_transaction_id);
		}
	}

	public function deleteTransaction($order_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_transaction WHERE order_id = '" . (int)$order_id . "'");
		$collection="mongo_affiliate_transaction";
		$where=array('order_id'=>(int)$order_id);
		$this->mongodb->delete($collection,$where); 
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
	
	public function addLoginAttempt($email) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "affiliate_login WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");
		$collection="mongo_affiliate_login";
		$where=array('email'=>utf8_strtolower((string)$email),'ip'=>$this->request->server['REMOTE_ADDR']);
		$affiliate_login_info=$this->mongodb->getBy($collection,$where);		
		//if (!$query->num_rows) {
		if (!$affiliate_login_info) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "affiliate_login SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', total = 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
			$collection="mongo_affiliate_login";
			$affiliate_login_id=1+(int)$this->mongodb->getlastid($collection,'affiliate_login_id');
			$newdocument=array('affiliate_login_id'=>(int)$affiliate_login_id, 'email'=>utf8_strtolower((string)$email), 'ip'=>$this->request->server['REMOTE_ADDR'], 'total'=>1,'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))),'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
			$this->mongodb->create($collection,$newdocument); 
		} else {
			//$this->db->query("UPDATE " . DB_PREFIX . "affiliate_login SET total = (total + 1), date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE affiliate_login_id = '" . (int)$query->row['affiliate_login_id'] . "'");
			$collection="mongo_affiliate_login";
			$where=array('affiliate_login_id'=>(int)$affiliate_login_info['affiliate_login_id']);
			$info= array('$inc'=> array('total'=>1));
			$this->mongodb->incelement($collection,$where, $info); 
			/////
			$newdocument=array('date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
			$this->mongodb->update($collection,$infoupdate,$where);
		}			
	}	
	
	public function getLoginAttempts($email) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		//return $query->row;
		$collection="mongo_affiliate_login";
		$where=array('email'=>utf8_strtolower($email));
		$affiliate_login_info=$this->mongodb->getBy($collection,$where);
		return $affiliate_login_info;
	}
	
	public function deleteLoginAttempts($email) {
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		$collection="mongo_affiliate_login";
		$where=array('email'=>utf8_strtolower($email));
		$this->mongodb->delete($collection,$where); 
	}	
}