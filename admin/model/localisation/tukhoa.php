<?php
class ModelLocalisationTukhoa extends Model {
	public function addTukhoa($data) {
		$collection="mongo_tukhoa";
		$tukhoa_id=1+(int)$this->mongodb->getlastid($collection,'tukhoa_id');
		$tukhoa_description= array();

		foreach ($data['tukhoa_description'] as $language_id => $value) {
			$tukhoa_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$where=array('tukhoa_id'=>(int)$tukhoa_id, 'tukhoa_description'=>$tukhoa_description, 'link'=>$data['link'], 'follow'=>(int)$data['follow'], 'target'=>(int)$data['target'], 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status']);
		$this->mongodb->create($collection,$where); 

		$this->cache->delete('tukhoa');
	}

	public function editTukhoa($tukhoa_id, $data) {
		$collection="mongo_tukhoa";
		$tukhoa_description= array();

		foreach ($data['tukhoa_description'] as $language_id => $value) {
			$tukhoa_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$infoupdate=array('tukhoa_id'=>(int)$tukhoa_id, 'tukhoa_description'=>$tukhoa_description, 'link'=>$data['link'], 'follow'=>(int)$data['follow'], 'target'=>(int)$data['target'], 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status']);
		$where=array('tukhoa_id'=>(int)$tukhoa_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('tukhoa');
	}

	public function deleteTukhoa($tukhoa_id) {
		$collection="mongo_tukhoa";
		$where=array('tukhoa_id'=>(int)$tukhoa_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('tukhoa');
	}

	public function getTukhoas($data = array()) {
		$collection="mongo_tukhoa";
		if ($data) {
			$where=array();
			if (!empty($data['filter_name'])) {
				$where['tukhoa_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
			}
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
				'link',
				'follow',
				'target',
				'sort_order'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'tukhoa_description.'. (int)$this->config->get('config_language_id').'.title';
			}
			if ($orderby == 'name') $orderby = 'tukhoa_description.'. (int)$this->config->get('config_language_id').'.title';	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$tukhoa_data = $this->cache->get('tukhoa');
			if (!$tukhoa_data) {
				$where=array();
				$order=array('tukhoa_description.'. (int)$this->config->get('config_language_id').'.title'=> 1);
				$tukhoa_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('tukhoa', $tukhoa_data);
			}
			return $tukhoa_data;
		}
	}

	public function getTukhoa($tukhoa_id) {
		$collection="mongo_tukhoa";
		$where=array('tukhoa_id'=>(int)$tukhoa_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getTotalTukhoas($data) {
		$collection="mongo_tukhoa";
		$where=array();
		if (!empty($data['filter_name'])) {
			$where['tukhoa_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
		}
		$tukhoa_data=$this->mongodb->gettotal($collection,$where);
		return $tukhoa_data;
	}
}