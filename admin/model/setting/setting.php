<?php
class ModelSettingSetting extends Model {
	public function getSetting($code, $store_id = 0) {
		$setting_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");
		$collection="mongo_setting";
		$where=array('store_id'=>(int)$store_id, 'code'=>$code);
		$setting_info = $this->mongodb->getBy($collection,$where);

		foreach ($setting_info as $result) {
		//foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$setting_data[$result['key']] = $result['value'];
			} else {
				$setting_data[$result['key']] = unserialize($result['value']);
			}
		}

		return $setting_data;
	}

	public function editSetting($code, $data, $store_id = 0) { //print_r($data); die();
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");
		$collection="mongo_setting";
		$where=array('store_id'=>(int)$store_id, 'code'=>$code);
		$this->mongodb->delete($collection,$where); //echo $code; die();

		foreach ($data as $key => $value) { 
			if (substr($key, 0, strlen($code)) == $code) {
				$setting_id=1+(int)$this->mongodb->getlastid($collection,'setting_id');
				if (!is_array($value)) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
					$where=array('setting_id'=>(int)$setting_id, 'store_id'=>(int)$store_id, 'code'=>$code, 'key'=>$key, 'value'=>$value, 'serialized'=>0);
				} else {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
					$where=array('setting_id'=>(int)$setting_id, 'store_id'=>(int)$store_id, 'code'=>$code, 'key'=>$key, 'value'=>serialize($value), 'serialized'=>1);
				}
				$this->mongodb->create($collection,$where); 
			}
		}
	}

	public function deleteSetting($code, $store_id = 0) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");
		$collection="mongo_setting";
		$where=array('store_id'=>(int)$store_id, 'code'=>$code);
		$this->mongodb->delete($collection,$where); 
	}

	public function editSettingValue($code = '', $key = '', $value = '', $store_id = 0) {
		$collection="mongo_setting";
		if (!is_array($value)) {
			//$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape($value) . "' WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
			$infoupdate=array('value'=>$value);
		} else {
			//$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1' WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
			$infoupdate=array('value'=>serialize($value), 'serialized'=>1);
		}
		$where=array('code'=>$code, 'key'=>$key, 'store_id'=>(int)$store_id);
		$this->mongodb->update($collection,$infoupdate,$where); 
	}
}
