<?php

class legacy_inventory {

    public function process($verb, $args){
        @require_once($_SERVER['DOCUMENT_ROOT'].'/lib/scripts/class/legacyModel.php');
        $this->lo = new LegacyModel();

        if (method_exists($this, $verb )) {
            return $this->{$verb}();
        }
    }

        
    protected function get_map(){
        $id = $_REQUEST['id'];
        return $this->lo->getInventoryMap($id);
    }


    protected function unlink_legacy_map(){
        $legacy_stock_id = $_REQUEST['legacy_stock_id'];
        $location_id = $_REQUEST['location_id'];
        $inventory_id = $_REQUEST['inventory_id'];
        return $this->lo->unlinkInventoryMap($legacy_stock_id, $location_id, $inventory_id);
    }


    protected function remove_inventory_store(){
        $locationID = $_REQUEST['location_id'];
        $myobID = $_REQUEST['myob_id'];
        return $this->lo->removeInventoryStore($myobID, $locationID);
    }


    protected function get_unsynchronised(){
        $locationID = $_REQUEST['location_id'];
        return $this->lo->getInventoryBranchNonsync($locationID);
    }


    protected function get_mismatch(){
        $locationID = $_REQUEST['location_id'];
        $match = $_REQUEST['match'];
        return $this->lo->getInventoryMismatch($locationID, $match);
    }




}


?>