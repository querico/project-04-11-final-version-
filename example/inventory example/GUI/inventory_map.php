<?php
	//define('NAV', 'reports');
	//include($_SERVER['DOCUMENT_ROOT']."/lib/template/header.php");

//@require_once($_SERVER['DOCUMENT_ROOT'].'/lib/scripts/class/legacyModel.php');

//$legacyObj = new LegacyModel();

//if (isset($_GET["id"])){
	//$inventoryID = $_GET["id"];
//}




/*
echo "<pre>";
print_r($legacyObj->getInventoryBranchNonsync(2) );
echo "</pre>";
*/
?>
<style>

.unlink, .remove {
	font-weight : bold;
	font-size:18px;
	color : #BD0000;
}

.jarvis {
	color:#31708f;
}

.myob {
	color:#8F5431;
	border-right: 1px solid #ECDCD2;
}

</style>


<script src="/lib/template/js/jquery-ui.js"></script>
<script src="/lib/template/js/bootstrap-table.js"></script>






<div class="row">
	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title">Inventory Map</h3>
		</div>
		
		<div class="panel-body">

			<h3>Jarvis :: Inventory</h3>
			<table class='table' data-toggle="table" data-url="../api/v2/legacy_inventory/get_map?id=<?php echo $inventoryID; ?>" data-card-view="false" data-response-handler="rhInventory" >
			    <thead>
			        <tr>
			            <th data-field="id">ID</th>
			            <th data-field="stockcode">SKU</th>
			            <th data-field="barcode">Barcode</th>
			            <th data-field="description">Description</th>
			            <th data-field="cat1">Cat1</th>
			            <th data-field="cost_ex" class='text-right' data-formatter="priceFormatter">Cost (ex)</th>
			            <th data-field="retail_ex" class='text-right' data-formatter="priceFormatter">Retail (ex)</th>
			        </tr>
			    </thead>
			</table>


			<h3>Jarvis :: Inventory Stores</h3>
			<table class='table' id='tableStores' data-toggle="table" data-url="../api/v2/legacy_inventory/get_map?id=<?php echo $inventoryID; ?>" data-card-view="false" data-response-handler="rhInventoryStores" >
			    <thead>
			        <tr>
			            <th data-field="myob_id">MYOB ID</th>
			            <th data-field="location">Location</th>
			            <th data-field="dev_stockcode">SKU</th>
						<th data-field="dev_barcode">Barcode</th>
						<th data-field="dev_description">Description</th>
						<th data-field="cat1">Cat 1</th>
			            <th data-field="cost_exc" class='text-right' data-formatter="priceFormatter">Cost (ex)</th>
			            <th data-field="retail_exc" class='text-right' data-formatter="priceFormatter">Retail (ex)</th>
			            <th data-field="operate" class='text-left' data-formatter="removeFormatter" data-events="removeEvents"></th>
			        </tr>
			    </thead>
			</table>

			<h3>Legacy Map</h3>
			<table class='table' id='tableLegacyMap' data-toggle="table" data-url="../api/v2/legacy_inventory/get_map?id=<?php echo $inventoryID; ?>" data-card-view="false" data-response-handler="rhInventoryMap" >
			    <thead>
			        <tr>
			            <th data-field="legacy_stock_id">MYOB ID</th>
			            <th data-field="location_abbrv">Location</th>
			            <th data-field="stockcode">SKU</th>
			            <th data-field="barcode">Barcode</th>
			            <th data-field="description">Description</th>
						<th data-field="cat1">Cat 1</th>
			            <th data-field="cost_ex" class='text-right' data-formatter="priceFormatter">Cost (ex)</th>
			            <th data-field="retail_ex" class='text-right' data-formatter="priceFormatter">Retail (ex)</th>
			            <th data-field="operate" class='text-left' data-formatter="unlinkFormatter" data-events="unlinkEvents"></th>
			        </tr>
			    </thead>
			</table>

		</div>
	</div>
</div>


<script>
    // client side
    function rhInventory(res) {
        return res.inventory;
    }

	function rhInventoryStores(res) {
        return res.inventory_stores;
    }

	function rhInventoryMap(res) {
        return res.inventory_mapped;
    }
    

    function priceFormatter(value) {
    			n = parseFloat(value);
    		    return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");

    }

  </script>



<script>
    function unlinkFormatter(value, row, index) {
        return [
            '<a class="unlink" href="javascript:void(0)" title="Unlink">',
                '<i class="fa fa-chain-broken"></i>',
            '</a>'
        ].join('');
    }

    window.unlinkEvents = {'click .unlink': function (e, value, row, index) {
		//if (confirm('Note: This record will remain in "unsyncronised" until stockcode is matched in myob. \r\rDelete inventory map?')) {
			$.post( "../api/v2/legacy_inventory/unlink_legacy_map", { legacy_stock_id:row.legacy_stock_id,location_id:row.location_id,inventory_id:row.inventory_id})
			  .done(function( data ) {
			    console.log(data);
			    $("#tableLegacyMap").bootstrapTable('refresh');
			});
		//}
    }};




    function removeFormatter(value, row, index) {
        return [
            '<a class="remove" href="javascript:void(0)" title="Remove">',
                '<i class="fa fa-times"></i>',
            '</a>'
        ].join('');
    }

    window.removeEvents = {'click .remove': function (e, value, row, index) {
	    //if (confirm('Note: This will re-sync from myob. \rEnsure stockcode is updated first. \r\rDelete branch record?')) {

			$.post( "../api/v2/legacy_inventory/remove_inventory_store", { location_id:row.location , myob_id:row.myob_id})
			  .done(function( data ) {
			    console.log(data);
			    $("#tableStores").bootstrapTable('refresh');
 			});

		//}
    }};
</script>













<?php
	include($_SERVER['DOCUMENT_ROOT']."/lib/template/footer.php");
?>