<?php
class Dbmongo {
	private $db;

	public function __construct($hostname, $port, $database) {
		try{
			if ( !class_exists('Mongo')){
	            echo ("The MongoDB PECL extension has not been installed or enabled");
	            return false;
	        }
			$connection=  new \MongoClient("mongodb://".$hostname.":".$port);
	    	return $this->db = $connection->selectDB($database);
		}catch(Exception $e) {
			return false;
		}
	}
	/**
	 * get one article by id
	 * @return array
	 */
	public function getlastid($collection,$last_id=''){
		$where =  array(
			'$group' => array(
				'_id'  => '',
				'last' => array('$max'=> '$'.$last_id)
			)
		);
		$table = $this->db->selectCollection($collection);
		$results = $table->aggregate($where);
		if (isset($results['result'][0]['last'])) {
			return $results['result'][0]['last'];
		} else {
			return 0;
		}
	}
	public function getfirstid($collection,$last_id=''){
		$where =  array(
			'$group' => array(
				'_id'  => '',
				'first' => array('$min'=> '$'.$last_id)
			)
		);
		$table = $this->db->selectCollection($collection);
		$results = $table->aggregate($where);
		if (isset($results['result'][0]['first'])) {
			return $results['result'][0]['first'];
		} else {
			return 0;
		}
	}
	/**
	 * get max value by id
	 * @return array
	 */
	public function getaggregate($collection, $match, $group){
		$table = $this->db->selectCollection($collection);
		$results = $table->aggregate($match, $group);
		if (isset($results['result'][0]['ketqua'])) {
			return $results['result'][0]['ketqua'];
		} else {
			return 0;
		}
	}
	/**
	 * get one article by id
	 * @return array
	 */
	public function getBy($collection, $where){ 
		$table = $this->db->selectCollection($collection);
		$article  = $table->findOne($where);
		//$cursor  = $table->find($where);
		//$article = $cursor->getNext();
		//print_r($article); die();
		//$article=iterator_to_array($article);
		if (!$article ){
			return false ;
		}
		return $article;
	}
	/**
	 * get all data in collection and paginator
	 *
	 * @return multi array 
	 */
	public function getall($collection,$where=array(), $order=array()){ 
		$data =array();
		$table = $this->db->selectCollection($collection);
		$data = $table->find($where);
		$data->sort($order);
		$data=iterator_to_array($data);
		return $data;
	}
	public function gettotal($collection,$where=array()){
		$data=0;
		$table = $this->db->selectCollection($collection);
		$data = $table->find($where)->count();
		//$count=$data->count();
		return $data;
	}
	public function get($collection,$where=array(), $order=array(), $start=0, $limit=0){
		$data=$results =array();//print_r($collection); die();
		$table = $this->db->selectCollection($collection);
			$results = $table->find($where);
			$count=$results->count();
			$results=$results->sort($order);
		if ($limit!=0) {
			$results->skip($start);
			$results->limit($limit);
		} 
		$data['results']=iterator_to_array($results);
		$data['count']=$count;
		return $data;
	}
	public function getelement($collection,$where=array(), $fields = array(), $order=array(), $start=0, $limit=0){		
		$data=$results =array();
		$table = $this->db->selectCollection($collection);
			$results = $table->find($where, $fields);
			$results=$results->sort($order);
		if ($limit!=0) {
			$results->skip($start);
			$results->limit($limit);
		} 
		$data=iterator_to_array($results);
		return $data;
	}
	public function getlimit($collection,$where=array(), $order=array(), $start=0, $limit=0){
		$data=$results =array();
		$table = $this->db->selectCollection($collection);
			$results = $table->find($where);
			$results=$results->sort($order);
		if ($limit!=0) {
			$results->skip($start);
			$results->limit($limit);
		} 
		$data=iterator_to_array($results);
		return $data;
	}
	public function getgroupby($collection, $keys=array(), $initial=array(), $reduce, $condition=array()){
		$data=$results =array();
		$table = $this->db->selectCollection($collection);
		$results = $table->group($keys, $initial, $reduce, $condition); 
		$data = $results['retval'];
		return $data;
	}
	/**
	 * get all data in collection and paginator
	 *
	 * @return data
	 */
	 /*
	public function getall($collection, $where=array()){
		$table = $this->db->selectCollection($collection);
		$data = $table->find($where);
		return $data;
	}*/
	/**
	 * Create article
	 * @return boolean
	 */
	public function create($collection,$newdocument){ 

		$table 	 = $this->db->selectCollection($collection);
		$table->insert($newdocument);
		return 1;
	}
	/**
	 * delete article via id
	 * @return boolean
	 */
	public function delete($collection, $where){
		$table 	 = $this->db->selectCollection($collection);
		$result = $table->remove($where);
		return 1;

	}
	/**
	 * Update article
	 * @return boolean
	 */
	 //
	public function update($collection,$infoupdate,$where, $multiple=0){
		$table 	 = $this->db->selectCollection($collection); //print_r ($where); print_r($infoupdate); die();
		if ($multiple) {
			$result  = $table->update($where, array('$set'=>$infoupdate), array("multiple"=>1));
		} else {
			$result  = $table->update($where, array('$set'=>$infoupdate));
		}
		return $result;

	}
	public function incelement($collection,$where, $info){
		$table 	 = $this->db->selectCollection($collection);
		$result  = $table->update($where, $info, array("multiple"=>1));
		return $result;
	}
	private function build_sorter($order, $key) {
		if ($order) {
			return function ($a, $b) use ($key) {
				return strnatcmp($a[$key], $b[$key]);
			};
		} else {
			return function ($a, $b) use ($key) {
				return strnatcmp($b[$key], $a[$key]);
			};
		}
	}
	public function sapxepthemphantu($data=array(),$element='', $order=1){		
		usort($data, $this->build_sorter($order, $element));
		return $data;
	}
	public function date_start_end($start,$end){		
		$begin = new DateTime($start);
		$end = new DateTime($end.' +1 day');	
		$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);	
		$dates = array();
		foreach($daterange as $date){
			$daytt=$date->format("Y-m-d");
			$dates[] = array(
				'start'=>$daytt .' 00:00:01',
				'end'  =>$daytt .' 23:59:59',
			);
		}
		return $dates;
	}
	public function month_start_end($start,$end){		
		$begin = new DateTime($start);
		$end = new DateTime($end);
		$k=0;
		$array_month= array();
		$array_month[]=$begin->format('Y-m-d');
		while ($begin < $end) {
			if ($k==0) {
				$array_month[]=$begin->format('Y-m-t');
				$k=1;
			} else {
				$array_month[]=$begin->format('Y-m-01');
				$k=0;
			}
			$begin->modify('first day of next month');
		}
		$array_month[]=$end->format('Y-m-d'); 
		$array_month2 = array(); 
		$soluongmang=count($array_month);
		if ($soluongmang>3) {
			for ($i=0; $i<$soluongmang-1; $i=$i+2) {
				$array_month2[] = array(
					'start'=>$array_month[$i] .' 00:00:01',
					'end'  =>$array_month[$i+1] .' 23:59:59',
				);
			}  
		} else {
			$array_month2[] = array(
				'start'=>$array_month[0] .' 00:00:01',
				'end'  =>$array_month[2] .' 23:59:59',
			);
		}
		return $array_month2;
	}
	public function year_start_end($start,$end){	
		$intstart = (int)substr($start, 0, 4); 	
		$intend = (int)substr($end, 0, 4); 	
		$intsub=$intend-$intstart;
		$dates=array();
		if ($intsub>0) {
			$k=0;
			for ($i=0; $i<=$intsub; $i++) {
				$date_start=($intstart+$i).'-01-01 00:00:01';
				$date_end=($intstart+$i).'-12-31 23:59:59';
				if ($i==0) {
					$date_start=$start.' 00:00:01';
					$date_end=($intstart+$i).'-12-31 23:59:59';
				} 
				if ($i==$intsub) {
					$date_start=($intstart+$i).' 00:00:01';
					$date_end=$end.' 23:59:59';
				} 
				$dates[] = array(
					'start'=>$date_start,
					'end'  =>$date_end,
				);
			}
		} else {
			$dates[] = array(
				'start'=>$start .' 00:00:01',
				'end'  =>$end .' 23:59:59',
			);
		}		
	}
	public function week_start_end($start,$end){		
		$w_e_start=date("Y-m-d", strtotime('sunday this week', strtotime($start)));
		$w_s_end= date("Y-m-d", strtotime('monday this week', strtotime($end)));  
		$w_e_end=date("Y-m-d", strtotime('sunday this week', strtotime($end)));
		$datearrays = array();
		$begin = new DateTime($w_e_start);
		$endt = new DateTime($w_e_end);	
		$daterange = new DatePeriod($begin, new DateInterval('P1W'), $endt);
		$dates = array();
		$dateaarays[]=$start;
		foreach($daterange as $date){
			$daytt=$date->format("Y-m-d");
			$dateaarays[] = $daytt;
		}
		$dateaarays[]=$end;
		$dateaarays=array_unique($dateaarays);
		$k=0;
		for ($i=0; $i<count($dateaarays)-1; $i++) {
			if ($i==0) {
				$date_added=$dateaarays[$i];
			} else {
				$date_added=date('Y-m-d', strtotime($dateaarays[$i] .' +1 day'));
			}
			if ($k==0) {
					$dates[] = array(
						'start'=>$date_added .' 00:00:01',
						'end'  =>$dateaarays[$i+1] .' 23:59:59',
					); $k=1;
			} else {
					$dates[] = array(
						'start'=>$date_added .' 00:00:01',
						'end'  =>$dateaarays[$i+1] .' 23:59:59',
					);$k=0;
			}
		}
		return $dates;
	}
	public function chentukhoa($tukhoa_array,$description){
		/* Xử lý đợt sau */
		return $description;
	}
}
