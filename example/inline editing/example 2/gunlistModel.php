<?php
class GunlistModel {
	protected $mysqli;

	public function __construct($mysqli = null) {
		@require_once($_SERVER['DOCUMENT_ROOT'].'/lib/scripts/config.php');
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DB);
		
		if (mysqli_connect_errno()) {
			error("mysqli_connect_error()");
			$this->mysqli = null;
		}
		else {
			$this->mysqli = $mysqli;
		}
	}


	//************   CREATE GUNLIST   **************
	public function createGunlist($name, $location) {
		if ( $stmt = $this->mysqli->prepare("INSERT INTO gunlist (name, location) VALUES (?,?)") ) {
			$stmt->bind_param("si", $name, $location);
			$stmt->execute();
			return($stmt->insert_id);
			$stmt->close();
		}
		else return -1;
	}


	//************   CREATE GUNLIST AREA   **************
	public function createArea($name, $location) {
		$query = "INSERT INTO gunlist_area (name, location) VALUES (?,?)";
		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param("si", $name, $location);
			$stmt->execute();
			return($stmt->insert_id);
			$stmt->close();
		}
		else return -1;
	}


	//************   CREATE GUNLIST ITEM   **************
	public function createItem($gunlistID, $areaID, $armsbookID, $newSpecial) {
		$querySelectArmsbook = "SELECT gb_number, description, retail_inc, special_inc FROM armsbook WHERE gb_number = $armsbook_id LIMIT 1";
		if ($stmt = $this->mysqli->prepare($querySelectArmsbook)) {
			$stmt->bind_result($gbNumber, $description, $retailInc, $specialInc);
			$stmt->fetch();
			$stmt->close();
		}

		//TODO : check we got good result from querySelectArmsbook

		$queryInsert = "INSERT INTO gunlist_item (gunlist_id, area_id, armsbook_id, book_number, description, retail_inc, current_special_inc, new_special_inc)
						VALUES (?,?,?,?,?,?,?,?)";

		if ($stmt = $this->mysqli->prepare($queryInsert) ) {
			$stmt->bind_param("iiissddd", $gunlistID, $areaID, $armsbookID, $gbNumber, $description, $retailInc, $specialInc, $newSpecialInc);
			$stmt->execute();
			return($stmt->insert_id);
			$stmt->close();
		}
		else return -1;
	}			


	//************   GET FIREARM ACCESSORIES FROM INVENTORY TABLE  **************
	public function getFirearmAccessories($barcode, $location){
		$output = array();
		$query = "SELECT id, barcode, stockcode, manuf_code, description, (retail_exc * 1.15) AS retail_inc
					  FROM inventory
				  LEFT JOIN inventory_stores ON inventory.id = inventory_stores.product_id AND inventory_stores.location = ?
				  WHERE inventory.barcode = ?
				  ORDER BY description";

		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param("is", $location, $barcode);
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc()) {
				$output[$row['id']] = [ "barcode"=>$row['barcode'], 
										"stockcode"=>$row['stockcode'], 
										"manuf_code"=>$row['manuf_code'], 
										"description"=>$row['description'], 
										"retail_inc"=>$row['retail_inc'] ];
				
			}
			$result->free();
			$stmt->close();
		}
		return($output);
	}




	//************   GET FIREARM ACCESSORIES FROM INVENTORY TABLE  **************
	public function getCommonAccessories($location){
		$output = array();
		$query = "SELECT id, barcode, stockcode, manuf_code, description, (retail_exc * 1.15) AS retail_inc
			      FROM inventory
				  LEFT JOIN inventory_stores ON inventory.id = inventory_stores.product_id AND inventory_stores.location = ?
				  WHERE inventory.id IN (903,447,2641,835,438,379,444,446,1022,1019,1275)
				  ORDER BY description";

		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param("i", $location);
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc()) {
				$output[$row['id']] = [ "inventory_id"=>$row['id'], 
										"barcode"=>$row['barcode'], 
										"stockcode"=>$row['stockcode'], 
										"manuf_code"=>$row['manuf_code'], 
										"description"=>$row['description'], 
										"retail_inc"=>$row['retail_inc'] ];
				
			}
			$result->free();
			$stmt->close();
		}
		return($output);
	}







	//************   GET GUNLIST AREAS   **************
	public function getAreas($gunlistID, $indexed=false) {
		$output = array();
		$query  =  "SELECT gunlist_area.id, gunlist_area.name, gunlist_area.category_id, gunlist_category.category_name, COUNT(book_number) AS number_firearms
					FROM gunlist
					LEFT JOIN gunlist_area ON gunlist.location = gunlist_area.location
					LEFT JOIN gunlist_item ON gunlist.id = gunlist_item.gunlist_id AND gunlist_item.area_id = gunlist_area.id
					LEFT JOIN gunlist_category ON gunlist_area.category_id = gunlist_category.id
					WHERE gunlist.id = ? AND gunlist_area.active
					GROUP BY gunlist_area.id, gunlist_area.name
					ORDER BY sorting";

		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param("i", $gunlistID);
			$stmt->execute();
			$result  = $stmt->get_result();

			while ($row = $result->fetch_assoc()) {

				if (!isset($output[$row['category_id']])){
					$output[$row['category_id']] = ['id'=>$row['category_id'],
													'category_name' => $row['category_name'],
													'areas' => [] ];
				}

				if (!$indexed){
				

				$output[$row['category_id']]['areas'][] = ['id'=>$row['id'],
							  	'name'=>$row['name'], 
							  	'cat'=>$row['category_name'], 
							  	'number_firearms'=>$row['number_firearms']   
							];

				}
				else{
				$output[$row['category_id']]['areas'][$row['id']] = [ 'id'=>$row['id'],
							  			'name'=>$row['name'], 
							  			'cat'=>$row['category_name'], 
							  			'number_firearms'=>$row['number_firearms'],
							  			'items'=>[]
									  ];

				}
			}

			$result->free();
			$stmt->close();
		}
		return($output);
	}


	//************   GET GUNLISTS   **************
	public function getGunlists(){
		$output = array();
		$query = "SELECT id, name, date_created FROM gunlist WHERE active ORDER BY date_created DESC";
		if ($result = $this->mysqli->query($query)) {
			while ($row = $result->fetch_assoc()) {
				$createdDate = new DateTime($row['date_created']);
				$output[] = [ 	'id'=>$row['id'],
								'name'=>$row['name'], 
								'date_created'=>$createdDate->format('M y')  
							];
			}
			$result->free();
		}
		return($output);
	}



	//************   GET GUNLISTS   **************
	
	public function getGunlist($gunlistID){
		$output = array();
		$query = "SELECT id, name, date_created FROM gunlist WHERE id = ? LIMIT 1";
		if ($stmt = $this->mysqli->prepare($query)) {
			
			$stmt->bind_param("i", $gunlistID);
			$stmt->execute();
		    $stmt->bind_result($gunlist_id, $gunlist_name, $gunlist_date);
		    $stmt->fetch();
		    $stmt->close();


			return array(	'gunlist_id'=>$gunlist_id,
							'gunlist_name'=>$gunlist_name );
		}

		return $output;
	}



	//************   GET FIREARM INFO FROM ARMSBOOK   **************
	public function getFirearmInfo($bookNumber, $book='GB') {
		$query = "SELECT * FROM armsbook WHERE gb_number = ? LIMIT 1";
		if ($stmt = $this->mysqli->prepare($querySelectArmsbook)) {
		    $stmt->bind_result($book_number, $description, $retailInc, $current_special_inc, $new, $type, $barcode, $image_url, $serial);
		    $stmt->fetch();
		    $stmt->close();

			return array(	'book_number'=>$book_number,
							'description'=>$row['description'], 
							'retail_inc'=>$retailInc,
							'current_special_inc'=>$current_special_inc,
							'new'=>$new,
							'type'=>$type,
							'barcode'=>$barcode,
							'image_url'=>$image_url,
							'serial'=>$serial_number
					 	);
		}
		return null;
	}


	//************   GET LIST OF ITEMS IN AREA   **************
	public function getItems($gunlistID, $areaID) {
		$query = "SELECT id, armsbook_id, book_number, description, retail_inc, current_special_inc, new_special_inc FROM gunlist_item 
				  WHERE gunlist_id = ? 
				  AND area_id = ? 
				  ORDER BY id DESC";

		if ($stmt = $this->mysqli->prepare($query)) {
			$output = array();
			$stmt->bind_param("ii", $gunlistID, $areaID);
			$stmt->execute();
			$result  = $stmt->get_result();
			while ($row = $result->fetch_assoc()) {
				$output[] = [ 	'id'=>$row['id'],
							  	'armsbook_id'=>$row['armsbook_id'], 
							  	'book_number'=>$row['book_number'],  
							  	'description'=>$row['description'], 
							  	'retail_inc'=>$row['retail_inc'], 
							  	'current_special_inc'=>$row['current_special_inc'], 
							  	'new_special_inc'=>$row['new_special_inc']
							];
			}
			$result->free();
			$stmt->close();
		}
		return $output;
	}






public function getPriceChange($inventoryID, $location){
		
	$output = [];
	$query = "SELECT date, old_price*1.15 AS old_price, new_price*1.15 AS new_price  FROM gclocal.myob_price_changes WHERE product_id = ? AND location = ? ORDER BY date DESC LIMIT 1";
		
	if ($stmt = $this->mysqli->prepare($query)) {
		$stmt->bind_param("ii", $inventoryID, $location);
		$stmt->execute();

		$result  = $stmt->get_result();
		
		while ($row = $result->fetch_assoc()) {

			$output = [ 'd'=>new DateTime($row['date']), 'old_price'=>round($row['old_price']), 'new_price'=>round($row['new_price']) ];

		}
		
	}
	return $output;
}














	//************   GET LIST OF ITEMS IN AREA   **************
	public function getAllItems($gunlistID) {
		$output = $this->getAreas($gunlistID, true);


$query = "SELECT gi.id, gi.armsbook_id, gi.description AS package_desc, gi.retail_inc, 
gi.current_special_inc, gi.new_special_inc, gi.area_id, gi.comment AS comment, gi.recheck,
b.id AS book_id, b.prefix, r.new, r.description AS firearm_desc, r.retail_inc AS firearm_rrp, 
armsbook_category.category,armsbook_caliber.name AS caliber, armsbook_cartridge.cartridge,armsbook_licence.grade AS licence,
armsbook_finish.code AS finish, armsbook_action.code AS action, armsbook_action.action AS action_long, armsbook_choke.choke, 
barrel_length, capacity, attr_fbs, attr_hb, attr_tfs, attr_thole, attr_comb, attr_lh,
r.comment AS armsbook_comment, r.cost_inc, r.category_id AS ab_category_id, r.caliber_id as ab_caliber_id,
armsbook_purchase.date_received,
last_sale.new_special_inc AS last_new_special, last_sale.current_special_inc AS last_current_special, last_sale.retail_inc AS last_retail_inc, last_sale.description AS last_description,
GROUP_CONCAT(inventory.description SEPARATOR ', ') AS accessories,SUM(inventory_stores.retail_exc)*1.15 AS accessories_rrp,
i.barcode, i.description AS inventory_desc, invs.retail_exc*1.15 AS inventory_rrp, invs.quantity AS inventory_qty, i.id AS inventory_id, b.location_id, gunlist_area.category_id,
isg.description AS sh_desc, isg.retail_exc*1.15 AS sh_rrp 
FROM gunlist_item gi
LEFT JOIN gunlist_area ON gi.area_id = gunlist_area.id 
LEFT JOIN armsbook_record r ON gi.armsbook_id = r.id AND gi.book_id = r.book_id
LEFT JOIN armsbook_book b ON gi.book_id = b.id
LEFT JOIN armsbook_category ON r.category_id = armsbook_category.id 
LEFT JOIN armsbook_caliber ON r.caliber_id = armsbook_caliber.id 
LEFT JOIN armsbook_cartridge ON r.cartridge_id = armsbook_cartridge.id 
LEFT JOIN armsbook_licence ON r.licence_id = armsbook_licence.id 
LEFT JOIN armsbook_finish ON r.finish_id = armsbook_finish.id 
LEFT JOIN armsbook_action ON r.action_id = armsbook_action.id 
LEFT JOIN armsbook_choke ON r.choke_id = armsbook_choke.id 
LEFT JOIN armsbook_purchase ON r.purchase_id = armsbook_purchase.id
LEFT JOIN (
	SELECT MAX(id) AS max_id, current_special_inc, new_special_inc, armsbook_id, book_id, description, retail_inc
    FROM gunlist_item 
    WHERE NOT gunlist_id = ? 
    GROUP BY armsbook_id, book_id 
) last_sale ON gi.book_id = last_sale.book_id AND gi.armsbook_id = last_sale.armsbook_id 
LEFT JOIN gunlist_accessory ON gi.id = gunlist_accessory.gunlist_item_id
LEFT JOIN inventory ON gunlist_accessory.inventory_id = inventory.id
LEFT JOIN inventory_stores ON inventory.id = inventory_stores.product_id AND inventory_stores.location=b.location_id
LEFT JOIN inventory i ON r.inventory_id = i.id
LEFT JOIN inventory_stores invs ON i.id = invs.product_id AND invs.location=b.location_id
LEFT JOIN inventory_sh_guns isg ON gi.book_number = isg.book_number 
WHERE gi.gunlist_id = ? 
GROUP BY gi.id
ORDER BY gi.sort, gi.id ASC";


//join to second hand guns :: 




		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param("ii", $gunlistID, $gunlistID);
			$stmt->execute();
			$result  = $stmt->get_result();
			while ($row = $result->fetch_assoc()) {

				if ($row['inventory_id'] == null) {
					$inventory_rrp = $row['sh_rrp'];
					$inventory_desc = $row['sh_desc'];
					$inventory_qty = 1;
				} else {
					$inventory_rrp = $row['inventory_rrp'];
					$inventory_desc = $row['inventory_desc'];
					$inventory_qty = $row['inventory_qty'];
				}


				$accessories = [];
				

				
				if ($inventory_desc=="") $inventory_desc = "stockcoard not found";

				$accessories[] = array('description'=>$inventory_desc, 'rrp'=>round($inventory_rrp) );
				

				$accessoryList = $row['accessories'];
				if ($accessoryList=="") $accessoryList = "";
				$accessories[] = array('description'=>$accessoryList, 'rrp'=>round($row['accessories_rrp']) );
				//$accessories[] = array('description'=>'another Accessory', 'rrp'=>33.00);


//$output[$row['area_id']]["items"][] = [ 'id'=>$row['id'],





				$output[ $row['category_id'] ]['areas'][$row['area_id']]["items"][] = [ 'id'=>$row['id'],
														  	'armsbook_id'=>$row['armsbook_id'], 
														  	'book_id'=>$row['book_id'], 
														  	'prefix'=>$row['prefix'], 
														  	'package_description'=>trim($row['package_desc']), 
														  	'retail_inc'=>$row['retail_inc'], 
														  	'current_special_inc'=>$row['current_special_inc'], 
														  	'new_special_inc'=>$row['new_special_inc'],
														  	'new'=>$row['new'],
														  	'cost_inc'=>$row['cost_inc'],
														  	'comment'=>$row['comment'],
														  	'accessories' => $accessories,
															'category'=>$row['category'],
                                                                                                                        'ab_category_id'=>$row['ab_category_id'],
                                                                                                                        'ab_caliber_id'=>$row['ab_caliber_id'],
															'caliber'=>$row['caliber'], 
															'cartridge'=>$row['cartridge'],
															'licence'=>$row['licence'],
															'finish'=>$row['finish'], 
															'action'=>$row['action'],
															'action_long'=>$row['action_long'],
															'choke'=>$row['choke'], 
															'barrel_length'=>$row['barrel_length'], 
															'capacity'=>$row['capacity'], 
															'attr_fbs'=>$row['attr_fbs'], 
															'attr_hb'=>$row['attr_hb'], 
															'attr_tfs'=>$row['attr_tfs'], 
															'attr_thole'=>$row['attr_thole'], 
															'attr_comb'=>$row['attr_comb'], 
															'attr_lh'=>$row['attr_lh'],
															'armsbook_comment'=>$row['armsbook_comment'],
															'purchase_date'=>$row['date_received'],
															'last_new_special' => $row['last_new_special'],
															'last_current_special' => $row['last_current_special'],
															'last_retail_inc'=>$row['last_retail_inc'],
															'last_description' => trim($row['last_description']),
															'recheck' => $row['recheck'],
															'barcode' => $row['barcode'],
															'inventory_rrp' => $inventory_rrp,
															'inventory_desc' => $inventory_desc,
															'inventory_id'=> $row['inventory_id'],
															'location_id'=> $row['location_id'],
															'inventory_qty'=>$inventory_qty,




														  ];
			}
			$result->free();
			$stmt->close();
		}
		return $output;
	}






}



//FOR DEBUGGING
/*
require_once("../connection_only.php");

  $obj = new GunlistModel($mysqli);

  $recordInfo = $obj->getAllItems(5);
  echo "<pre>";
  print_r($recordInfo);
  echo "</pre>";
*/


?>