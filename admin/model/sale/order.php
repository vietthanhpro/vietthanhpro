<?php
class ModelSaleOrder extends Model {
	public function getOrder($order_id) {
		//$order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
		$order_query_data = array();
		$collection="mongo_order";
		$where=array('order_id'=>(int)$order_id);
		$order_query_data=$this->mongodb->getBy($collection,$where);
		//if ($order_query->num_rows) {
		if ($order_query_data) {
			$customer_query_data = array();
			$collection="mongo_customer";
			$where=array('customer_id'=>(int)$order_query_data['customer_id']);
			$customer_query_data=$this->mongodb->getBy($collection,$where);
			
			$reward = 0;
			//$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
			$order_product_query_data=array();
			$collection="mongo_order_product";
			$where=array('order_id'=>(int)$order_id);
			$order=array();
			$order_product_query_data = $this->mongodb->getall($collection,$where, $order);
			//foreach ($order_product_query->rows as $product) {
			foreach ($order_product_query_data as $product) {
				$reward += $product['reward'];
			}
			//$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
			$country_query_data = array();
			$collection="mongo_country";
			$where=array('country_id'=>(int)$order_query_data['payment_country_id']);
			$country_query_data=$this->mongodb->getBy($collection,$where);
			//if ($country_query->num_rows) {
			if ($country_query_data) {
				//$payment_iso_code_2 = $country_query->row['iso_code_2'];
				//$payment_iso_code_3 = $country_query->row['iso_code_3'];
				$payment_iso_code_2 = $country_query_data['iso_code_2'];
				$payment_iso_code_3 = $country_query_data['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}
			//$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
			$zone_query_data = array();
			$collection="mongo_zone";
			$where=array('zone_id'=>(int)$order_query_data['payment_zone_id']);
			$zone_query_data=$this->mongodb->getBy($collection,$where);
			//if ($zone_query->num_rows) {
			if ($zone_query_data) {
				//$payment_zone_code = $zone_query->row['code'];
				$payment_zone_code = $zone_query_data['code'];
			} else {
				$payment_zone_code = '';
			}
			//$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
			$country_query_data = array();
			$collection="mongo_country";
			$where=array('country_id'=>(int)$order_query_data['shipping_country_id']);
			$country_query_data=$this->mongodb->getBy($collection,$where);
			//if ($country_query->num_rows) {
			if ($country_query_data) {
				//$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				//$shipping_iso_code_3 = $country_query->row['iso_code_3'];
				$shipping_iso_code_2 = $country_query_data['iso_code_2'];
				$shipping_iso_code_3 = $country_query_data['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}
			//$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
			$zone_query_data = array();
			$collection="mongo_zone";
			$where=array('zone_id'=>(int)$order_query_data['shipping_zone_id']);
			$zone_query_data=$this->mongodb->getBy($collection,$where);
			//if ($zone_query->num_rows) {
			if ($zone_query_data) {
				//$shipping_zone_code = $zone_query->row['code'];
				$shipping_zone_code = $zone_query_data['code'];
			} else {
				$shipping_zone_code = '';
			}
			//if ($order_query->row['affiliate_id']) {
			if ($order_query_data['affiliate_id']) {
				//$affiliate_id = $order_query->row['affiliate_id'];
				$affiliate_id = $order_query_data['affiliate_id'];
			} else {
				$affiliate_id = 0;
			}
			$this->load->model('marketing/affiliate');
			$affiliate_info = $this->model_marketing_affiliate->getAffiliate($affiliate_id);
			if ($affiliate_info) {
				$affiliate_firstname = $affiliate_info['firstname'];
				$affiliate_lastname = $affiliate_info['lastname'];
			} else {
				$affiliate_firstname = '';
				$affiliate_lastname = '';
			}
			$this->load->model('localisation/language');
			//$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);
			$language_info = $this->model_localisation_language->getLanguage($order_query_data['language_id']);
			if ($language_info) {
				$language_code = $language_info['code'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code = '';
				$language_directory = '';
			}
			return array(
				'order_id'                => $order_query_data['order_id'],
				'invoice_no'              => $order_query_data['invoice_no'],
				'invoice_prefix'          => $order_query_data['invoice_prefix'],
				'store_id'                => $order_query_data['store_id'],
				'store_name'              => $order_query_data['store_name'],
				'store_url'               => $order_query_data['store_url'],
				'customer_id'             => $order_query_data['customer_id'],
				'customer'                => trim($order_query_data['firstname'].' '.$order_query_data['lastname']),
				'customer_group_id'       => $order_query_data['customer_group_id'],
				'firstname'               => $order_query_data['firstname'],
				'lastname'                => $order_query_data['lastname'],
				'email'                   => $order_query_data['email'],
				'telephone'               => $order_query_data['telephone'],
				'fax'                     => $order_query_data['fax'],
				'custom_field'            => unserialize($order_query_data['custom_field']),
				'payment_firstname'       => $order_query_data['payment_firstname'],
				'payment_lastname'        => $order_query_data['payment_lastname'],
				'payment_company'         => $order_query_data['payment_company'],
				'payment_address_1'       => $order_query_data['payment_address_1'],
				'payment_address_2'       => $order_query_data['payment_address_2'],
				'payment_postcode'        => $order_query_data['payment_postcode'],
				'payment_city'            => $order_query_data['payment_city'],
				'payment_zone_id'         => $order_query_data['payment_zone_id'],
				'payment_zone'            => $order_query_data['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query_data['payment_country_id'],
				'payment_country'         => $order_query_data['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query_data['payment_address_format'],
				'payment_custom_field'    => unserialize($order_query_data['payment_custom_field']),
				'payment_method'          => $order_query_data['payment_method'],
				'payment_code'            => $order_query_data['payment_code'],
				'shipping_firstname'      => $order_query_data['shipping_firstname'],
				'shipping_lastname'       => $order_query_data['shipping_lastname'],
				'shipping_company'        => $order_query_data['shipping_company'],
				'shipping_address_1'      => $order_query_data['shipping_address_1'],
				'shipping_address_2'      => $order_query_data['shipping_address_2'],
				'shipping_postcode'       => $order_query_data['shipping_postcode'],
				'shipping_city'           => $order_query_data['shipping_city'],
				'shipping_zone_id'        => $order_query_data['shipping_zone_id'],
				'shipping_zone'           => $order_query_data['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query_data['shipping_country_id'],
				'shipping_country'        => $order_query_data['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query_data['shipping_address_format'],
				'shipping_custom_field'   => unserialize($order_query_data['shipping_custom_field']),
				'shipping_method'         => $order_query_data['shipping_method'],
				'shipping_code'           => $order_query_data['shipping_code'],
				'comment'                 => $order_query_data['comment'],
				'total'                   => $order_query_data['total'],
				'reward'                  => $reward,
				'order_status_id'         => $order_query_data['order_status_id'],
				'affiliate_id'            => $order_query_data['affiliate_id'],
				'affiliate_firstname'     => $affiliate_firstname,
				'affiliate_lastname'      => $affiliate_lastname,
				'commission'              => $order_query_data['commission'],
				'language_id'             => $order_query_data['language_id'],
				'language_code'           => $language_code,
				'language_directory'      => $language_directory,
				'currency_id'             => $order_query_data['currency_id'],
				'currency_code'           => $order_query_data['currency_code'],
				'currency_value'          => $order_query_data['currency_value'],
				'ip'                      => $order_query_data['ip'],
				'forwarded_ip'            => $order_query_data['forwarded_ip'],
				'user_agent'              => $order_query_data['user_agent'],
				'accept_language'         => $order_query_data['accept_language'],
				'date_added'              => $order_query_data['date_added'],
				'date_modified'           => $order_query_data['date_modified']
			);
		} else {
			return;
		}
	}

	public function getOrders($data = array()) {
		//$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";
		$order_query_data = array();
		$collection="mongo_order";
		$where=array();		
		if (isset($data['filter_order_status'])) {
			//$implode = array();
			$order_status_id_array= array();
			$order_statuses = explode(',', $data['filter_order_status']);
			foreach ($order_statuses as $order_status_id) {
				//$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
				$order_status_id_array[]=(int)$order_status_id;
			}
			//if ($implode) {
			if ($order_status_id_array) {	
				//$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
				$where['order_status_id']= array('$in'=>$order_status_id_array);
			} else {
				$where['order_status_id']=array('$gt'=>0);
			}
		} else {
			//$sql .= " WHERE o.order_status_id > '0'";
			$where['order_status_id']=array('$gt'=>0);
		}
		if (!empty($data['filter_order_id'])) {
			//$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
			$where['order_id']=(int)$data['filter_order_id'];
		}
		if (!empty($data['filter_customer'])) {
			//$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
			$where['firstname']=new MongoRegex('/'.$data['filter_customer'].'/');
		}
		if (!empty($data['filter_date_added'])) {
			//$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		if (!empty($data['filter_date_modified'])) {
			//$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
			$where['date_modified']=array('$gte'=>new MongoDate(strtotime($data['filter_date_modified'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_modified'].' 23:59:59')));
		}
		if (!empty($data['filter_total'])) {
			//$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
			$where['total']=(int)$data['filter_total'];
		}
		$sort_data = array(
			'order_id',
			'firstname',
			'order_status_id',
			'date_added',
			'date_modified',
			'total'
		);
		$order=array();
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			//$sql .= " ORDER BY " . $data['sort'];
			$orderby = $data['sort'];
		} else {
			//$sql .= " ORDER BY o.order_id";
			$orderby = 'order_id';
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
			$start=$data['start'];
			$limit=$data['limit'];
		}
		$order_query_data_list = array();
		$order_query_data_list = $this->mongodb->getlimit($collection,$where, $order, $start, $limit); 
		$this->load->model('localisation/order_status');
		foreach ($order_query_data_list as $order_query_data_list_info) {
			$order_status_info= array();
			$order_status_info=$this->model_localisation_order_status->getOrderStatus((int)$order_query_data_list_info['order_status_id']);
			$order_query_data[] = array(
				'order_id'=>$order_query_data_list_info['order_id'],
				'customer'=>trim($order_query_data_list_info['firstname'].' '.$order_query_data_list_info['lastname']),
				'status'=>$order_status_info['name'],
				'shipping_code'=>$order_query_data_list_info['shipping_code'],
				'total'=>$order_query_data_list_info['total'],
				'currency_code'=>$order_query_data_list_info['currency_code'],
				'currency_value'=>$order_query_data_list_info['currency_value'],
				'date_added'=>$order_query_data_list_info['date_added'],
				'date_modified'=>$order_query_data_list_info['date_modified'],
			);
		}
		return $order_query_data;
		//$query = $this->db->query($sql);
		//return $query->rows;
	}

	public function getTotalOrders($data = array()) {
		//$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`";
		$collection="mongo_order";
		$where=array();
		if (isset($data['filter_order_status'])) {
			//$implode = array();
			$order_status_id_array= array();
			$order_statuses = explode(',', $data['filter_order_status']);
			foreach ($order_statuses as $order_status_id) {
				//$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
				$order_status_id_array[]=(int)$order_status_id;
			}
			//if ($implode) {
			if ($order_status_id_array) {	
				//$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
				$where['order_status_id']= array('$in'=>$order_status_id_array);
			} else {
				$where['order_status_id']=array('$gt'=>0);
			}
		} else {
			//$sql .= " WHERE o.order_status_id > '0'";
			$where['order_status_id']=array('$gt'=>0);
		}
		if (!empty($data['filter_order_id'])) {
			//$sql .= " AND order_id = '" . (int)$data['filter_order_id'] . "'";
			$where['order_id']=(int)$data['filter_order_id'];
		}
		if (!empty($data['filter_customer'])) {
			//$sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
			$where['firstname']=new MongoRegex('/'.$data['filter_customer'].'/');
		}
		if (!empty($data['filter_date_added'])) {
			//$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		if (!empty($data['filter_date_modified'])) {
			//$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
			$where['date_modified']=array('$gte'=>new MongoDate(strtotime($data['filter_date_modified'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_modified'].' 23:59:59')));
		}
		if (!empty($data['filter_total'])) {
			//$sql .= " AND total = '" . (float)$data['filter_total'] . "'";
			$where['total']=(int)$data['filter_total'];
		}
		//$query = $this->db->query($sql);
		//return $query->row['total'];
		$order_data=$this->mongodb->gettotal($collection,$where);
		return $order_data;
	}

	public function getOrderProducts($order_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		//return $query->rows;
		$collection="mongo_order_product";
		$where=array('order_id'=>(int)$order_id);
		$order=array();
		$query_data = $this->mongodb->getall($collection,$where, $order);
		return $query_data;
	}

	public function getOrderOption($order_id, $order_option_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_option_id = '" . (int)$order_option_id . "'");
		//return $query->row;
		$order_option_query_info = array();
		$collection="mongo_order_option";
		$where=array('order_id'=>(int)$order_id, 'order_option_id'=>(int)$order_option_id);
		$order_option_query_info=$this->mongodb->getBy($collection,$where);
		return $order_option_query_info;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
		//return $query->rows;
		$collection="mongo_order_option";
		$where=array('order_id'=>(int)$order_id, 'order_product_id'=>(int)$order_product_id);
		$order=array();
		$query_data = $this->mongodb->getall($collection,$where, $order);
		return $query_data;
	}

	public function getOrderVouchers($order_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
		//return $query->rows;
		$collection="mongo_order_voucher";
		$where=array('order_id'=>(int)$order_id);
		$order=array();
		$query_data = $this->mongodb->getall($collection,$where, $order);
		return $query_data;
	}

	public function getOrderVoucherByVoucherId($voucher_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE voucher_id = '" . (int)$voucher_id . "'");
		//return $query->row;
		$collection="mongo_order_voucher";
		$where=array('voucher_id'=>(int)$voucher_id);
		$order=array();
		$query_data = $this->mongodb->getall($collection,$where, $order);
		return $query_data;
	}

	public function getOrderTotals($order_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");
		//return $query->rows;
		$collection="mongo_order_total";
		$where=array('order_id'=>(int)$order_id);
		$order=array('sort_order'=>1);
		$query_data = $this->mongodb->getall($collection,$where, $order);
		return $query_data;
	}

	public function getTotalOrdersByStoreId($store_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE store_id = '" . (int)$store_id . "'");
		//return $query->row['total'];
		$order_data = array();
		$collection="mongo_order";
		$where=array('store_id'=>(int)$store_id);
		$order_data=$this->mongodb->gettotal($collection,$where);
		return $order_data;
	}

	public function getTotalOrdersByOrderStatusId($order_status_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$order_status_id . "' AND order_status_id > '0'");
		//return $query->row['total'];
		$order_data = array();
		$collection="mongo_order";
		$where=array('order_status_id'=>(int)$order_status_id);
		$order_data=$this->mongodb->gettotal($collection,$where);
		return $order_data;
	}

	public function getTotalOrdersByProcessingStatus() {
		//$implode = array();
		$order_status_id_array=array();
		$order_statuses = $this->config->get('config_processing_status');
		foreach ($order_statuses as $order_status_id) {
			//$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
			$order_status_id_array[]=(int)$order_status_id;
		}
		//if ($implode) {
		if ($order_status_id_array) {
			//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode));
			//return $query->row['total'];
			$collection="mongo_order";
			$where=array('order_status_id' => array('$in'=>$order_status_id_array));
			$order_data=$this->mongodb->gettotal($collection,$where);
			return $order_data;
		} else {
			return 0;
		}
	}

	public function getTotalOrdersByCompleteStatus() {
		//$implode = array();
		$order_status_id_array=array();
		$order_statuses = $this->config->get('config_complete_status');
		foreach ($order_statuses as $order_status_id) {
			//$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
			$order_status_id_array[]=(int)$order_status_id;
		}
		//if ($implode) {
		if ($order_status_id_array) {
			//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode) . "");
			//return $query->row['total'];
			$collection="mongo_order";
			$where=array('order_status_id' => array('$in'=>$order_status_id_array));
			$order_data=$this->mongodb->gettotal($collection,$where);
			return $order_data;
		} else {
			return 0;
		}
	}

	public function getTotalOrdersByLanguageId($language_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE language_id = '" . (int)$language_id . "' AND order_status_id > '0'");
		//return $query->row['total'];
		$order_data = array();
		$collection="mongo_order";
		$where=array('language_id'=>(int)$language_id,'order_status_id'=>array('$gt'=>0));
		$order_data=$this->mongodb->gettotal($collection,$where);
		return $order_data;
	}

	public function getTotalOrdersByCurrencyId($currency_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE currency_id = '" . (int)$currency_id . "' AND order_status_id > '0'");
		//return $query->row['total'];
		$order_data = array();
		$collection="mongo_order";
		$where=array('currency_id'=>(int)$currency_id,'order_status_id'=>array('$gt'=>0));
		$order_data=$this->mongodb->gettotal($collection,$where);
		return $order_data;
	}

	public function createInvoiceNo($order_id) {
		$order_info = $this->getOrder($order_id);
		if ($order_info && !$order_info['invoice_no']) {
			//$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");			
			$collection="mongo_order";
			$match=array('$match'=> array('invoice_prefix'=>$order_info['invoice_prefix']));
			$group=array('$group'=> array('_id'=>'','ketqua'=>array('$max' => '$points')));		
			$max_invoice_no_data=$this->mongodb->getaggregate($collection, $match, $group);
			//if ($query->row['invoice_no']) {
			if ($max_invoice_no_data) {
				$invoice_no = (int)$max_invoice_no_data + 1;
			} else {
				$invoice_no = 1;
			}
			//$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int)$order_id . "'");
			$collection="mongo_order";
			$infoupdate=array('invoice_no'=>(int)$invoice_no, 'invoice_prefix'=>$order_info['invoice_prefix']);
			$where=array('order_id'=>(int)$order_id);
			$this->mongodb->update($collection,$infoupdate,$where);
			return $order_info['invoice_prefix'] . $invoice_no;
		}
	}

	public function getOrderHistories($order_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 10;
		}
		//$query = $this->db->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);
		//return $query->rows;
		$query_data = array();$order_history=array();
		$collection="mongo_order_history";
		$where=array('order_id'=>(int)$order_id);
		$order=array('date_added'=>1);
		$this->load->model('localisation/order_status');
		$query_data= $this->mongodb->getlimit($collection,$where, $order, (int)$start, (int)$limit);
		foreach ($query_data as $query_data_info) {
			$order_status_info= array();
			$order_status_info=$this->model_localisation_order_status->getOrderStatus($query_data_info['order_status_id']);
			$order_history[]=array(
				'date_added'=>$query_data_info['date_added'],
				'status'=>$order_status_info['name'],
				'comment'=>$query_data_info['comment'],
				'notify'=>$query_data_info['notify'],
			);
		}
		return $order_history;
	}

	public function getTotalOrderHistories($order_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
		//return $query->row['total'];
		$order_history_data = array();
		$collection="mongo_order_history";
		$where=array('order_id'=>(int)$order_id);
		$order_history_data=$this->mongodb->gettotal($collection,$where);
		return $order_history_data;
	}

	public function getTotalOrderHistoriesByOrderStatusId($order_status_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int)$order_status_id . "'");
		//return $query->row['total'];
		$order_history_data = array();
		$collection="mongo_order_history";
		$where=array('order_status_id'=>(int)$order_status_id);
		$order_history_data=$this->mongodb->gettotal($collection,$where);
		return $order_history_data;
	}

	public function getEmailsByProductsOrdered($products, $start, $end) {		
		$product_id_array = array();
		$order_id_array = array();
		$order_productquery_data=array();
		foreach ($products as $product_id) {
			$product_id_array[]=(int)$product_id;
		}
		$collection="mongo_order_product";
		$where=array('product_id'=>array('$in'=>$product_id_array));
		$order=array();
		$order_productquery_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($order_productquery_data as $order_productquery_data_info) {
			$order_id_array[]=(int)$order_productquery_data_info['order_id'];
		}
		$order_id_array=array_unique($order_id_array);
		$email_array=array();
		$collection="mongo_order";
		$where=array('order_id'=>array('$in'=>$order_id_array));
		$order=array();
		$order_query_data_info = $this->mongodb->getall($collection,$where, $order);
		foreach ($order_query_data_info as $order_query_data_info) {
			$email_array[]=(int)$order_query_data_info['email'];
		}
		$email_array=array_unique($email_array);
		return array_slice($email_array, $start, $end);
	}

	public function getTotalEmailsByProductsOrdered($products) {
		//$implode = array();
		$product_id_array = array();
		$order_id_array = array();
		$order_productquery_data=array();
		foreach ($products as $product_id) {
			//$implode[] = "op.product_id = '" . (int)$product_id . "'";
			$product_id_array[]=(int)$product_id;
		}
		$collection="mongo_order_product";
		$where=array('product_id'=>array('$in'=>$product_id_array));
		$order=array();
		$order_productquery_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($order_productquery_data as $order_productquery_data_info) {
			$order_id_array[]=(int)$order_productquery_data_info['order_id'];
		}
		$order_id_array=array_unique($order_id_array);
		//$query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");
		//return $query->row['total'];
		$email_array=array();
		$collection="mongo_order";
		$where=array('order_id'=>array('$in'=>$order_id_array));
		$order=array();
		$order_query_data_info = $this->mongodb->getall($collection,$where, $order);
		foreach ($order_query_data_info as $order_query_data_info) {
			$email_array[]=(int)$order_query_data_info['email'];
		}
		$email_array=array_unique($email_array);
		return count($email_array);
	}
}