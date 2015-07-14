<?php
class ModelLocalisationLanguage extends Model {
	public function getLanguage($language_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$language_id . "'");
		//return $query->row;	
		$collection="mongo_language";
		$where=array('language_id'=>(int)$language_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getLanguages() {
		$language_data = $this->cache->get('language');

		if (!$language_data) {
			$language_data = array();
			//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language ORDER BY sort_order, name");
			$collection="mongo_language";
			$where=array();
			$order=array('sort_order'=> 1, 'name'=> 1);
			$language_list=$this->mongodb->getall($collection,$where, $order);
			//foreach ($query->rows as $result) {
			foreach ($language_list as $result) {
				$language_data[$result['code']] = array(
					'language_id' => $result['language_id'],
					'name'        => $result['name'],
					'code'        => $result['code'],
					'locale'      => $result['locale'],
					'image'       => $result['image'],
					'directory'   => $result['directory'],
					'sort_order'  => $result['sort_order'],
					'status'      => $result['status']
				);
			}
			$this->cache->set('language', $language_data);
		}

		return $language_data;
	}
}