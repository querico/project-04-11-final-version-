<?php
header('Content-Type: application/json');
$output = [];
$output['success'] = false;

if (isset($_REQUEST['action'])) {

	function send($theType) {
		require_once($_SERVER['DOCUMENT_ROOT'].'/lib/scripts/connection.php');
		$staffObj = new staffModel($mysqli);
		
		if($_REQUEST["value"] != '') {
			$value = $_REQUEST["value"];
		} else { 
			$value = ''; 
		}
		
		$id = substr($_REQUEST["id"], strpos($_REQUEST["id"], "_") + 1);
		$result = $staffObj->updateStaff($id, $value, $theType);
		
		$output['success'] = true;
		echo json_encode($output);
	}
	
	switch ($_REQUEST['action']){
	
		//************   UPDATE Password   **************
		case 'update_barcode':
				$type = "barcode";
				send($type);
			exit;
			
		case 'update_shortcode':
				$type = "shortcode";
				send($type);
			exit;
		
		case 'update_role':
				$type = "role";
				send($type);
			exit;
			
		case 'update_role_title':
				$type = "role_title";
				send($type);
			exit;
		
		case 'update_email':
				$type = "email";
				send($type);
			exit;

		case 'update_phone':
				$type = "phone2";
				send($type);
			exit;
		
		case 'update_ddi':
				$type = "ddi";
				send($type);
			exit;	

		case 'update_ext':
				$type = "ph_ext";
				send($type);
			exit;
		
		case 'update_skype':
				$type = "skype";
				send($type);
			exit;		

		case 'update_address':
				$type = "address";
				send($type);
			exit;
			
	}
	


}
echo json_encode($output);
?>