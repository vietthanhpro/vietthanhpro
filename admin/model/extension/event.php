<?php
class ModelExtensionEvent extends Model {
	public function addEvent($code, $trigger, $action) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "event SET `code` = '" . $this->db->escape($code) . "', `trigger` = '" . $this->db->escape($trigger) . "', `action` = '" . $this->db->escape($action) . "'");
		$collection="mongo_event";
		$event_id=1+(int)$this->mongodb->getlastid($collection,'event_id');
		$where=array('event_id'=>(int)$event_id, 'code'=>$code, 'trigger'=>$trigger, 'action'=>$action);
		$this->mongodb->create($collection,$where); 
	}

	public function deleteEvent($code) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "event WHERE `code` = '" . $this->db->escape($code) . "'");
		$collection="mongo_event";
		$where=array('code'=>$code);
		$this->mongodb->delete($collection,$where); 
	}
}