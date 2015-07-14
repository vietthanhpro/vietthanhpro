<?php
class ModelCatalogTag extends Model {
	public function addTag($data) {
		$this->event->trigger('pre.admin.tag.add', $data);
		$collection="mongo_tag";
		$tag_id=1+(int)$this->mongodb->getlastid($collection,'tag_id');
		$tag_description= array();
		$tag_to_store= array();
		$tag_to_type= array();

		foreach ($data['tag_description'] as $language_id => $value) {
			$tag_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>trim($value['name']),
				'description'=>$value['description'],
				'meta_title'=>trim($value['meta_title']),
				'meta_description'=>$value['meta_description'],
				'meta_keyword'=>$value['meta_keyword']
			);
		}

		if (isset($data['tag_store'])) {
			foreach ($data['tag_store'] as $store_id) {
				$tag_to_store[]= (int)$store_id;
			}
		}

		if (isset($data['tag_type'])) {
			foreach ($data['tag_type'] as $type_id) {
				$tag_to_type[]= (int)$type_id;
			}
		}
		$newdocument=array('tag_id'=>(int)$tag_id, 'tag_description'=>$tag_description, 'tag_to_store'=>$tag_to_store, 'tag_to_type'=>$tag_to_type, 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 

		$this->cache->delete('tag');

		$this->event->trigger('post.admin.tag.add', $tag_id);

		return $tag_id;
	}

	public function editTag($tag_id, $data) {
		$this->event->trigger('pre.admin.tag.edit', $data);
		$collection="mongo_tag";
		$tag_description= array();
		$tag_to_store= array();
		$tag_to_type = array();

		foreach ($data['tag_description'] as $language_id => $value) {
			$tag_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
				'description'=>$value['description'],
				'meta_title'=>$value['meta_title'],
				'meta_description'=>$value['meta_description'],
				'meta_keyword'=>$value['meta_keyword']
			);
		}

		if (isset($data['tag_store'])) {
			foreach ($data['tag_store'] as $store_id) {
				$tag_to_store[]= (int)$store_id;
			}
		}

		if (isset($data['tag_type'])) {
			foreach ($data['tag_type'] as $type_id) {
				$tag_to_type[]= (int)$type_id;
			}
		}
		
		$infoupdate=array('tag_id'=>(int)$tag_id, 'tag_description'=>$tag_description, 'tag_to_store'=>$tag_to_store, 'tag_to_type'=>$tag_to_type, 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('tag_id'=>(int)$tag_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('tag');

		$this->event->trigger('post.admin.tag.edit', $tag_id);
	}

	public function deleteTag($tag_id) {
		$this->event->trigger('pre.admin.tag.delete', $tag_id);
		$collection="mongo_tag";
		$where=array('tag_id'=>(int)$tag_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('tag');

		$this->event->trigger('post.admin.tag.delete', $tag_id);
	}

	public function getTag($tag_id) {
		
		$tag_info = array();
		$collection="mongo_tag";
		$where=array('tag_id'=>(int)$tag_id);
		$tag_info=$this->mongodb->getBy($collection,$where);
		return $tag_info;
	}

	public function getTags($data = array()) {
		$collection="mongo_tag";
		if ($data) {
			$where=array();
			if (!empty($data['filter_name'])) {
				$where['tag_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
			}
			if (!empty($data['tag_to_type'])) {
				$where['tag_to_type']=(int)$data['tag_to_type'];
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
				'sort_order'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'tag_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'tag_description.'. (int)$this->config->get('config_language_id').'.name';	
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			   $tag_data = array();
				$where=array();
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
					$orderby = 'tag_description.'. (int)$this->config->get('config_language_id').'.name';
				}
				if ($orderby == 'name') $orderby = 'tag_description.'. (int)$this->config->get('config_language_id').'.name';	
		
				if (isset($data['order']) && ($data['order'] == 'DESC')) {
					$order[$orderby] = -1;
				} else {
					$order[$orderby]= 1;
				} 
				$tag_list = $this->mongodb->getall($collection,$where, $order);
				foreach ($tag_list as $result) {
					$tag_data[] = array(
						'tag_id' => $result['tag_id'],
						'name'        => $result['tag_description'][$this->config->get('config_language_id')]['name'],
						'status'  	  => $result['status'],
						'sort_order'  => $result['sort_order']
					); 
				}	
			return $tag_data;
		}
	}

	public function getTotalTags() {
		$collection="mongo_tag";
		$where=array();
		$tag_data=$this->mongodb->gettotal($collection,$where);
		return $tag_data;
	}	
}
