<?php
	define('NAV', 'staff');
	include($_SERVER['DOCUMENT_ROOT']."/lib/template/header.php");
	
	include_once($_SERVER['DOCUMENT_ROOT']."/lib/scripts/config.php");
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DB);
	if (mysqli_connect_errno()) {
		error("mysqli_connect_error()");
		return false;
	}
        
        $locationObj = new locationModel($mysqli);
        $locations = $locationObj->getLocations();
?>

	<!--X-editable [ OPTIONAL ]-->
	<link href="/lib/template/plugins/x-editable/css/bootstrap-editable.css" rel="stylesheet">

<style type="text/css">
img.profile{
	height:50px;
}

.street_address {
	width: 160px;
}

.label-table {
	width: 100%;
}

.table-striped td {
	height: 67px;
	vertical-align: middle !important;
}

.location {
	font-size:40px;
	display:inline-block;
	vertical-align: top;
}

.location_info {
	display: block;
	vertical-align: top;
	width: 100%;
	margin-left: 30px;
	margin-bottom: 30px;
}

.location_info td {
	min-width: 100px;
}

#title_sm {
	display: none;
}

@media (max-width:550px) {
	#title_lg {
		display: none;
	}
	
	#title_sm {
		display: block;
	}
	
	.location_info {
		margin-left: 10px;
	}
}
</style>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-pink">
			<div class="panel-heading">
				<div class="panel-control">
					<ul class="nav nav-tabs">
                                            <?php
                                            foreach($locations as $location){
                                                if($location['short_code'] == "CHC"){
                                                    print("<li class='active'>");
                                                } else {
                                                    print("<li>");
                                                }
                                                ?>
						<a data-toggle="tab" href="#tabs-box-<?=$location['location']?>" aria-expanded="true"><span id="title_lg"><?=$location['location']?></span><span id="title_sm"><?=$location['short_code']?></span></a></li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
				</div>
				<h3 class="panel-title">Edit Staff</h3>
			</div>
			<div class="panel-body">
				<div class="tab-content">
<?php
	$crntLocation = "";
	$crntRole = "";
	$imageURL = 'lib/img/profile/';

		$result = mysqli_query($conn, "SELECT *, staff.id AS staffID, location.id AS locationID FROM staff JOIN location ON staff.location = location.id ORDER BY staff.location, active DESC, name"); // JOIN location ON staff.location = location.id");
		while ($row = mysqli_fetch_array($result)) {
			$id = $row['staffID'];
			$imageName = $row['image_url'];
			$name = $row['name'];
			$barcode = $row['barcode'];
			$shortcode = $row['shortcode'];
			$location = $row['location'];
			$locationID = $row['locationID'];
			$role = $row['role'];
			$role_title = $row['role_title'];
			$email = $row['email'];
			$email2 = $row['email2'];
			$phone1 = $row['phone1'];
			$phone2 = $row['phone2'];
			$ddi = $row['ddi'];
			$ext = $row['ph_ext'];
			$skype = $row['skype'];
			$address = $row['address'];
			$bank_account = $row['bank_acc'];
			$firearms_licence = $row['firearms_licence'];
			$active = $row['active'];
			
			
			if ($crntLocation != $location){
				
				if ($crntLocation != "") {
?>
				</table>
				</div>
				</div>
<?php
				}
?>
					<div id="tabs-box-<?php print $location ?>" class="tab-pane fade <?php print ($location == "Christchurch" ? "active in" : "") ?>">
					<div class="row">
													<?php 
									if(isset($_GET['sucess'])) {
										if($_GET['sucess'] == true) {
											print "<div class='alert alert-success alert-dismissable'>
													<button type='button' class='close' data-dismiss='alert' 
													aria-hidden='true'>&times;</button>
													Successfully added new Staff
													</div>";
										} else {
											print "<div class='alert alert-danger'>
													<button type='button' class='close' data-dismiss='alert'
													aria-hidden='true'>&times;</button>
													Oh no! Something went worng!
													</div>";
										}
									}
								?>
						<div class="col-md-4">
							<div class="location"><?php print $location ?></div>
							<div class='location_info'>
								<table class='store_info'>
									<tr><td class='storeInfoHeader'>Phone</td><td><?php print $row['store_phone'] ?></td></tr>
									<tr><td class='storeInfoHeader'>Fax</td><td><?php print $row['store_fax'] ?></td></tr>
									<tr><td class='storeInfoHeader'>Email</td><td<?php print $row['store_email'] ?></td></tr>
									<tr><td class='storeInfoHeader'>Address</td><td><?php print $row['store_address'] ?></td></tr>
									<tr><td class='storeInfoHeader'>GST number</td><td><?php print $row['store_GST'] ?></td></tr>
								</table>
							</div>
							<div class="col-sm-12">
								<a target="_new" href="create_staff.php"><button class="btn btn-info btn-labeled fa fa-user fa-lg" type="btn">Create Employee</button></a>
								<input style="margin-left:2em" type="text" id="search" class="input-sm" placeholder="Search Staff">
							</div>
							</div>

							
						
						
					</div>
				
					<div class="table-responsive">
					<table class="table table-striped" id="staffTable">
						<thead>
							<tr>
								<th></th>
								<th>ID</th>
								<th>Name</th>
								<th>Barcode</th>
								<th>Shortcode</th>
								<th>Role</th>
								<th>Role Title</th>
								<th>Email</th>
								<th>Phone</th>
								<th>DDI</th>
								<th>Ext</th>
								<th>Skype</th>
								<th>Address</th>
								<th>Bank Account</th>
								<th>Firearms Licence</th>
                                <th>Reset Password</th>
								<th>Active</th>
							</tr>
						</thead>
<?php
				$crntLocation = $location;
			}
?>
			<tr>
<!--
				<td><?php print ($imageName == '' ? '' : "<img class='profile img-circle' src='".$imageURL.$imageName."'>") ?></td>
				<td><?php print $name ?></td>
				<td><?php print $barcode ?></td>
				<td><?php print $email ?></td>
				<td><?php print ( $phone1 == '' ? '' : $phone1."<br/>" ) . ( $phone2 == '' ? '' : $phone2 ) ?></td>
				<td><?php print ( $ddi == '' ? '' : 'ddi:'.$ddi."<br/>" ) . ( $ext == '' ? '' : 'ext:'.$ext ) ?></td>
				<td><?php print $skype ?></td>
-->
				<!-- Image -->
				<td class="id">
					<?php print ($imageName == '' ? '' : "<img class='profile img-circle' src='".$imageURL.$imageName."'>") ?>
				</td>
				
				<!-- ID -->
				<td class="id">
					<?php echo $id; ?>
				</td>
				
				<!-- NAME -->
				<td>
					<span id="name_<?php echo $id; ?>" class="name"><?php echo $name; ?></span> 
				</td>
				
				<!-- MYOB CODE -->
				<td>
					<span id="barcode_<?php echo $id; ?>" class="barcode"><?php echo $barcode; ?></span>
				</td>
				
				<!-- SHORT CODE -->
				<td>
					<span id="barcode_<?php echo $id; ?>" class="shortcode"><?php echo $shortcode; ?></span>
				</td>

				<!-- role -->
				<td>
					<span id="role_<?php echo $id; ?>" class="role"><?php echo $role; ?></span>
				</td>
				
				<!-- role title -->
				<td>
					<span id="roletitlename_<?php echo $id; ?>" class="roleTitle"><?php echo $role_title; ?></span>
				</td>
				
				<!-- // Email 1 & 2 -->
				<td>
					<span id="email_<?php echo $id; ?>" class="email1"><?php echo $email; ?></span><br/>
					<span id="email2_<?php echo $id; ?>" class="email2"><?php echo $email2; ?></span>
				</td>
				
				<!-- //phone 1 & 2 -->
				<td>
					<span id="phone2_<?php echo $id; ?>" class="phone2"><?php echo $phone2; ?></span><br>
					<span id="phone1_<?php echo $id; ?>" class="phone1"><?php echo $phone1; ?></span>
				</td>
				
				<!-- DDI -->
				<td>
					<span id="ddi_<?php echo $id; ?>" class="ddi"><?php echo $ddi; ?></span>
				</td>
				
				<!-- Ext -->
				<td>
					<span id="ext_<?php echo $id; ?>" class="ext"><?php echo $ext; ?></span>
				</td>
				
				<!-- SKYPE -->
				<td>
					<span id="skype_<?php echo $id; ?>" class="skype"><?php echo $skype; ?></span>
				</td>
				
				<!-- ADDRESS -->
				<td class="street_address">
					<span id="address_<?php echo $id; ?>" class="address"><?php echo nl2br($address); ?></span>
				</td>
				
				<!-- BANK ACCOUNT -->
				<td>
					<span id="bank_account_<?php echo $id; ?>" class="bankAccount"><?php echo $bank_account; ?></span>
				</td>
				
				<!-- FIREARMS LICENCE -->
				<td>
					<span id="firearms_licence_<?php echo $id; ?>" class="firearmsLicence"><?php echo $firearms_licence; ?></span>
				</td>
				
                                <td>
                                    <button type="button" data-name="<?=$name?>" data-id="<?=$id?>" class="btn btn-info resetPass"><i class="fa fa-repeat"></i></button>
                                </td>
                                
				<!-- ACTIVE -->
				<td>
					<label id="active_<?php echo $id; ?>" class="staff_active label label-table label-<?php echo ($active ? "success" : "warning"); ?>"><?php echo ($active ? "Active" : "Disabled"); ?></label>
				</td>
			</tr>
<?php
	}
	mysqli_close($conn);
	?>
</table>
</div>
</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	include($_SERVER['DOCUMENT_ROOT']."/lib/template/footer.php");
?>

	<!--X-editable [ OPTIONAL ]-->
	<script src="/lib/template/plugins/x-editable/js/bootstrap-editable.js"></script>

<script>
$(document).ready(function() {
	//$.fn.editable.defaults.showbuttons = false;
	
	// X-EDITABLE USING FONT AWESOME ICONS
	// =================================================================
	$.fn.editableform.buttons =
		'<button type="submit" class="btn btn-primary editable-submit">'+
			'<i class="fa fa-fw fa-check"></i>'+
		'</button>'+
		'<button type="button" class="btn btn-default editable-cancel">'+
			'<i class="fa fa-fw fa-times"></i>'+
		'</button>';

	$('.barcode').editable({
		type: 'text',
		title: 'Enter staff barcode',
		pk: 0,
		params: function (params) {
			var data = {};
			data.id = $(this).attr("id");
			data.action = "update_barcode";
			data.value =  params.value;
			return data;
		},
		url: '/lib/scripts/api/staff_edit_table.json.php',
	});
	
	$('.shortcode').editable({
		type: 'text',
		title: 'Enter staff shortcode',
		pk: 0,
		params: function (params) {
			var data = {};
			data.id = $(this).attr("id");
			data.action = "update_shortcode";
			data.value =  params.value;
			return data;
		},
		url: '/lib/scripts/api/staff_edit_table.json.php',
	});
	
	$('.role').editable({
		type: 'text',
		title: 'Enter staff role',
		pk: 0,
		params: function (params) {
			var data = {};
			data.id = $(this).attr("id");
			data.action = "update_role";
			data.value =  params.value;
			return data;
		},
		url: '/lib/scripts/api/staff_edit_table.json.php',
	});
	
	$('.roleTitle').editable({
		type: 'text',
		title: 'Enter staff role title',
		pk: 0,
		params: function (params) {
			var data = {};
			data.id = $(this).attr("id");
			data.action = "update_role_title";
			data.value =  params.value;
			return data;
		},
		url: '/lib/scripts/api/staff_edit_table.json.php',
	});
	
	$('.email1').editable({
		type: 'text',
		title: 'Enter staff email',
		pk: 0,
		params: function (params) {
			var data = {};
			data.id = $(this).attr("id");
			data.action = "update_email";
			data.value =  params.value;
			return data;
		},
		url: '/lib/scripts/api/staff_edit_table.json.php',
	});
	
	$('.phone2').editable({
		type: 'text',
		title: 'Enter staff phone',
		pk: 0,
		params: function (params) {
			var data = {};
			data.id = $(this).attr("id");
			data.action = "update_phone";
			data.value =  params.value;
			return data;
		},
		url: '/lib/scripts/api/staff_edit_table.json.php',
	});
	
	$('.ddi').editable({
		type: 'text',
		title: 'Enter staff ddi',
		pk: 0,
		params: function (params) {
			var data = {};
			data.id = $(this).attr("id");
			data.action = "update_ddi";
			data.value =  params.value;
			return data;
		},
		url: '/lib/scripts/api/staff_edit_table.json.php',
	});
	
	$('.ext').editable({
		type: 'text',
		title: 'Enter staff Ext',
		pk: 0,
		params: function (params) {
			var data = {};
			data.id = $(this).attr("id");
			data.action = "update_ext";
			data.value =  params.value;
			return data;
		},
		url: '/lib/scripts/api/staff_edit_table.json.php',
	});
	
	$('.skype').editable({
		type: 'text',
		title: 'Enter staff skype',
		pk: 0,
		params: function (params) {
			var data = {};
			data.id = $(this).attr("id");
			data.action = "update_skype";
			data.value =  params.value;
			return data;
		},
		url: '/lib/scripts/api/staff_edit_table.json.php',
	});
	
	$('.address').editable({
		type: 'text',
		title: 'Enter staff address',
		pk: 0,
		params: function (params) {
			var data = {};
			data.id = $(this).attr("id");
			data.action = "update_address";
			data.value =  params.value;
			return data;
		},
		url: '/lib/scripts/api/staff_edit_table.json.php',
	});
	
	// $('.staff_active').editable({
		// type: 'text',
		// title: 'Enter staff barcode',
		// pk: 0,
		// params: function (params) {
			// var data = {};
			// data.id = $(this).attr("id");
			// data.action = "update_comment";
			// data.value =  params.value;
			// return data;
		// },
		// url: '/lib/scripts/api/staff_edit_table.json.php',
	// });
});

$("#search").keyup(function ()
	{
		var search = $(this).val().toLowerCase();
		//console.log($(this));
		//console.log($("#staffTable tr"));
		//console.log($(this).val());
		
		jQuery.expr[':'].icontains = function(a, i, m) {
		return jQuery(a).text().toLowerCase()
			.indexOf(m[3].toLowerCase()) >= 0;
		};
		
		

		$("#staffTable tr span.name:icontains(" + search + ")").each(function () 
		{
			$(this).closest("tr").show();

			if(search == "")
			{
				$(this).closest("tr").show();
			}
		});
		
		$("#staffTable tr span.name:not(:icontains(" + search + "))").each(function () 
		{
			$(this).closest("tr").hide();

			if(search == "")
			{
				$(this).closest("tr").show();
			}
		});
	});

    $(".resetPass").click(function(){
        var id = $(this).data("id");
        var name = $(this).data("name");

        $.ajax({
            type: "POST",
            url: "/lib/scripts/api/staff.json.php",
            data: {
                action : "reset_password",
                id : id
            },
            dataType: 'json', }).done(function(json){
                console.log(json);
                alert("Password for " + name + " has been reset.");
            }).fail(function(errMsg){
                console.log(errMsg);
        });

    });
</script>