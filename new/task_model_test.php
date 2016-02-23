<?php
require_once("task_modelCopy.php");
$myTaskObj = new TaskModel();

class testing{

static function TaskCreatByTest() {

	global $myTaskObj;

	$r = $myTaskObj->displayListOfTask("parent", 93);


	echo "<pre>";
		print_r($r);
	echo "</pre>";

	}


static function TaskShareTest() {

	global $myTaskObj;

	$r = $myTaskObj-> displayDelegated_ShareTask(2, "", 3);

		echo "<pre>";
		print_r($r);
		echo "</pre>";
	}


static function TaskDelegateTest() {

	global $myTaskObj;

	$r = $myTaskObj-> displayDelegated_ShareTask(3, "", 3);

		echo "<pre>";
		print_r($r);
		echo "</pre>";
	}


static function TaskInfoTest() {
	
	global $myTaskObj;

	$r = $myTaskObj-> displayTaskInfo(1);

		echo "<pre>";
		print_r($r);
		echo "</pre>";
	}



static function shareStaffTest() {

	global $myTaskObj;

	$r = $myTaskObj-> displayShare_DelegatedStaff(3, 3);

		echo "<pre>";
		print_r($r);
		echo "</pre>";
	}


static function delegareStaffTest() {

	global $myTaskObj;

	$r = $myTaskObj-> displayShare_DelegatedStaff(2, 2);

		echo "<pre>";
		print_r($r);
		echo "</pre>";
	}

static function achivedtaskTest() {

	global $myTaskObj;

	$r = $myTaskObj-> displayAchivedTask(6);

		echo "<pre>";
		print_r($r);
		echo "</pre>";
	}


static function parentTaskTest() {

	global $myTaskObj;

	$r = $myTaskObj-> displayparentTask(5);

		echo "<pre>";
		print_r($r);
		echo "</pre>";
	}


static function subTaskTest() {

	global $myTaskObj;

	$r = $myTaskObj-> displaySubTask(6);

		echo "<pre>";
		print_r($r);
		echo "</pre>";
	}

static function staffInfoTest() {

	global $myTaskObj;

	$r = $myTaskObj-> displayStaffInfo('fullInfo', 3); 

		echo "<pre>";
		print_r($r);
		echo "</pre>";

} 



static function changeTextDataTest () {

	global $myTaskObj;

	$r = $myTaskObj-> updateTaskString(20, 'change content to test model updateTaskText working', 'name');

	echo "success";
		echo "<pre>";
		print_r($r);
		echo "</pre>";
	}



static function changeIntdataTest() {

	global $myTaskObj;

	$r = $myTaskObj-> updateTaskInt(21, 1, 'status');

		echo "<pre>";
		print_r($r);
		echo "</pre>";
	}


static function changePrrentTest() {

	global $myTaskObj;

	$r = $myTaskObj-> updateParentTask(24,2);

		echo "<pre>";
		print_r($r);
		echo "</pre>";

}


static function deleteDelegatedTaskTest() {

	global $myTaskObj;

	$r = $myTaskObj-> removeDelegeted_SharedStaff(3, 21, 1); //($type, $task_id, $staff_id)

		echo "<pre>";
		print_r($r);
		echo "</pre>";
}


static function deleteSharedTaskTest() {

	global $myTaskObj;

	$r = $myTaskObj-> removeDelegeted_SharedStaff(2, 21, 4); //($type, $task_id, $staff_id)

		echo "<pre>";
		print_r($r);
		echo "</pre>";
}


static function addStaffDelegateTest() {

	global $myTaskObj;

	$r = $myTaskObj-> insertDelegate_ShateStaff(3, 21, 1); // ($type, $task_id, $staff_id)

		echo "<pre>";
		print_r($r);
		echo "</pre>";

}


static function addStaffShareTest() {

	global $myTaskObj;

	$r = $myTaskObj-> insertDelegate_ShateStaff(2, 21, 4);  //($type, $task_id, $staff_id)

		echo "<pre>";
		print_r($r);
		echo "</pre>";
}



static function changeShareToArchiveTest() {

	global $myTaskObj;

	$r = $myTaskObj-> toggleBetweenShare_ArchiveTask(4, 21, 4);

		echo "<pre>";
		print_r($r);
		echo "</pre>";
}



static function changeArchiveToShareTest() {

	global $myTaskObj;

	$r = $myTaskObj-> toggleBetweenShare_ArchiveTask(2, 21, 4);

		echo "<pre>";
		print_r($r);
		echo "</pre>";
}


static function insertTasksTest() {
	
	global $myTaskObj;

	$r = $myTaskObj-> insertTask('newTask Rui', 'fortesting', 93, '2015-11-15 00:54:37', 6, 2, 3, 1,3);

	echo "<pre>";
		echo "adddddddd";
	echo "</pre>";

}


static function displaytaskNameTest(){
	global $myTaskObj;

	$r = $myTaskObj->displayStaffInfo("nameOnly", 3);

	echo "<pre>";
		print_r($r);
	echo "</pre>";

}



}


//testing :: displaytaskNameTest();
testing :: TaskCreatByTest();   			//tested
//testing :: TaskShareTest();    			//tested
//testing :: TaskDelegateTest();			//tested
//testing :: TaskInfoTest();				//tested
//testing :: shareStaffTest();				//tested
//testing :: delegareStaffTest();			//tested
//testing :: achivedtaskTest();				//tested
//testing :: parentTaskTest();  			//tested
//testing :: subTaskTest();					//tested
//testing :: staffInfoTest();				//tested
//testing :: changeTextDataTest();			//tested
//testing :: changeIntdataTest();			//tested
//testing :: changePrrentTest();			//tested
//testing :: deleteDelegatedTaskTest();		//tested	
//testing :: deleteSharedTaskTest();		//tested
//testing :: addStaffDelegateTest();		//tested
//testing :: addStaffShareTest();			//tested
//testing :: changeShareToArchiveTest();	//tested
//testing :: changeArchiveToShareTest();	//tested
//testing :: insertTasksTest();				//tested

?>