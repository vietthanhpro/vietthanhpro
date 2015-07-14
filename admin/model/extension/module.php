<?php
class ModelExtensionModule extends Model {
	public function addModule($code, $data) {
		$collection="mongo_module";
		$module_id=1+(int)$this->mongodb->getlastid($collection,'module_id');
		$where=array('module_id'=>(int)$module_id, 'name'=>$data['name'], 'code'=>$code, 'setting'=>serialize($data));
		$this->mongodb->create($collection,$where); 
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data['name']) . "', `code` = '" . $this->db->escape($code) . "', `setting` = '" . $this->db->escape(serialize($data)) . "'");
	}
	
	public function editModule($module_id, $data) {
		$collection="mongo_module";
		$infoupdate=array('module_id'=>(int)$module_id, 'name'=>$data['name'], 'code'=>$data['code'], 'setting'=>serialize($data));
		$where=array('module_id'=>(int)$module_id);
		$this->mongodb->update($collection,$infoupdate,$where); 
		//$this->db->query("UPDATE `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data['name']) . "', `setting` = '" . $this->db->escape(serialize($data)) . "' WHERE `module_id` = '" . (int)$module_id . "'");
	}

	public function deleteModule($module_id) {
		$collection="mongo_module";
		$where=array('module_id'=>(int)$module_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");
		$collection="mongo_layout_module";
		$where=array('code'=>new MongoRegex('/.'.(int)$module_id.'^/'));
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE `code` LIKE '%." . (int)$module_id . "'");
	}
		
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
	
	public function getModules() {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` ORDER BY `code`");
		//return $query->rows;
		$collection="mongo_module";
		$where=array();
		$order=array('code'=> 1);
		return $this->mongodb->getall($collection,$where, $order);
	}	
		
	public function getModulesByCode($code) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `name`");
		//return $query->rows;
		$collection="mongo_module";
		$where=array('code'=>(string)$code);
		$order=array('code'=> 1);
		return $this->mongodb->getall($collection,$where, $order);
	}	
	
	public function deleteModulesByCode($code) {
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "'");
		$collection="mongo_module";
		$where=array('code'=>(string)$code);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE `code` LIKE '" . $this->db->escape($code) . "' OR `code` LIKE '" . $this->db->escape($code . '.%') . "'");
		$collection="mongo_layout_module";
		$where=array('$or'=>array(array('code'=>'/.'.(int)$module_id.'/'),array('code'=>new MongoRegex('/^'.(int)$module_id.'./'))));
		$this->mongodb->delete($collection,$where); 
	}	
}