<?php
class ModelAffiliateActivity extends Model {
	public function addActivity($key, $data) {
		if (isset($data['affiliate_id'])) {
			$affiliate_id = $data['affiliate_id'];
		} else {
			$affiliate_id = 0;
		}

		//$this->db->query("INSERT INTO " . DB_PREFIX . "affiliate_activity SET `affiliate_id` = '" . (int)$affiliate_id . "', `key` = '" . $this->db->escape($key) . "', `data` = '" . $this->db->escape(serialize($data)) . "', `ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', `date_added` = NOW()");
		$collection="mongo_affiliate_activity";
		$activity_id=1+(int)$this->mongodb->getlastid($collection,'activity_id');
		$where=array('activity_id'=>(int)$activity_id, 'affiliate_id'=>(int)$affiliate_id, 'key'=>$key, 'data'=>serialize($data), 'ip'=>$this->request->server['REMOTE_ADDR'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 
	}
}