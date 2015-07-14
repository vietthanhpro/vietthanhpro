<?php
class ModelCheckoutVoucher extends Model {
	public function addVoucher($order_id, $data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "voucher SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($data['code']) . "', from_name = '" . $this->db->escape($data['from_name']) . "', from_email = '" . $this->db->escape($data['from_email']) . "', to_name = '" . $this->db->escape($data['to_name']) . "', to_email = '" . $this->db->escape($data['to_email']) . "', voucher_theme_id = '" . (int)$data['voucher_theme_id'] . "', message = '" . $this->db->escape($data['message']) . "', amount = '" . (float)$data['amount'] . "', status = '1', date_added = NOW()");
		//return $this->db->getLastId();
		$collection="mongo_voucher";
		$voucher_id=1+(int)$this->mongodb->getlastid($collection,'voucher_id');
		$where=array('voucher_id'=>(int)$voucher_id, 'order_id'=>(int)$order_id, 'code'=>$data['code'], 'from_name'=>$data['from_name'], 'from_email'=>$data['from_email'], 'to_name'=>$data['to_name'], 'to_email'=>$data['to_email'], 'voucher_theme_id'=>(int)$data['voucher_theme_id'], 'message'=>$data['message'], 'amount'=>(float)$data['amount'], 'status'=>1, 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 
	}
	
	public function disableVoucher($order_id) {
		//$this->db->query("UPDATE " . DB_PREFIX . "voucher SET status = '0' WHERE order_id = '" . (int)$order_id . "'");
		$collection="mongo_voucher";
		$infoupdate=array('status'=>0);
		$where=array('order_id'=>(int)$order_id);
		$this->mongodb->update($collection,$infoupdate,$where);
	}
	
	public function getVoucher($code) {
		$status = true;
		//$voucher_query = $this->db->query("SELECT *, vtd.name AS theme FROM " . DB_PREFIX . "voucher v LEFT JOIN " . DB_PREFIX . "voucher_theme vt ON (v.voucher_theme_id = vt.voucher_theme_id) LEFT JOIN " . DB_PREFIX . "voucher_theme_description vtd ON (vt.voucher_theme_id = vtd.voucher_theme_id) WHERE v.code = '" . $this->db->escape($code) . "' AND vtd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND v.status = '1'");
		$collection="mongo_voucher";
		$where=array('code'=>$code, 'status'=>1);
		$voucher_query_data= $this->mongodb->getBy($collection,$where);

		//if ($voucher_query->num_rows) {
		if ($voucher_query_data) {
			//if ($voucher_query->row['order_id']) {
			if ($voucher_query_data['order_id']) {
				//$implode = array();
	
				//foreach ($this->config->get('config_complete_status') as $order_status_id) {
					//$implode[] = "'" . (int)$order_status_id . "'";
				//}
				$order_status_id_array=$this->config->get('config_complete_status');
				
				//$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$voucher_query->row['order_id'] . "' AND order_status_id IN(" . implode(",", $implode) . ")");
				$collection="mongo_order";
				$where=array('order_id'=>(int)$voucher_query_data['order_id'], 'order_status_id' => array('$in'=>$order_status_id_array));
				$order_query_data= $this->mongodb->getBy($collection,$where);

				//if (!$order_query->num_rows) {
				if (!$order_query_data) {
					$status = false;
				}

				//$order_voucher_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$voucher_query->row['order_id'] . "' AND voucher_id = '" . (int)$voucher_query->row['voucher_id'] . "'");
				$collection="mongo_order_voucher";
				$where=array('order_id'=>(int)$voucher_query_data['order_id'], 'voucher_id' =>(int)$voucher_query_data['voucher_id']);
				$order_voucher_query_data= $this->mongodb->getBy($collection,$where);

				//if (!$order_voucher_query->num_rows) {
				if (!$order_voucher_query_data) {
					$status = false;
				}
			}

			//$voucher_history_query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "voucher_history` vh WHERE vh.voucher_id = '" . (int)$voucher_query->row['voucher_id'] . "' GROUP BY vh.voucher_id");
			$collection="mongo_voucher_history";
			$match=array('$match'=> array('voucher_id'=>(int)$voucher_query->row['voucher_id']));
			$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$amount')));		
			$voucher_history_query_data=$this->mongodb->getaggregate($collection, $match, $group);

			//if ($voucher_history_query->num_rows) {
			if ($voucher_history_query_data) {
				//$amount = $voucher_query->row['amount'] + $voucher_history_query->row['total'];
				$amount = $voucher_query_data['amount'] + $voucher_query_data['total'];
			} else {
				//$amount = $voucher_query->row['amount'];
				$amount = $voucher_query_data['amount'];
			}

			if ($amount <= 0) {
				$status = false;
			}
		} else {
			$status = false;
		}
		$this->load->model('checkout/voucher_theme');
		$voucher_theme_info = $this->model_checkout_voucher_theme->getVoucherTheme($voucher_query_data['voucher_theme_id']);

		if ($status) {
			return array(/*
				'voucher_id'       => $voucher_query->row['voucher_id'],
				'code'             => $voucher_query->row['code'],
				'from_name'        => $voucher_query->row['from_name'],
				'from_email'       => $voucher_query->row['from_email'],
				'to_name'          => $voucher_query->row['to_name'],
				'to_email'         => $voucher_query->row['to_email'],
				'voucher_theme_id' => $voucher_query->row['voucher_theme_id'],
				'theme'            => $voucher_query->row['theme'],
				'message'          => $voucher_query->row['message'],
				'image'            => $voucher_query->row['image'],
				'status'           => $voucher_query->row['status'],
				'date_added'       => $voucher_query->row['date_added']*/
				'voucher_id'       => $voucher_query_data['voucher_id'],
				'code'             => $voucher_query_data['code'],
				'from_name'        => $voucher_query_data['from_name'],
				'from_email'       => $voucher_query_data['from_email'],
				'to_name'          => $voucher_query_data['to_name'],
				'to_email'         => $voucher_query_data['to_email'],
				'voucher_theme_id' => $voucher_query_data['voucher_theme_id'],
				'theme'            => $voucher_theme_info['voucher_theme_description'][(int)$this->config->get('config_language_id')]['name'],
				'message'          => $voucher_query_data['message'],
				'image'            => $voucher_theme_info['image'],
				'amount'           => $amount,
				'status'           => $voucher_query_data['status'],
				'date_added'       => $voucher_query_data['date_added']
			);
		}
	}

	public function confirm($order_id) {
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if ($order_info) {
			$this->load->model('localisation/language');

			$language = new Language($order_info['language_directory']);
			$language->load('default');
			$language->load('mail/voucher');

			//$voucher_query = $this->db->query("SELECT *, vtd.name AS theme FROM `" . DB_PREFIX . "voucher` v LEFT JOIN " . DB_PREFIX . "voucher_theme vt ON (v.voucher_theme_id = vt.voucher_theme_id) LEFT JOIN " . DB_PREFIX . "voucher_theme_description vtd ON (vt.voucher_theme_id = vtd.voucher_theme_id) AND vtd.language_id = '" . (int)$order_info['language_id'] . "' WHERE v.order_id = '" . (int)$order_id . "'");
			$collection="mongo_voucher";
			$where=array();
			$order=array('order_id'=> (int)$order_id);
			$voucher_query_data= $this->mongodb->getall($collection,$where, $order);

			//foreach ($voucher_query->rows as $voucher) {
			foreach ($voucher_query_data as $voucher) {
				$this->load->model('checkout/voucher_theme');
				$voucher_theme_info = $this->model_checkout_voucher_theme->getVoucherTheme($voucher['voucher_theme_id']);
				// HTML Mail
				$data = array();

				$data['title'] = sprintf($language->get('text_subject'), $voucher['from_name']);

				$data['text_greeting'] = sprintf($language->get('text_greeting'), $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']));
				$data['text_from'] = sprintf($language->get('text_from'), $voucher['from_name']);
				$data['text_message'] = $language->get('text_message');
				$data['text_redeem'] = sprintf($language->get('text_redeem'), $voucher['code']);
				$data['text_footer'] = $language->get('text_footer');

				if (is_file(DIR_IMAGE . $voucher_theme_info['image'])) {
					$data['image'] = $this->config->get('config_url') . 'image/' . $voucher_theme_info['image'];
				} else {
					$data['image'] = '';
				}

				$data['store_name'] = $order_info['store_name'];
				$data['store_url'] = $order_info['store_url'];
				$data['message'] = nl2br($voucher['message']);

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/voucher.tpl')) {
					$html = $this->load->view($this->config->get('config_template') . '/template/mail/voucher.tpl', $data);
				} else {
					$html = $this->load->view('default/template/mail/voucher.tpl', $data);
				}

				$mail = new Mail($this->config->get('config_mail'));
				$mail->setTo($voucher['to_email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($order_info['store_name']);
				$mail->setSubject(sprintf($language->get('text_subject'), $voucher['from_name']));
				$mail->setHtml($html);
				$mail->send();
			}
		}
	}
}