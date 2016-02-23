<?php
class orderModel 
{
	protected $mysqli;

	public function __construct($mysqli=null) {
		
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
	
	
	//**************   GET CURRENT ORDER RECORDS   *********************
	// function getCurrentRecords($location, $supplierID) {
		// $query = "SELECT order_workbench.id, myob_order_id, order_date, due_date, order_workbench.comments, order_workbench.id, myob_stock_id, qty, cost_ex, inventory.barcode, inventory.stockcode, inventory.manuf_code, inventory.description 
									// FROM ((order_workbench LEFT JOIN order_workbench_line ON order_workbench.id = order_workbench_line.order_id AND order_workbench.location = order_workbench_line.location)
									// LEFT JOIN inventory_stores ON order_workbench_line.myob_stock_id = inventory_stores.myob_id AND order_workbench_line.location = inventory_stores.location)
									// LEFT JOIN inventory ON inventory_stores.product_id = inventory.id
									// WHERE inventory_stores.location=$location AND order_workbench.status=1 AND order_workbench.supplier_id=$supplierID
									// AND stockcode NOT IN ('171056', '170422', '170423', '170428', '170289', '170325', '172022', '172010', '170222', '172016', '170427', '172011', '170046', '172015')";
									// NOTE these stockcodes are LED Lenser Products excluded for Tim Tipple - Done by Sam Laurie 14/01/2015";
		// $output = array();
		// if ($result = $this->mysqli->query($query)) {
			// $output = $result->fetch_all(MYSQLI_ASSOC);
			// $result->free();
		// }
		// return($output);
	// }
	
    //**************   GET CURRENT ORDER RECORDS   *********************
    // New paramertised query Josh 24/6/15
    // @param $location integer store location
    // @param $supplierID integer supplier Id
    function getCurrentRecords($location, $supplierID) 
    {
        $query = "SELECT order_workbench.id, myob_order_id, order_date, 
            due_date, order_workbench.comments, order_workbench.id, 
            myob_stock_id, qty, inventory.cost_ex, inventory.barcode, 
            inventory.stockcode, inventory.manuf_code, inventory.description 
            FROM ((order_workbench LEFT JOIN order_workbench_line 
            ON order_workbench.id = order_workbench_line.order_id 
            AND order_workbench.location = order_workbench_line.location)
            LEFT JOIN inventory_stores 
            ON order_workbench_line.myob_stock_id = inventory_stores.myob_id 
            AND order_workbench_line.location = inventory_stores.location)
            LEFT JOIN inventory ON inventory_stores.product_id = inventory.id
            WHERE inventory_stores.location= ? 
            AND order_workbench.status=1
            AND order_workbench.supplier_id= ? 
            AND stockcode NOT IN ('171056', '170422', '170423', '170428', 
            '170289', '170325', '172022', '172010', '170222', '172016', 
            '170427', '172011', '170046', '172015')";
            // NOTE these stockcodes are LED Lenser Products excluded for Tim Tipple - Done by Sam Laurie 14/01/2015";
        
        $output = array();
        $stmt = $this->mysqli->prepare($query);
        if($stmt)
        {
            $stmt->bind_param('ii', $location, $supplierID);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                $output = $result->fetch_all(MYSQLI_ASSOC);				
            }
        }  
        return($output);
    }  	
	
	
	//**************   GET PENDING OPEN ORDER ID   *********************
	// function getPendingOrderID($location, $suppID) {
		// $query = "SELECT id FROM order_workbench WHERE location = $location AND status = 0 AND supplier_id = $suppID LIMIT 1";
		// if ($result = $this->mysqli->query($query)) {
			// $output = mysqli_fetch_assoc($result);
			// $result->free();
			// $output = $output['id'];									// Change to row['id'];
		// }
		// else {
			// $output = -1;
		// }
		// return($output);
	// }
	
    //**************   GET PENDING OPEN ORDER ID   *********************
    // New paramertised query Josh 24/6/15
    // @param $location integer store location
    // @param $supplierID integer supplier Id    
    function getPendingOrderID($location, $supplierID) 
    {
        $query = "SELECT id FROM order_workbench "
                . "WHERE location = ? "
                . "AND status = 0 AND supplier_id = ? LIMIT 1";
                
        $output = array();
        $stmt = $this->mysqli->prepare($query);
        if($stmt)
        {
            $stmt->bind_param('ii', $location, $supplierID);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                $output = $result->fetch_all(MYSQLI_ASSOC);
            }
        }
        if ($result->num_rows > 0)
        {
            return($output[0]['id']);
        }
        else 
        {
            return -1;
        }
    }   	
	
	
	//**************   GET PENDING ORDER RECORD   *********************
	// function getPendingOrder($location, $orderID) {
		// $query = "SELECT order_workbench_line.id AS order_workbench_line_id, myob_stock_id, qty, cost_ex, inventory.barcode, inventory.stockcode, inventory.manuf_code, inventory.description 
									// FROM ( order_workbench_line 
									// LEFT JOIN inventory_stores ON order_workbench_line.myob_stock_id = inventory_stores.myob_id AND order_workbench_line.location = inventory_stores.location)
									// LEFT JOIN inventory ON inventory_stores.product_id = inventory.id
									// WHERE order_workbench_line.location = $location AND order_workbench_line.order_id = $orderID";
		// $output = array();
		// if ($result = $this->mysqli->query($query)) {
			// $output = $result->fetch_all(MYSQLI_ASSOC);
			// $result->free();
		// }
		// return($output);
	// }
	
    //**************   GET PENDING ORDER RECORD   *********************
    // New paramertised query Josh 24/6/15
    // @param $location integer store location
    // @param $orderID integer supplier Id       
    function getPendingOrder($location, $orderID) {
        $query = "SELECT order_workbench_line.id AS order_workbench_line_id, 
                  myob_stock_id, qty, inventory.cost_ex, inventory.barcode, 
                  inventory.stockcode, inventory.manuf_code, 
                  inventory.description 
                  FROM ( order_workbench_line 
                         LEFT JOIN inventory_stores 
                         ON order_workbench_line.myob_stock_id = inventory_stores.myob_id 
                         AND order_workbench_line.location = inventory_stores.location)
                  LEFT JOIN inventory ON inventory_stores.product_id = inventory.id 
                  WHERE order_workbench_line.location = ?  
                  AND order_workbench_line.order_id = ?";
        
        $output = array();
        $stmt = $this->mysqli->prepare($query);
        if($stmt)
        {
            $stmt->bind_param('ii', $location, $orderID);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                $output = $result->fetch_all(MYSQLI_ASSOC);
            }
        }  
        return($output);
    }   	



    /*	Byron Ling 9 sept 2015
    	returns limited info on all open pending orders for given supplier
    */
    function getAllPendingOrders($supplierID) {
		$output = array();
        $query = "SELECT o.id, o.order_date, staff.name AS staff_name, 
        COUNT(ol.id) AS line_count, SUM(ol.qty*ol.cost_ex) AS total_cost_ex
        FROM order_workbench o
        LEFT JOIN order_workbench_line ol ON o.id = ol.order_id
        LEFT JOIN staff ON o.staff_id = staff.id
        WHERE status = 0 AND supplier_id = ? GROUP BY o.id";
                
        $output = array();
        $stmt = $this->mysqli->prepare($query);
        if($stmt)
        {
            $stmt->bind_param('i', $supplierID);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                if ($result->num_rows > 0){
                	$output = $result->fetch_all(MYSQLI_ASSOC);
            	}
            	else
            	{
            		return $output;
            	}
            }
        }

        return($output);
    }






    function getPendingOrder2($staffID, $supplierID) {

		$output = array();

        $query = "SELECT o.id, o.order_date, staff.name AS staff_name FROM order_workbench o "
        		. "LEFT JOIN staff ON o.staff_id = staff.id "
                . "WHERE o.staff_id = ? "
                . "AND o.status = 0 AND o.supplier_id = ? LIMIT 1";
                
        $output = array();
        $stmt = $this->mysqli->prepare($query);
        if($stmt)
        {
            $stmt->bind_param('ii', $staffID, $supplierID);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                if ($result->num_rows > 0){
                	$output = $result->fetch_all(MYSQLI_ASSOC)[0];
            	}
            	else
            	{
            		return $output;
            	}
            }
        }

        $query = "SELECT order_workbench_line.id AS order_workbench_line_id, order_workbench_line.inventory_id,
                  myob_stock_id, qty, inventory.cost_ex, inventory.barcode, 
                  inventory.stockcode, inventory.manuf_code, 
                  inventory.description 
                  FROM order_workbench_line 
                  LEFT JOIN inventory 
                  	ON  order_workbench_line.inventory_id = inventory.id
                  LEFT JOIN inventory_stores 
                  	ON order_workbench_line.inventory_id = inventory_stores.product_id
       			  	AND order_workbench_line.location = inventory_stores.location
                  WHERE order_workbench_line.order_id = ?";
        
        
        $stmt = $this->mysqli->prepare($query);
        if($stmt)
        {
            $stmt->bind_param('i', $output["id"]);
            if ($stmt->execute())
            {
                $result = $stmt->get_result();
                $output["lines"] = $result->fetch_all(MYSQLI_ASSOC);
            }
        }

        return($output);
    }



    function getOrderLines($orderID) {

		$output = array();

        $query = "SELECT order_workbench_line.id AS order_workbench_line_id, order_workbench_line.inventory_id,
                  myob_stock_id, qty, inventory.cost_ex, inventory.barcode, 
                  inventory.stockcode, inventory.manuf_code, 
                  inventory.description 
                  FROM order_workbench_line 
                  LEFT JOIN inventory 
                  	ON  order_workbench_line.inventory_id = inventory.id
                  LEFT JOIN inventory_stores 
                  	ON order_workbench_line.inventory_id = inventory_stores.product_id
       			  	AND order_workbench_line.location = inventory_stores.location
                  WHERE order_workbench_line.order_id = ?";
        
        
        $stmt = $this->mysqli->prepare($query);
        if($stmt)
        {
            $stmt->bind_param('i', $orderID);
            if ($stmt->execute())
            {
                $result = $stmt->get_result();
                $output = $result->fetch_all(MYSQLI_ASSOC);
            }
        }

        return($output);
    }







	function insertLine($order_id, $order_qty, $cost_ex, $comment, $inventory_id, $location_id, $supplier_id, $staff_id){
			

			$output = array();
			if ($order_id <= 0) {
				$query  =  "INSERT INTO order_workbench (location, supplier_id, order_date, due_date, staff_id) 
							VALUES (?, ?, ?, ?, ?)";
			
				if($stmt = $this->mysqli->prepare($query))
				{
					$stmt->bind_param('iissi', $location_id, $supplier_id, date("Y-m-d"), date("Y-m-d"), $staff_id);
					if ($stmt->execute()){
						$order_id = $this->mysqli->insert_id;
					}
					$stmt->close();
				}							
			}
			$output["order_id"] = $order_id;


			$query  =  "INSERT INTO order_workbench_line (order_id, qty, cost_ex, comment, inventory_id) VALUES (?, ?, ?, ?, ?)";

			if($stmt = $this->mysqli->prepare($query))
			{
				$stmt->bind_param('iidsi', $order_id, $order_qty, $cost_ex, $comment, $inventory_id);
				if ($stmt->execute()){
					$line_id = $this->mysqli->insert_id;
					$output["line_id"] = $line_id;
					$output["success"] = true;
				}
				$stmt->close();
			}
		
		return $output;
		exit;
	}



    function removeLine($lineID){
    	//print_r($lineID);
    	$output = false;
			$query  =  "DELETE FROM order_workbench_line WHERE id = ?";

			if($stmt = $this->mysqli->prepare($query))
			{
				$stmt->bind_param('i', $lineID);
				if ($stmt->execute()){
					$output = true;
				}
				$stmt->close();
			}
		return $output;
		exit;
    }







	//**************   GET NEW ORDER RECORDS   *********************
	function getNewOrderRecords2($location, $suppID, $cover) {
		/*
		$query = "";
		$output = array();
		if ($result = $this->mysqli->query($query)) {
			$output = $result->fetch_all(MYSQLI_ASSOC);
			$result->free();
		}
		return($output);
		*/
		
		//get the month ids
		$dateStart = strtotime("-10 months");
		$dateEnd = strtotime("now");

		$resMonths = mysqli_query($this->mysqli, "SELECT id, date_start FROM order_workbench_month WHERE date_start >= '".date('Y-m-d', $dateStart)."' AND date_start < '".date( 'Y-m-d', $dateEnd )."'  ORDER BY date_start DESC LIMIT 6");

		//$month_ids = array(0=>'Jan', 1=>'Feb', 2=>'Mar', 3=>'April', 4=>'May', 5=>'June', 6=>'July');
		$month_ids = array();
		$defaultMonthSold = array();

		while($rowMonths = mysqli_fetch_assoc($resMonths)) {
			$month_ids[$rowMonths["id"]] = date("M y",strtotime($rowMonths["date_start"]));
			
			//default 
			//$defaultMonthSold[ $rowMonths["id"] ] = array("qty"=> 0, "instock" => 0, "date" => date("M y",strtotime($rowMonths["date_start"])));
		}

		$this->res = mysqli_query($this->mysqli, "SELECT inventory.id AS inventory_id, inventory_stores.myob_id, inventory.barcode, inventory.stockcode, inventory.manuf_code, inventory.description, inventory_stores.quantity, inventory_stores.cost_exc AS cost_ex, last_gr_date, last_gr_qty, stock_cover, webinfo.web_url
									FROM inventory_stores LEFT JOIN inventory ON inventory_stores.product_id = inventory.id 
									LEFT JOIN webinfo ON (inventory_stores.myob_id = webinfo.myob_id AND inventory_stores.location = 1)
									WHERE inventory_stores.supplier_id = $suppID AND inventory_stores.location = $location");

		if(mysqli_num_rows($this->res)){
			$numbMonths = count($month_ids);
			
			if ($location == 1) $table_name = "order_workbench_month_totals";
			else if ($location == 2) $table_name = "order_workbench_month_totals_akl";
			else if ($location == 3) $table_name = "order_workbench_month_totals_wgn";
            else if ($location == 4) $table_name = "order_workbench_month_totals_ham";
			
			while($this->row = mysqli_fetch_assoc($this->res)){
				$record = array_map('stripslashes', $this->row);

				$numbMonthsInStock = 0;
				$totalQtySold = 0;
				$totalValueSold = 0;
				$totalQtyGr = 0;

				//default 
				$record["month_sold"] = $defaultMonthSold;

				$monthCount=0;;
				foreach ($month_ids as $key=>$mname){
					$res2 = mysqli_query($this->mysqli, "SELECT qty_sold, value_sold, qty_gr, instock FROM $table_name WHERE month_id = ".$key." AND stock_id = ".$record["myob_id"]);
					while($row2 = mysqli_fetch_assoc($res2)){
						$totalQtySold += $row2["qty_sold"];
						$totalValueSold += $row2["value_sold"];
						$totalQtyGr += $row2["qty_gr"];
						if ( $row2["instock"] ) $numbMonthsInStock++;
						//$record["instock"][$key] = $row2["instock"];
						//$record["month_sold"][$key] = array("qty"=> $row2["qty_sold"], "instock" => $row2["instock"], "date"=>$mname);
						
						//$record["month_sold"][$key]["qty"] = $row2["qty_sold"];

						//if ($row2["instock"]) $record["month_sold"][$key]["instock"] = 1;


						
						$record["month_sold_$monthCount"] = array("qty"=> $row2["qty_sold"], "instock" => $row2["instock"]);
						$monthCount++;

					}
				}

				$record["numbMonthsHistory"] = $numbMonths;

				$record["total_sold"] = $totalQtySold;
				if ($numbMonthsInStock ==0) $record["monthly_avg"] = 0;
				else $record["monthly_avg"] = $record["total_sold"] / $numbMonthsInStock;
				
				if ($numbMonthsInStock ==0) $dayAvg = 0;
				else $dayAvg = $record["total_sold"] / ($numbMonthsInStock*(365/12));

				$record["daily_avg"] = $dayAvg;
				$crntQty = $record["quantity"];

				$onOrderQty = 0; //TODO - On Current Order

				$coverQty = $cover * $dayAvg;
				$orderQty = $coverQty - $crntQty - $onOrderQty;

				//override the database stock_cover by calculating it on the fly
				if ($record["monthly_avg"] == 0) $record["stock_cover"] = "N.A.";
				else $record["stock_cover"] = $crntQty / $record["monthly_avg"];  // stock_cover = SOH / avg-per-month

				if ($orderQty < 0)$record["order"] = 0;
				else $record["order"] = ceil($orderQty);

				$this->records[] = $record;
			}
			return $this->records;
		}
	}









	
	//**************   GET NEW ORDER RECORDS   *********************
	function getNewOrderRecords($location, $suppID, $cover) {
		/*
		$query = "";
		$output = array();
		if ($result = $this->mysqli->query($query)) {
			$output = $result->fetch_all(MYSQLI_ASSOC);
			$result->free();
		}
		return($output);
		*/
		
		//get the month ids
		$dateStart = strtotime("-10 months");
		$dateEnd = strtotime("now");

		$resMonths = mysqli_query($this->mysqli, "SELECT id, date_start FROM order_workbench_month WHERE date_start >= '".date('Y-m-d', $dateStart)."' AND date_start < '".date( 'Y-m-d', $dateEnd )."'  ORDER BY date_start DESC LIMIT 6");

		//$month_ids = array(0=>'Jan', 1=>'Feb', 2=>'Mar', 3=>'April', 4=>'May', 5=>'June', 6=>'July');
		$month_ids = array();
		$defaultMonthSold = array();

		while($rowMonths = mysqli_fetch_assoc($resMonths)) {
			$month_ids[$rowMonths["id"]] = date("M y",strtotime($rowMonths["date_start"]));
			
			//default 
			$defaultMonthSold[ $rowMonths["id"] ] = array("qty"=> 0, "instock" => 0, "date" => date("M y",strtotime($rowMonths["date_start"])));
		}

		$this->res = mysqli_query($this->mysqli, "SELECT inventory_stores.myob_id, inventory.barcode, inventory.stockcode, inventory.manuf_code, inventory.description, inventory_stores.quantity, inventory_stores.cost_exc AS cost_ex, last_gr_date, last_gr_qty, stock_cover, webinfo.web_url
									FROM inventory_stores LEFT JOIN inventory ON inventory_stores.product_id = inventory.id 
									LEFT JOIN webinfo ON (inventory_stores.myob_id = webinfo.myob_id AND inventory_stores.location = 1)
									WHERE inventory_stores.supplier_id = $suppID AND inventory_stores.location = $location");

		if(mysqli_num_rows($this->res)){
			$numbMonths = count($month_ids);
			
			if ($location == 1) $table_name = "order_workbench_month_totals";
			else if ($location == 2) $table_name = "order_workbench_month_totals_akl";
			else if ($location == 3) $table_name = "order_workbench_month_totals_wgn";
            else if ($location == 4) $table_name = "order_workbench_month_totals_ham";
			
			while($this->row = mysqli_fetch_assoc($this->res)){
				$record = array_map('stripslashes', $this->row);

				$numbMonthsInStock = 0;
				$totalQtySold = 0;
				$totalValueSold = 0;
				$totalQtyGr = 0;

				//default 
				$record["month_sold"] = $defaultMonthSold;

				foreach ($month_ids as $key=>$mname){
					$res2 = mysqli_query($this->mysqli, "SELECT qty_sold, value_sold, qty_gr, instock FROM $table_name WHERE month_id = ".$key." AND stock_id = ".$record["myob_id"]);
					while($row2 = mysqli_fetch_assoc($res2)){
						$totalQtySold += $row2["qty_sold"];
						$totalValueSold += $row2["value_sold"];
						$totalQtyGr += $row2["qty_gr"];
						if ( $row2["instock"] ) $numbMonthsInStock++;
						//$record["instock"][$key] = $row2["instock"];
						//$record["month_sold"][$key] = array("qty"=> $row2["qty_sold"], "instock" => $row2["instock"], "date"=>$mname);
						$record["month_sold"][$key]["qty"] = $row2["qty_sold"];
						if ($row2["instock"]) $record["month_sold"][$key]["instock"] = 1;
					}
				}

				$record["numbMonthsHistory"] = $numbMonths;

				$record["total_sold"] = $totalQtySold;
				if ($numbMonthsInStock ==0) $record["monthly_avg"] = 0;
				else $record["monthly_avg"] = $record["total_sold"] / $numbMonthsInStock;
				
				if ($numbMonthsInStock ==0) $dayAvg = 0;
				else $dayAvg = $record["total_sold"] / ($numbMonthsInStock*(365/12));

				$record["daily_avg"] = $dayAvg;
				$crntQty = $record["quantity"];

				$onOrderQty = 0; //TODO - On Current Order

				$coverQty = $cover * $dayAvg;
				$orderQty = $coverQty - $crntQty - $onOrderQty;

				//override the database stock_cover by calculating it on the fly
				if ($record["monthly_avg"] == 0) $record["stock_cover"] = "N.A.";
				else $record["stock_cover"] = $crntQty / $record["monthly_avg"];  // stock_cover = SOH / avg-per-month

				if ($orderQty < 0)$record["order"] = 0;
				else $record["order"] = ceil($orderQty);

				$this->records[] = $record;
			}
			return $this->records;
		}
	}
	
	
	//**************   GET SUPPLIER RECORDS   *********************
	// function getSupplierRecords($location) {
		// $query = "SELECT * FROM suppliers WHERE id > 0 AND location = $location ORDER BY supplier";
		// $output = array();
		// if ($result = $this->mysqli->query($query)) {
			// $output = $result->fetch_all(MYSQLI_ASSOC);
			// $result->free();
		// }
		// return($output);
	// }
	
    //**************   GET SUPPLIER RECORDS   *********************
    // New parametrised query Josh 24/6/15
    // @param $location integer store location  
    function getSupplierRecords($location) 
    {
        $query = "SELECT * FROM suppliers "
                . "WHERE id > 0 "
                . "AND location = ? "
                . "ORDER BY supplier";
        
        $output = array();
        $stmt = $this->mysqli->prepare($query);
        if($stmt)
        {
            $stmt->bind_param('i', $location);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                $output = $result->fetch_all(MYSQLI_ASSOC);				
            }
        }  
        return($output);
    }
	
	
	//**************   GET PRODUCT RECORD   *********************
	// function getProductRecord($myob_stock_id, $location) {
		// $query = "SELECT inventory.barcode, inventory.stockcode, inventory.manuf_code, inventory.description, inventory_stores.cost_exc AS cost_ex 
						// FROM inventory_stores LEFT JOIN inventory ON inventory_stores.product_id = inventory.id 
						// WHERE inventory_stores.myob_id = $myob_stock_id AND inventory_stores.location = $location LIMIT 1";
		// $output = array();
		// if ($result = $this->mysqli->query($query)) {
			// $output = $result->fetch_object();
			// $result->free();
		// }
		// return($output);
	// }
	
    //**************   GET PRODUCT RECORD   *********************
    // New paramertised query Josh 24/6/15
    // @param $myob_stock_id integer myob stock id 
    // @param $location integer store location 
    function getProductRecord($myob_stock_id, $location)
    {
        $query = "SELECT inventory.barcode, inventory.stockcode, 
                  inventory.manuf_code, inventory.description, 
                  inventory_stores.cost_exc AS cost_ex 
                  FROM inventory_stores 
                  LEFT JOIN inventory ON inventory_stores.product_id = inventory.id 
                  WHERE inventory_stores.myob_id = ?
                  AND inventory_stores.location = ? LIMIT 1";
        
        $output = array();
        $stmt = $this->mysqli->prepare($query);
        if($stmt)
        {
            $stmt->bind_param('ii', $myob_stock_id, $location);
            if ($stmt->execute()) 
            {
                $result = $stmt->get_result();
                $output = $result->fetch_object();				
            }
        }  
        return($output);
    }	
	
	
	//**************   REMOVE ORDER LINE   *********************
	// RETURNS true if updated successfully
	public function removeOrderLine($line_id) {
		$output = false;
		$stmt = $this->mysqli->prepare("DELETE FROM order_workbench_line WHERE id = ?");
		$stmt->bind_param('i', $line_id);
		$output = $stmt->execute();
		$stmt->close();
		return ($output);
	}
	
	
	//**************   GET ORDER CART RECORDS   *********************
	function getOrderCartRecords($location, $staffID, $cover) {
		/*
		$query = "";
		$output = array();
		if ($result = $this->mysqli->query($query)) {
			$output = $result->fetch_all(MYSQLI_ASSOC);
			$result->free();
		}
		return($output);
		*/
		
		//get the month ids
		$dateStart = strtotime("-10 months");
		$dateEnd = strtotime("now");

		$resMonths = mysql_query("SELECT id, date_start FROM order_workbench_month WHERE date_start >= '".date('Y-m-d', $dateStart)."' AND date_start < '".date( 'Y-m-d', $dateEnd )."'  ORDER BY date_start DESC LIMIT 6");
		
		$month_ids = array();
		$defaultMonthSold = array();
		
		while($rowMonths = mysql_fetch_assoc($resMonths)){
			$month_ids[$rowMonths["id"]] = date("M y",strtotime($rowMonths["date_start"]));
			
			//default 
			$defaultMonthSold[ $rowMonths["id"] ] = array("qty"=> 0, "instock" => 0, "date" => date("M y",strtotime($rowMonths["date_start"])));
		}
		
		$this->res = mysql_query("SELECT inventory_stores.product_id, inventory_stores.myob_id, inventory.barcode, inventory.stockcode, inventory.manuf_code, inventory.description, inventory_stores.quantity, inventory_stores.cost_exc AS cost_ex, suppliers.supplier
									FROM order_cart
										LEFT JOIN inventory_stores ON order_cart.product_id = inventory_stores.product_id
										LEFT JOIN inventory ON order_cart.product_id = inventory.id
										LEFT JOIN suppliers ON (inventory_stores.supplier_id = suppliers.id AND suppliers.location = 1)
									WHERE staff_id = $staffID AND inventory_stores.location = $location ORDER BY suppliers.supplier, inventory.description");
		
		if(mysql_num_rows($this->res)){
			$numbMonths = count($month_ids);
			
			if ($location == 1) $table_name = "order_workbench_month_totals";
			else if ($location == 2) $table_name = "order_workbench_month_totals_akl";
			else if ($location == 3) $table_name = "order_workbench_month_totals_wgn";
            else if ($location == 4) $table_name = "order_workbench_month_totals_ham";
			
			while($this->row = mysql_fetch_assoc($this->res)){
				$record = array_map('stripslashes', $this->row);
				$numbMonthsInStock = 0;
				$totalQtySold = 0;
				$totalValueSold = 0;
				$totalQtyGr = 0;
				
				$record["akl_qty"] = 0;
				$record["wgn_qty"] = 0;
				
				$res_qty = mysql_query("SELECT quantity, location FROM inventory_stores WHERE product_id = " . $record['product_id']);
				while($row_qty = mysql_fetch_assoc($res_qty)) {
					if ($row_qty['location'] == 2) {
						$record["akl_qty"] = $row_qty['quantity'];
					}
					else if ($row_qty['location'] == 3) {
						$record["wgn_qty"] = $row_qty['quantity'];
					}
				}
				
				//default 
				$record["month_sold"] = $defaultMonthSold;
				
				foreach ($month_ids as $key=>$mname){
					$res2 = mysql_query("SELECT qty_sold, value_sold, qty_gr, instock FROM $table_name WHERE month_id = ".$key." AND stock_id = ".$record["myob_id"]);
					while($row2 = mysql_fetch_assoc($res2)){
						$totalQtySold += $row2["qty_sold"];
						$totalValueSold += $row2["value_sold"];
						$totalQtyGr += $row2["qty_gr"];
						if ( $row2["instock"] ) $numbMonthsInStock++;
						$record["month_sold"][$key]["qty"] = $row2["qty_sold"];
						if ($row2["instock"]) $record["month_sold"][$key]["instock"] = 1;
					}
				}

				$record["numbMonthsHistory"] = $numbMonths;
				
				$record["total_sold"] = round($totalQtySold, 2);
				if ($numbMonthsInStock == 0) $record["monthly_avg"] = 0;
				else $record["monthly_avg"] = $record["total_sold"] / $numbMonthsInStock;
				
				if ($numbMonthsInStock == 0) $dayAvg = 0;
				else $dayAvg = $record["total_sold"] / ($numbMonthsInStock*(365/12));
				
				$record["daily_avg"] = $dayAvg;
				$crntQty = $record["quantity"];
				
				$onOrderQty = 0; //TODO - On Current Order
				
				$coverQty = $cover * $dayAvg;
				$orderQty = $coverQty - $crntQty - $onOrderQty;
				
				if ($orderQty < 0)$record["order"] = 0;
				else $record["order"] = ceil($orderQty);
				
				$this->records[] = $record;
			}
			return $this->records;
		}
	}
}
?>