<?php
class ModelShippingWeight extends Model {
	public function getQuote($address) {
		$this->load->language('shipping/weight');

		$quote_data = array();$geo_zone_data= array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");
		$collection="mongo_geo_zone";
		$where=array();
		$order=array('name'=> 1);
		$geo_zone_data=$this->mongodb->getall($collection,$where, $order);

		//foreach ($query->rows as $result) {
		foreach ($geo_zone_data as $result) {
			if ($this->config->get('weight_' . $result['geo_zone_id'] . '_status')) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
				$collection="mongo_zone_to_geo_zone";
				$zone_to_geo_zone_data = array();
				$where=array('geo_zone_id'=>(int)$result['geo_zone_id'],'country_id'=>(int)$address['country_id'],'$or'=>array(array('zone_id'=>(int)$address['zone_id']),array('zone_id'=>0)));
				$order=array();
				$zone_to_geo_zone_data=$this->mongodb->getall($collection,$where, $order);

				//if ($query->num_rows) {
				if ($zone_to_geo_zone_data) {
					$status = true;
				} else {
					$status = false;
				}
			} else {
				$status = false;
			}

			if ($status) {
				$cost = '';
				$weight = $this->cart->getWeight();

				$rates = explode(',', $this->config->get('weight_' . $result['geo_zone_id'] . '_rate'));

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$cost = $data[1];
						}

						break;
					}
				}

				if ((string)$cost != '') {
					$quote_data['weight_' . $result['geo_zone_id']] = array(
						'code'         => 'weight.weight_' . $result['geo_zone_id'],
						'title'        => $result['name'] . '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class_id')) . ')',
						'cost'         => $cost,
						'tax_class_id' => $this->config->get('weight_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('weight_tax_class_id'), $this->config->get('config_tax')))
					);
				}
			}
		}

		$method_data = array();

		if ($quote_data) {
			$method_data = array(
				'code'       => 'weight',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('weight_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}
}