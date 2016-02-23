<?php
class TaskModel{

protected $mysqli;

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


	function displayTopLevelTask($sort, $staff_id) {
	$output = array();
	$TaskInfo = array();
	$taskCount = array();
	$percentage = array();
	$parentName = array();
	$temp = array();

		switch($sort) {
			case 'unCompleted':
				$queryInfo = "SELECT DISTINCT task.task_id AS TaskID, staff.name AS CreateBy, task.name AS TaskName , 
						ETA, duration, task.status AS Status, task.priority AS Priority, parent_task_id
						FROM task LEFT JOIN staff ON task.staff_id = staff.id WHERE parent_task = 1 
						AND staff_id = $staff_id AND task.status <> 4";

				break;
			case 'completed':
				$queryInfo = "SELECT DISTINCT task.task_id AS TaskID, staff.name AS CreateBy, task.name AS TaskName , 
						ETA, duration, task.status AS Status, task.priority AS Priority, parent_task_id
						FROM task LEFT JOIN staff ON task.staff_id = staff.id WHERE parent_task = 1 
						AND staff_id = $staff_id AND task.status = 4";

				break;	
			default:
				$queryInfo = "SELECT DISTINCT task.task_id AS TaskID, staff.name AS CreateBy, task.name AS TaskName , 
						ETA, duration, task.status AS Status, task.priority AS Priority, parent_task_id
						FROM task LEFT JOIN staff ON task.staff_id = staff.id WHERE parent_task = 1 
						AND staff_id = $staff_id AND task.status <> 4";

		}	

			$stmt = $this->mysqli->prepare($queryInfo);
	        
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $TaskInfo = $result->fetch_all(MYSQLI_ASSOC);
					}
	            $stmt->close();
			}
	
			foreach ($TaskInfo as $key => $value) { //total numer of finish task/total number of subtask 
				$id = $value['TaskID'];
				$querytaskCount = "SELECT ROUND((SUM(CASE WHEN parent_task_id = $id AND status = 4 THEN 1 ELSE 0 END)/
								   SUM(CASE WHEN parent_task_id = $id THEN 1 ELSE 0 END)), 2) AS completedLevel, 
								   SUM(CASE WHEN parent_task_id = $id THEN 1 ELSE 0 END) AS numberOfSub FROM task";
	   
					$stmtcount = $this->mysqli->prepare($querytaskCount);					
						if($stmtcount) {
							if ($stmtcount->execute()){
									$result = $stmtcount->get_result();
									$taskCount = $result->fetch_all(MYSQLI_ASSOC);
									array_push($percentage, $taskCount);
							}

							$stmtcount->close();	
						}
				}


				$count = count($TaskInfo);  // merge the array of task details and percentage of compelion to together
				if ($count != 0 ) {
					for($i = 0; $i < $count; $i++ ) {
					if (count($TaskInfo[$i])!=0) {	
						array_push($output, array_merge($TaskInfo[$i], $percentage[$i][0]));
						} 
					}
				}
				return($output);
	}

	 
	function displayListOfTask($staff_id) {
	$output = array();
	$parentName = array();
	$temp = array();


		$query = "SELECT DISTINCT task.task_id AS TaskID, staff.name AS CreateBy, 
				  task.name AS TaskName , ETA, duration, task.status AS Status, 
				  task.priority AS Priority, parent_task_id 
				  FROM task LEFT JOIN staff ON task.staff_id = staff.id
				  LEFT JOIN task_staff ON task.task_id = task_staff.task_id
				  WHERE task.status = 4 AND staff.id = $staff_id AND task_staff.task_type = 1 
				  AND parent_task = 0";

		$stmt = $this->mysqli->prepare($query);
			if($stmt){
				if ($stmt->execute()){
					$result = $stmt->get_result();
					$TaskInfo = $result->fetch_all(MYSQLI_ASSOC);	
				}
				$stmt->close();
			}  

			foreach ($TaskInfo as $key => $value) {
				$parent_id = $value['parent_task_id'];

				$queryParent = "SELECT task.name AS ParentTask FROM task WHERE task.task_id = $parent_id";
				$stmt = $this->mysqli->prepare($queryParent);

				if($stmt) {
				        if ($stmt->execute()){
				            $result = $stmt->get_result();
				            $temp = $result->fetch_all(MYSQLI_ASSOC); 

							$count = count($temp);
								if ($count == 0) {
									array_push($parentName, array('ParentTask'=>'N/A'));
								} else 
				           			array_push($parentName, $temp[0]);
				            }
				            $stmt->close();
				        }  
					}

			$count = count($TaskInfo);  // merge the array of task details and percentage of compelion to together
				if ($count != 0 ) {
					for($i = 0; $i < $count; $i++ ) {
					if (count($TaskInfo[$i])!=0) {	
						array_push($output, array_merge($TaskInfo[$i], $parentName[$i]));
						} 
					}
				}	
		
		return($output);
	}

	 
	function displayExpandTask($task_id, $staff_id) {
		$TaskInfo = array();
		$output = array();
		$taskCount = array();
		$percentage = array();

		$query = "SELECT DISTINCT task.task_id AS TaskID, staff.name AS CreateBy, 
				  task.name AS TaskName , ETA, duration, task.status AS Status, 
				  task.priority AS Priority FROM task LEFT JOIN staff ON task.staff_id = staff.id
				  LEFT JOIN task_staff ON task.task_id = task_staff.task_id
				  WHERE task.parent_task_id = $task_id AND task.staff_id = $staff_id";

		$stmt = $this->mysqli->prepare($query);
	        
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $TaskInfo = $result->fetch_all(MYSQLI_ASSOC);
					}
	            $stmt->close();
			}

/*echo "<pre>";
print_r($TaskInfo);
echo "</pre>";*/
			foreach ($TaskInfo as $key => $value) { //total numer of finish task/total number of subtask 
				$id = $value['TaskID'];
				$querytaskCount = "SELECT ROUND((SUM(CASE WHEN parent_task_id = $id AND status = 4 THEN 1 ELSE 0 END)/
								   SUM(CASE WHEN parent_task_id = $id THEN 1 ELSE 0 END)), 2) AS completedLevel, 
								   SUM(CASE WHEN parent_task_id = $id THEN 1 ELSE 0 END) AS numberOfSub FROM task";
	   
					$stmtcount = $this->mysqli->prepare($querytaskCount);					
						if($stmtcount) {
							if ($stmtcount->execute()){
									$result = $stmtcount->get_result();
									$taskCount = $result->fetch_all(MYSQLI_ASSOC);
									array_push($percentage, $taskCount);
							}

							$stmtcount->close();	
						}
				}


		$count = count($TaskInfo);  // merge the array of task details and percentage of compelion to together
				if ($count != 0 ) {
					for($i = 0; $i < $count; $i++ ) {
					if (count($TaskInfo[$i])!=0) {	
						array_push($output, array_merge($TaskInfo[$i], $percentage[$i][0]));
						} 
					}
				}	
		
		return($output);

	}


function displayDelegated_ShareTask ($type, $sort, $staff_id){

	$output = array(); 
	$info = array();
	$temp = array();
	$parentName = array();

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

$queryTask = "SELECT DISTINCT task.task_id AS TaskID, staff.name AS CreateBy, 
				  task.name AS TaskName , ETA, duration, task.status AS Status, 
				  task.priority AS Priority, parent_task_id FROM task LEFT JOIN staff ON task.staff_id = staff.id
				  LEFT JOIN task_staff ON task.task_id = task_staff.task_id
				  WHERE task.task_id IN (SELECT DISTINCT task_staff.task_id AS TaskID FROM task_staff 
				  LEFT JOIN task ON task.task_id = task_staff.task_id
				  WHERE task_staff.staff_id = $staff_id AND task_staff.task_type = $type AND $whereClause)";

	$stmt = $this->mysqli->prepare($queryTask);

	if($stmt) {
	        if ($stmt->execute()){
	            $result = $stmt->get_result();
	            $info = $result->fetch_all(MYSQLI_ASSOC);
	            }
	            $stmt->close();
	        } 


foreach ($info as $key => $value) {
	$parent_id = $value['parent_task_id'];

	$query = "SELECT task.name AS ParentTask FROM task WHERE task.task_id = $parent_id";
	$stmt = $this->mysqli->prepare($query);

	if($stmt) {
	        if ($stmt->execute()){
	            $result = $stmt->get_result();
	            $temp = $result->fetch_all(MYSQLI_ASSOC); 

				$count = count($temp);
					if ($count == 0) {
						array_push($parentName, array('ParentTask'=>'N/A'));
					} else 
	           			array_push($parentName, $temp[0]);
	            }
	            $stmt->close();
	        }  
		}

	$count = count($info);  // merge the array of task details and parent task name together
				if ( $count != 0 ) {
					for($i = 0; $i < $count; $i++ ) {
					if (count($info[$i])!=0) {	
						array_push($output, array_merge($info[$i],$parentName[$i]));
						} 
					}
				}
	 
		return($output);
	 }

	 
/***********display detail of a sigle task************/ 
function displayTaskInfo ($task_id) {

	$output = array();

	$query = "SELECT task.name AS TaskName, task.description AS TaskDescription, task.status AS Status, 
			  task.priority AS Priority, staff.name AS CreateBy, task.created_date AS CreateDate, task.ETA, 
			  task.duration AS Duration, (SELECT task.name FROM task WHERE task_id IN (SELECT parent_task_id 
			  FROM task WHERE task.task_id = $task_id)) AS parentName
			  FROM task LEFT JOIN staff on task.staff_id = staff.id WHERE task.task_id = $task_id";

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

/**************display info about all shared and delegate shaffs ****************/
	function displayShare_DelegatedStaff($type, $task_id) {
	switch ($type) {
		case 2: 
		$whereCluase = " (task_staff.task_id = $task_id && task_staff.task_type = 2) || (task_staff.task_id = $task_id && task_staff.task_type = 4)";
		break;
		case 3: 
		$whereCluase = "task_staff.task_id = $task_id &&  task_staff.task_type = 3";
		break;
	}
	$output = array();
	$query = "SELECT task_staff.staff_id AS staffID, 
			  staff.name AS staffName, 
			  staff.email AS staffEmail FROM task_staff 
			  LEFT JOIN staff ON task_staff.staff_id = staff.id
			  WHERE $whereCluase";
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

	$output = array();   

	$queryAchive = "SELECT DISTINCT task.task_id AS TaskID, staff.name AS CreateBy, 
						task.name AS TaskName , ETA, duration, task.status AS Status, 
						task.priority AS Priority, parent_task_id AS ParentTask
						FROM task LEFT JOIN staff ON task.staff_id = staff.id
						LEFT JOIN task_staff ON task.task_id = task_staff.task_id
						WHERE task.task_id IN (SELECT DISTINCT task_staff.task_id AS TaskID 
						FROM task_staff LEFT JOIN task ON task.task_id = task_staff.task_id
				      	WHERE task_staff.staff_id = $staff_id AND task_staff.task_type = 4 AND task.status <> 4)";

	$stmt = $this->mysqli->prepare($queryAchive);

	if($stmt) {
	        if ($stmt->execute()){
	            $result = $stmt->get_result();
	            $output = $result->fetch_all(MYSQLI_ASSOC);
	            }
	            $stmt->close();
	        }  

		return($output);
	 }



/***********display parent task for current task if there is one**********/
function displayparentTask($task_id) {
	$parentTaskID = array();
	$output = array();
	

	$query = "SELECT DISTINCT task.task_id AS TaskID, task.staff_id AS staffID,
				 task.name AS TaskName, staff.name AS CreateBy, ETA, duration, task.status AS Status, 
				 task.priority AS Priority FROM task LEFT JOIN staff ON task.staff_id = staff.id
				 LEFT JOIN task_staff ON task.task_id = task_staff.task_id
				 WHERE task_staff.task_type = 1 AND task.task_id IN (SELECT task.parent_task_id 
				 FROM task WHERE task.parent_task_id <> 0 AND task.task_id = $task_id)" ;
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

/****************display all sub tasks which related to current task is there is any*******************/
function displaySubTask($task_id) {
;
	$output = array();
	$query = "SELECT task.task_id AS TaskID, staff.name AS CreateBy, task.staff_id AS staffID,
				 task.name AS TaskName , ETA, duration, task.status AS Status, 
				 task.priority AS Priority FROM task LEFT JOIN staff ON task.staff_id = staff.id
				 LEFT JOIN task_staff ON task.task_id = task_staff.task_id
				 WHERE task_staff.task_type =1 AND task.task_id IN (SELECT task.task_id 
				 FROM task WHERE task.parent_task_id = $task_id)";
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

/**************display all staff infoamtion list to help task owner select right person for delegate or share task***************/

	function displayStaffInfo($staffID) {

		$output = array();

		
		$query = "SELECT id AS staffID, name AS staffName FROM staff WHERE staff.id <> $staffID AND active = 1 ";
		//$query = " SELECT id AS staffID, name AS staffName FROM staff WHERE staff.id <> $staffID ";
		
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
// when comes to select task created by user or delegated to this user and all it sub tasks cant be selected. 
	function displayTaskName($staffID, $taskID) {

		$output = array();
		$query = "SELECT DISTINCT task.task_id AS ID, task.name AS Name FROM task LEFT JOIN task_staff 
				  ON task.task_id = task_staff.task_id WHERE task_staff.staff_id = $staffID AND task.task_id <> $taskID
				  AND task.parent_task_id <> $taskID AND task.status <> 4 AND (task_staff.task_type <> 2 && task_staff.task_type <> 4)";

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


/***************return page for information about percentage of parent task completion***************/
	function displayCompletedLevel($taskID){
		$query = "SELECT ROUND((SUM(CASE WHEN parent_task_id = $taskID AND status = 4 THEN 1 ELSE 0 END)/
				  SUM(CASE WHEN parent_task_id = $taskID THEN 1 ELSE 0 END)), 2) AS completedLevel FROM task"; 
		
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


	function isParentTask ($taskID) {
		$outCome = "";   
		$queryParent = "SELECT task_id FROM task WHERE parent_task_id = $taskID";

		$stmt = $this->mysqli->prepare($queryParent);
	    
	        if($stmt) {
	            if ($stmt->execute()){
	                    $result = $stmt->get_result();
	                    $outputParent = $result->fetch_all(MYSQLI_ASSOC);	
	           	 }
	            $stmt->close();
			}

		$querySub= "SELECT task_id FROM task WHERE task_id = $taskID AND parent_task_id <> 0";

			$stmt = $this->mysqli->prepare($querySub);
				if($stmt) {
		            if ($stmt->execute()){
		                    $result = $stmt->get_result();
		                    $outputSub = $result->fetch_all(MYSQLI_ASSOC);	
		           	 }
		            $stmt->close();
				}

		$parent = count($outputParent);
		$sub = count($outputSub);


			if ( $parent == 0 && $sub == 0) {
				$outCome = 00; //not top level task and not sub task
			} else if($parent == 0 && $sub != 0) {
				$outCome = 01; //not a top level and is a sub task
			} else if($parent != 0 && $sub == 0) {
				$outCome = 10; // is a top level task and not a sub task
			} else {
				$outCome = 11; // is a top level task and is a sub task 
			}
			return $outCome;
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

			} else if ($value == 0) { // user select not to be a sub task, than i will become a top level task
				$parentTask = 1;
				$query = "UPDATE task SET parent_task = ?, parent_task_id = ? WHERE task_id = $task_id";
					if ($stmt = $this->mysqli->prepare($query)) {
		           		$stmt->bind_param('ii', $parentTask, $value); 
					    $stmt->execute();
					    $stmt->close();
					    return true;
					}  
				}else if ($value != 0 && $staff_id == $taskOwner) { // chenge the top level task form 1 to an other but new top level task still belong to this user
					$parentTask = 0;
					$query = "UPDATE task SET parent_task = ?, parent_task_id = ? WHERE task_id = $task_id";
						if ($stmt = $this->mysqli->prepare($query)) {
				           	$stmt->bind_param('ii', $parentTask, $value); 
							$stmt->execute();
							$stmt->close();
							return true;
							}
						} else if($value != 0 && $staff_id != $taskOwner){ // chenge the top level task form one to an other but new top level task not belong to this user
							$parentTask = 1;
						$query = "UPDATE task SET parent_task = ?, parent_task_id = ? WHERE task_id = $task_id";
						if ($stmt = $this->mysqli->prepare($query)) {
				           	$stmt->bind_param('ii', $parentTask, $value); 
							$stmt->execute();
							$stmt->close();
							}
							$type = 2;  // auto share task to toplevel task owner 
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

		
	 if ($staffID != $parentTaskOwner &&  $parentTaskOwner != 0) { // if the parent task owner is not current task creater than the parent task owner will shared view to this task.

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
		else {  // owner = creater 
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