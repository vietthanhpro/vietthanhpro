<?php
class ModelLocalisationAccessby extends Model {
	public function addAccessby($data) {
		$collection="mongo_accessby";
		$accessby_id=1+(int)$this->mongodb->getlastid($collection,'accessby_id');
		$accessby_description= array();

		foreach ($data['accessby_description'] as $language_id => $value) {
			$accessby_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$where=array('accessby_id'=>(int)$accessby_id, 'accessby_description'=>$accessby_description, 'sort_order'=>(int)$data['sort_order']);
		$this->mongodb->create($collection,$where); 

		$this->cache->delete('accessby');
	}

	public function editAccessby($accessby_id, $data) {
		$collection="mongo_accessby";
		$accessby_description= array();

		foreach ($data['accessby_description'] as $language_id => $value) {
			$accessby_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$infoupdate=array('accessby_id'=>(int)$accessby_id, 'accessby_description'=>$accessby_description, 'sort_order'=>(int)$data['sort_order']);
		$where=array('accessby_id'=>(int)$accessby_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('accessby');
	}

	public function deleteAccessby($accessby_id) {
		$collection="mongo_accessby";
		$where=array('accessby_id'=>(int)$accessby_id);
		$this->mongodb->delete($collection,$where); 
		$this->cache->delete('accessby');
	}

	public function getAccessbys($data = array()) {
		$collection="mongo_accessby";
		if ($data) {
			$where=array();
			$order=array();
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}
	
				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}
				$start=$data['start'];
				$limit=$data['limit'];
			} else {
				$start=0;
				$limit=0;
			}
			$sort_data = array(
				'name',
				'sort_order'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'accessby_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'accessby_description.'. (int)$this->config->get('config_language_id').'.name';	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$accessby_data = $this->cache->get('accessby');
			if (!$accessby_data) {
				$where=array();
				$order=array('sort_order'=> 1);
				$accessby_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('accessby', $accessby_data);
			}
			return $accessby_data;
		}
	}

	public function getAccessby($accessby_id) {
		$collection="mongo_accessby";
		$where=array('accessby_id'=>(int)$accessby_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getTotalAccessbys() {
		$collection="mongo_accessby";
		$where=array();
		$accessby_data=$this->mongodb->gettotal($collection,$where);
		return $accessby_data;
	}
}