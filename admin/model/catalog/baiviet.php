<?php
class ModelCatalogBaiviet extends Model {
	
	public function addBaiviet($data) {
		$this->event->trigger('pre.admin.baiviet.add', $data);
		$collection="mongo_baiviet";
		$baiviet_image= array();
		if (isset($data['baiviet_image'])) {
			foreach ($data['baiviet_image'] as $baiviet_image_value) {
				$baiviet_image[]= array(
					'image'=>$baiviet_image_value['image'],
					'sort_order'=>(int)$baiviet_image_value['sort_order']
				);
			}
		}
		
		$baiviet_danhmucbaiviet= array();
		if (isset($data['baiviet_danhmucbaiviet'])) {
			foreach ($data['baiviet_danhmucbaiviet'] as $danhmucbaiviet_id) {
				$baiviet_danhmucbaiviet[]= (int)$danhmucbaiviet_id;
			}
		}
		
		$baiviet_tag= array();
		if (isset($data['baiviet_tag'])) {
			foreach ($data['baiviet_tag'] as $tag_id) {
				$baiviet_tag[]= (int)$tag_id;
			}
		}
		
		$baiviet_description= array();
		foreach ($data['baiviet_description'] as $language_id => $value) {
			$baiviet_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
				'description'=>$value['description'],
				'meta_title'=>$value['meta_title'],
				'meta_description'=>$value['meta_description'],
				'meta_keyword'=>$value['meta_keyword']
			);
		}
		$baiviet_to_store=array();
		if (isset($data['baiviet_store'])) {
			foreach ($data['baiviet_store'] as $store_id) {
				$baiviet_to_store[]= (int)$store_id;
			}
		}
		
		$baiviet_id=1+(int)$this->mongodb->getlastid($collection,'baiviet_id');
		$newdocument=array('baiviet_id'=>(int)$baiviet_id, 'baiviet_description'=>$baiviet_description, 'baiviet_danhmucbaiviet'=>$baiviet_danhmucbaiviet, 'baiviet_tag'=>$baiviet_tag, 'baiviet_image'=>$baiviet_image, 'classname'=>trim($data['classname']), 'createby'=>(int)$this->user->getId(), 'updateby'=>(int)$this->user->getId(),'accessby_id'=>(int)$data['accessby_id'], 'baiviet_to_store'=>$baiviet_to_store, 'status'=>(int)$data['status'], 'viewed'=>(int)$data['viewed'], 'sort_order'=>(int)$data['sort_order'],'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))),'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 

		if (isset($data['image'])) {
			$infoupdate=array('image'=>$data['image']);
			$where=array('baiviet_id'=>(int)$baiviet_id);
			$this->mongodb->update($collection,$infoupdate,$where);
		}

		if (isset($data['baiviet_related'])) {
			$collection="mongo_baiviet_related";
			foreach ($data['baiviet_related'] as $related_id) {
				$where=array('baiviet_id'=>(int)$baiviet_id,'related_id'=>(int)$related_id);
				$this->mongodb->delete($collection,$where); 
				$newdocument=array('baiviet_id'=>(int)$baiviet_id, 'related_id'=>$related_id);
				$this->mongodb->create($collection,$newdocument); 
				$where=array('baiviet_id'=>(int)$related_id,'related_id'=>(int)$baiviet_id);
				$this->mongodb->delete($collection,$where); 
				$newdocument=array('baiviet_id'=>(int)$related_id, 'related_id'=>$baiviet_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		if (isset($data['baiviet_layout'])) {
			$collection="mongo_baiviet_to_layout";
			foreach ($data['baiviet_layout'] as $store_id => $layout_id) {
				$newdocument=array('baiviet_id'=>(int)$baiviet_id, 'store_id'=>$store_id, 'layout_id'=>(int)$layout_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		if (isset($data['keyword'])) {
			$collection="mongo_url_alias";
			$url_alias_id=1+(int)$this->mongodb->getlastid($collection,'url_alias_id');
			$newdocument=array('url_alias_id'=>(int)$url_alias_id, 'query'=>'baiviet_id=' . (int)$baiviet_id, 'keyword'=>$data['keyword']);
		}

		$this->cache->delete('baiviet');

		$this->event->trigger('post.admin.baiviet.add', $baiviet_id);

		return $baiviet_id;
	}

	public function editBaiviet($baiviet_id, $data) {
		$this->event->trigger('pre.admin.baiviet.edit', $data);
		$collection="mongo_baiviet";
		$baiviet_image= array();

		if (isset($data['baiviet_image'])) {
			foreach ($data['baiviet_image'] as $baiviet_image_value) {
				$baiviet_image[]= array(
					'image'=>$baiviet_image_value['image'],
					'sort_order'=>(int)$baiviet_image_value['sort_order']
				);
			}
		}
		$baiviet_danhmucbaiviet= array();
		if (isset($data['baiviet_danhmucbaiviet'])) {
			foreach ($data['baiviet_danhmucbaiviet'] as $danhmucbaiviet_id) {
				$baiviet_danhmucbaiviet[]= (int)$danhmucbaiviet_id;
			}
		}		
		$baiviet_tag= array();
		if (isset($data['baiviet_tag'])) {
			foreach ($data['baiviet_tag'] as $tag_id) {
				$baiviet_tag[]= (int)$tag_id;
			}
		}
		
		$baiviet_description= array();
		foreach ($data['baiviet_description'] as $language_id => $value) {
			$baiviet_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
				'description'=>$value['description'],
				'meta_title'=>$value['meta_title'],
				'meta_description'=>$value['meta_description'],
				'meta_keyword'=>$value['meta_keyword']
			);
		}
		$baiviet_to_store=array();
		if (isset($data['baiviet_store'])) {
			foreach ($data['baiviet_store'] as $store_id) {
				$baiviet_to_store[]= (int)$store_id;
			}
		}
		$infoupdate=array('baiviet_description'=>$baiviet_description, 'baiviet_danhmucbaiviet'=>$baiviet_danhmucbaiviet, 'baiviet_tag'=>$baiviet_tag, 'classname'=>trim($data['classname']), 'updateby'=>(int)$this->user->getId(),'accessby_id'=>(int)$data['accessby_id'], 'baiviet_image'=>$baiviet_image, 'baiviet_to_store'=>$baiviet_to_store, 'status'=>(int)$data['status'], 'sort_order'=>(int)$data['sort_order'],'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))); 
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		if (isset($data['image'])) {
			$infoupdate=array('image'=>$data['image']);
			$where=array('baiviet_id'=>(int)$baiviet_id);
			$this->mongodb->update($collection,$infoupdate,$where);
		}
		$collection="mongo_baiviet_related";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$this->mongodb->delete($collection,$where); 
		$where=array('related_id'=>(int)$baiviet_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['baiviet_related'])) {
			foreach ($data['baiviet_related'] as $related_id) {
				$where=array('baiviet_id'=>(int)$baiviet_id,'related_id'=>(int)$related_id);
				$this->mongodb->delete($collection,$where); 
				$newdocument=array('baiviet_id'=>(int)$baiviet_id, 'related_id'=>$related_id);
				$this->mongodb->create($collection,$newdocument); 
				$where=array('baiviet_id'=>(int)$related_id,'related_id'=>(int)$baiviet_id);
				$this->mongodb->delete($collection,$where); 
				$newdocument=array('baiviet_id'=>(int)$related_id, 'related_id'=>$baiviet_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}
		$collection="mongo_baiviet_to_layout";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['baiviet_layout'])) {
			foreach ($data['baiviet_layout'] as $store_id => $layout_id) {
				$newdocument=array('baiviet_id'=>(int)$baiviet_id, 'store_id'=>$store_id, 'layout_id'=>(int)$layout_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}
		$collection="mongo_url_alias";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$this->mongodb->delete($collection,$where); 

		if ($data['keyword']) {
			$url_alias_id=1+(int)$this->mongodb->getlastid($collection,'url_alias_id');
			$newdocument=array('url_alias_id'=>(int)$url_alias_id, 'query'=>'baiviet_id=' . (int)$baiviet_id, 'keyword'=>$data['keyword']);
		}

		$this->cache->delete('baiviet');

		$this->event->trigger('post.admin.baiviet.edit', $baiviet_id);
	}

	public function deleteBaiviet($baiviet_id) {
		$this->event->trigger('pre.admin.baiviet.delete', $baiviet_id);
		$collection="mongo_baiviet";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_baiviet_related";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$this->mongodb->delete($collection,$where); 
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_baiviet_to_layout";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_baiviet_review";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_url_alias";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('baiviet');

		$this->event->trigger('post.admin.baiviet.delete', $baiviet_id);
	}

	public function getBaiviet($baiviet_id) {
		$baiviet_info = array();
		$collection="mongo_baiviet";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$baiviet_info=$this->mongodb->getBy($collection,$where);
		if ($baiviet_info) {
			$collection="mongo_url_alias";
			$where=array('query'=>'baiviet_id='.(int)$baiviet_id);
			$url_alias_info=$this->mongodb->getBy($collection,$where);
			if ($url_alias_info) {$baiviet_info['keyword']=$url_alias_info['keyword'];}
			else {$baiviet_info['keyword']='';}
		} 
		return $baiviet_info;
	}

	public function getBaiviets($data = array()) {
		$baiviet_data = array();
		$collection="mongo_baiviet";
		$where=array();
		if (!empty($data['filter_name'])) {
			$where['baiviet_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
		}
		if (isset($data['filter_danhmucbaiviet_id']) && !is_null($data['filter_danhmucbaiviet_id'])) {			
			$where['baiviet_danhmucbaiviet']=(int)$data['filter_danhmucbaiviet_id'];
		}
		if (isset($data['filter_createby']) && !is_null($data['filter_createby'])) {			
			$where['createby']=(int)$data['filter_createby'];
		}
		if (isset($data['filter_updateby']) && !is_null($data['filter_updateby'])) {			
			$where['updateby']=(int)$data['filter_updateby'];
		}
		if (isset($data['filter_accessby_id']) && !is_null($data['filter_accessby_id'])) {			
			$where['accessby_id']=(int)$data['filter_accessby_id'];
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {			
			$where['status']=(int)$data['filter_status'];
		}
		if (!empty($data['filter_date_added'])) {
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		if (!empty($data['filter_date_modified'])) {
			$where['date_modified']=array('$gte'=>new MongoDate(strtotime($data['filter_date_modified'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_modified'].' 23:59:59')));
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
			'viewed',
			'date_added',
			'date_modified',
			'status',
			'accessby_id',
			'sort_order'
		);	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$orderby = $data['sort'];
		} else {
			$orderby = 'baiviet_description.'. (int)$this->config->get('config_language_id').'.name';
		}
		if ($orderby == 'name') $orderby = 'baiviet_description.'. (int)$this->config->get('config_language_id').'.name';	

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order[$orderby] = -1;
		} else {
			$order[$orderby]= 1;
		} 
		$baiviet_data = $this->mongodb->get($collection,$where, $order, $start, $limit);
		return $baiviet_data;
	}

	public function getTotalBaiviets($data = array()) {
		$collection="mongo_baiviet";
		$where=array();
		if (!empty($data['filter_name'])) {
			$where['name']=new MongoRegex('/^'.$data['filter_name'].'/');
		}
		if (isset($data['filter_danhmucbaiviet_id']) && !is_null($data['filter_danhmucbaiviet_id'])) {			
			$where['baiviet_danhmucbaiviet']=(int)$data['filter_danhmucbaiviet_id'];
		}
		if (isset($data['filter_createby']) && !is_null($data['filter_createby'])) {			
			$where['createby']=(int)$data['filter_createby'];
		}
		if (isset($data['filter_updateby']) && !is_null($data['filter_updateby'])) {			
			$where['updateby']=(int)$data['filter_updateby'];
		}
		if (isset($data['filter_accessby_id']) && !is_null($data['filter_accessby_id'])) {			
			$where['accessby_id']=(int)$data['filter_accessby_id'];
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {			
			$where['status']=(int)$data['filter_status'];
		}
		if (!empty($data['filter_date_added'])) {
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		if (!empty($data['filter_date_modified'])) {
			$where['date_modified']=array('$gte'=>new MongoDate(strtotime($data['filter_date_modified'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_modified'].' 23:59:59')));
		}
		$baiviet_data=$this->mongodb->gettotal($collection,$where);
		return $baiviet_data;
	}

	public function getBaivietsByDanhmucbaivietId($danhmucbaiviet_id) {
		$baiviet_data = array();
		$collection="mongo_baiviet";
		$where=array('danhmucbaiviet_id'=>(int)$danhmucbaiviet_id);
		$order=array('name'=> 1);
		$baiviet_data = $this->mongodb->getall($collection,$where, $order);
		return $baiviet_data;
	}
	
	public function getBaivietLayouts($baiviet_id) {
		$baiviet_layout_data = array();
		$baiviet_data = array();
		$collection="mongo_baiviet_to_layout";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$order=array();
		$baiviet_data = $this->mongodb->getall($collection,$where, $order);
		
		foreach ($baiviet_data as $result) {
			$baiviet_layout_data[$result['store_id']] = $result['layout_id'];
		}
		return $baiviet_layout_data;
	}

	public function getBaivietRelated($baiviet_id) {
		$baiviet_related_data = array();
		$baiviet_data = array();
		$collection="mongo_baiviet_related";
		$where=array('baiviet_id'=>(int)$baiviet_id);
		$order=array();
		$baiviet_data = $this->mongodb->getall($collection,$where, $order);
		
		foreach ($baiviet_data as $result) {
			$baiviet_related_data[] = $result['related_id'];
		}
		return $baiviet_related_data;
	}

	public function getTotalBaivietsByLayoutId($layout_id) {
		$baiviet_data= array();
		$collection="mongo_baiviet_to_layout";
		$where=array('layout_id'=>(int)$layout_id);
		$baiviet_data=$this->mongodb->gettotal($collection,$where);
		return $baiviet_data;
	}
}