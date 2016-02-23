<?php
class StaffModel {	
	protected $mysqli;
	
	public function __construct() {
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
	
	
	//**************   CHANGE PASSWORD   *********************
	public function updatePassword($staffID, $newPass){
		$query = "SELECT login_pass FROM staff WHERE id = $staffID LIMIT 1";
		$oldPassCorrect = true;
		if ($oldPassCorrect){
			$passHash = password_hash($newPass, PASSWORD_DEFAULT);
			$query = "UPDATE staff SET login_pass = '$passHash' WHERE id = $staffID";
			if ($result = $this->mysqli->query($query)) {
				return true;
			}
		}
		return false;
	}
	
	
	public function resetPassToDefalt($id){
		$query = "UPDATE staff SET login_pass = ? AND auth = '' WHERE id = ?";
		$newPass = password_hash("guncity3798", PASSWORD_DEFAULT);
		
		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('si', $newPass, $id);
			$stmt->execute();
			$stmt->close();
			return true;
		}
		return false;
	}


	//**************   CHANGE PRINTER IP   *********************
	public function updatePrinter($staffID, $newIp){
		$query = "UPDATE staff_printer SET ip = '$newIp' WHERE id = $staffID";
			if ($result = $this->mysqli->query($query)) {
				return(array('ip'=>$newIp));
			}
			return false;
	}
	
	
	//**************  UPDATE STAFF EDITABLE TABLE   *********************
	public function updateStaff($staffID, $value, $type){
		$query = "UPDATE staff SET $type = ? WHERE id = $staffID";
                if ($stmt = $this->mysqli->prepare($query)) {
                    $stmt->bind_param('s', $value);
                    $stmt->execute();
                    $stmt->close();
                    return(array('result'=>'true'));
		}
		return false;
	}
	
	
	public function updateMessages($id){
		$query = "UPDATE inventory_alerts SET complete = 1 WHERE id = ?";
		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$stmt->close();
			return true;
		}
		return false;
	}
	
	
	//**************  UPDATE STAFF EDITABLE TABLE   *********************
	public function updateStaffBool($staffID, $value, $type){
		$query = "UPDATE staff SET $type = ? WHERE id = $staffID";
                if ($stmt = $this->mysqli->prepare($query)) {
                    $stmt->bind_param('i', $value);
                    $stmt->execute();
                    $stmt->close();
                    return(array('result'=>'true'));
		}
		return false;
	}
	
	
	//**************   GET IP ADDRESS    *********************
	public function getPrinterIp($staffId){
		$query = "Select ip FROM staff_printer WHERE id = ? LIMIT 1";
		$output = array();
		
		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('s', $staffId);
			$stmt->execute();
		    $stmt->bind_result($ip);
		    $stmt->fetch();
		    $stmt->close();

		   	return(array('ip'=>$ip));
		   	exit;
		}
		return(array());
		exit;
	}
	
	
	//**************   GET STAFF MEMBER   *********************
	public function getStaffMember($code){
		$output = array();		
		$query = "SELECT name, id FROM staff WHERE barcode = ? LIMIT 1";

		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('s', $code);
			$stmt->execute();
		    $stmt->bind_result($name, $id);
		    $stmt->fetch();
		    $stmt->close();

		   	return(array('name'=>$name, 'id'=>$id));
		   	exit;
		}

		return(array());
		exit;
	}
	
	
	//**************   GET STAFF MEMBER   *********************
	public function searchStaff($search){
		$output = array();		
		$query = "SELECT name, id FROM staff WHERE name LIKE '%?%'";

		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('s', $search);
			$stmt->execute();
		    $stmt->bind_result($name, $id);
		    $stmt->fetch();
		    $stmt->close();

		   	return(array('name'=>$name, 'id'=>$id));
		   	exit;
		}

		return(array());
		exit;
	}
	
	
	public function getStaffMemberById($id){
		$output = array();		
		$query = "SELECT name FROM staff WHERE id = ? LIMIT 1";

		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('i', $id);
			$stmt->execute();
		    $stmt->bind_result($name);
		    $stmt->fetch();
		    $stmt->close();

		   	return(array('name'=>$name));
		   	exit;
		}

		return(array());
		exit;
	}
	
	
	public function getStaffByLocation($id){
		$output = array();		
		$query = "SELECT s.id, s.role, s.barcode, s.name, s.email, s.phone1, s.ddi,
                        s.image_url, s.ph_ext, s.skype, s.location,
                        s.email2, s.phone2
                        FROM staff s
                        WHERE active='1'
                        AND location = ?";

		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('i', $id);
			$stmt->execute();
                        $output = array();
		    $result = $stmt->get_result();
                    $output = $result->fetch_all(MYSQLI_ASSOC);	
//		    while($row = $result->fetch_assoc()) {
//                        array_push($output, $row);
//                    }
//		    $stmt->close();
		}
		return($output);
		exit;
	}
        
        public function getStaffByLocationForRoster($id){
		$output = array();		
		$query = "SELECT s.active, s.id, s.role, s.barcode, s.name, s.email, s.phone1, s.ddi,
                        s.image_url, s.ph_ext, s.skype, s.location,
                        s.email2, s.phone2
                        FROM staff s
                        WHERE s.sort_hours > 0
                        AND location = ?
                        ORDER BY s.role DESC";

		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('i', $id);
			$stmt->execute();
                        $output = array();
		    $result = $stmt->get_result();
                    $output = $result->fetch_all(MYSQLI_ASSOC);	
//		    while($row = $result->fetch_assoc()) {
//                        array_push($output, $row);
//                    }
//		    $stmt->close();
		}
		return($output);
		exit;
	}
        
        public function getStaffByLocationForPayweek($id){
		$output = array();		
		$query = "SELECT  s.id as staffID, s.role, s.barcode, s.name, s.email, s.phone1, s.ddi,
                        s.image_url, s.ph_ext, s.skype, s.location,
                        s.email2, s.phone2
                        FROM staff s
                        WHERE s.sort_hours > 0
                        AND s.active = 1
                        AND location = ?
                        ORDER BY s.role DESC";

		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('i', $id);
			$stmt->execute();
                        $output = array();
		    $result = $stmt->get_result();
                    $output = $result->fetch_all(MYSQLI_ASSOC);	
//		    while($row = $result->fetch_assoc()) {
//                        array_push($output, $row);
//                    }
//		    $stmt->close();
		}
		return($output);
		exit;
	}
	
	
	public function getStaffMemberDetailsById($id){
		$output = array();		
		$query = "SELECT * FROM staff WHERE id = ? LIMIT 1";
	
		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param("i", $id);
			if ($stmt->execute()) 
			{
				$result = $stmt->get_result();
				$output = $result->fetch_all(MYSQLI_ASSOC);				
			}
		}
		return($output);
	}
	
	
	public function getStaffMemberDetailById($detail ,$id){
		$output = array();		
		$query = "SELECT $detail FROM staff WHERE id = ? LIMIT 1";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("i", $id);
			$stmt->execute(); 
			$stmt->bind_result($res);
			$stmt->fetch();
			$stmt->close();
			
			return(array($detail => $res));
			exit;
			
		}
		return(array());
		exit;
	}
	
	
	public function getStaffMemberPassById($id){
		$output = array();		
		$query = "SELECT login_pass FROM staff WHERE id = ? LIMIT 1";

		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('i', $id);
			$stmt->execute();
		    $stmt->bind_result($name);
		    $stmt->fetch();
		    $stmt->close();

		   	return(array('name'=>$name));
		   	exit;
		}

		return(array());
		exit;
	}
	
	
	public function createStaff($data) { 
        //TODO INCLUDE $bank into table
		$query = "INSERT INTO staff (`shortcode`, `name`, `email`, `location`, `phone1`, `bank_acc`, `email2`, `account_name`, `dob`, `ird_number`, `tax_code`, `kiwisaver_rate`, `address`, `login_pass`, `login_name`) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$result = [];
		if($stmt = $this->mysqli->prepare($query))
		{
			$stmt->bind_param('sssisssssssssss', 
							  $data['shortcode'], $data['name'], $data['email'], 
							  $data['location'], $data['phone1'], $data['bank_acc'], $data['email2'], 
							  $data['account_name'], $data['dob'], $data['ird_number'], 
							  $data['tax_code'], $data['kiwisaver_rate'], $data['address'], $data['pass'], $data['user']);
			$stmt->execute();
			if($stmt->affected_rows > 0) {
				$result['result'] = TRUE;
				$result['id'] = $stmt->insert_id;
			}
			$stmt->close();
		}
		return $result;
		exit;
	}
	
	
	public function getAllActiveStaff(){
		$output = array();		
		$query = "SELECT staff.name, staff.id, 
				  staff.location, location.short_code
				  FROM staff
				  LEFT JOIN location ON location.id = staff.location
				  WHERE active = 1
				  ORDER BY location ASC, name ASC";

		if ($stmt = $this->mysqli->prepare($query)) {
			if ($stmt->execute()) 
			{
				$result = $stmt->get_result();
				$output = $result->fetch_all(MYSQLI_ASSOC);				
			}
		}
		return($output);
		exit;
	}
	
	
	public function getStaffByDepArea($categpry_id, $caliber_id, $action_id){
		$query = "SELECT staff_id FROM staff_areas WHERE category_id = ? AND caliber_id = ? AND action_id = ?";
		if ($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param('iii', $categpry_id, $caliber_id, $action_id);
				$stmt->execute();
				$stmt->bind_result($id);
				$stmt->fetch();
				$stmt->close();
	}
		
		return($this->getStaffMemberDetailsById($id));
	}
	
	
	public function sendMessage($staffTo, $staffFrom, $message){
		$query = "INSERT INTO staff_messages (staff_to, staff_from, message) VALUES (?,?,?)";
		if($stmt = $this->mysqli->prepare($query)){
			$stmt->bind_param('iis', $staffTo, $staffFrom, $message);
			$stmt->execute();
			if($stmt->affected_rows > 0) {
				$result['result'] = TRUE;
			}
			$stmt->close();
		}
		return $result;
		exit;
	}
	
	
	public function getAllActiveStaffa(){
		$output = array();		
		$query = "SELECT name, id FROM staff WHERE active = 1 
					ORDER BY name ASC";

		if ($stmt = $this->mysqli->prepare($query)) {
				if ($stmt->execute()) 
				{
						$result = $stmt->get_result();
						$output = $result->fetch_all(MYSQLI_ASSOC);				
				}
		}
		return($output);
		exit;
	}
	
	
	public function getAllMessageById($id){
		$query = "SELECT * FROM staff_messages WHERE staff_to = ? AND complete = 0";
		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param("i", $id);
			if ($stmt->execute()) 
			{
				$result = $stmt->get_result();
				$output = $result->fetch_all(MYSQLI_ASSOC);				
			}
		}
		return($output);
	}
	
	
	public function markMessageAsRead($id){
		$query = "UPDATE staff_messages SET complete = 1 WHERE id = ?";
		if ($stmt = $this->mysqli->prepare($query)) {
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$stmt->close();
			return(true);
		}
		return false;
	}
}
?>
