<?php
class ModelAccountOrder extends Model {
	public function getOrder($order_id) {
		//$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND order_status_id > '0'");
		$order_query_data = array();
		$collection="mongo_order";
		$where=array('order_id'=>(int)$order_id, 'customer_id'=>(int)$this->customer->getId(), 'order_status_id'=> array('$gt'=>0));
		$order_query_data=$this->mongodb->getBy($collection,$where);

		//if ($order_query->num_rows) {
		if ($order_query_data) {
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

			return array(/*
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],*/
				'order_id'                => $order_query_data['order_id'],
				'invoice_no'              => $order_query_data['invoice_no'],
				'invoice_prefix'          => $order_query_data['invoice_prefix'],
				'store_id'                => $order_query_data['store_id'],
				'store_name'              => $order_query_data['store_name'],
				'store_url'               => $order_query_data['store_url'],
				'customer_id'             => $order_query_data['customer_id'],
				'firstname'               => $order_query_data['firstname'],
				'lastname'                => $order_query_data['lastname'],
				'telephone'               => $order_query_data['telephone'],
				'fax'                     => $order_query_data['fax'],
				'email'                   => $order_query_data['email'],
				'payment_firstname'       => $order_query_data['payment_firstname'],
				'payment_lastname'        => $order_query_data['payment_lastname'],
				'payment_company'         => $order_query_data['payment_company'],
				'payment_address_1'       => $order_query_data['payment_address_1'],
				'payment_address_2'       => $order_query_data['payment_address_2'],
				'payment_postcode'        => $order_query_data['payment_postcode'],
				'payment_city'            => $order_query_data['payment_city'],
				'payment_zone_id'         => $order_query_data['payment_zone_id'],
				'payment_zone'            => $order_query_data['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,/*
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],*/
				'payment_country_id'      => $order_query_data['payment_country_id'],
				'payment_country'         => $order_query_data['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,/*
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],*/
				'payment_address_format'  => $order_query_data['payment_address_format'],
				'payment_method'          => $order_query_data['payment_method'],
				'shipping_firstname'      => $order_query_data['shipping_firstname'],
				'shipping_lastname'       => $order_query_data['shipping_lastname'],
				'shipping_company'        => $order_query_data['shipping_company'],
				'shipping_address_1'      => $order_query_data['shipping_address_1'],
				'shipping_address_2'      => $order_query_data['shipping_address_2'],
				'shipping_postcode'       => $order_query_data['shipping_postcode'],
				'shipping_city'           => $order_query_data['shipping_city'],
				'shipping_zone_id'        => $order_query_data['shipping_zone_id'],
				'shipping_zone'           => $order_query_data['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,/*
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],*/
				'shipping_country_id'     => $order_query_data['shipping_country_id'],
				'shipping_country'        => $order_query_data['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,/*
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip']*/
				'shipping_address_format' => $order_query_data['shipping_address_format'],
				'shipping_method'         => $order_query_data['shipping_method'],
				'comment'                 => $order_query_data['comment'],
				'total'                   => $order_query_data['total'],
				'order_status_id'         => $order_query_data['order_status_id'],
				'language_id'             => $order_query_data['language_id'],
				'currency_id'             => $order_query_data['currency_id'],
				'currency_code'           => $order_query_data['currency_code'],
				'currency_value'          => $order_query_data['currency_value'],
				'date_modified'           => $order_query_data['date_modified'],
				'date_added'              => $order_query_data['date_added'],
				'ip'                      => $order_query_data['ip']
			);
		} else {
			return false;
		}
	}

	public function getOrders($start = 0, $limit = 20) {
		if ($start < 0) {$start = 0;}
		if ($limit < 1) {$limit = 1;}
		$collection="mongo_order";
		$order_query_data = array();
		$where=array('customer_id'=>(int)$this->customer->getId(), 'order_status_id'=>array('$gt'=>0), 'store_id'=>(int)$this->config->get('config_store_id'));
		$order=array('order_id'=>-1);
		$order_query_list= $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		$collection="mongo_order_status";
		foreach ($order_query_list as $order_query_list_info) {
			$order_status_info= array();
			$where=array('language_id'=>(int)$this->config->get('config_language_id'), 'order_status_id'=>(int)$order_query_list_info['order_status_id']);
			$order_status_info= $this->mongodb->getBy($collection,$where);
			$order_query_data[] = array(
				'order_id'=>$order_query_list_info['order_id'],
				'firstname'=>$order_query_list_info['firstname'],
				'lastname'=>$order_query_list_info['lastname'],
				'status'=>$order_status_info['name'],
				'date_added'=>$order_query_list_info['date_added'],
				'total'=>$order_query_list_info['total'],
				'currency_code'=>$order_query_list_info['currency_code'],
				'currency_value'=>$order_query_list_info['currency_value'],
			);
		} 
		return $order_query_data;
		//$query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);
		//return $query->rows;
	}

	public function getOrderProduct($order_id, $order_product_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
		//return $query->row;
		$collection="mongo_order_product";
		$order_product_info= array();
		$where=array('order_id'=>(int)$order_id, 'order_product_id'=>(int)$order_product_id);
		$order_product_info= $this->mongodb->getBy($collection,$where);
		return $order_product_info;
	}

	public function getOrderProducts($order_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		//return $query->rows;
		$collection="mongo_order_product";$order_product_list= array();
		$where = array('order_id'=>(int)$order_id);
		$order = array();
		$order_product_list = $this->mongodb->getall($collection,$where, $order);
		return $order_product_list;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
		//return $query->rows;
		$collection="mongo_order_option";$order_option_list = array();
		$where = array('order_id'=>(int)$order_id, 'order_product_id'=>(int)$order_product_id);
		$order = array();
		$order_option_list = $this->mongodb->getall($collection,$where, $order);
		return $order_option_list;
	}

	public function getOrderVouchers($order_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");
		//return $query->rows;
		$collection="mongo_order_voucher";$order_voucher_list = array();
		$where = array('order_id'=>(int)$order_id);
		$order = array();
		$order_voucher_list = $this->mongodb->getall($collection,$where, $order);
		return $order_voucher_list;
	}

	public function getOrderTotals($order_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");
		//return $query->rows;
		$collection="mongo_order_total";$order_total_list = array();
		$where = array('order_id'=>(int)$order_id);
		$order = array('sort_order'=>1);
		$order_total_list = $this->mongodb->getall($collection,$where, $order);
		return $order_total_list;
	}

	public function getOrderHistories($order_id) {
		//$query = $this->db->query("SELECT date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added");
		//return $query->rows;
		$collection="mongo_order_history";$order_history_query_list = array();$order_query_data=array();
		$where = array('order_id'=>(int)$order_id);
		$order = array('sort_order'=>1);
		$order_history_query_list = $this->mongodb->getall($collection,$where, $order);
		$collection="mongo_order_status";
		foreach ($order_history_query_list as $order_history_query_list_info) {
			$order_status_info= array();
			$where=array('language_id'=>(int)$this->config->get('config_language_id'), 'order_status_id'=>(int)$order_history_query_list_info['order_status_id']);
			$order_status_info= $this->mongodb->getBy($collection,$where);
			$order_query_data[] = array(
				'notify'=>$order_query_list_info['notify'],
				'comment'=>$order_query_list_info['comment'],
				'status'=>$order_status_info['name'],
				'date_added'=>$order_query_list_info['date_added'],
			);
		} 
		return $order_query_data;
	}

	public function getTotalOrders() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o WHERE customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		//return $query->row['total'];
		$collection="mongo_order";$order_data = array();
		$where=array('customer_id'=>(int)$this->customer->getId(),'store_id'=>(int)$this->config->get('config_store_id'), 'order_status_id'=>array('$gt'=>0));
		$order_data=$this->mongodb->gettotal($collection,$where);
		return $order_data;
	}

	public function getTotalOrderProductsByOrderId($order_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		//return $query->row['total'];
		$collection="mongo_order_product";$order_product_data = array();
		$where=array('order_id'=>(int)$order_id);
		$order_product_data=$this->mongodb->gettotal($collection,$where);
		return $order_product_data;
	}

	public function getTotalOrderVouchersByOrderId($order_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");
		//return $query->row['total'];
		$collection="mongo_order_voucher";$order_voucher_data = array();
		$where=array('order_id'=>(int)$order_id);
		$order_voucher_data=$this->mongodb->gettotal($collection,$where);
		return $order_voucher_data;
	}
}