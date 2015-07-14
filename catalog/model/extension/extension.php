<?php
class ModelExtensionExtension extends Model {
	function getExtensions($type) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");
		//return $query->rows;
		$collection="mongo_extension";
		$where=array('type'=> $type);
		$order=array();$extension_list = array();
		$extension_list= $this->mongodb->getall($collection,$where, $order);
		return $extension_list;
	}
}