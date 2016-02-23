<?php
header('Content-Type: application/json');
include ($_SERVER['DOCUMENT_ROOT'].'/lib/scripts/search_functions.php');
$output = [];
$output['success'] = false;

if (isset($_REQUEST['action'])) {
	
	require_once($_SERVER['DOCUMENT_ROOT'].'/lib/scripts/connection.php');
	$armsObj = new armsbookModel($mysqli);
	
	// Checks user security level to access
	//include($_SERVER['DOCUMENT_ROOT']."/lib/scripts/helper_functions.php");
	//checkSecurityLevelJSON(100);
	
	switch ($_REQUEST['action']){
	
		//************   UPDATE/CREATE PURCHASE   **************
		case 'updatePurchase':
		
			$date = new datetime ($_POST['purchaseDateRecived']);
			$theDate = $date->format('Y-m-d');
		
			if($_POST['purchaseId'] != '') {			
				//update			
				$result =  $armsObj->updatePurchase($_POST['purchaseId'], $theDate, $_POST['purchasePermitNumber'], 
							$_POST['purchaseLicenceNumber'], $_POST['purchaseSupplierNumber'], $_POST['purchaseInvoiceRef'], 
							$_POST['purchaseInwardsRef'], $_POST['purchaseComment'], $_POST['purchaseSupplierAddress'], $_POST['staffId']);
							
				$output['success'] = true;			
			} else {
				//create
				$result =  $armsObj->createPurchase($theDate, $_POST['purchasePermitNumber'], $_POST['purchaseLicenceNumber'], 
							$_POST['purchaseSupplierNumber'], $_POST['purchaseInvoiceRef'], $_POST['purchaseInwardsRef'], 
							$_POST['purchaseComment'], $_POST['purchaseSupplierAddress'], $_POST['staffId'], $_POST['bookId'], $_POST['id']);
				
				$output['success'] = true;
			}
			echo json_encode($output);
			exit;
			
		//************   UPDATE/CREATE SALE   **************	
		case 'updateSale':
                        
                        if(isset($_POST['soldDate'])) {
                    
                            $date = new datetime ($_POST['soldDate']);
                            $theDate = $date->format('Y-m-d');
                        }
                        $saleId = $_POST['saleId'];
			//update
			if($saleId != 0) {
				$result = $armsObj->updateSale($theDate, $_POST['firearmsLicence'], $_POST['docketRef'], $_POST['customerFirstName'], 
						$_POST['customerLastName'], $_POST['customerAddress'], $_POST['customerCity'], $_POST['customerCountry'], 
						$_POST['customerPhoneNumber'], $_POST['customerEmail'], $_POST['soldPrice'], $saleId, $_POST['staffId'], $_POST['comments']);
			} else {
			//create
				$saleId = $armsObj->createSale($theDate, $_POST['firearmsLicence'], $_POST['docketRef'], $_POST['customerFirstName'], 
						$_POST['customerLastName'], $_POST['customerAddress'], $_POST['customerCity'], $_POST['customerCountry'], 
						$_POST['customerPhoneNumber'], $_POST['customerEmail'], $_POST['staffId'], $_POST['comments']);
			}
                        
                        $result = $armsObj->createSaleRecord($saleId, $_POST['bookId'], $_POST['id'], $_POST['soldPrice']);
                        
			echo json_encode($result);
			exit;
		
		case 'createSale':
                        if(isset($_POST['soldDate'])) {
                    
			$date = new datetime ($_POST['soldDate']);
			$theDate = $date->format('Y-m-d');
                        }
			$result = $armsObj->createSaleNew($theDate, $_POST['firearmsLicence'], $_POST['docketRef'], $_POST['customerFirstName'], 
					$_POST['customerLastName'], $_POST['customerAddress'], $_POST['customerCity'], $_POST['customerCountry'], 
					$_POST['customerPhoneNumber'], $_POST['customerEmail'], $_POST['staffId'], $_POST['comments'], 
					$_POST['permitNumber'], $_POST['saleId']);
			
			echo json_encode($result);
			exit;
		
		case 'addGunToSale':
		
			$saleId = $_POST['saleId'];
			$bookNumber = $_POST['bookNumber'];
			$soldPrice = $_POST['soldPrice'];
			
			$output = searchArmsBookbyGB($bookNumber);
			$theResult = $armsObj->isSold($output[0]['record_id'], $output[0]['book_id']);
			
			if(!empty($output)){
				$result = $armsObj->addGunToSale($output[0]['book_id'], $output[0]['record_id'], $saleId, $soldPrice);
			} else {
				$result = "";
			}
			
			$theResult = array(
				'output' => $output,
				'result' => $result,
				'isSold' => $theResult
			);
			
			echo json_encode($theResult);
		
		exit;
		
		
		case 'removeGun':
			$saleId = $_POST['saleId'];
			$bookId = $_POST['bookId'];
			$recordId = $_POST['recordId'];
			
			$result = $armsObj->removeRecordSale($saleId, $bookId, $recordId);
			$result = "";
			echo json_encode($result);
		exit;
		
		//************   GET RECORD   **************
		case 'get_record':
			$armsbook_id = filter_input(INPUT_GET, 'gb_id', FILTER_SANITIZE_SPECIAL_CHARS);
			$book_id = filter_input(INPUT_GET, 'book_id', FILTER_SANITIZE_SPECIAL_CHARS);
			$record = $armsObj->getRecord($book_id, $armsbook_id);
			$output = (array)$record;
			echo json_encode($output, JSON_PRETTY_PRINT);
			exit;	
			
		case 'updateRecord':
		
			$data['armsbook_id'] = $_POST['armsbook_id'];
			$data['book_id'] = $_POST['book_id'];
			$data['description'] = $_POST['description']; 
			$data['serial'] = $_POST['serial']; 
			$data['barcode'] = $_POST['barcode']; 
			$data['retail_inc'] = $_POST['retail_inc']; 
			$data['cost_inc'] = $_POST['cost_inc']; 
			$data['sale_inc'] = $_POST['sale_inc']; 
			$data['comment'] = $_POST['comment'];
			$data['capacity'] = $_POST['capacity'];

			if($_POST['Action'] == "-1") {
				$data['Action'] = '';
			} else {
				$data['Action'] = $_POST['Action'];
			}
			 
			if($_POST['category'] == "-1") {
				$data['category'] = '';
			} else {
				$data['category'] = $_POST['category'];
			}
			
			if($_POST['caliber'] == "-1") {
				$data['caliber'] = '';
			} else {
				$data['caliber'] = $_POST['caliber'];
			}
			
			if($_POST['cartridge'] == "-1") {
				$data['cartridge'] = '';
			} else {
				$data['cartridge'] = $_POST['cartridge'];
			}
			
			if($_POST['licence'] == "-1") {
				$data['licence'] = '';
			} else {
				$data['licence'] = $_POST['licence'];
			}
			
			if($_POST['finish'] == "-1") {
				$data['finish'] = '';
			} else {
				$data['finish'] = $_POST['finish'];
			}
			
			if(!isset($_POST['attributes'])){
				$_POST['attributes'] = array();
			}
			if(in_array('attr_fbs', $_POST['attributes'])) {
				$data['attr_fbs'] = 1;
			} else {
				$data['attr_fbs'] = 0;
			}
			
			if(in_array('attr_hb', $_POST['attributes'])) {
				$data['attr_hb'] = 1;
			} else {
				$data['attr_hb'] = 0;
			}
			
			if(in_array('attr_tfs', $_POST['attributes'])) {
				$data['attr_tfs'] = 1;
			} else {
				$data['attr_tfs'] = 0;
			}
			
			if(in_array('attr_thole', $_POST['attributes'])) {
				$data['attr_thole'] = 1;
			} else {
				$data['attr_thole'] = 0;
			}
			
			if(in_array('attr_comb', $_POST['attributes'])) {
				$data['attr_comb'] = 1;
			} else {
				$data['attr_comb'] = 0;
			}
			
			if(in_array('attr_lh', $_POST['attributes'])) {
				$data['attr_lh'] = 1;
			} else {
				$data['attr_lh'] = 0;
			}
			
			$result = $armsObj->updateRecord($data);
			
			$output['success'] = 'true';
			
			echo json_encode($output);
			exit;
                        
                case 'update_category':
                    
                    $field = 'category_id';
                    $value = $_POST['value'];
                    $armsbookId = $_POST['armsbook_id'];
                    $bookId = $_POST['book_id'];
                        
                    
                    $output = $armsObj->updateArmsbookByField($armsbookId,$bookId, $field, $value);
                    
                    echo json_encode($output);
                    exit;
                    
                case 'update_caliber':
                    
                    $field = 'caliber_id';
                    $value = $_POST['value'];
                    $armsbookId = $_POST['armsbook_id'];
                    $bookId = $_POST['book_id'];
                        
                    
                    $output = $armsObj->updateArmsbookByField($armsbookId,$bookId, $field, $value);
                    
                    echo json_encode($output);
                    exit;
                    
                    
                case 'update_cartridge':
                    
                    $field = 'cartridge_id';
                    $value = $_POST['value'];
                    $armsbookId = $_POST['armsbook_id'];
                    $bookId = $_POST['book_id'];
                        
                    
                    $output = $armsObj->updateArmsbookByField($armsbookId,$bookId, $field, $value);
                    
                    echo json_encode($output);
                    exit;
                    
                
                case 'update_licence':
                    
                    $field = 'licence_id';
                    $value = $_POST['value'];
                    $armsbookId = $_POST['armsbook_id'];
                    $bookId = $_POST['book_id'];
                        
                    
                    $output = $armsObj->updateArmsbookByField($armsbookId,$bookId, $field, $value);
                    
                    echo json_encode($output);
                    exit;
                    
                case 'update_finish':
                    
                    $field = 'finish_id';
                    $value = $_POST['value'];
                    $armsbookId = $_POST['armsbook_id'];
                    $bookId = $_POST['book_id'];
                        
                    
                    $output = $armsObj->updateArmsbookByField($armsbookId,$bookId, $field, $value);
                    
                    echo json_encode($output);
                    exit;
                    
                    
                case 'update_action':
                    
                    $field = 'action_id';
                    $value = $_POST['value'];
                    $armsbookId = $_POST['armsbook_id'];
                    $bookId = $_POST['book_id'];
                        
                    
                    $output = $armsObj->updateArmsbookByField($armsbookId,$bookId, $field, $value);
                    
                    echo json_encode($output);
                    exit;
                    
                case 'update_choke':
                    
                    $field = 'choke_id';
                    $value = $_POST['value'];
                    $armsbookId = $_POST['armsbook_id'];
                    $bookId = $_POST['book_id'];
                        
                    
                    $output = $armsObj->updateArmsbookByField($armsbookId,$bookId, $field, $value);
                    
                    echo json_encode($output);
                    exit;  

	}
}

echo json_encode($output);

?>