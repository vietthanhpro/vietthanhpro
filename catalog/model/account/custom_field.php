<?php
class ModelAccountCustomField extends Model {
	public function getCustomField($custom_field_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cf.status = '1' AND cf.custom_field_id = '" . (int)$custom_field_id . "' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$collection="mongo_custom_field";
		$where=array('custom_field_id'=>(int)$custom_field_id);
		return $this->mongodb->getBy($collection,$where);
	}
	
	public function getCustomFieldCustomerGroup($custom_field_id, $customer_group_id) {
		$collection="mongo_custom_field_customer_group";
		$where=array('custom_field_id'=>(int)$custom_field_id, 'customer_group_id'=>(int)$customer_group_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getCustomFields($customer_group_id = 0) { 
		$custom_field_data = array();
		
		$custom_field_query_list=array();
		if (!$customer_group_id) {
			//$custom_field_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cf.status = '1' ORDER BY cf.sort_order ASC");
			$collection="mongo_custom_field"; 
			$where=array('status'=>1);
			$order=array('sort_order'=>1);
			$custom_field_query_list_data= $this->mongodb->getall($collection,$where, $order);
			foreach ($custom_field_query_list_data as $custom_field_query_list_data_info) {
				$customfieldcustomergroup_info= array();
				$customfieldcustomergroup_info=$this->getCustomFieldCustomerGroup($custom_field_query_list_data_info['custom_field_id'],(int)$customer_group_id);
				$custom_field_query_list[]=array(
					'custom_field_id'=>$custom_field_query_list_data_info['custom_field_id'],
					'type'=>$custom_field_query_list_data_info['type'],
					'value'=>$custom_field_query_list_data_info['value'],
					'location'=>$custom_field_query_list_data_info['location'],
					'status'=>$custom_field_query_list_data_info['status'],
					'sort_order'=>$custom_field_query_list_data_info['sort_order'],
					'custom_field_description'=>$custom_field_query_list_data_info['custom_field_description'],
					'customer_group_id'=>$customfieldcustomergroup_info['customer_group_id'],
					'required'=>$customfieldcustomergroup_info['required'],
				);
			}
		} else {
			//$custom_field_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_customer_group` cfcg LEFT JOIN `" . DB_PREFIX . "custom_field` cf ON (cfcg.custom_field_id = cf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cfcg.customer_group_id = '" . (int)$customer_group_id . "' ORDER BY cf.sort_order ASC");
			$custom_field_customer_group_query_list=array();
			$custom_field_id_array=array();
			$collection="mongo_custom_field_customer_group";
			$where=array('customer_group_id'=>(int)$customer_group_id);
			$order=array();
			$custom_field_customer_group_value_query_list= $this->mongodb->getall($collection,$where, $order);
			foreach ($custom_field_customer_group_value_query_list as $custom_field_customer_group_value_query_list_info) {
				$custom_field_id_array[]=$custom_field_customer_group_value_query_list_info['custom_field_id'];
			}
			$collection="mongo_custom_field";
			$where=array('status'=>1, 'custom_field_id'=>array('$in'=>$custom_field_id_array));
			$order=array('sort_order'=>1);
			$custom_field_query_list_data= $this->mongodb->getall($collection,$where, $order);
			//print_r($where); die();
			foreach ($custom_field_query_list_data as $custom_field_query_list_data_info) {
				$customfieldcustomergroup_info= array();
				$customfieldcustomergroup_info=$this->getCustomFieldCustomerGroup($custom_field_query_list_data_info['custom_field_id'],(int)$customer_group_id);
				$custom_field_query_list[]=array(
					'custom_field_id'=>$custom_field_query_list_data_info['custom_field_id'],
					'type'=>$custom_field_query_list_data_info['type'],
					'value'=>$custom_field_query_list_data_info['value'],
					'location'=>$custom_field_query_list_data_info['location'],
					'status'=>$custom_field_query_list_data_info['status'],
					'sort_order'=>$custom_field_query_list_data_info['sort_order'],
					'custom_field_description'=>$custom_field_query_list_data_info['custom_field_description'],
					'customer_group_id'=>$customfieldcustomergroup_info['customer_group_id'],
					'required'=>$customfieldcustomergroup_info['required'],
				);
			}
		}
		//foreach ($custom_field_query->rows as $custom_field) {
		foreach ($custom_field_query_list as $custom_field) {
			$custom_field_value_data = array();

			if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio' || $custom_field['type'] == 'checkbox') {
				//$custom_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value cfv LEFT JOIN " . DB_PREFIX . "custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) WHERE cfv.custom_field_id = '" . (int)$custom_field['custom_field_id'] . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cfv.sort_order ASC");
				$collection="mongo_custom_field_value";$custom_field_value_query_list = array();
				$where=array('custom_field_id'=>(int)$custom_field['custom_field_id']);
				$order=array();
				$custom_field_value_query_list= $this->mongodb->getall($collection,$where, $order);
				//foreach ($custom_field_value_query->rows as $custom_field_value) {
				foreach ($custom_field_value_query_list as $custom_field_value) {
					$custom_field_value_data[] = array(
						'custom_field_value_id' => $custom_field_value['custom_field_value_id'],
						'name'                  => $custom_field_value['custom_field_value_description'][(int)$this->config->get('config_language_id')]['name']
					);
				}
			}

			$custom_field_data[] = array(
				'custom_field_id'    => $custom_field['custom_field_id'],
				'custom_field_value' => $custom_field_value_data,
				'name'               => $custom_field['custom_field_description'][(int)$this->config->get('config_language_id')]['name'],
				'type'               => $custom_field['type'],
				'value'              => $custom_field['value'],
				'location'           => $custom_field['location'],
				'required'           => empty($custom_field['required']) || $custom_field['required'] == 0 ? false : true,
				'sort_order'         => $custom_field['sort_order']
			);
		}

		return $custom_field_data;
	}
}