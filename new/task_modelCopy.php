<?php
class TaskModel{

protected $mysqli;

/******database connection at Gun City*******/
public function __construct() {
		@require_once($_SERVER['DOCUMENT_ROOT'].'/lib/scripts/config.php');
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DEV);
		

		if (mysqli_connect_errno()) {
			error("mysqli_connect_error()");
			$this->mysqli = null;
		}
		else {
			$this->mysqli = $mysqli;

		}
}
/**********database connection at CPIT**************
public function __construct() {
	@require_once ("SQLConnection.php");
	$mysqli = SqlConnection :: connect('localhost','root','','taskmanagement' );
	$this->mysqli = $mysqli;
	} 
*/
/************toggle display 'create by table' by sort key word************/	
	function displayListOfTask($sort, $staff_id) {
	$output = array();
	$parentID = array();
	$parenttask = array();
	$ownedParentTask = array();
	$TaskInfo = array();
	
	if ($sort == 'parent') { // if sort == "parent" than do the rest
		$queryParent = "SELECT DISTINCT parent_task_id FROM task WHERE parent_task <> 0 AND task.status <> 4 AND staff_id =  $staff_id"; // stop repeate parent task id
			$stmt = $this->mysqli->prepare($queryParent);
	        
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $parentID = $result->fetch_all(MYSQLI_ASSOC);
					}
	            $stmt->close();
			}

			foreach ($parentID as $element) {
				$id = $element['parent_task_id']; 
				$queryOwnedParentTask = "SELECT task.task_id FROM task LEFT JOIN task_staff ON task.task_id = task_staff.task_id
										 WHERE task.staff_id = $staff_id AND task_staff.task_type = 1 AND task.task_id = $id AND task.status <> 4";
					$stmt = $this->mysqli->prepare($queryOwnedParentTask);
						if($stmt) {
								if ($stmt->execute()){
										$result = $stmt->get_result();
										$ownedParentTaskSingle = $result->fetch_all(MYSQLI_ASSOC);
										array_push($ownedParentTask,$ownedParentTaskSingle);
								}

								$stmt->close();	
							}


			}

			echo '<pre>';
			print_r($ownedParentTask);
			echo '</pre>';

			foreach ($ownedParentTask as $key => $value) {

					print_r($value);

				@$id = $value[0]['task_id'];

					print_r($id);
				$querySelectParent = "SELECT task.task_id AS TaskID, staff.name AS CreateBy, 
									  task.name AS TaskName , ETA, duration, task.status AS Status, 
									  task.priority AS Priority, parent_task_id AS ParentTask
									  FROM task LEFT JOIN staff ON task.staff_id = staff.id
									  LEFT JOIN task_staff ON task.task_id = task_staff.task_id
									  WHERE task.task_id = $id AND task.staff_id = $staff_id AND task_staff.task_type = 1
									  AND task.status <> 4";
											
						$stmtinfo = $this->mysqli->prepare($querySelectParent);					
						if($stmtinfo) {
							if ($stmtinfo->execute()){
									$result = $stmtinfo->get_result();
									$parenttask = $result->fetch_all(MYSQLI_ASSOC);
									array_push($TaskInfo, $parenttask);
							}

							$stmtinfo->close();	
						}
						$stmtcount = "";
			}			
				$count = count($TaskInfo);  
				if ($count != 0 ) {
					for($i = 0; $i < $count; $i++ ) {
					if (count($TaskInfo[$i])!=0) {	
						array_push($output, $TaskInfo[$i][0]);// strip the array to become 2 level Multidimensional Arrays
						} 
					}
				}
				
	} else { // if sort != "parent" then match the keyword to switch cases
		switch($sort) {
			case 'unCompleted':
				$whereClause = "task.status <> 4 AND staff.id = $staff_id AND task_staff.task_type = 1";
				break;
			case 'completed':
				$whereClause = "task.status = 4 AND staff.id = $staff_id AND task_staff.task_type = 1";
				break;	
			/*case 'subTask':
				$whereClause = "task.status <> 4 AND staff.id = $staff_id AND task.parent_task_id <> 0 AND  task_staff.task_type = 1";
				break;*/
			default:
				$whereClause = "task.status <> 4  AND staff.id = $staff_id AND task_staff.task_type = 1";
		}	
			
		$query = "SELECT task.task_id AS TaskID, staff.name AS CreateBy, 
				  task.name AS TaskName , ETA, duration, task.status AS Status, 
				  task.priority AS Priority, parent_task_id AS ParentTask
				  FROM task LEFT JOIN staff ON task.staff_id = staff.id
				  LEFT JOIN task_staff ON task.task_id = task_staff.task_id
				  WHERE $whereClause";

		$stmt = $this->mysqli->prepare($query);
			if($stmt){
				if ($stmt->execute()){
					$result = $stmt->get_result();
					$output = $result->fetch_all(MYSQLI_ASSOC);	
				}
				$stmt->close();
			}  
		}		
		return($output);
	}


	 
function displayDelegated_ShareTask ($type, $sort, $staff_id){

	switch($sort) {
		case 'unCompleted':
			$whereClause = "task.status <> 4";
			break;
		case 'completed':
			$whereClause = "task.status = 4";
			break;
		default:
			$whereClause = "task.status <> 4";
			} 

	$taskArray = array();
	$output = array(); 
	$allDelegArray = array();
	$delegArray = array();

	$queryTask = "SELECT DISTINCT task_staff.task_id AS TaskID FROM task_staff 
				  LEFT JOIN task ON task.task_id = task_staff.task_id
				  WHERE task_staff.staff_id = $staff_id AND task_staff.task_type = $type
				  AND $whereClause";;

	$stmt = $this->mysqli->prepare($queryTask);

	if($stmt) {
	        if ($stmt->execute()){
	            $result = $stmt->get_result();
	            $taskArray = $result->fetch_all(MYSQLI_ASSOC);
	            }
	            $stmt->close();
	        }  


	foreach ($taskArray as $element) {

		$newElement = $element["TaskID"];

		$queryDelegatedTask = "SELECT task.task_id AS TaskID, staff.name AS CreateBy, 
						task.name AS TaskName , ETA, duration, task.status AS Status, 
						task.priority AS Priority, parent_task_id AS ParentTask
						FROM task LEFT JOIN staff ON task.staff_id = staff.id
						LEFT JOIN task_staff ON task.task_id = task_staff.task_id
						WHERE task.task_id = $newElement" ;

			$stmt = $this->mysqli->prepare($queryDelegatedTask);

			if($stmt) {
		        if ($stmt->execute()){
		            $result = $stmt->get_result();
		            $output = $result->fetch_all(MYSQLI_ASSOC);
		            array_push($allDelegArray, $output);
		            }
		            $stmt->close();
		        }  
		 }  

		 $count = count( $allDelegArray);

		 for($i = 0; $i < $count; $i++ ) {
		 	array_push($delegArray, $allDelegArray[$i][0]);
		 }
		 
		return($delegArray);
	 }

	 
/***********display detail of a sigle task************/ 
function displayTaskInfo ($task_id) {

	$output = array();
	$parentName = array();
	$query = "SELECT task.name AS TaskName, task.description AS TaskDescription,
			  task.status AS Status, task.priority AS Priority, staff.name AS CreateBy,
			  task.created_date AS CreateDate, task.ETA, task.duration AS Duration,
			  parent_task_id AS ParentTask FROM task LEFT JOIN staff 
			  on task.staff_id = staff.id WHERE task.task_id = $task_id";

	 $stmt = $this->mysqli->prepare($query);
	        
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $output = $result->fetch_all(MYSQLI_ASSOC);
	            }
	            $stmt->close();
	        } 

	$parentID = $output[0]["ParentTask"];
	if ($parentID != 0) {
	$queryParenttask = "SELECT task.name AS parentTaskName FROM task WHERE task.task_id = $parentID";
	 $stmt = $this->mysqli->prepare($queryParenttask);
	        
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $parentName = $result->fetch_all(MYSQLI_ASSOC);
	                    array_push($output, $parentName[0]["parentTaskName"]);
	            }
	            $stmt->close();
	        } 
	    } 

	return($output);
}

/**************display info about all shared and delegate shaffs ****************/
function displayShare_DelegatedStaff($type, $task_id) {
	$output = array();
	$query = "SELECT task_staff.staff_id AS staffID, 
			  staff.name AS staffName, staff.phone1 AS staffContact, 
			  staff.email AS staffEmail FROM task_staff 
			  LEFT JOIN staff ON task_staff.staff_id = staff.id
			  WHERE task_staff.task_id = $task_id AND task_staff.task_type = $type";
	$stmt = $this->mysqli->prepare($query);
	        
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $output = $result->fetch_all(MYSQLI_ASSOC);
	            }
	            $stmt->close();
	        }  
	return($output);

}




function displayAchivedTask($staff_id) {

	$archiveArray = array(); 
	$output = array();   
	$allAchivedArray = array();
	$ArchiveArray = array(); 

	$queryAchive = "SELECT DISTINCT task_staff.task_id AS TaskID FROM task_staff 
				  LEFT JOIN task ON task.task_id = task_staff.task_id
				  WHERE task_staff.staff_id = $staff_id AND task_staff.task_type = 4
				  AND task.status <> 4";

	$stmt = $this->mysqli->prepare($queryAchive);

	if($stmt) {
	        if ($stmt->execute()){
	            $result = $stmt->get_result();
	            $archiveArray = $result->fetch_all(MYSQLI_ASSOC);
	            }
	            $stmt->close();
	        }  

	foreach ($archiveArray as $element) {

		$newElement = $element["TaskID"]; 

		$queryArchiveTask = "SELECT task.task_id AS TaskID, staff.name AS CreateBy, 
						task.name AS TaskName , ETA, duration, task.status AS Status, 
						task.priority AS Priority, parent_task_id AS ParentTask
						FROM task LEFT JOIN staff ON task.staff_id = staff.id
						LEFT JOIN task_staff ON task.task_id = task_staff.task_id
						WHERE task.task_id = $newElement " ;

			$stmt = $this->mysqli->prepare($queryArchiveTask);

			if($stmt) {
		        if ($stmt->execute()){
		            $result = $stmt->get_result();
		            $output = $result->fetch_all(MYSQLI_ASSOC);
		            array_push($allAchivedArray, $output); 
		            }
		            $stmt->close();
		        }  

		 }  

		 $count = count( $allAchivedArray);  

		 for($i = 0; $i < $count; $i++ ) {
		 	array_push($ArchiveArray, $allAchivedArray[$i][0]);
		 }
		return($ArchiveArray);
	 }



/***********display parent task for current task if there is one**********/
function displayparentTask($task_id) {
	$parentTaskID = array();
	$output = array();
	
	$queryPre = "SELECT task.parent_task_id FROM task WHERE task.parent_task = 1 AND task.task_id = $task_id";

	$stmt = $this->mysqli->prepare($queryPre);
	        
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $parentTaskID = $result->fetch_all(MYSQLI_ASSOC);		
	            }

	            $stmt->close();
	        }
	if (count($parentTaskID)!=0) {


		$parentID = $parentTaskID[0]['parent_task_id'];
		$query = "SELECT task.task_id AS TaskID, task.staff_id AS staffID,
				 task.name AS TaskName, staff.name AS CreateBy, ETA, duration, task.status AS Status, 
				 task.priority AS Priority FROM task LEFT JOIN staff ON task.staff_id = staff.id
				 LEFT JOIN task_staff ON task.task_id = task_staff.task_id
				 WHERE task.task_id = $parentID AND task_staff.task_type = 1";
				 
		$stmt = $this->mysqli->prepare($query);
	        
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $output = $result->fetch_all(MYSQLI_ASSOC);
	            }
	            $stmt->close();
			}
	}

	return($output);

}

/****************display all sub tasks which related to current task is there is any*******************/
function displaySubTask($task_id) {
	$subTaskInfo = array();
	$subTaskArray = array();
	$subTaskID = array();
	$output = array();
	$queryPre = "SELECT task.task_id FROM task WHERE task.parent_task_id = $task_id";
	$stmt = $this->mysqli->prepare($queryPre);
	if($stmt) {
	    if ($stmt->execute()){
	            $result = $stmt->get_result();
	            $subTaskID = $result->fetch_all(MYSQLI_ASSOC);
	    }
	        $stmt->close();
	}
	if (count($subTaskID)!=0) {

		foreach ($subTaskID as $element) {
			$theTaskID = $element['task_id'];
			$query = "SELECT task.task_id AS TaskID, staff.name AS CreateBy, task.staff_id AS staffID,
				 task.name AS TaskName , ETA, duration, task.status AS Status, 
				 task.priority AS Priority FROM task LEFT JOIN staff ON task.staff_id = staff.id
				 LEFT JOIN task_staff ON task.task_id = task_staff.task_id
				 WHERE task.task_id = $theTaskID AND task_staff.task_type =1";
				 
			$stmt = $this->mysqli->prepare($query);	 
				if($stmt) {
					if ($stmt->execute()){
							$result = $stmt->get_result();
							$subTaskInfo = $result->fetch_all(MYSQLI_ASSOC);
							array_push($subTaskArray, $subTaskInfo);
					}
					$stmt->close();
				}
			}

		}	

	$count = count($subTaskArray);
			for ($i=0 ; $i<$count; $i++) {
				array_push($output, $subTaskArray[$i][0]);
			}		
	return($output);
}

/**************display all staff infoamtion list to help task owner select right person for delegate or share task***************/

	function displayStaffInfo($type, $staffID) {

		switch ($type) {
			case 'fullInfo':
				$select = "id AS staffID, name AS staffName, store, email, phone1, phone2";
				break;
			case 'nameOnly':
				$select = "id As staffID, name AS staffName";
				break;
		}


		$output = array();

		$query = "SELECT $select FROM staff WHERE active = 1 AND staff.id <> $staffID";
		//$query = "SELECT $select FROM staff WHERE id <> $staffID";
		
		$stmt = $this->mysqli->prepare($query);
	        
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $output = $result->fetch_all(MYSQLI_ASSOC);	
	           	 }
	            $stmt->close();
			}
	
	return($output);

}


/*******DISPLAY TASK NAME FOR GIVEN USER FOR SELECT PARENT TASK(ONLY DISPLAY TASK WHICH CREATED, SHARED AND DELEGATED TO THIS USER)********/
// when comes to select parent task that task itself and all it sub tasks cant be selected. 
	function displayTaskName($staffID, $taskID) {

		$output = array();
		$query = "SELECT DISTINCT task.task_id AS ID, task.name AS Name FROM task LEFT JOIN task_staff 
				  ON task.task_id = task_staff.task_id WHERE task_staff.staff_id = $staffID AND task.task_id <> $taskID
				  AND task.parent_task_id <> $taskID AND task.status <> 4";

				  $stmt = $this->mysqli->prepare($query);
	        
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $output = $result->fetch_all(MYSQLI_ASSOC);	
	           	 }
	            $stmt->close();
			}
	
	return($output);

}

/**************  UPDATE TASK EDITABLE TABLE   *********************/

	function updateTaskString($task_id, $value, $type){
		
	$query = "UPDATE task SET $type = ? WHERE task_id = $task_id";

			if ($stmt = $this->mysqli->prepare($query)) {
	            $stmt->bind_param('s', $value);  // update only for type string
	            $stmt->execute();
	            $stmt->close();
	            
	            return true;
	            //return(array('result'=>'true')); 
	        }
		return false;
	}



	function updateTaskInt($task_id, $value, $type){

	$query = "UPDATE task SET $type = ? WHERE task_id = $task_id ";

			if ($stmt = $this->mysqli->prepare($query)) {
	            $stmt->bind_param('i', $value); // update only for type integer 
	            $stmt->execute();
	            $stmt->close();
	            return true;
	            //return(array('result'=>'true')); 
	        }
		return false;
	}


	function updateParentTask($task_id, $staff_id, $value){  // update parent task 

		$output = array();
		$subtasks = array($task_id);

		$querySubTasks = "SELECT task.task_id FROM task WHERE task.parent_task_id = $task_id"; // get all subtask id if there is any
			$stmt = $this->mysqli->prepare($querySubTasks);	 
				if($stmt) {
					if ($stmt->execute()){
							$result = $stmt->get_result();
							$output = $result->fetch_all(MYSQLI_ASSOC);
						}
						$stmt->close();
					} 

		$querytaskOwner = "SELECT staff_id AS staffID FROM task_staff WHERE task_type = 1 AND task_id = $value"; // check for who is the task owner
		$taskOwner = 0;
		$stmt = $this->mysqli->prepare($querytaskOwner);	 
				if($stmt) {
					if ($stmt->execute()){
							$result = $stmt->get_result();
							$taskOwner = $result->fetch_all(MYSQLI_ASSOC);
						}
						$stmt->close();
					} 
		if (count($taskOwner) == 0) {
			$taskOwner = 0;
		} else {
				$taskOwner = $taskOwner[0]['staffID']; // get the id of task owner
			}


		foreach ($output as $key => $theValue) {
			array_push($subtasks, $theValue['task_id']); // push all subtasks which belong to this task to an array.
		} 

			if (in_array($value, $subtasks)) { // MAKE SURE YOUR NEW PARENT TASK IS NOT ONE OF IT SUB TASK
				return false;

			} else if ($value == 0) {
				$parentTask = 0;
				$query = "UPDATE task SET parent_task = ?, parent_task_id = ? WHERE task_id = $task_id";
					if ($stmt = $this->mysqli->prepare($query)) {
		           		$stmt->bind_param('ii', $parentTask, $value); 
					    $stmt->execute();
					    $stmt->close();
					    return true;
					} 
				}else if ($value != 0 && $staff_id == $taskOwner) {
					$parentTask = 1;
					$query = "UPDATE task SET parent_task = ?, parent_task_id = ? WHERE task_id = $task_id";
						if ($stmt = $this->mysqli->prepare($query)) {
				           	$stmt->bind_param('ii', $parentTask, $value); 
							$stmt->execute();
							$stmt->close();
							return true;
							}
						} else if($value != 0 && $staff_id != $taskOwner){ 
							$parentTask = 1;
						$query = "UPDATE task SET parent_task = ?, parent_task_id = ? WHERE task_id = $task_id";
						if ($stmt = $this->mysqli->prepare($query)) {
				           	$stmt->bind_param('ii', $parentTask, $value); 
							$stmt->execute();
							$stmt->close();
							}
							$type = 2;
							$queryInsert ="INSERT INTO task_staff(task_id, staff_id, task_type)VALUES(?,?,?)";
								if($stmt = $this->mysqli->prepare($queryInsert)) {
								$stmt->bind_param('iii',$task_id, $taskOwner, $type);
									if ($stmt->execute()) {
										return true;	
										}
									$stmt->close();
								}

				} 
	}

			 


/*************remove staff from delegate list**************/
	function removeDelegeted_SharedStaff($type, $task_id, $staff_id) {
		$output["success"] = false;
			$query = "DELETE FROM task_staff WHERE task_id = ? AND staff_id = ? AND task_type = $type";

			if ($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param('ii',$task_id, $staff_id);
					if($stmt->execute()){
						$output["success"] = true;
					}
					$stmt->close();
			}
		return $output;
	}



/**************add new staff to delegate and shared list****************/

function insertDelegate_ShateStaff ($taskType, $task_id, $staff_id) {
	
	$output["success"] = false;
	$query = "INSERT INTO task_staff (task_id, staff_id, task_type)
			  VALUES(?,?,?)";


		if($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('iii',$task_id, $staff_id, $taskType);
				if ($stmt->execute()) {
					$output["success"] = true;	
			}
			$stmt->close();
		}
	
		return $output;
	}







/************archive and show for shared task ****************/

function toggleBetweenShare_ArchiveTask ($type, $task_id, $staff_id) {

	$query = "UPDATE task_staff SET task_type = $type WHERE task_id = ? AND staff_id = ?";

			if ($stmt = $this->mysqli->prepare($query)) {
	            $stmt->bind_param('ii', $task_id, $staff_id); // update only for type integer 
	            $stmt->execute();
	            $stmt->close();
	            return true;
	        }
		return false;
	}




/*********inset new task and sub task along with task_staff table*********/
function insertTask($taskName, $taskDes, $staffID, $ETA, $duration, $status, $priority, $parentTask, $parentID) {
	
	$output = array();
	$taskID = 0;
	$date = date("Y-m-d H:i:s");
	$query = "INSERT INTO task (name, description, staff_id, created_date, ETA, duration, status, priority, parent_task, parent_task_id)
			  VALUES (?,?,?,?,?,?,?,?,?,?)";

	if($stmt = $this->mysqli->prepare($query)) {
		$stmt->bind_param('ssissiiiii', $taskName, $taskDes, $staffID, $date, $ETA, $duration, $status, $priority, $parentTask, $parentID);
			if ($stmt->execute()){
					//$output["success"] = true;
				 	$taskID = $this->mysqli->insert_id;  // retrieve taskID from mySQL
					//print_r($taskID);
				}
				$stmt->close();
		} 


		if ($parentID != 0) { // if new task has a parent task than get the staff ID of that parent task ID. 
			$queryParentTaskOwner = "SELECT staff_id AS Owner FROM task WHERE task.task_id = $parentID";
			$parentTaskOwner = 0;
			$stmt = $this->mysqli->prepare($queryParentTaskOwner);	 
				if($stmt) {
					if ($stmt->execute()){
							$result = $stmt->get_result();
							$parentTaskOwner = $result->fetch_all(MYSQLI_ASSOC);
						}
						$stmt->close();
					} 

			$parentTaskOwner = $parentTaskOwner[0]['Owner'];

			} else {
				$parentTaskOwner = 0;
				}

		
	 if ($staffID != $parentTaskOwner &&  $parentTaskOwner != 0) { // is the parent task owner is not current task creater than the parent task owner will shared view to this task.

		$query2 = "INSERT INTO task_staff (task_id, staff_id, task_type)
				VALUES (?,?,?)";
		$typeOwn = 1;
		if($stmt = $this->mysqli->prepare($query2)) {
			$stmt->bind_param('iii', $taskID, $staffID, $typeOwn);
			if ($stmt->execute()){
				}
				$stmt->close();
			}

		$query3 = "INSERT INTO task_staff (task_id, staff_id, task_type)
				VALUES (?,?,?)";
		$typeShare = 2;
		if($stmt = $this->mysqli->prepare($query2)) {
			$stmt->bind_param('iii', $taskID, $parentTaskOwner, $typeShare);
			if ($stmt->execute()){
					$output["success"] = true;
				}
				$stmt->close();
			}
		} 
		else { 
		$query2 = "INSERT INTO task_staff (task_id, staff_id, task_type)
				VALUES (?,?,?)";
		$type = 1;
		if($stmt = $this->mysqli->prepare($query2)) {
			$stmt->bind_param('iii', $taskID, $staffID, $type);
			if ($stmt->execute()){
					$output["success"] = true;
				}
				$stmt->close();
		}

	}


	return $output;
	exit;	
	} 
	
	
	
	
	
	
}

?>