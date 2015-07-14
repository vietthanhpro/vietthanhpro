<?php
class ModelPaymentCOD extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/cod');
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('cod_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		$collection="mongo_zone_to_geo_zone";
		$where=array('geo_zone_id' => (int)$this->config->get('cod_geo_zone_id'),'country_id'=>(int)$address['country_id'],'zone_id'=>(int)$address['zone_id']);
		$order=array();
		$zone_to_geo_zone_data=$this->mongodb->getall($collection,$where, $order);
		if (!$zone_to_geo_zone_data) {
			$where=array('geo_zone_id' => (int)$this->config->get('cod_geo_zone_id'),'country_id'=>(int)$address['country_id'],'zone_id'=>0);
			$zone_to_geo_zone_data=$this->mongodb->getall($collection,$where, $order);
		}

		if ($this->config->get('cod_total') > 0 && $this->config->get('cod_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('cod_geo_zone_id')) {
			$status = true;
		//} elseif ($query->num_rows) {
		} elseif ($zone_to_geo_zone_data) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'cod',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('cod_sort_order')
			);
		}

		return $method_data;
	}
}