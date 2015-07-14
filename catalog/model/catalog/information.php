<?php
class ModelCatalogInformation extends Model {
	public function getInformation($information_id) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");
		//return $query->row;
		$information_info = array();
		$collection="mongo_information";
		$where=array('information_id'=>(int)$information_id, 'information_to_store'=>(int)$this->config->get('config_store_id'), 'status'=>1);
		$information_info=$this->mongodb->getBy($collection,$where);
		return $information_info;
	}

	public function getInformations() {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC");
		//return $query->rows;
		$information_data = array();
		$collection="mongo_information";
		$where=array('information_to_store'=>(int)$this->config->get('config_store_id'), 'status'=>1);
		$order=array('sort_order'=>1, 'information_description.'. (int)$this->config->get('config_language_id').'.title'=> 1);
		$information_data = $this->mongodb->getall($collection,$where, $order);
		return $information_data;
	}

	public function getInformationLayoutId($information_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		$information_info = array();
		$collection="mongo_information_to_layout";
		$where=array('information_id'=>(int)$information_id, 'store_id'=>(int)$this->config->get('config_store_id'));
		$information_info=$this->mongodb->getBy($collection,$where);

		//if ($query->num_rows) {
		if ($information_info) {
			//return $query->row['layout_id'];
			return $information_info['layout_id'];
		} else {
			return 0;
		}
	}
}