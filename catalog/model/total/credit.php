<?php
class ModelTotalCredit extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->config->get('credit_status')) {
			$this->load->language('total/credit');

			$balance = $this->customer->getBalance();

			if ((float)$balance) {
				if ($balance > $total) {
					$credit = $total;
				} else {
					$credit = $balance;
				}

				if ($credit > 0) {
					$total_data[] = array(
						'code'       => 'credit',
						'title'      => $this->language->get('text_credit'),
						'value'      => -$credit,
						'sort_order' => $this->config->get('credit_sort_order')
					);

					$total -= $credit;
				}
			}
		}
	}

	public function confirm($order_info, $order_total) {
		$this->load->language('total/credit');

		if ($order_info['customer_id']) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$order_info['customer_id'] . "', order_id = '" . (int)$order_info['order_id'] . "', description = '" . $this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_info['order_id'])) . "', amount = '" . (float)$order_total['value'] . "', date_added = NOW()");
			$collection="mongo_customer_transaction";
			$customer_transaction_id=1+(int)$this->mongodb->getlastid($collection,'customer_transaction_id');
			$newdocument=array('customer_transaction_id'=>(int)$customer_transaction_id, 'customer_id'=>(int)$order_info['customer_id'], 'order_id'=>(int)$order_info['order_id'], 'description'=>sprintf($this->language->get('text_order_id'), (int)$order_info['order_id']), 'amount'=>(float)$order_total['value'],'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
			$this->mongodb->create($collection,$newdocument); 
		}
	}

	public function unconfirm($order_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
		$collection="mongo_customer_transaction";
		$where=array('order_id'=>(int)$order_id);
		$this->mongodb->delete($collection,$where); 
	}
}