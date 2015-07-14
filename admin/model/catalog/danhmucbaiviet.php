<?php
class ModelCatalogDanhmucbaiviet extends Model {
	public function addDanhmucbaiviet($data) {
		$this->event->trigger('pre.admin.danhmucbaiviet.add', $data);
		$collection="mongo_danhmucbaiviet";
		$danhmucbaiviet_id=1+(int)$this->mongodb->getlastid($collection,'danhmucbaiviet_id');
		$danhmucbaiviet_description= array();
		$danhmucbaiviet_to_store= array();

		foreach ($data['danhmucbaiviet_description'] as $language_id => $value) {
			$danhmucbaiviet_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>trim($value['name']),
				'description'=>$value['description'],
				'meta_title'=>trim($value['meta_title']),
				'meta_description'=>$value['meta_description'],
				'meta_keyword'=>$value['meta_keyword']
			);
		}

		if (isset($data['danhmucbaiviet_store'])) {
			foreach ($data['danhmucbaiviet_store'] as $store_id) {
				$danhmucbaiviet_to_store[]= (int)$store_id;
			}
		}
		$newdocument=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id, 'danhmucbaiviet_description'=>$danhmucbaiviet_description, 'danhmucbaiviet_to_store'=>$danhmucbaiviet_to_store, 'parent_id'=>(int)$data['parent_id'], 'image'=>trim($data['image']), 'classname'=>trim($data['classname']), 'column'=>(int)$data['column'], 'createby'=>(int)$this->user->getId(), 'updateby'=>(int)$this->user->getId(),'accessby_id'=>(int)$data['accessby_id'], 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 
		
		if (isset($data['danhmucbaiviet_layout'])) {
			$collection="mongo_danhmucbaiviet_to_layout";
			foreach ($data['danhmucbaiviet_layout'] as $store_id => $layout_id) {
				$newdocument=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id, 'store_id'=>$store_id, 'layout_id'=>(int)$layout_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		if (isset($data['keyword'])) {
			$collection="mongo_url_alias";
			$url_alias_id=1+(int)$this->mongodb->getlastid($collection,'url_alias_id');
			$newdocument=array('url_alias_id'=>(int)$url_alias_id, 'query'=>'danhmucbaiviet_id=' . (int)$danhmucbaiviet_id, 'keyword'=>$data['keyword']);
			$this->mongodb->create($collection,$newdocument); 
		}

		$this->cache->delete('danhmucbaiviet');

		$this->event->trigger('post.admin.danhmucbaiviet.add', $danhmucbaiviet_id);

		return $danhmucbaiviet_id;
	}

	public function editDanhmucbaiviet($danhmucbaiviet_id, $data) {
		$this->event->trigger('pre.admin.danhmucbaiviet.edit', $data);
		$collection="mongo_danhmucbaiviet";
		$danhmucbaiviet_description= array();
		$danhmucbaiviet_to_store= array();

		foreach ($data['danhmucbaiviet_description'] as $language_id => $value) {
			$danhmucbaiviet_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
				'description'=>$value['description'],
				'meta_title'=>$value['meta_title'],
				'meta_description'=>$value['meta_description'],
				'meta_keyword'=>$value['meta_keyword']
			);
		}

		if (isset($data['danhmucbaiviet_store'])) {
			foreach ($data['danhmucbaiviet_store'] as $store_id) {
				$danhmucbaiviet_to_store[]= (int)$store_id;
			}
		}
		$infoupdate=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id, 'danhmucbaiviet_description'=>$danhmucbaiviet_description, 'danhmucbaiviet_to_store'=>$danhmucbaiviet_to_store, 'parent_id'=>(int)$data['parent_id'], 'classname'=>trim($data['classname']), 'column'=>(int)$data['column'], 'createby'=>(int)$this->user->getId(), 'updateby'=>(int)$this->user->getId(),'accessby_id'=>(int)$data['accessby_id'], 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		if (isset($data['image'])) {
			$infoupdate=array('image'=>$data['image']);
			$where=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id);
			$this->mongodb->update($collection,$infoupdate,$where);
		}
		$collection="mongo_danhmucbaiviet_to_layout";
		$where=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['danhmucbaiviet_layout'])) {
			foreach ($data['danhmucbaiviet_layout'] as $store_id => $layout_id) {
				$newdocument=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id, 'store_id'=>$store_id, 'layout_id'=>(int)$layout_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}
		$collection="mongo_url_alias";
		$where=array('query'=>'danhmucbaiviet_id='.(int)$danhmucbaiviet_id);
		$this->mongodb->delete($collection,$where); 

		if ($data['keyword']) {
			$url_alias_id=1+(int)$this->mongodb->getlastid($collection,'url_alias_id');
			$newdocument=array('url_alias_id'=>(int)$url_alias_id, 'query'=>'danhmucbaiviet_id=' . (int)$danhmucbaiviet_id, 'keyword'=>$data['keyword']);
			$this->mongodb->create($collection,$newdocument); 
		}

		$this->cache->delete('danhmucbaiviet');

		$this->event->trigger('post.admin.danhmucbaiviet.edit', $danhmucbaiviet_id);
	}

	public function deleteDanhmucbaiviet($danhmucbaiviet_id) {
		$this->event->trigger('pre.admin.danhmucbaiviet.delete', $danhmucbaiviet_id);
		$collection="mongo_danhmucbaiviet";
		$where=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_danhmucbaiviet_to_layout";
		$where=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id);
		$this->mongodb->delete($collection,$where);
		$collection="mongo_url_alias";
		$where=array('query'=>'danhmucbaiviet_id='.(int)$danhmucbaiviet_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('danhmucbaiviet');

		$this->event->trigger('post.admin.danhmucbaiviet.delete', $danhmucbaiviet_id);
	}

	public function getDanhmucbaiviet($danhmucbaiviet_id) {
		
		$danhmucbaiviet_info = array();
		$collection="mongo_danhmucbaiviet";
		$where=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id);
		$danhmucbaiviet_info=$this->mongodb->getBy($collection,$where);
		if ($danhmucbaiviet_info) {
			$collection="mongo_url_alias";
			$where=array('query'=>'danhmucbaiviet_id='.(int)$danhmucbaiviet_id);
			$url_alias_info=$this->mongodb->getBy($collection,$where);
			if ($url_alias_info) {$danhmucbaiviet_info['keyword']=$url_alias_info['keyword'];}
			else {$danhmucbaiviet_info['keyword']='';}
		} 
		return $danhmucbaiviet_info;
	}

	public function getDanhmucbaiviets($data = array()) {
		$collection="mongo_danhmucbaiviet";
		if (!empty($data['filter_name'])) {
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
				$orderby = 'danhmucbaiviet_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'danhmucbaiviet_description.'. (int)$this->config->get('config_language_id').'.name';	
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			   $danhmucbaiviet_data = array();
				$where=array();
				if (!empty($data['filter_parent_id'])) {
					$where['parent_id']=(int)$data['filter_parent_id']; 
				} else {
					$where['parent_id']=0;
				}
				$sort_data = array(
					'name',
					'sort_order'
				);	
				if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
					$orderby = $data['sort'];
				} else {
					$orderby = "name";
				}
				if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
					$orderby = $data['sort'];
				} else {
					$orderby = 'danhmucbaiviet_description.'. (int)$this->config->get('config_language_id').'.name';
				}
				if ($orderby == 'name') $orderby = 'danhmucbaiviet_description.'. (int)$this->config->get('config_language_id').'.name';	
		
				if (isset($data['order']) && ($data['order'] == 'DESC')) {
					$order[$orderby] = -1;
				} else {
					$order[$orderby]= 1;
				} 
				$danhmucbaiviet_list = $this->mongodb->getall($collection,$where, $order);
				foreach ($danhmucbaiviet_list as $result) {
					$danhmucbaiviet_data[] = array(
						'danhmucbaiviet_id' => $result['danhmucbaiviet_id'],
						'path'        => $this->getPath($result['danhmucbaiviet_id']),
						'name'        => $result['danhmucbaiviet_description'][$this->config->get('config_language_id')]['name'],
						'status'  	  => $result['status'],
						'sort_order'  => $result['sort_order']
					); 
					$filter_data = array(
						'filter_parent_id' => $result['danhmucbaiviet_id'],
						'sort'  => $data['sort'],
						'order' => $data['order']
					);		
					$danhmucbaiviet_data_child=$this->getDanhmucbaiviets($filter_data);
					if ($danhmucbaiviet_data_child) {
						$danhmucbaiviet_data = array_merge($danhmucbaiviet_data, $danhmucbaiviet_data_child); 
					} 
				}	
			return $danhmucbaiviet_data;
		}
	}
		
	public function getPath($danhmucbaiviet_id) {
		$danhmucbaiviet_info=$this->getDanhmucbaiviet($danhmucbaiviet_id);
		if ($danhmucbaiviet_info['parent_id']) {
				return $this->getPath($danhmucbaiviet_info['parent_id']) .'&nbsp;&nbsp;&gt;&nbsp;&nbsp;'. $danhmucbaiviet_info['danhmucbaiviet_description'][$this->config->get('config_language_id')]['name'];
		} else {
				return $danhmucbaiviet_info['danhmucbaiviet_description'][$this->config->get('config_language_id')]['name'];
		}
	}

	public function getDanhmucbaivietLayouts($danhmucbaiviet_id) {
		$danhmucbaiviet_layout_data = array();
		$collection="mongo_danhmucbaiviet_to_layout";
		$where=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id);
		$order=array();
		$danhmucbaiviet_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($danhmucbaiviet_data as $result) {
			$danhmucbaiviet_layout_data[$result['store_id']] = $result['layout_id'];
		}
		return $danhmucbaiviet_layout_data;
	}

	public function getTotalDanhmucbaiviets() {
		$collection="mongo_danhmucbaiviet";
		$where=array();
		$danhmucbaiviet_data=$this->mongodb->gettotal($collection,$where);
		return $danhmucbaiviet_data;
	}
	
	public function getTotalDanhmucbaivietsByLayoutId($layout_id) {
		$collection="mongo_danhmucbaiviet_to_layout";
		$where=array('layout_id'=>(int)$layout_id);
		$danhmucbaiviet_data=$this->mongodb->gettotal($collection,$where);
		return $danhmucbaiviet_data;
	}	
}
