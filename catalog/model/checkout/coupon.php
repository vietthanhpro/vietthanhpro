<?php
class ModelCheckoutCoupon extends Model {
	public function getCoupon($code) {
		$status = true;

		//$coupon_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code = '" . $this->db->escape($code) . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");
		$coupon_query_data = array();
		$collection="mongo_coupon";
		$where=array('code'=>$code, 'date_added'=>array('$lt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'date_modified'=>array('$gt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))));
		$coupon_query_data=$this->mongodb->getBy($collection,$where);
		//if ($coupon_query->num_rows) {
		if ($coupon_query_data) {
			//if ($coupon_query_data['total'] > $this->cart->getSubTotal()) {
			if ($coupon_query_data['total'] > $this->cart->getSubTotal()) {
				$status = false;
			}
			//$coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query_data['coupon_id'] . "'");
			$coupon_history_query_data=array();
			$collection="mongo_coupon_history";
			$where=array('coupon_id'=>(int)$coupon_query_data['coupon_id']);
			$coupon_history_query_data=$this->mongodb->gettotal($collection,$where);

			//if ($coupon_query_data['uses_total'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query_data['uses_total'])) {
			if ($coupon_query_data['uses_total'] > 0 && ($coupon_history_query_data >= $coupon_query_data['uses_total'])) {
				$status = false;
			}

			if ($coupon_query_data['logged'] && !$this->customer->getId()) {
				$status = false;
			}

			if ($this->customer->getId()) {
				//$coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query_data['coupon_id'] . "' AND ch.customer_id = '" . (int)$this->customer->getId() . "'");
				$coupon_history_query_data=array();
				$collection="mongo_coupon_history";
				$where=array('coupon_id'=>(int)$coupon_query_data['coupon_id']);
				$coupon_history_query_data=$this->mongodb->gettotal($collection,$where);

				//if ($coupon_query_data['uses_customer'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query_data['uses_customer'])) {
				if ($coupon_query_data['uses_customer'] > 0 && ($coupon_history_query_data >= $coupon_query_data['uses_customer'])) {
					$status = false;
				}
			}

			// Products
			$coupon_product_data = array();

			//$coupon_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon_product` WHERE coupon_id = '" . (int)$coupon_query_data['coupon_id'] . "'");
			$coupon_product_query_list=array();
			$collection="mongo_coupon_product";
			$where=array('coupon_id'=>(int)$coupon_query_data['coupon_id']);
			$order=array();
			$coupon_product_query_list=$this->mongodb->getall($collection,$where, $order);
			//foreach ($coupon_product_query->rows as $product) {
			foreach ($coupon_product_query_list as $product) {
				$coupon_product_data[] = $product['product_id'];
			}

			// Categories
			$coupon_category_data = array();
			//$coupon_category_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon_category` cc LEFT JOIN `" . DB_PREFIX . "category_path` cp ON (cc.category_id = cp.path_id) WHERE cc.coupon_id = '" . (int)$coupon_query_data['coupon_id'] . "'");
			$coupon_category_query_list=array();
			$collection="mongo_coupon_product";
			$where=array('coupon_id'=>(int)$coupon_query_data['coupon_id']);
			$order=array();
			$coupon_category_query_list=$this->mongodb->getall($collection,$where, $order);
			//foreach ($coupon_category_query->rows as $category) {
			foreach ($coupon_category_query_list as $category) {
				$coupon_category_data[] = $category['category_id'];
			}

			$product_data = array();

			if ($coupon_product_data || $coupon_category_data) {
				foreach ($this->cart->getProducts() as $product) {
					if (in_array($product['product_id'], $coupon_product_data)) {
						$product_data[] = $product['product_id'];

						continue;
					}

					foreach ($coupon_category_data as $category_id) {
						//$coupon_category_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int)$product['product_id'] . "' AND category_id = '" . (int)$category_id . "'");
						$collection="mongo_product";$coupon_category_query_info=array();
						$where=array('product_id'=>(int)$product['product_id'], 'product_category'=>(int)$category_id);
						$coupon_category_query_info=$this->mongodb->getBy($collection,$where);
						//if ($coupon_category_query->row['total']) {
						if ($coupon_category_query_info) {
							$product_data[] = $product['product_id'];

							continue;
						}
					}
				}

				if (!$product_data) {
					$status = false;
				}
			}
		} else {
			$status = false;
		}

		if ($status) {
			return array(
				'coupon_id'     => $coupon_query_data['coupon_id'],
				'code'          => $coupon_query_data['code'],
				'name'          => $coupon_query_data['name'],
				'type'          => $coupon_query_data['type'],
				'discount'      => $coupon_query_data['discount'],
				'shipping'      => $coupon_query_data['shipping'],
				'total'         => $coupon_query_data['total'],
				'product'       => $product_data,
				'date_start'    => $coupon_query_data['date_start'],
				'date_end'      => $coupon_query_data['date_end'],
				'uses_total'    => $coupon_query_data['uses_total'],
				'uses_customer' => $coupon_query_data['uses_customer'],
				'status'        => $coupon_query_data['status'],
				'date_added'    => $coupon_query_data['date_added']
			);
		}
	}
}