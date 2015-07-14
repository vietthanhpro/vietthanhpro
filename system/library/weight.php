<?php
class Weight {
	private $weights = array();

	public function __construct($registry) {
		//$this->db = $registry->get('db');
		$this->mongodb = $registry->get('mongodb');
		$this->config = $registry->get('config');

		//$weight_class_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		$collection="mongo_weight_class";
		$where=array();
		$order=array('weight_class_description.'. (int)$this->config->get('config_language_id').'.title'=> 1);
		$length_class_data = $this->mongodb->getall($collection,$where, $order);

		//foreach ($weight_class_query->rows as $result) {
		foreach ($length_class_data as $result) {
			$this->weights[$result['weight_class_id']] = array(
				'weight_class_id' => $result['weight_class_id'],
				'title'           => $result['weight_class_description'][(int)$this->config->get('config_language_id')]['title'],
				'unit'            => $result['weight_class_description'][(int)$this->config->get('config_language_id')]['unit'],
				'value'           => $result['value']
			);
		}
	}

	public function convert($value, $from, $to) {
		if ($from == $to) {
			return $value;
		}

		if (isset($this->weights[$from])) {
			$from = $this->weights[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->weights[$to])) {
			$to = $this->weights[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function format($value, $weight_class_id, $decimal_point = '.', $thousand_point = ',') {
		if (isset($this->weights[$weight_class_id])) {
			return number_format($value, 2, $decimal_point, $thousand_point) . $this->weights[$weight_class_id]['unit'];
		} else {
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	public function getUnit($weight_class_id) {
		if (isset($this->weights[$weight_class_id])) {
			return $this->weights[$weight_class_id]['unit'];
		} else {
			return '';
		}
	}
}