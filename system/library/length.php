<?php
class Length {
	private $lengths = array();

	public function __construct($registry) {
		//$this->db = $registry->get('db');
		$this->mongodb = $registry->get('mongodb');
		$this->config = $registry->get('config');

		//$length_class_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class mc LEFT JOIN " . DB_PREFIX . "length_class_description mcd ON (mc.length_class_id = mcd.length_class_id) WHERE mcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		$collection="mongo_length_class";
		$where=array();
		$order=array('length_class_description.'. (int)$this->config->get('config_language_id').'.title'=> 1);
		$length_class_data = $this->mongodb->getall($collection,$where, $order);

		//foreach ($length_class_query->rows as $result) {
		foreach ($length_class_data as $result) {
			$this->lengths[$result['length_class_id']] = array(
				'length_class_id' => $result['length_class_id'],
				'title'           => $result['length_class_description'][(int)$this->config->get('config_language_id')]['title'],
				'unit'            => $result['length_class_description'][(int)$this->config->get('config_language_id')]['unit'],
				'value'           => $result['value']
			);
		}
	}

	public function convert($value, $from, $to) {
		if ($from == $to) {
			return $value;
		}

		if (isset($this->lengths[$from])) {
			$from = $this->lengths[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->lengths[$to])) {
			$to = $this->lengths[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function format($value, $length_class_id, $decimal_point = '.', $thousand_point = ',') {
		if (isset($this->lengths[$length_class_id])) {
			return number_format($value, 2, $decimal_point, $thousand_point) . $this->lengths[$length_class_id]['unit'];
		} else {
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	public function getUnit($length_class_id) {
		if (isset($this->lengths[$length_class_id])) {
			return $this->lengths[$length_class_id]['unit'];
		} else {
			return '';
		}
	}
}
