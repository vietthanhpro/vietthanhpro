<?php
class ModelDesignBanner extends Model {
	public function getBanner($banner_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image bi LEFT JOIN " . DB_PREFIX . "banner_image_description bid ON (bi.banner_image_id  = bid.banner_image_id) WHERE bi.banner_id = '" . (int)$banner_id . "' AND bid.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY bi.sort_order ASC");
		//return $query->rows;
		$banner_data = $this->cache->get('banner_id_'.$banner_id);
		if (!$banner_data) {
			$collection="mongo_banner";
			$banner_data = array();
			$where=array('banner_id'=>(int)$banner_id);
			$banner_info= $this->mongodb->getBy($collection,$where);
			if ($banner_info) {
				$banner_data = $banner_info['banner_images']; 
				$this->cache->set('banner_id_'.$banner_id, $banner_data);
			}
			return $banner_data;
		} else {
			return $banner_data;
		}
	}

	public function getBanners() {/*
		$sql = "SELECT * FROM " . DB_PREFIX . "banner";
		//$query = $this->db->query($sql);
		return $query->rows;*/
		
		$query_data = array();
		$collection="mongo_banner";
		$where=array();
		$order=array();
		$query_data = $this->mongodb->getall($collection,$where, $order);
		return $query_data;
	}
	/*
	public function getBannerImages($banner_id) {
		$banner_image_data = array();

		$banner_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "' ORDER BY sort_order ASC");

		foreach ($banner_image_query->rows as $banner_image) {
			$banner_image_description_data = array();

			$banner_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image_description WHERE banner_image_id = '" . (int)$banner_image['banner_image_id'] . "' AND banner_id = '" . (int)$banner_id . "'");

			foreach ($banner_image_description_query->rows as $banner_image_description) {
				$banner_image_description_data[] = array(
					'language_id' => $banner_image_description['language_id'],
					'title' => $banner_image_description['title']
				);
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
}