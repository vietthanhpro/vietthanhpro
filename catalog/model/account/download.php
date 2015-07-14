<?php
class ModelAccountDownload extends Model {
	public function getDownload($download_id) {
		$implode = array();
		$order_statuses = $this->config->get('config_complete_status');
		$order_status_id_array = array();
		foreach ($order_statuses as $order_status_id) {
			//$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			$order_status_id_array[] =(int)$order_status_id;
		}
		//if ($implode) {
			//$query = $this->db->query("SELECT d.filename, d.mask FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) LEFT JOIN " . DB_PREFIX . "product_to_download p2d ON (op.product_id = p2d.product_id) LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND (" . implode(" OR ", $implode) . ") AND d.download_id = '" . (int)$download_id . "'");
			//return $query->row;
		//} else {
			//return;
		//}		
		$download_info = array();
		$collection="mongo_download";
		$where=array('download_id'=>(int)$download_id);
		$download_info=$this->mongodb->getBy($collection,$where);
		////
		$product_info = array();
		$collection="mongo_product";
		$where=array('product_download'=>(int)$download_id);
		$product_info=$this->mongodb->getBy($collection,$where);
		if ($product_info) {
			$order_product_info = array();
			$collection="mongo_order_product";
			$where=array('product_id'=>(int)$product_info['product_id']);
			$order_product_info=$this->mongodb->getBy($collection,$where);
			if ($order_product_info) {
				$order_info = array();
				$collection="mongo_order";
				$where=array('order_id'=>(int)$order_product_info['order_id'],'customer_id'=>(int)$this->customer->getId(),'order_status_id'=>array('$in'=>$order_status_id_array));
				$order_info=$this->mongodb->getBy($collection,$where);
				if ($order_info) {
					$download_info['order_id']=(int)$order_info['order_id'];
					$download_info['date_added']=(int)$order_info['date_added'];
					return $download_info;
				}
			}
		}
		return;
	}

	public function getDownloads($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 20;
		}
		$collection="mongo_download";
		$where=array();
		$order=array();
		$download_query_data = array();
		$download_list = $this->mongodb->getall($collection,$where, $order);
		foreach ($download_list as $download_list_info) {
			$download_info=array();
			$download_info=getDownload($download_list_info['download_id']);
			if ($download_info) $download_query_data[]=$download_info;
		}
		$download_total= count($download_query_data);
		$vtp_download_total=$start+$limit;
		if ($vtp_download_total>$download_total) {
			$vtp_download=$download_total;
		} else {
			$vtp_download=$vtp_download_total;
		}
		$download_result=array();
		for ($i=$start; $i<$vtp_download; $i++) {
			$download_result[]=$download_query_data[$i];
		}
		$ketquatrave['results']=$download_result;
		$ketquatrave['count']=$download_total;
		return $ketquatrave;
		/*
		$implode = array();
		$order_statuses = $this->config->get('config_complete_status');
		foreach ($order_statuses as $order_status_id) {
			$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
		}
		if ($implode) {
			//$query = $this->db->query("SELECT DISTINCT d.download_id, o.order_id, o.date_added, dd.name, d.filename FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) LEFT JOIN " . DB_PREFIX . "product_to_download p2d ON (op.product_id = p2d.product_id) LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND (" . implode(" OR ", $implode) . ") ORDER BY o.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
			return $query->rows;
		} else {
			return array();
		}*/
	}
/*
	public function getTotalDownloads() {
		$implode = array();
		$order_statuses = $this->config->get('config_complete_status');
		foreach ($order_statuses as $order_status_id) {
			$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
		}
		if ($implode) {
			//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) LEFT JOIN " . DB_PREFIX . "product_to_download p2d ON (op.product_id = p2d.product_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND (" . implode(" OR ", $implode) . ")");
			return $query->row['total'];
		} else {
			return 0;
		}
	}*/
}