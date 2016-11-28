<?php 
namespace Application\Controller\Training;
use Controller;
use User;
use UserInfo;
class Dashboard extends Controller {

	public function get_course_dropdown($tablePrefix=NULL){
		$db = \Database::connection();
		$courses = $db->getAll("SELECT * FROM C5CBT ORDER BY name");
		$output .= '<select name="p" id="courseID">
		';
		foreach ($courses as $course){
			$selected = ($tablePrefix == $course['tablePrefix'] ? 'selected="selected"' : '');
			$output .= '<option value="'.$course['tablePrefix'].'" '.$selected.'>'.$course['name'].'</option>
			';
		}
		$output .= '</select>';
		return $output;
	}
	
	public function get_course_name($tablePrefix){
		$db = \Database::connection();
		if ($tablePrefix){
		return $db->getOne("SELECT name from C5CBT WHERE tablePrefix LIKE '$tablePrefix'");
		} else {
		return false;
		}
	}
	

	public function get_examType($tablePrefix){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_types';
		return $db->GetAll("SELECT * FROM $table");
	}
	
	public function edit_type($tablePrefix, $post_array){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_types';
		$oldname = $db->getOne("SELECT name FROM $table WHERE typeID =".$post_array['typeID']);
		//if ($db->AutoExecute($table, $post_array, "UPDATE", "typeID =".$post_array['typeID'])){
        if ($db->update($table, $post_array, array("typeID" => $post_array['typeID']))){
			Loader::model('collection_attributes');
			$ak=CollectionAttributeKey::getByHandle('examType');
			$so= SelectAttributeTypeOption::getByValue($oldname, $ak);
			$so->delete();
			SelectAttributeTypeOption::add( $ak, $_POST['name'], 0); 
			return true;
		} else {
			return false;
		}	
	}
	
	public function new_type($tablePrefix, $post_array){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_types';
		if ($db->insert($table, $post_array)){
			Loader::model('collection_attributes');
			$examTypeAttr=CollectionAttributeKey::getByHandle('examType'); //
		if( is_object($examTypeAttr)){ // if its not already installed, do so
			$ak= CollectionAttributeKey::getByHandle('examType');
			// Add Options
			SelectAttributeTypeOption::add( $ak, $_POST['name'], 0);    
		}
			return true;
		} else {
			return false;
		}	
	}
	
	public function delete_type($tablePrefix, $name) {
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_types';
		if ($db->execute("DELETE  FROM $table WHERE name LIKE '$name'")){
			Loader::model('collection_attributes');
			$ak=CollectionAttributeKey::getByHandle('examType');
			$so= SelectAttributeTypeOption::getByValue($name, $ak);
			$so->delete();
			return true;
		} else {
			return false;
		}

	}

	public function get_shippingOptions($tablePrefix){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_shippingOptions';
		return $db->GetAll("SELECT * FROM $table");
	}

	public function edit_shipping($tablePrefix, $post_array){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_shippingOptions';
		if ($db->update($table, $post_array, array("shippingID" => $post_array['shippingID']))){
			return true;
		} else {
			return false;
		}	
	}
	
	public function new_shipping($tablePrefix, $post_array){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_shippingOptions';
		if ($db->insert($table, $post_array)){
			return true;
		} else {
			return false;
		}	
	}
	
	public function delete_shipping($tablePrefix, $name) {
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_shippingOptions';
		if ($db->execute("DELETE  FROM $table WHERE name LIKE '$name'")){
			return true;
		} else {
			return false;
		}
	}
	
		public function get_features($tablePrefix){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_features';
		return $db->GetAll("SELECT * FROM $table");
	}

	public function edit_feature($tablePrefix, $post_array){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_features';
		if ($db->update($table, $post_array,array("featureID" => $post_array['featureID']))){
			return true;
		} else {
			return false;
		}	
	}
	
	public function new_feature($tablePrefix, $post_array){
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_features';
		if ($db->insert($table, $post_array)){
			return true;
		} else {
			return false;
		}	
	}
	
	public function delete_feature($tablePrefix, $name) {
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_features';
		if ($db->execute("DELETE  FROM $table WHERE name LIKE '$name'")){
			return true;
		} else {
			return false;
		}
	}
	
	public function save_question($tablePrefix, $question, $answers, $correct, $practice=FALSE, $final=false, $group=NULL, $questionID=NULL){
		$db= \Database::connection();
		$table = "C5CBT_".$tablePrefix."_questions";
		$params['question'] = $question;
		$params['answers'] = serialize($answers);
		$params['correct'] = $correct;
		$params['practice'] = $practice;
		$params['final'] = $final;
		$params['questionGroup'] = $group;
		
		if ($questionID) { //Update Existing Question
			if ($questionID == "new"){
			$action = $db->insert($table, $params);
			} else {
			$action = $db->update($table, $params, array("questionID" => $questionID));
			}
			if($action){
			return true;
			} else {
			return false;
			}
		} else { //Add New
			if ($db->insert($table, $params)){
			return true;
			} else {
			return false;
			}
		}
	}
	
	public function delete_question($tablePrefix, $id) {
		$db = \Database::connection();
		$table = 'C5CBT_'.$tablePrefix.'_questions';
		if ($db->execute("DELETE  FROM $table WHERE questionID = $id")){
			return true;
		} else {
			return false;
		}
	}
		public function get_all_orders($prefixArray, $keyword = false, $limit = 50, $offset = 0, $filter="all", $since = NULL, $mark_shipped = FALSE, $add_reshipments = FALSE) {

		$db = \Database::connection();

		$all_orders = array();
		
		
		
		foreach ($prefixArray as $prefix){
			unset($where);
			$table = "C5CBT_{$prefix}_orders";
			$completion_table =  "C5CBT_{$prefix}_completions";
			
			
			if($keyword){
			
			$where = "LEFT JOIN Users ON {$table}.uID = Users.uID LEFT JOIN UserSearchIndexAttributes ON {$table}.uID = UserSearchIndexAttributes.uID WHERE UserSearchIndexAttributes.ak_firstName LIKE '%{$keyword}%' OR UserSearchIndexAttributes.ak_lastName LIKE '%{$keyword}%' OR Users.uEmail LIKE '%{$keyword}%' ";
			}
			
			
			if ($filter == "all"){
			
			
			$orders = $db->getAll("SELECT $table.*, '$prefix' AS course FROM $table $where ORDER BY $table.timestamp desc");
			}
			
			if ($filter == "active"){
				if ($where){
				$where = "$where AND ";
				}
			
			$orders = $db->getAll("SELECT $table.*, '$prefix' AS course FROM $table $where WHERE $table.completionID IS NULL ORDER BY $table.timestamp desc");
			}
			
			if ($filter == "activenext"){
				if ($where){
				$where = "$where AND ";
				}
			$orders = $db->getAll("SELECT $table.*, '$prefix' AS course FROM $table $where WHERE $table.shipping LIKE '%Next Day%' AND $table.completionID IS NULL ORDER BY $table.timestamp desc");
			}
			
			if ($filter == "fail"){
			
					if ($where){
						$where = "$where AND ";
					}
			$orders = $db->getAll("SELECT $table.*, '$prefix' AS course FROM $table $where WHERE $table.completionID = 0 $where ORDER BY $table.timestamp desc");
			}
			
			if ($filter == "complete"){
			//Database::setDebug(1);
			if ($where){
						$where = "$where AND";
					} else {
					$where= "WHERE";
					}
					
				$sql = "SELECT {$table}.*, '$prefix' AS course, {$table}.typeID as typeID FROM $table LEFT JOIN $completion_table ON {$completion_table}.completionID = {$table}.completionID $where {$completion_table}.fulfilled = 1 ORDER BY {$table}.timestamp desc";

				$orders = $db->getAll($sql);
			}
			
			if ($filter == "ship"){
			//Database::setDebug(1);
			if ($where){
						$where = "$where AND";
					} else {
					$where= "WHERE";
					}
					
				$sql = "SELECT {$table}.*, '$prefix' AS course, {$table}.typeID as typeID, {$completion_table}.fulfilledTimestamp as fulfilledTimestamp FROM $table LEFT JOIN $completion_table ON {$completion_table}.completionID = {$table}.completionID $where {$completion_table}.fulfilled = 0 ORDER BY {$table}.timestamp desc";
				$orders = $db->getAll($sql);
			}
			
			if ($filter == "shipregular"){
			$timestamp = date("Y-m-d H:i:s", $since);
			//Database::setDebug(1);
			if ($where){
						$where = "$where AND";
					} else {
					$where= "WHERE";
					}
					
				$sql = "SELECT {$table}.*, '$prefix' AS course, {$table}.typeID as typeID, {$completion_table}.fulfilledTimestamp as fulfilledTimestamp, UserSearchIndexAttributes.* FROM $table LEFT JOIN UserSearchIndexAttributes ON {$table}.uID = UserSearchIndexAttributes.uID LEFT JOIN $completion_table ON {$completion_table}.completionID = {$table}.completionID $where {$table}.shipping NOT LIKE '%Next Day%' AND {$completion_table}.fulfilled = 0 AND {$completion_table}.fulfilledTimestamp > '$timestamp' ORDER BY {$table}.timestamp desc";
				$orders = $db->getAll($sql);
			}
			
			
			if ($filter == "shipnext"){
			$timestamp = date("Y-m-d H:i:s", $since);
			//Database::setDebug(1);
			if ($where){
						$where = "$where AND";
					} else {
					$where= "WHERE";
					}
					
				$sql = "SELECT {$table}.*, '$prefix' AS course, {$table}.typeID as typeID, {$completion_table}.fulfilledTimestamp as fulfilledTimestamp, UserSearchIndexAttributes.* FROM $table LEFT JOIN UserSearchIndexAttributes ON {$table}.uID = UserSearchIndexAttributes.uID LEFT JOIN $completion_table ON {$completion_table}.completionID = {$table}.completionID $where {$table}.shipping LIKE '%Next Day%' AND {$completion_table}.fulfilled = 0 AND {$completion_table}.fulfilledTimestamp > '$timestamp' ORDER BY {$table}.timestamp desc";
				$orders = $db->getAll($sql);
			}
			
			
			if ($orders){
			$all_orders = array_merge($all_orders, $orders);
			}
		}
		
		if ($add_reshipments){
		$reships = $db->getAll("SELECT * FROM C5CBT_reshipments");
		$timestamp = date("Y-m-d H:i:s", $since);
		foreach($reships as $r){
		
			$table = "C5CBT_{$r['tablePrefix']}_orders";
			$completion_table =  "C5CBT_{$r['tablePrefix']}_completions";
		
					
				$sql = "SELECT {$table}.*, '{$r['tablePrefix']}' AS course, {$table}.typeID as typeID, {$completion_table}.fulfilledTimestamp as fulfilledTimestamp, UserSearchIndexAttributes.* FROM $table LEFT JOIN UserSearchIndexAttributes ON {$table}.uID = UserSearchIndexAttributes.uID LEFT JOIN $completion_table ON {$completion_table}.completionID = {$table}.completionID WHERE {$table}.orderID = {$r['orderID']} ORDER BY {$table}.timestamp desc";
				$orders = $db->getAll($sql);
				if ($orders){
				$all_orders = array_merge($all_orders, $orders);
				}
				$db->execute("DELETE FROM C5CBT_reshipments WHERE reshipmentID = {$r['reshipmentID']}");
		}
		}
		
		if ($filter == "ship" || $filter == "shipnext"){
		usort($all_orders, 'self::completed_timestamp_sort');
		} else {
        usort($all_orders, 'self::timestamp_sort');
		}
		
		if ($filter == "shipregular"){
		usort($all_orders, 'self::lastName_sort');
		}
		
		if ($mark_shipped){
		//mark all shipped here
			foreach ($all_orders as $o){
				$tableName = "C5CBT_".$o['course']."_completions";
				$update['fulfilled'] = 1;
				$db->update($tableName, $update,array("orderID" => $o['orderID']));
			}
		}
		
		$paged_orders['total'] = count($all_orders);
		$paged_orders['orders'] = array_slice($all_orders, $offset, $limit); 
		$paged_orders['limit'] = $limit; 
		$paged_orders['offset'] = $offset; 
		
		return $paged_orders;

	}
	
	static function timestamp_sort( $a, $b) {
		$a['unix'] = strtotime($a["timestamp"]);
		$b['unix'] = strtotime($b["timestamp"]);
		return $b["unix"] - $a["unix"];
	}
	
	static function completed_timestamp_sort( $a, $b) {
		$a['unix'] = strtotime($a["fulfilledTimestamp"]);
		$b['unix'] = strtotime($b["fulfilledTimestamp"]);
		return $b["unix"] - $a["unix"];
	}
	
	static function lastName_sort( $a, $b) {
		$a = trim($a['ak_lastName']);
		$b = trim($b['ak_lastName']);
		return strcmp($a,$b);
	}
	
	public function print_pagination($base_url, $limit = 25, $offset = 0, $total = 0, $filter = false){
	
	$base_url = strtok($base_url, '?'); // strip the vars

	if ($total > $limit) { //pagination is needed
		
		if ($offset == 0) { // figure out if this is first page
			$prev = "disabled";
		} else {
			$prev = "";
			$prev_offset = $offset - $limit;
			$prev_url = $base_url . "?o={$prev_offset}&l={$limit}&filter={$filter}";
		}
		
		if ($offset > ($total - $limit)){ // figure out if this is the last page
			$next = "disabled";
		} else {
			$next = "";
			$next_offset = $offset + $limit;
			$next_url = $base_url . "?o={$next_offset}&l={$limit}&filter={$filter}";
		}
		
		$output .= '<div class="ccm-pagination-wrapper"><ul class="pagination">';
		$output .= '<li class="prev '.$prev.'"><a href="'.$prev_url.'">&larr; Previous</a></li>';
		
		$pages = $total / $limit;
		if ($pages > 10) { //too many pages to print them all
		
			$page = 0;

			while ($page <= $pages){
		
			$disp_page = $page + 1; 
			$this_offset = $page * $limit;
			
			if ($offset == $this_offset) {
			$active = 'class="active"';
			} else {
			$active = "";
			}
			
			$url = $base_url . "?o={$this_offset}&l={$limit}&filter={$filter}";
			
			// adjust the max and min offsets for wrap around
			$low_offset = $offset - ($limit *6);
			if ($low_offset < 0) {
			$high_adjust =  abs($low_offset);
			}
			$high_offset = ($offset + ($limit*6)) + $high_adjust;
			if ($high_offset > $total) {
			$low_adjust = $high_offset - $total;
			}
			$low_offset = $low_offset - $low_adjust;
			
			if ($this_offset >= $low_offset && $this_offset <= $high_offset ){ // in the first ten results
			$output .= '<li '.$active.'><a href="'.$url.'">'.$disp_page.'</a></li>';
			}
			$page++;
			}
			
		
		} else { // print all the pages
			$page = 0;

			while ($page <= $pages){
		
			$disp_page = $page + 1; 
			$this_offset = $page * $limit;
			
			if ($offset == $this_offset) {
			$active = 'class="active"';
			} else {
			$active = "";
			}
			
			$url = $base_url . "?o={$this_offset}&l={$limit}&filter={$filter}";
			$output .= '<li '.$active.'><a href="'.$url.'">'.$disp_page.'</a></li>';
			$page++;
			}
		}
		
		$output .= '<li class="next '.$next.'"><a href="'.$next_url.'">Next &rarr;</a></li>';
		$output .= '</ul></div>';
	}
	$first_result = $offset + 1;
	$last_result = $offset + $limit;
	
	if ($last_result > $total) {
	$last_result = $total;
	}
	
	$output .= "Showing $first_result - $last_result of $total Total Results";
	
	return $output;
	}
	

}