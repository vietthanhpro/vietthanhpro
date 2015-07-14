<?php
class ModelExtensionExtension extends Model {
	public function getInstalled($type) {
		$extension_data = array();
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "' ORDER BY code");
		$collection="mongo_extension";
		$where=array('type'=> $type);
		$order=array('code'=> 1);
		$extension_list= $this->mongodb->getall($collection,$where, $order);
		//foreach ($query->rows as $result) {
		foreach ($extension_list as $result) {
			$extension_data[] = $result['code'];
		}
		return $extension_data;
	}

	public function install($type, $code) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "extension SET `type` = '" . $this->db->escape($type) . "', `code` = '" . $this->db->escape($code) . "'");
		$collection="mongo_extension";
		$extension_id=1+(int)$this->mongodb->getlastid($collection,'extension_id');
		$where=array('extension_id'=>(int)$extension_id, 'type'=>(string)$type, 'code'=>(string)$code);
		$this->mongodb->create($collection,$where); 
	}

	public function uninstall($type, $code) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "' AND `code` = '" . $this->db->escape($code) . "'");
		$collection="mongo_extension";
		$where=array('type'=>$type, 'code'=>$code);
		$this->mongodb->delete($collection,$where); 
	}
}