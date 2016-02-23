<?php
	define('NAV', 'reports');
	include($_SERVER['DOCUMENT_ROOT']."/lib/template/header.php");



/*
echo "<pre>";
print_r($legacyObj->getInventoryBranchNonsync(2) );
echo "</pre>";
*/

	$orderObj = new orderModel();

	//defaults
	$cover = 1;
	$eta = 0;
	$eta_date = "";

?>

<style>
.out_of_stock{
	background-color: #FFB8B8;
	padding-right:2px;
}

.order_qty{
	width:90px !important;

}
</style>

<link rel="stylesheet" type="text/css" href="/lib/css/bootstrap-table.css">
<script src="/lib/template/js/jquery-ui.js"></script>
<script src="/lib/template/js/bootstrap-table.js"></script>
<!--Bootstrap Datepicker [ OPTIONAL ]-->
<link href="/lib/template/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel="stylesheet">



<div class="row">

	<div class="panel col-md-12">
		<div class="panel-heading">
			<h3 class="panel-title">Order Workbench</h3>
		</div>
		
		<div class="panel-body">
			<div class="form-inline">

				<!-- LOCATION -->
				<div class="form-group pad-lft">
					<select id="select-location" class="form-control">
						<option value='1' selected='selected'>Christchurch</option>
						<option value='2'>Auckland</option>
						<option value='3'>Wellington</option>
						<option value='4'>Hamilton</option>
					</select>
				</div>
				<!-- SUPPLIER -->
				<div class="form-group pad-lft">
					<label for="supplier">Supplier</label>
					<select id="select-supplier" class="form-control">
						<option value='0'></option>
						<?php
							//TODO change getSupplierRecords(location_id)
							foreach($orderObj->getSupplierRecords(1) as $supplier) {
								echo "<option value=".$supplier['id'].">".$supplier['supplier']."</option>";
							}
						?>
					</select>
				</div>
				
				<!-- COVER -->
				<div class="form-group pad-lft">
					<label for="cover">Cover</label>
					<div class="input-group">
						<input type="text" id="text-cover" name="cover" class="form-control" value="<?php echo $cover ?>" size="2" /><span class="input-group-addon">months</span>
					</div>
				</div>

				<!-- ETA -->
				<div class="form-group pad-lft">
					<label for="eta_date">ETA</label>
					<div class="input-group date"><input type="text" class="form-control" id="datepicker-eta" /><span class="input-group-addon"><i class="fa fa-calendar fa-lg"></i></span></div>
				</div>
				<div class="form-group pad-lft">
					<button class="btn btn-primary" id='btn-load_supplier_stock'>Load Supplier's Stock</button>
				</div>
			</div>
		</div>
	</div>

</div>
<div class='row'>




	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="panel-control">
				<button class="btn btn-default" data-target="#panelOpenOrders" data-toggle="collapse" aria-expanded="false" id="dontBreakMyScroll"><i class="fa fa-chevron-down"></i></button>
			</div>
			<h3 class="panel-title">Open Order : : <span id="open_order_date">None Selected</span></h3>
		</div>
		<div class="panel-body collapse" id="panelOpenOrders">
			<div class=''>
				<!-- OPEN ORDER INFO -->
				<button class='pull-right btn btn-warning'>Discard</button>
				<button class='pull-right btn btn-primary'>Export</button>

			</div>


<select class='pull-left' id="all_open_orders">

</select>

			<table class='table' id='tblOpenOrder' data-toggle="table" data-card-view="false" >
			    <thead>
			        <tr>
			            <th data-field="inventory_id">ID</th>
			            <th data-field="barcode">Barcode</th>
			            <th data-field="stockcode">SKU</th>
			            <th data-field="manuf_code">Manuf Code</th>
			            <th data-field="description">Description</th>
			            <th data-field="qty">QTY</th>
			            <th data-field="cost_ex" class='text-right' data-formatter="priceFormatter">Cost (ex)</th>
			            <th data-field="order_workbench_line_id" class='text-left' data-formatter="removeLineFormatter" data-events="removeLineEvents"></th>
			        </tr>
			    </thead>
			</table>
		</div>
	</div>






	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title">Supplier Stock</h3>
		</div>
		<div class="panel-body">
			<table class='table' id='tblSupplierStock' data-toggle="table" data-url="../api/v2/order_workbench/get_supplier_stock?supp_id=<?php echo $suppID; ?>" data-card-view="false" data-response-handler="handleSupplierStock" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-select-item-name="toolbar1">
			    <thead>
			        <tr>
			            <th data-field="web_url" data-formatter="urlFormatter">Web</th>
			            <th data-field="barcode">Barcode</th>
			            <th data-field="stockcode">Stockcode</th>
						<th data-field="manuf_code">Manuf. Code</th>
						<th data-field="description">Description</th>
						<th data-field="last_gr_date" class='text-center'>Last GR</th>
			            <th data-field="stock_cover" class='text-right'>Cover (months)</th>
			            <th data-field="quantity" class='text-right'>SOH</th>
			            <th data-field="order" class='text-right'>Est. Order</th>
			            <th data-field="cost_ex"  data-formatter='priceFormatter' class='text-right'>Cost(inc)</th>


	            		<th id="month5Header" data-field="month_sold_5" class='text-right' data-formatter="monthFormatter" data-visible="false">Month 6</th>
	            		<th id="month4Header" data-field="month_sold_4" class='text-right' data-formatter="monthFormatter" data-visible="false">Month 5</th>
	            		<th id="month3Header" data-field="month_sold_3" class='text-right' data-formatter="monthFormatter" data-visible="false">Month 4</th>
	            		<th id="month2Header" data-field="month_sold_2" class='text-right' data-formatter="monthFormatter" data-visible="false">Month 3</th>
	            		<th id="month1Header" data-field="month_sold_1" class='text-right' data-formatter="monthFormatter" data-visible="false">Month 2</th>
	            		<th id="month0Header" data-field="month_sold_0" class='text-right' data-formatter="monthFormatter" data-visible="false">Month 1</th>


			            <th data-field="total_sold" class='text-right'>Total Sales<br/>(6 Months)</th>
			            <th data-field="monthly_avg" data-formatter="roundFormatter" class='text-right'>Average Sales<br/>(per Month)</th>
			            <th data-field="daily_avg" data-formatter="roundFormatter" class='text-right'>Average Sales<br/>(per Day)</th>
			            <th data-field="order" data-formatter="textInputFormatter" data-events="orderEvents">Order</th>
			        </tr>
			    </thead>
			</table>
		</div>
	</div>

</div>










<script src="/lib/template/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript">

	//global variables
	
	var staffID=<?php echo $loginStaffID ; ?>;
	var locationID = -1;
	var supplierID = -1;
	var openOrderID = -1;

    function rhOpenOrderLines(res) {
    	if (typeof res.lines != 'undefined' && res.lines != null) {
	        return res.lines;
	    }
	    return [];
	    }

	//date picker
	$("#eta_date").datepicker({autoclose:true, format: 'dd/mm/yyyy'});	




	//on user initiate :: load supplier stock
	$("#btn-load_supplier_stock").on("click", function(){
		locationID = $("#select-location").val();
		supplierID = $("#select-supplier").val();
		var cover = $("#text-cover").val(); //in months
		var eta = $("#datepicker-eta").val();
	



		
		$.post( "../api/v2/order_workbench/get_all_open_orders", { supp_id: supplierID } )
			.done(function( data ) {

				console.log(data);
				
				var myList = "";
				var availableOrderCount = 0;
				
				$.each(data, function(i, item) {
    				myList += "<option>"+item.staff_name+" ("+item.order_date+") $"+item.total_cost_ex+"</option>";
					availableOrderCount ++;

					if (item.staff_id == staffID){
						//select this as current open order
					}


				});
				$("#open_order_date").text("None Selected, " + availableOrderCount + " available");

				myList += "";


	    	$("#all_open_orders").html(myList);

	  	});	



		



		$.post( "../api/v2/order_workbench/get_open_order", { supp_id: supplierID, staff_id: staffID } )
			.done(function( data ) {

			if (typeof data.id != 'undefined' && data.id != null) { 
		    	openOrderID = data.id;
		    	$("#open_order_date").text(data.staff_name +" ("+ data.order_date + ")");

		    	//gets the lines, LOAD INTO BOOTSTRAP TABLE
				$("#tblOpenOrder").bootstrapTable('refresh', {
				    url: '../api/v2/order_workbench/get_order_lines?order_id='+data.id
				});
			}
			else{
				
				
			}

	  	});	






		$("#tblSupplierStock").bootstrapTable('refresh', {
		    url: '../api/v2/order_workbench/get_supplier_stock?supp_id='+supplierID+'&loc_id='+locationID+'&cover='+cover
		});

	});





	//Format monthly sales qty
	function monthFormatter(value){
		if (typeof value != 'undefined' && value != null) {
			var myClass = "";
			if ( value["instock"] == '0') myClass = "out_of_stock";
			return "<div class='"+myClass+"'>"+value["qty"]+"</div>";
		}
		return "N/A";
	}			

	//format cell with textbox and button
	function textInputFormatter(value){
		return "<div class='input-group  order_qty'><input type='text' class='form-control txt_order_qty' value='"+value+"'><span class='input-group-btn'><button class='btn btn-default btnAddOrder' type='button' href='javascript:void(0)'>Add</button></span></div>";
	}

	
	//ADD LINE TO ORDER
    window.orderEvents = {'click .btnAddOrder': function (e, value, row, index) {

    	console.log("adding to order");

    	var qty =  $($(".txt_order_qty")[index]).val();
    	
    	var inventoryID = row["inventory_id"];
    	var cost_ex = row["cost_ex"];


    	if (supplierID < 0){
    		alert("Error: no supplier selected");
    		return;
    	}


    	if (staffID < 0){
    		alert("Error: no supplier selected");
    		return;
    	}


    	if (qty <= 0){
    		alert("Error: No quantity to order");
    		return;
    	}


		console.log( "qty: " + qty );
		console.log( "currentOrderID: " + openOrderID );
		console.log( "location_id: " + locationID );
		console.log( "inventory_id: " + inventoryID );
		
		console.log( "cost_ex: " + cost_ex );

		console.log( "supplierID: " + supplierID );
		console.log( "staffID: " + staffID );



			$.post( "../api/v2/order_workbench/add_line", { location_id: locationID, order_id: openOrderID , qty: qty, inventory_id : inventoryID, cost_ex:cost_ex, supplier_id: supplierID, staff_id: staffID })
			  .done(function( data ) {
			  	console.log("Line Added");
			    console.log(data);

			    if(openOrderID == -1){
			    	openOrderID = data.order_id;
			    	$("#open_order_date").text("New Order Opened...");
				
				}

				$("#tblOpenOrder").bootstrapTable('refresh', {
				    url: '../api/v2/order_workbench/get_order_lines?order_id='+openOrderID
				});


 			});

		
    }};



    function priceFormatter(value) {
    	n = parseFloat(value);
    	return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    }


    function urlFormatter(value) {
    	if (value == "") return "";
    	return "<a target='_blank' href='"+value+"'>link</a>";
    }


    function roundFormatter(value){
    	return value.toFixed(2) ;
    }






    function removeLineFormatter(value, row, index) {
        return [
            '<a class="remove" href="javascript:void(0)" title="Remove Line">',
                '<i class="fa fa-times"></i>',
            '</a>'
        ].join('');
    }

    window.removeLineEvents = {'click .remove': function (e, value, row, index) {

	    console.log("removing line " + value);

    	
		$.post( "../api/v2/order_workbench/remove_line", { line_id : value})
		  .done(function( data ) {
		    console.log(data);
		    $("#tblOpenOrder").bootstrapTable('refresh');
		});
	
		
    }};








</script>













<?php
	include($_SERVER['DOCUMENT_ROOT']."/lib/template/footer.php");
?>