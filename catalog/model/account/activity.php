<?php
class ModelAccountActivity extends Model {
	public function addActivity($key, $data) {
		if (isset($data['customer_id'])) {
			$customer_id = $data['customer_id'];
		} else {
			$customer_id = 0;
		}

		//$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_activity` SET `customer_id` = '" . (int)$customer_id . "', `key` = '" . $this->db->escape($key) . "', `data` = '" . $this->db->escape(serialize($data)) . "', `ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', `date_added` = NOW()");
		$collection="mongo_customer_activity";
		$activity_id=1+(int)$this->mongodb->getlastid($collection,'activity_id');
		$newdocument=array('activity_id'=>(int)$activity_id, 'customer_id'=>(int)$data['customer_id'], 'key'=>$key, 'data'=>serialize($data), 'ip'=>$this->request->server['REMOTE_ADDR'],'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 
	}
}