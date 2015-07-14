<?php
class ModelDesignBanner extends Model {
	public function addBanner($data) {
		$this->event->trigger('pre.admin.banner.add', $data); 
		$collection="mongo_banner";
		$banner_id=1+(int)$this->mongodb->getlastid($collection,'banner_id');
		$banner_image_= array();
		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");

				$banner_image_description_= array();

				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {					
					$banner_image_description_[$language_id]= array(
						'language_id'=>(int)$language_id,
						'title'=>$banner_image_description['title']
					);
				}
				$banner_image_[]= array(
					'banner_image_description'=>$banner_image_description_,
					'link'=>$banner_image['link'],
					'image'=>$banner_image['image'],
					'sort_order'=>(int)$banner_image['sort_order'],
				);
			}
		}
		$where=array('banner_id'=>(int)$banner_id, 'name'=>$data['name'], 'banner_images'=>$banner_image_, 'status'=>(int)$data['status']);
		$this->mongodb->create($collection,$where); 
		/*$this->db->query("INSERT INTO " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "'");

		$banner_id = $this->db->getLastId();

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");

				$banner_image_id = $this->db->getLastId();

				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image_description SET banner_image_id = '" . (int)$banner_image_id . "', language_id = '" . (int)$language_id . "', banner_id = '" . (int)$banner_id . "', title = '" .  $this->db->escape($banner_image_description['title']) . "'");
				}
			}
		}
		*/
		$this->event->trigger('post.admin.banner.add', $banner_id);

		return $banner_id;
	}

	public function editBanner($banner_id, $data) {
		$this->event->trigger('pre.admin.banner.edit', $data);
		
		$collection="mongo_banner";
		$banner_image_= array();
		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");

				$banner_image_description_= array();

				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {					
					$banner_image_description_[$language_id]= array(
						'language_id'=>(int)$language_id,
						'title'=>$banner_image_description['title']
					);
				}
				$banner_image_[]= array(
					'banner_image_description'=>$banner_image_description_,
					'link'=>$banner_image['link'],
					'image'=>$banner_image['image'],
					'sort_order'=>(int)$banner_image['sort_order'],
				);
			}
		}
		$infoupdate=array('banner_id'=>(int)$banner_id, 'name'=>$data['name'], 'banner_images'=>$banner_image_, 'status'=>(int)$data['status']);
		$where=array('banner_id'=>(int)$banner_id);
		$this->mongodb->update($collection,$infoupdate,$where); 
		/*$this->db->query("UPDATE " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "' WHERE banner_id = '" . (int)$banner_id . "'");

		//$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int)$banner_id . "'");

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");

				$banner_image_id = $this->db->getLastId();

				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					//$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image_description SET banner_image_id = '" . (int)$banner_image_id . "', language_id = '" . (int)$language_id . "', banner_id = '" . (int)$banner_id . "', title = '" .  $this->db->escape($banner_image_description['title']) . "'");
				}
			}
		}
		*/
		$this->event->trigger('post.admin.banner.edit', $banner_id);
	}

	public function deleteBanner($banner_id) {
		$this->event->trigger('pre.admin.banner.delete', $banner_id);
		$collection="mongo_banner";
		$where=array('banner_id'=>(int)$banner_id);
		$this->mongodb->delete($collection,$where); 
		/*
		//$this->db->query("DELETE FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int)$banner_id . "'");
		*/
		$this->event->trigger('post.admin.banner.delete', $banner_id);
	}

	public function getBanner($banner_id) {
		$collection="mongo_banner";
		$where=array('banner_id'=>(int)$banner_id);
		return $this->mongodb->getBy($collection,$where);
		/*$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
		return $query->row;
		*/
	}

	public function getBanners($data = array()) {
		//$collection,$where=array(), $order=array(), $start, $limit=10
		//$collection=DB_PREFIX . "banner";
		$collection="mongo_banner";
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
				'status'
			);
	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = "name";
			}
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
				$where=array();
				$order=array('name'=> 1);
				return $this->mongodb->getall($collection,$where, $order);
		}
		////////////
		/*
		$sql = "SELECT * FROM " . DB_PREFIX . "banner";

		$sort_data = array(
			'name',
			'status'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		
		//$query = $this->db->query($sql);

		return $query->rows;
		*/
	}
/*
	public function getBannerImages($banner_id) {
		$banner_image_data = array();

		$banner_image_query = //$this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "' ORDER BY sort_order ASC");

		foreach ($banner_image_query->rows as $banner_image) {
			$banner_image_description_data = array();

			$banner_image_description_query = //$this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image_description WHERE banner_image_id = '" . (int)$banner_image['banner_image_id'] . "' AND banner_id = '" . (int)$banner_id . "'");

			foreach ($banner_image_description_query->rows as $banner_image_description) {
				$banner_image_description_data[$banner_image_description['language_id']] = array('title' => $banner_image_description['title']);
			}

			$banner_image_data[] = array(
				'banner_image_description' => $banner_image_description_data,
				'link'                     => $banner_image['link'],
				'image'                    => $banner_image['image'],
				'sort_order'               => $banner_image['sort_order']
			);
		}

		return $banner_image_data;
	}*/

	public function getTotalBanners() {
		/*$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner");
		return $query->row['total'];*/
		$collection="mongo_banner";$banner_data= array();
		$where=array();
		$banner_data=$this->mongodb->gettotal($collection,$where);
		return $banner_data;
	}
}