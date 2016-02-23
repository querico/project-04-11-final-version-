<?php

class order_workbench {

    public function process($verb, $args){
        @require_once($_SERVER['DOCUMENT_ROOT'].'/lib/scripts/class/orderModel.php');
        $this->orderObj = new orderModel();

        if (method_exists($this, $verb)) {
            return $this->{$verb}();
        }
        return null;
    }


        
    protected function get_supplier_stock(){
        $supplierID = $_REQUEST['supp_id'];
        $locationID = $_REQUEST['loc_id'];
        $cover = $_REQUEST['cover'];
        return $this->orderObj->getNewOrderRecords2($locationID, $supplierID, $cover);
    }


    protected function get_open_order(){
        $supplierID = $_REQUEST['supp_id'];
        $staffID = $_REQUEST['staff_id'];
        return $this->orderObj->getPendingOrder2($staffID, $supplierID);
    }


    protected function get_all_open_orders(){
        $supplierID = $_REQUEST['supp_id'];
        return $this->orderObj->getAllPendingOrders($supplierID);
    }


    protected function get_order_lines(){
        $orderID = $_REQUEST['order_id'];
        return $this->orderObj->getOrderLines($orderID);
    }


    protected function add_line(){
        $output = array("success"=>false);
        $orderID = intval($_REQUEST['order_id']);
        $qty = $_REQUEST['qty'];
        $inventoryID = $_REQUEST['inventory_id'];
        $cost_ex =  $_REQUEST['cost_ex'];
        $comment = "";
        $locationID = $_REQUEST['location_id'];
        $supplierID = $_REQUEST['supplier_id'];
        $staffID = $_REQUEST['staff_id'];
        $output = $this->orderObj->insertLine($orderID, $qty, $cost_ex, $comment, $inventoryID, $locationID, $supplierID, $staffID);
        return $output;
    }


    protected function remove_line(){
        $output = array("success"=>false);
        $lineID = $_REQUEST['line_id'];
        $output["success"] = $this->orderObj->removeLine($lineID);
        return $output;
    }




}


?>