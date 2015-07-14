<?php
class ModelCheckoutRecurring extends Model {
	public function create($item, $order_id, $description) {
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "order_recurring` SET `order_id` = '" . (int)$order_id . "', `date_added` = NOW(), `status` = 6, `product_id` = '" . (int)$item['product_id'] . "', `product_name` = '" . $this->db->escape($item['name']) . "', `product_quantity` = '" . $this->db->escape($item['quantity']) . "', `recurring_id` = '" . (int)$item['recurring_id'] . "', `recurring_name` = '" . $this->db->escape($item['recurring_name']) . "', `recurring_description` = '" . $this->db->escape($description) . "', `recurring_frequency` = '" . $this->db->escape($item['recurring_frequency']) . "', `recurring_cycle` = '" . (int)$item['recurring_cycle'] . "', `recurring_duration` = '" . (int)$item['recurring_duration'] . "', `recurring_price` = '" . (float)$item['recurring_price'] . "', `trial` = '" . (int)$item['recurring_trial'] . "', `trial_frequency` = '" . $this->db->escape($item['recurring_trial_frequency']) . "', `trial_cycle` = '" . (int)$item['recurring_trial_cycle'] . "', `trial_duration` = '" . (int)$item['recurring_trial_duration'] . "', `trial_price` = '" . (float)$item['recurring_trial_price'] . "', `reference` = ''");
		//return $this->db->getLastId();
		$collection="mongo_order_recurring";
		$order_recurring_id=1+(int)$this->mongodb->getlastid($collection,'order_recurring_id');
		$newdocument=array('order_recurring_id'=>(int)$order_recurring_id, 'order_id'=>(int)$order_id, 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'status'=>6, 'product_id'=>(int)$item['product_id'], 'product_name'=>$item['name'], 'product_quantity'=>$item['quantity'], 'recurring_id'=>(int)$item['recurring_id'], 'recurring_name'=>$item['recurring_name'], 'recurring_description'=>$item['description'], 'recurring_frequency'=>$item['recurring_frequency'], 'recurring_cycle'=>(int)$item['recurring_cycle'], 'recurring_duration'=>(int)$item['recurring_duration'], 'recurring_price'=>(float)$item['recurring_price'], 'trial'=>(int)$item['recurring_trial'], 'trial_frequency'=>$item['recurring_trial_frequency'], 'trial_cycle'=>(int)$item['recurring_trial_cycle'], 'trial_duration'=>(int)$item['recurring_trial_duration'], 'trial_price'=>(float)$item['recurring_trial_price'], 'reference'=>'');
		$this->mongodb->create($collection,$newdocument); 
	}

	public function addReference($recurring_id, $ref) {
		//$this->db->query("UPDATE " . DB_PREFIX . "order_recurring SET reference = '" . $this->db->escape($ref) . "' WHERE order_recurring_id = '" . (int)$recurring_id . "'");
		$collection="mongo_order_recurring";
		$infoupdate=array('reference'=>$ref);
		$where=array('order_recurring_id'=>(int)$recurring_id);
		$data_result=$this->mongodb->update($collection,$infoupdate,$where);

		//if ($this->db->countAffected() > 0) {
		if ($data_result) {
			return true;
		} else {
			return false;

		}
	}
}
