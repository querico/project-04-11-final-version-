<?php

class task {

    public function process($verb, $args){
		/**************task model location for Gun city development**************/
        @require_once($_SERVER['DOCUMENT_ROOT'].'/development/rui/task_model.php');
		
		
        $this->to = new TaskModel();

        if (method_exists($this, $verb )) {
            return $this->{$verb}();
        }
    }

     /*********calling task model to display task creat by user********/  
    protected function get_completed_task(){
        $staff_id = $_REQUEST['staffID'];
        return $this->to->displayListOfTask($staff_id);	
    }

    protected function get_top_level_task(){
        $sort = $_REQUEST['sort'];
        $staff_id = $_REQUEST['staffID'];
        return $this->to->displayTopLevelTask($sort, $staff_id);	
    }

    protected function get_expand_task(){
		$task_id = $_REQUEST['taskID'];
		$staff_id = $_REQUEST['staffID'];
        return $this->to->displayExpandTask($task_id, $staff_id);
    }
 	/*********calling task model to display task shared to user********/  
	protected function  get_share_task(){
		$sort = $_REQUEST['sort'];
		$staff_id = $_REQUEST['staffID'];
		return $this->to->displayDelegated_ShareTask(2, $sort, $staff_id);
	} 

	/*********calling task model to display task delegated to user********/ 
	protected function get_delegate_task(){
		$sort = $_REQUEST['sort'];
		$staff_id = $_REQUEST['staffID'];
		return $this->to->displayDelegated_ShareTask(3, $sort, $staff_id);
	}  

	/*********calling task model to display details about single selected task********/ 
	protected function get_task_infoTb() {
		$task_id = $_REQUEST['taskID'];
		return $this->to->displayTaskInfo($task_id);
	}

	/*********calling task model to display list of user(s) who shared view to this task********/ 
	protected function get_share_Staff() {
		$task_id = $_REQUEST['taskID'];
		return $this->to->displayShare_DelegatedStaff(2, $task_id);
	}

	/*********calling task model to display list of user(s) who delegate to this task********/ 
	protected function get_delegate_Staff() {
		$task_id = $_REQUEST['taskID'];
		return $this->to->displayShare_DelegatedStaff(3, $task_id);
	}

	/**********calling task model to display a list of task which archived to user**********/
	protected function get_archive_task() {
		$staff_id = $_REQUEST['staffID'];
		return $this->to->displayAchivedTask($staff_id);
	} 

	/*********calling task model to search parent task for current task**********/
	protected function get_parent_task () {
		$task_id = $_REQUEST['taskID'];
		return $this->to->displayparentTask($task_id);
	}

	/**********calling task model to search sub task for current task**********/
	protected function get_sub_task() {
		$task_id = $_REQUEST['taskID'];
		return $this->to->displaySubTask($task_id);
	} 

	/*************calling task model to list all staff info which active = 1***************/
	protected function get_staff_info() {  
		return $this->to->displayStaffInfo('fullInfo',0);
	} 

	/*protected function get_staff_name() {  
		return $this->to->displayStaffInfo('nameOnly');
	} */

	protected function is_parent_task() {
		$task_id = $_REQUEST['taskID'];
		return $this->to->isParentTask($task_id);
	}
	/**********calling task model to remove deleaged task form given staff***********/
	protected function remove_delegate_task() {
		$task_id = $_REQUEST['taskID'];
		$staff_id = $_REQUEST['staffID'];
		return $this->to->removeDelegeted_SharedStaff(3, $task_id, $staff_id);
	}

	/**********calling task model to remove shared task form given staff***********/
	protected function remove_share_task() {
		$task_id = $_REQUEST['taskID'];
		$staff_id = $_REQUEST['staffID'];
		return $this->to->removeDelegeted_SharedStaff(2, $task_id, $staff_id);
	}

	/**********calling task model to add deleaged task to given staff***********/
	protected function add_delegate_task() {
		$task_id = $_REQUEST['taskID'];
		$staff_id = $_REQUEST['staffID'];
		return $this->to->insertDelegate_ShateStaff(3, $task_id, $staff_id);
	}

	/**********calling task model to add shared task to given staff***********/
	protected function add_shared_task() {
		$task_id = $_REQUEST['taskID'];
		$staff_id = $_REQUEST['staffID'];
		return $this->to->insertDelegate_ShateStaff(2, $task_id, $staff_id);
	}

	/**********calling task model to push task to archive table***********/
	protected function task_archive () {
		$task_id = $_REQUEST['taskID'];
		$staff_id = $_REQUEST['staffID'];
		return $this->to->toggleBetweenShare_ArchiveTask(4, $task_id, $staff_id);
	}

	/**********calling task model to push task to view shared table***********/
	protected function task_unarchive () {
		$task_id = $_REQUEST['taskID'];
		$staff_id = $_REQUEST['staffID'];
		return $this->to->toggleBetweenShare_ArchiveTask(2, $task_id, $staff_id);
	}

	
	/*************calling model to add task from as index page************/
	protected function add_task(){

		$output = array("success" => false);
		$taskName = $_REQUEST['taskName'];
		$staffID = intval($_REQUEST['staffID']);
		$duration = intval($_REQUEST['Duration']);
		$status = intval($_REQUEST['status']);
		$priority = intval($_REQUEST['priority']);
		$parentTask = $_REQUEST['parent'];
		$parentID = $_REQUEST['parentID'];

		if (!empty($_REQUEST['taskDis'])) {// if task description not been entered will grab task name as description;
			$taskDes = $_REQUEST['taskDis'];
		} else {
			$taskDes = $_REQUEST['taskName'];
		}

		if (!empty($_REQUEST['eta'])) {// if ETA not setted it will grab the same time tomorrow.
			$input = $_REQUEST['eta'];
			$dateobject = DateTime::createFromFormat('d-m-Y H:i', $input);
			$ETA = $dateobject->format('Y-m-d H:i:s');
		} else {
			$theDate = date("Y-m-d H:i:s");
			$ETA = date("Y-m-d H:i:s", strtotime($theDate . ' +1 day'));
		}


		$output = $this->to->insertTask($taskName, $taskDes, $staffID, $ETA, $duration, $status, $priority, $parentTask, $parentID);

		return $output;
    }
		
	/*********calling mdoel to add a sub task to a task in the ***********/
	protected function add_sub_task(){
		$type =  $_REQUEST['type'];
		$parentTask = 0; // sub task has to have a parentTask 1 = 'ture'
		switch ($type) {
			case "own" : 
				$parentTask = 0; 
				break;
			case "delegate":
				$parentTask = 1; 
		}

		$output = array("success" => false);
		$taskName = $_REQUEST['taskName'];
		$staffID = intval($_REQUEST['staffID']);
		$duration = intval($_REQUEST['Duration']);
		$status = intval($_REQUEST['status']);
		$priority = intval($_REQUEST['priority']);

		$parentID = $_REQUEST['parentID'];
		
		if (!empty($_REQUEST['taskDis'])) {
			$taskDes = $_REQUEST['taskDis'];		
		} else {
			$taskDes = $_REQUEST['taskName'];
		}
		if (!empty($_REQUEST['eta'])) {
			$input = $_REQUEST['eta'];
			$dateobject = DateTime::createFromFormat('d-m-Y H:i', $input);
			$ETA = $dateobject->format('Y-m-d H:i:s');
		} else {
			$theDate = date("Y-m-d H:i:s");
			$ETA = date("Y-m-d H:i:s", strtotime($theDate . ' +1 day'));
		}

		$output = $this->to->insertTask($taskName, $taskDes, $staffID, $ETA, $duration, $status, $priority, $parentTask, $parentID);
        return $output;

	}
	


}


?>