<?php
/********include the header file from Gun City File**********/
	define('NAV', 'reports');
	include($_SERVER['DOCUMENT_ROOT']."/lib/template/header.php");




//$loginStaffID = 3;
//$loginStaffName = 'Rui';

if (isset($_GET["id"])){
		$taskID = $_GET["id"];	
	}
?>




	<!-- ***********script and CSS links at GUN CITY*********-->
	
	<link rel = "stylesheet" href = "./CSS/main.css"/>
	<link rel = "stylesheet" type = "./CSS/bootstrap.min.css"/>
	<link rel = "stylesheet" href = "./CSS/jquery.simple-dtpicker.css" />
	<link rel = "stylesheet" href = "/lib/css/bootstrap-table.css"/>

	
	
	
	
<!--********script and CSS links at CPIT**********
	<link rel = "stylesheet" type = "text/css" href = "./CSS/main.css"/>
	<link rel = "stylesheet" type = "text/css" href = "./CSS/bootstrap.min.css"/>
	<link rel = "stylesheet" type = "text/css" href = "./CSS/bootstrap-table.css"/>
	<link rel = "stylesheet" type = "text/css" href = "./CSS/jquery.simple-dtpicker.css"/>

*-->	
	<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-back-12">
			<div class="panel-heading">
				<div class="alert alert-success">
  					<h3 class= "panel-title"><strong>Hi <?=$loginStaffName?>! Infomation in the next section is partially editable in line.</h3> 
				</div>
			</div>
			
			<div class="panel-body">
			<br><br>
			<table id = "taskInfo" class="table table-bordered table-striped" style="clear:both">
						<tr>
							<td width="20%"><b>Task Create By ::</b></td>
							<td width="80%"><b class="createBy"></b></td>
						</tr>
							
						<tr>
							<td width="20%"><b>Task Name::</b></td>
							<td width="80%"><b class="taskName"> </b></td>
						</tr>

						<tr>
							<td width="20%"><b>Task Decription <span style="color:red">(Editable)</span>::</b></td>
							<td width="80%"><b class="taskDescription"> </b></td>
						</tr>

						<tr>
							<td width="20%"><b>Task Duration(Hrs) <span style="color:red">(Editable)</span>::</b></td>
							<td width="80%"><b class="duration"></b></td>
						</tr>

						<tr>
							<td width="20%"><b>Task ETA <span style="color:red">(Editable)</span>::</b></td>
							<td width="80%"><b class="ETA" data-template="D MMM YYYY" data-formate = "DD-MM-YYYY " data-viewformat="DD/MM/YYYY"></b></td>
						</tr>

						<tr>
							<td width="20%"><b>Task Status <span style="color:red">(Editable)</span>::</b></td>
							<td width="80%"><b class="status"></b></td>
						</tr>

						<tr>
							<td width="20%"><b>Task Priority <span style="color:red">(Editable)</span>::</b></td>
							<td width="80%"><b class="priority"></b></td>
						</tr>

				</table>

				<div>
					<ul class="nav nav-tabs">
				    <li class="active alert-info"><a data-toggle="tab" href="#Hierarchy" ><strong >Task Hierarchy</strong></a></li>
				    <li><a class="alert alert-info" data-toggle="tab" href="#Desination "><strong> Staff Designed</strong></a></li>

				    </ul>
				</div>
				<div class="tab-content">

					<div id="Hierarchy" class="tab-pane fade in active">
						<div class="alert alert-info">
		  					<h5><strong>Parent Task For :: &nbsp<span class="TheTaskName" style="color:black"> </span></strong></h5> 
						</div>
						<table class="table table-condensed" id="parentTask" data-toggle="table" data-url="./api/task/get_parent_task?taskID=<?php echo $taskID; ?>" >
							<thead>
								<tr>
									<th data-field="<?php echo $loginStaffID ?>;" data-formatter ="infromationFormater" data-halign="center" data-align="center">Info</th>
									<th data-field="TaskName">Task Name</th>
									<th data-field="CreateBy">Create By</th>
									<th data-field="ETA" data-formatter ="eatFormater">ETA</th>
									<th data-field="duration" data-formatter = "durationFormater">Duration</th>
									<th data-field="Status" data-formatter = "starusFormater"  >Status</th>
									<th data-field="Priority" data-formatter = "priorityFomater" >Priority</th>
								</tr>
							</thead>
						</table><br><br>

						<div class="alert alert-info">
		  					<h5><strong>Sub Task(s) For :: &nbsp<span class="TheTaskName" style="color:black"> </span></strong></h5> 
						</div>
						<table class="table table-condensed" id="subTask" data-toggle="table" data-url="./api/task/get_sub_task?taskID=<?php echo $taskID; ?>" >
							<thead>
								<tr>
									<th data-field="<?php echo $loginStaffID ?>;" data-formatter ="infromationFormater" data-halign="center" data-align="center">Info</th>
									<th data-field="TaskName" data-sortable="true">Task Name</th>
									<th data-field="CreateBy" data-sortable="true">Create By</th>
									<th data-field="ETA" data-formatter ="eatFormater" data-sortable="true">ETA</th>
									<th data-field="duration" data-formatter = "durationFormater" data-sortable="true">Duration</th>
									<th data-field="Status" data-formatter = "starusFormater" data-sortable="true" >Status</th>
									<th data-field="Priority" data-formatter = "priorityFomater" data-sortable="true">Priority</th>
								</tr>
							</thead>
						</table><br><br>
				</div>
				
				<div id="Desination" class="tab-pane fade">
					<div class="alert alert-info">
	  					<h5><strong>Staff Delegated to :: &nbsp<span class="TheTaskName" style="color:black"> </span></strong></h5> 
					</div>
					<table class="table table-condensed" id="delegate" data-toggle="table" data-url="./api/task/get_delegate_Staff?taskID=<?php echo $taskID; ?>">
						<thead>
							<tr>
								<!--  <th data-field="staffID" ata-halign="center" data-align="center">Staff ID</th> -->
								<th data-field="staffName" data-halign="center" data-align="center">Staff Name</th>
								<th data-field="staffContact" data-halign="center" data-align="center">Staff Contact</th>
								<th data-field="staffEmail" data-halign="center" data-align="center">Staff Email</th>
							</tr>
						</thead>
					</table><br><br>


					<div class="alert alert-info">
	  					<h5><strong> Staff Share to :: &nbsp <span class="TheTaskName" style="color:black"></span></strong></h5> 
					</div>
					<table class="table table-condensed" id="share" data-toggle="table" data-url="./api/task/get_share_Staff?taskID=<?php echo $taskID; ?>" >
						<thead>
							<tr>
								<!-- <th data-field="staffID" ata-halign="center" data-align="center">Staff ID</th> -->
								<th data-field="staffName" data-halign="center" data-align="center">Staff Name</th>
								<th data-field="staffContact" data-halign="center" data-align="center">Staff Contact</th>
								<th data-field="staffEmail" data-halign="center" data-align="center">Staff Email</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>

			
				
    		</div>
    	</div>
    </div>
</div>
	
	
<?php
	include($_SERVER['DOCUMENT_ROOT']."/lib/template/footer.php");
?>


<!-- ********js selection for GC******* -->

	<script type="text/javascript" src="./Javascript/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="/lib/template/js/bootstrap-table.js"></script>
	<script type="text/javascript" src="./Javascript/jquery.simple-dtpicker.js"></script>
	<script type="text/javascript" src="./Javascript/moment.js"></script>
	<script type="text/javascript" src="./Javascript/task_managment.js"></script>
	<script type="text/javascript" src="/lib/template/js/bootstrap.js"></script>
	<script type="text/javascript" src="/lib/template/plugins/x-editable/js/bootstrap-editable.js"></script> 
	<script type="text/javascript" src="/lib/template/js/jquery.number.min.js"></script>


	<!-- ********js selection for CPIT******
	
	<script type = "text/javascript" src = "./Javascript/jquery-1.11.3.min.js"></script>
	<script type = "text/javascript" src = "./Javascript/jquery-ui.js"></script>
	<script type = "text/javascript" src = "./Javascript/bootstrap-table.js"></script>
	<script type = "text/javascript" src = "./Javascript/jquery.simple-dtpicker.js"></script>
	<script type = "text/javascript" src = "./Javascript/task_managment.js"></script>

	* -->
	

	<script>

	$.post( "./api/task/get_task_infoTb?taskID=<?php echo $taskID; ?>").then(function (value) {
		console.log(value);
		var task_name = value[0].TaskName;
		var task_description = value[0].TaskDescription;
		var task_owner = value[0].CreateBy;
		var task_CD = value[0].CreateDate;
		var task_Duration = value[0].Duration;
		var task_ETA = value[0].ETA;
		var task_status = value[0].Status;
		var task_priority = value[0].Priority;
		var parent_task_id= value[0].ParentTask;
		var parent_task_name = value[1];
		var parent_task = "ID: " + parent_task_id + "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Name: "+ parent_task_name;

		switch (task_priority) {
			case 1:  
			task_priority = 'Urgent';
			break;
			case 2:
			task_priority = 'High';
			break;
			case 3:
			task_priority = 'Medium';
			break;
			case 4:
			task_priority = 'Low';
			break;	
		}

		switch (task_status) {
			case 1:  
			task_status = 'Not yet started';
			break;
			case 2:
			task_status = 'Underway';
			break;
			case 3:
			task_status = 'Nearly Completed';
			break;
			case 4:
			task_status = 'Completed';
			break;	
			}

		task_CD = moment(task_CD).format('DD/MM/YYYY');
		task_ETA = moment(task_ETA).format('DD/MM/YYYY');

		$('.TheTaskName').html(task_name);
		$('.taskName').html(task_name);
		$('.taskDescription').html(task_description);
		$('.createBy'). html(task_owner);
		$('.duration'). html(task_Duration);
		$('.ETA'). html(task_ETA);
		$('.status'). html(task_status);
		$('.priority').html(task_priority);
		//alert(task_name);
	});

$(document).ready(function() {
	//$.fn.editable.defaults.showbuttons = false;
	
	// X-EDITABLE USING FONT AWESOME ICONS

	$.fn.editableform.buttons =
		'<button type="submit" class="btn btn-primary editable-submit">'+
			'<i class="fa fa-fw fa-check"></i>'+
		'</button>'+
		'<button type="button" class="btn btn-default editable-cancel">'+
			'<i class="fa fa-fw fa-times"></i>'+
		'</button>';


		$('.taskDescription').editable({
			type: 'text',
			title: 'Edit Task Description',
			pk: 0,
			params: function (params) {
				var data = {};
				data.id = <?=$taskID?>;
				data.action = "update_taskDesc";
				data.value =  params.value;
				console.log(data);
				return data;
			},
			url: './api/task_edit_table.json.php',
		});


		$('.duration').editable ({
			type: 'number',
			title: 'Change task Duration',
			pk: 0,
			params: function (params) {
				var data = {};
				data.id = <?=$taskID?>;
				data.action = "update_duration";
				data.value = params.value;
				console.log(data);
				return data;
			},
			url: './api/task_edit_table.json.php',

		});

		$('.ETA').editable ({
			type:'combodate',
			//combodate:{minYear:2015 maxYear: 2020};
			title:'Change Estimated Time of Finish',
			pk:0,
			params: function (params) {
				var data = {};
				data.id = <?=$taskID?>;
				data.action = "update_ETA";
				data.value = params.value;
				console.log(data);
				return data;
			},
			url: './api/task_edit_table.json.php',

		});


		$('.priority').editable({
			type: 'select',
			title: 'Select Priority',
			pk:0,
			source:[{'value': 1, text: 'Urgent'}, 
					{'value': 2, text: 'High'},
					{'value': 3, text: 'Medium'}, 
					{'value': 4, text: 'Low'}],
			params: function (params) {
					var data = {};
					data.id = <?=$taskID?>;
					data.action = "update_priority";
					data.value = params.value;
					return data;
				},
				url: './api/task_edit_table.json.php',
			});



			$('.status').editable({
			type: 'select',
			title: 'Select Status',
			pk:0,
			source: [{value: 1, text: 'Not yet started'},
		             {value: 2, text: 'Underway'},
		             {value: 3, text: 'Nearly Completed'},
		             {value: 4, text: 'Completed'}],
			params: function (params) {
					var data = {};
					data.id = <?=$taskID?>;
					data.action = "update_status";
					data.value = params.value;
					return data;
				},
				url: './api/task_edit_table.json.php',
			});


}); 

	function infromationFormater(value, row ) {
		var taskID = row.TaskID 
		var staff_id = <?=$loginStaffID?>;
		var staffID = row.staffID;
		//var theName = row.TaskName;
		var newString = taskID;//+'&TaskName='+theName;
		if (staffID == staff_id) {
		return [
		'<a href="task_display_owner.php?id='+newString+'" title="Task Details" target="_blank">',
		'<i class="fa fa-info-circle fa-lg"></i>',
		'</a>'].join('');
		} else { return [
		'<a href="task_display_delegated.php?id='+newString+'" title="Task Details" target="_blank">',
		'<i class="fa fa-info-circle fa-lg"></i>',
		'</a>'].join('')} 					
	}

	
	</script>


	
