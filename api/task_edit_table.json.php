<?php
header('Content-Type: application/json');
//$taskObject;

@require_once($_SERVER['DOCUMENT_ROOT'].'/development/rui/task_model.php');
$taskObject = new TaskModel();
$output = [];
$output['success'] = false;

if (isset($_REQUEST['action'])) {
$value = $_REQUEST["value"];
$id = $_REQUEST["id"];


switch ($_REQUEST["action"]){

	case 'update_taskName':
		if ($value == '' || $value = null) {
			$value = "Task Name Can not Be Blank";
		} else {
			$value = $_REQUEST["value"];
		}
			$type = "name";
			$output['success'] = $taskObject->updateTaskString($id, $value, $type);
			break;

	case 'update_taskDesc':
			$type = "description";
			$output['success'] = $taskObject->updateTaskString($id, $value, $type);
			break;

	case 'update_ETA':
			$type = "ETA";
			$output['success'] = $taskObject->updateTaskString($id, $value, $type);
			break;

	case 'update_duration':
			$type = "duration";
			$output['success'] = $taskObject->updateTaskInt($id, $value, $type);
			break;

	case 'update_status':
			$type = "status";
			$output['success'] = $taskObject->updateTaskInt($id, $value, $type);
			break;

	case 'update_priority':
			$type = "priority";
			$output['success'] = $taskObject->updateTaskInt($id, $value, $type);
			break;

	case 'update_parent_task':
			$staff_id =  $_REQUEST["staffID"];
			if ($id == $value) {
				$output['success'] = false;
				} else {
			$output['success'] = $taskObject->updateParentTask($id, $staff_id, $value);
				}
			break;
}
echo json_encode($output);
exit;

}



?>