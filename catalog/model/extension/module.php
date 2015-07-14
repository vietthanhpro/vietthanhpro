<?php
class ModelExtensionModule extends Model {		
	public function getModule($module_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . $this->db->escape($module_id) . "'");
		$collection="mongo_module";
		$where=array('module_id'=>(int)$module_id);
		$module_info = $this->mongodb->getBy($collection,$where);
		
		if ($module_info) {
		//if ($query->row) {
			//return unserialize($query->row['setting']);
			return unserialize($module_info['setting']);
		} else {
			return array();	
		}
	}	
}