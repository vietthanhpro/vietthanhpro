<?php
class ModelReportActivity extends Model {
	public function getActivities() {
		//$query = $this->db->query("SELECT a.key, a.data, a.date_added FROM ((SELECT CONCAT('customer_', ca.key) AS `key`, ca.data, ca.date_added FROM `" . DB_PREFIX . "customer_activity` ca) UNION (SELECT CONCAT('affiliate_', aa.key) AS `key`, aa.data, aa.date_added FROM `" . DB_PREFIX . "affiliate_activity` aa)) a ORDER BY a.date_added DESC LIMIT 0,5");
		//return $query->rows;
		$data_result=array();
		$collection="mongo_customer_activity";
		$where=array();
		$order=array('date_added'=> -1);
		$customer_activity_data=$this->mongodb->getall($collection,$where, $order);
		foreach ($customer_activity_data as $customer_activity_data_info) {
			$data_result[]=array(
				'key'=>'customer_'.$customer_activity_data_info['key'],
				'data'=>$customer_activity_data_info['data'],
				'date_added'=>$customer_activity_data_info['date_added'],
			);
		}
		///
		$collection="mongo_affiliate_activity";
		$where=array();
		$order=array('date_added'=> -1);
		$affiliate_activity_data=$this->mongodb->getall($collection,$where, $order);
		foreach ($affiliate_activity_data as $affiliate_activity_data_info) {
			$data_result[]=array(
				'key'=>'affiliate_'.$affiliate_activity_data_info['key'],
				'data'=>$affiliate_activity_data_info['data'],
				'date_added'=>$affiliate_activity_data_info['date_added'],
			);
		}
		$data_result = $this->mongodb->sapxepthemphantu($data_result,'date_added', 0);
		return $data_result; 
	}
}