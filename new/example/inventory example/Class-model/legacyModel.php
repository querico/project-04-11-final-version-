<?php
class LegacyModel 
{
	protected $mysqli;
    protected $branches;
    protected $branchesAbbrv;

    protected $legacyDatabases = array(1=>'myob_chc',2=>'myob_akl',3=>'myob_wtn',4=>'myob_ham');

	public function __construct() {
		@require_once($_SERVER['DOCUMENT_ROOT'].'/lib/scripts/config.php');
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DB);
		

		if (mysqli_connect_errno()) {
			error("mysqli_connect_error()");
			$this->mysqli = null;
		}
		else {
			$this->mysqli = $mysqli;

            //TODO load tehse from DB
            $this->branches = array(1=>"Christchurch", 2=>"Auckland", 3=>"Wellington", 4=>"Hamilton");
            $this->branchesAbbrv = array(1=>"CHC", 2=>"AKL", 3=>"WGN", 4=>"HAM");
		}
	}
	


    function getBranchName($locationID){
        return $this->branches[$locationID];
    }

    function getBranchAbbrv($locationID){
        return $this->branchesAbbrv[$locationID];
    }



    function getBranches(){
        $output = array();
        $query = "SELECT id, location, short_code FROM location WHERE id > 0";
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



        
    function getInventoryBranchNonsync($location_id=1){
        $output = array();
        $legDB = $this->legacyDatabases[$location_id];

        $query = "SELECT s.stock_id, s.barcode, s.custom1 AS sku, s.description, s.cat1 
        FROM gclocal.legacy_map_inventory map
        LEFT JOIN $legDB.stock s ON map.legacy_stock_id = s.stock_id
        WHERE inventory_id IS null AND map.location_id=$location_id AND NOT s.inactive;";
                
        $stmt = $this->mysqli->prepare($query);
        //$stmt->bind_param('i', $id);
        if($stmt) {
            if ($stmt->execute()){
                    $result = $stmt->get_result();
                    $output = $result->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }
        return($output);
    }









    function getInventoryMismatch($location_id=1, $match="barcode"){
        
        switch ($match) {
            case 'barcode':
                $matchMyob = "barcode";
                $matchJarvis = "barcode";
                break;
            case 'sku':
                $matchMyob = "custom1";
                $matchJarvis = "stockcode";
                break;
            case 'description':
                $matchMyob = "description";
                $matchJarvis = "description";
                break;
            default:
                $matchMyob = "barcode";
                $matchJarvis = "barcode";
                break;
        }

        $output = array();
        $legDB = $this->legacyDatabases[$location_id];

        $query = "SELECT i.id, i.stockcode AS sku, i.barcode, i.description, s.stock_id AS myob_stock_id, s.custom1 AS myob_stockcode, s.barcode AS myob_barcode, s.description AS myob_description 
        FROM gclocal.legacy_map_inventory map
        LEFT JOIN gclocal.inventory i ON map.inventory_id = i.id
        LEFT JOIN $legDB.stock s ON map.legacy_stock_id = s.stock_id
        WHERE NOT s.$matchMyob = i.$matchJarvis 
        AND map.location_id = $location_id";
                
        $stmt = $this->mysqli->prepare($query);
        //$stmt->bind_param('i', $id);
        if($stmt) {
            if ($stmt->execute()){
                    $result = $stmt->get_result();
                    $output = $result->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }
        return($output);
    }





    /*
    Created Byron :: 6 Sep 2015
    Disconnects a legacy record from a inventory record
    primary use : maintaining synchronisatin between stores
    */
    function unlinkInventoryMap($legacy_stock_id, $location_id, $inventory_id){
        $output = array("success"=>false);
        $query = "UPDATE legacy_map_inventory SET inventory_id = NULL WHERE legacy_stock_id = ? AND location_id = ? AND inventory_id = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('iii', $legacy_stock_id, $location_id, $inventory_id);
        if($stmt) {
            if ($stmt->execute()){
                $output["success"] = true;
            }
            $stmt->close();
        }
        return($output);
    }


    /*
    Created Byron :: 6 Sep 2015
    removes a record from the inventory_stores table
    primary use : maintaining synchronisatin between stores
    */
    function removeInventoryStore($myob_id, $location_id){
        $output = array("success"=>false);
        $query = "DELETE FROM inventory_stores WHERE myob_id = ? AND location = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('ii', $myob_id, $location_id);
        if($stmt) {
            if ($stmt->execute()){
                $output["success"] = true;
            }
            $stmt->close();
        }
        return($output);
    }




    /* Created Byron :: Sep 2015
       Retruns the full array of all mapped connections to given inventory id
    */
    function getInventoryMap($id){
        $output = array();

        $queryInventory = "SELECT * FROM inventory WHERE id=$id LIMIT 1";
        $queryInventoryStores = "SELECT * FROM inventory_stores WHERE product_id = $id ORDER BY location";
        $queryInventoryMap = "SELECT * FROM legacy_map_inventory WHERE inventory_id = $id  ORDER BY location_id";
        $queryInventoryLegacy = "SELECT * FROM myob_chc.stock WHERE stock_id = 20710";


        //inventory record
        $stmt = $this->mysqli->prepare($queryInventory);
        //$stmt->bind_param('i', $id);
        if($stmt) {
            if ($stmt->execute()){
                    $result = $stmt->get_result();
                    $output["inventory"] = $result->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }


        //inventory_stores records
        $stmt = $this->mysqli->prepare($queryInventoryStores);
        //$stmt->bind_param('i', $id);
        if($stmt) {
            if ($stmt->execute()){
                    $result = $stmt->get_result();
                    $output["inventory_stores"] = $result->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }



        //inventory_stores records
        $stmt = $this->mysqli->prepare($queryInventoryMap);
        //$stmt->bind_param('i', $id);
        if($stmt) {
            if ($stmt->execute()){
                    $result = $stmt->get_result();
                    
                    //$output["inventory_map"] = $result->fetch_all(MYSQLI_ASSOC);

                    while ($row = $result->fetch_assoc()) {

                        $stockID = $row["legacy_stock_id"];
                        $legDB = $this->legacyDatabases[$row["location_id"]];


                        $stmt2 = $this->mysqli->prepare("SELECT barcode, custom1 AS stockcode, description, cat1, cost, sell FROM $legDB.stock WHERE stock_id = $stockID LIMIT 1");
                        //$stmt->bind_param('i', $id);
                        if($stmt2) {
                            if ($stmt2->execute()){
                                    $result2 = $stmt2->get_result();
                                    $output2 = $result2->fetch_all(MYSQLI_ASSOC)[0];
                            }
                            $stmt2->close();
                        }




                        $output["inventory_mapped"][] = [
                            "legacy_stock_id"=>$row["legacy_stock_id"],
                            "location_id"=>$row["location_id"],
                            "location_abbrv"=>$this->getBranchAbbrv($row["location_id"]),
                            "inventory_id"=>$row["inventory_id"],
                            "barcode"=>$output2["barcode"],
                            "stockcode"=>$output2["stockcode"],
                            "description"=>$output2["description"],
                            "cat1"=>$output2["cat1"],
                            "cost_ex"=>$output2["cost"],
                            "retail_ex"=>$output2["sell"]

//:"9420030043135","PLU":0,"custom1":"156444\/S","custom2":"","sales_prompt":"","inactive":0,"allow_renaming":0,"allow_fractions":0,"package":0,"tax_components":0,"print_components":0,"description":"HE HYDRAPEL TROUSERS VEIL S","longdesc":"","cat1":"156","cat2":"<N\/A>","goods_tax":"GST","cost":"61.4000","sales_tax":"GST","sell":"129.5652","quantity":2,"layby_qty":0,"salesorder_qty":0,"date_created":"2014-01-31 14:18:45","track_serial":0,"static_quantity":0,"bonus":"0.0000","order_threshold":0,"order_quantity":0,"supplier_id":142,"date_modified":"2015-06-24 08:21:51","freight":0,"tare_weight":0,"unitof_measure":0,"weighted":0,"external":0,"legacy_modified":"2015-08-26 06:04:51"



                            //"myob"=>$output2
                        ];



                        
                    }                    




            }
            $stmt->close();
        }




        return($output);        
    }



}
?>