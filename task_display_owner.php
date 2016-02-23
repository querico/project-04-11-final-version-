<?php
/********include the header file from Gun City File**********/
	define('NAV', 'reports');
	include($_SERVER['DOCUMENT_ROOT']."/lib/template/header.php");

if (isset($_GET["id"])){
		$taskID = $_GET["id"];	
	} 


	
require_once("task_model.php");
$myTaskObj = new TaskModel();
//$staffname = $myTaskObj->displayStaffInfo('nameOnly',$loginStaffID);// get all staff
$staffname = $myTaskObj->displayStaffInfo($loginStaffID);

$taskName = $myTaskObj->displayTaskName($loginStaffID, $taskID); // get all task name and id for all task create by, shared and delegated to this staff
$sourceTaskName = array();
array_push($sourceTaskName, array('value' => 0, 'text' => 'N/A'));
foreach ($taskName as  $value) {
array_push($sourceTaskName, array('value' => $value['ID'], 'text' => $value['Name']));
}

$complted = $myTaskObj-> displayCompletedLevel($taskID);
$complted = (($complted[0]['completedLevel']) * 100);
$compltedPercentage = $complted .'%';
$warning = "";
	if ($complted == 100) {
		$warning = "progress-bar-danger";
	} 
	


/*(echo'<pre>';
print_r($taskName);
echo'</pre>';



echo'<pre>';
print_r($sourceTaskName);
echo'</pre>';
*/

?>

	
	<link rel = "stylesheet" href = "./CSS/main.css"/>
	<link rel = "stylesheet" type = "./CSS/bootstrap.min.css"/>
	<link rel = "stylesheet" href = "./CSS/jquery.simple-dtpicker.css" />
	<link rel = "stylesheet" href = "/lib/css/bootstrap-table.css"/>

	
	
	
	<div class="row">
	<div class="col-md-9">
		<div class="panel panel-back-9">
			<div class="panel-heading">
				<div class="alert alert-success">
  					<h2 class= "panel-title"><strong>Hi <?=$loginStaffName?>!</strong> Infromation in the next section is editable in line.</h2> 
				</div>
			</div>
			
			<div class="panel-body">
				<br><br>
				<table id = "taskInfo" class="table table-bordered table-striped" style="clear:both">

						<tr style="display : none" id="progressBar">
							<td width="20%"><b>Completed Level::</b></td>
							<td width="80%"> 
								<div class="progress">
									<div class="progress-bar progress-bar-striped <?=$warning?> active" role="progressbar" style="width:<?php print($compltedPercentage); ?>">
		   							 <?php print( $compltedPercentage); ?>
		 							 </div>
								</div>
							</td>
						</tr>

						<tr>
							<td width="20%"><b>Task Name::</b></td>
							<td width="80%"><b class="taskName"> </b></td>
						</tr>

						<tr>
							<td width="20%"><b>Task Decription ::</b></td>
							<td width="80%"><b class="taskDescription"> </b></td>
						</tr>

						<tr>
							<td width="20%"><b>Task Duration(Hrs) ::</b></td>
							<td width="80%"><b class="duration"></b></td>
						</tr>

						<tr>
							<td width="20%"><b>Task ETA ::</b></td>
							<td width="80%"><b class="ETA" data-template="D MMM YYYY" data-formate = "DD-MM-YYYY " data-viewformat="DD/MM/YYYY"></b></td>
						</tr>

						<tr>
							<td width="20%"><b>Task Status ::</b></td>
							<td width="80%"><b class="status"></b></td>
						</tr>

						<tr>
							<td width="20%"><b>Task Priority ::</b></td>
							<td width="80%"><b class="priority"></b></td>
						</tr>

						<tr>
							<td width="20%"><b>Parent Task::</b></td>
							<td width="80%"><b class="parentTask"></b></td>
						</tr>
				</table>

					<ul class="nav nav-tabs">
				    <li class="active"><a class="alert alert-info" data-toggle="tab" href="#Hierarchy"><strong <i class="fa fa-lg fa-sitemap">&nbsp</i>Task Hierarchy</strong></a></li>
				    <li><a class="alert alert-info" data-toggle="tab" href="#Designation"><strong <i class="fa fa-lg fa-users">&nbsp</i>Staff Designation</strong></a></li>
				    </ul>

				<div class="tab-content">

					<div id="Hierarchy" class="tab-pane fade in active">
						<div style="display : none" id="parentTaskTable" >
							<div class="alert alert-info">
			  					<h5><strong>Parent Task of :: <span class="TheTaskName" style="color:black"></span></strong></h5> 
							</div>
							<table class="table table-condensed" id="parentTask" data-toggle="table" data-url="./api/task/get_parent_task?taskID=<?php echo $taskID; ?>" >
								<thead>
									<tr>
										<th data-field="" data-formatter ="infromationFormater" data-halign="center" data-align="center">Info</th>
										<!--   <th data-field="TaskID" data-sortable="true" data-halign="center" data-align="center">Task ID</th>  -->
										<th data-field="TaskName">Task Name</th>
										<th data-field="CreateBy">Create By</th>
										<th data-field="ETA" data-formatter="eatFormater">ETA</th>
										<th data-field="duration" data-formatter="durationFormater">Duration</th>
										<th data-field="Status" data-formatter="statusFormater"  >Status</th>
										<th data-field="Priority" data-formatter="priorityFomater" >Priority</th>
									</tr>
								</thead>
							</table><br><br>
						</div>

						<div class="alert alert-info">
		  					<h5><strong>Sub Task(s) of :: <span class="TheTaskName" style="color:black"> </span></strong></h5> 
						</div>
						<table class="table table-condensed" id="subTask" data-toggle="table" data-url="./api/task/get_sub_task?taskID=<?php echo $taskID; ?>" >
							<thead>
								<tr>
									 <th data-field="" data-formatter ="infromationFormater" data-halign="center" data-align="center">Info</th> 
								<!-- 	<th data-field="TaskID" data-sortable="true" data-halign="center" data-align="center">Task ID</th> -->
									<th data-field="TaskName" data-sortable="true">Task Name</th>
									<th data-field="CreateBy" data-sortable="true">Create By</th>
									<th data-field="ETA" data-formatter="eatFormater" data-sortable="true">ETA</th>
									<th data-field="duration" data-formatter="durationFormater" data-sortable="true">Duration</th>
									<th data-field="Status" data-formatter="statusFormater" data-sortable="true" >Status</th>
									<th data-field="Priority" data-formatter="priorityFomater" data-sortable="true">Priority</th>
								</tr>
							</thead>
						</table><br><br>
					</div>
					
					<div id="Designation" class="tab-pane fade">
						<div class="alert alert-info">
		  					<h5><strong>Staff Delegated to :: <span class="TheTaskName" style="color:black"> </span></strong></h5> 
						</div>
						<table class="table table-condensed" id="delegate" data-toggle="table" data-url="./api/task/get_delegate_Staff?taskID=<?php echo $taskID; ?>">
							<thead>
								<tr>
									<!--  <th data-field="staffID" ata-halign="center" data-align="center">Staff ID</th>  -->
									<th data-field="staffName" data-halign="center" data-align="center">Staff Name</th>
									<th data-field="staffContact" data-halign="center" data-align="center">Staff Contact</th>
									<th data-field="staffEmail" data-halign="center" data-align="center">Staff Email</th>
									<th data-field="staffID" data-formatter="formatterRemoveDelegated" ata-halign="center" data-align="center" data-events="removeDelegatedTask">Remove Staff</th>
								</tr>
							</thead>
						</table>
						<a class="btn btn-primary" id ="showDelegateTask" ><i class="fa fa-cog fa-spin fa-lg"></i> Add New Staff</a>
						<lable style="display : none"  id="DelegatedSuccess"><b style="color:Green"> Staff Successfully Added</b></lable>
						<lable style="display : none"  id="DelegatedDelete"><b style="color:Red">Staff Successfully Deleted</b></lable>
							<div class="form-control" id="addTaskDelegate" style="display : none">
								<label class="delegateTask" for="StaffID"> Select staff you wish to delegate</label> 
								<!--<input type="number" class="delegateTask" id="staffIDDelegate">  -->
								<select  class="delegateTask" id="staffIDDelegate"> 
									<option value="0" disabled>Select the staff</option>
										<?php 
											foreach ($staffname as $key => $value) {
												echo '<option value="'.$value['staffID'] .'">' .$value['staffName'] .'</option>';
											}
										?>
								</select>
							
								<a class="btn btn-success" id ="delegateSubmit" ><i class="fa fa-plus-circle fa-lg"></i> Submit</a>
								<br>
							</div><br><br>

						<div class="alert alert-info">
		  					<h5><strong> Staff Share to :: <span class="TheTaskName" style="color:black"></span></strong></h5> 
						</div>
						<table class="table table-condensed" id="share" data-toggle="table" data-url="./api/task/get_share_Staff?taskID=<?php echo $taskID; ?>" >
							<thead>
								<tr>
									<!--  <th data-field="staffID" ata-halign="center" data-align="center">Staff ID</th>  -->
									<th data-field="staffName" data-halign="center" data-align="center">Staff Name</th>
									<th data-field="staffContact" data-halign="center" data-align="center">Staff Contact</th>
									<th data-field="staffEmail" data-halign="center" data-align="center">Staff Email</th>
									<th data-field="staffID" data-formatter="formatterRemoveShared" ata-halign="center" data-align="center" data-events="removeSharedTask">Remove Staff</th>
								</tr>
							</thead>
						</table>
						<a class="btn btn-primary" id ="showShareTask" ><i class="fa fa-cog fa-spin fa-lg"></i> Add New Staff</a>
						<lable style="display : none"  id="SharedSuccess"><b style="color:Green" > Staff Successfully Added</b></lable>
						<lable style="display : none"  id="SharedDelete"><b style="color:Red">Staff Successfully Deleted</b></lable>
							<div class="form-control" id="addTaskShare" style="display : none">
								<label class="shareTask" for="StaffID"> Select staff you wish to share</label> 
								<!-- <input type="number" class="shareTask" id="staffIDShare"> -->
								<select  class="shareTask" id="staffIDShare">
									<option value="0" disabled>Select the staff</option>
									<?php 
											foreach ($staffname as $key => $value) {
												echo '<option value="'.$value['staffID'] .'">' .$value['staffName'] .'</option>';
											}
										?>
								</select>
								<a class="btn btn-success" id ="shareSubmit" ><i class="fa fa-plus-circle fa-lg"></i> Submit</a>
						</div>	
					</div>
				</div>
    		</div>
    	</div>
    </div>
	<div class="col-sm-3">
		<div class="panel panel-back-3">
			<div class="panel-heading-3">
				<div class="alert alert-success">
  					<h3 class= "panel-title">New sub Tasks for &nbsp <strong style="color:Green" class="TheTaskName"> </strong></h3> 
				</div>
			</div>
			
			<form action="./api/task/add_sub_task?type=own" method="post" class="ajax" name="taskform">
				
					<div class="form-group">
							<label for ="name">Task Name:</label>
							<input type ="text" class="form-control" name="taskName" id="taskName" placeholder ="Must fill field, max 100 characters" required><br>
						</div>
						
						<div class="form-group">
							<label for="decription">Task Description: </label>
							<textarea rows="6" cols="50" class="form-control" name="taskDis" id="taskDis" placeholder ="Optional field max 300 characters"></textarea><br>
						</div>

						<div class="form-group" style="display: none;">
							<input type="number"  class="form-control" value="<?php echo $loginStaffID;?>" name='staffID'>
						</div>

						<div class="form-group">
							<label for="ETA">ETA: (Click to set a specific date and time)</label>
							<input type="text" class="form-control" name='eta' id='eta' placeholder ="Auto select tomorrow same time if leave blank "><br>
		                </div>
						
						<div class="form-group">
							<label for="Duration">Duration (hours): </label>
							<input type="number" class="form-control" name='Duration' id="Duration" placeholder = "optional if leave blank as not setted"><br>
						</div>
						
						<div class="form-group">
							<label for="status">Status: </label>
							<select class = "form-control" name='status' id="status">
								<option value="1" selected="selected">Not yet started</option>
								<option value="2" >Underway</option>
								<option value="3">Nearly Completed</option>
								<option value="4">Completed</option>
							</select><br>
						</div>
						
						<div class="form-group">
							<label for="priority">Priority: </label>
							<select class = "form-control" name='priority' id='priority'>
								<option value="1">Urgent</option>
								<option value="2">High</option>
								<option value="3" selected = "selected" >Medium</option>
								<option value="4">Low</option>
							</select><br>
						</div>


					<div class = "form-group" id ="hide" style="display:none;" >
						<label for = "ParentTaskID" > Parent ID: </label>
						<input type="number"  class="form-control" name='parentID' value="<?php echo $taskID; ?>" ><br>
					</div>

					<input type="submit" class="btn btn-primary btn-block" value = "Submit New Task"><br>	

					<div style="display:none" id="taskNotification">
						<input type="Button" class="btn btn-danger btn-block" value = "Task Created">
					</div>

			</form>		

		</div>
	</div>
</div>	

	
	
	
<?php
	include($_SERVER['DOCUMENT_ROOT']."/lib/template/footer.php");
?>


	<script type="text/javascript" src="./Javascript/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="/lib/template/js/bootstrap-table.js"></script>
	<script type="text/javascript" src="./Javascript/jquery.simple-dtpicker.js"></script>
	<script type="text/javascript" src="./Javascript/sugar.js"></script>
	<script type="text/javascript" src="./Javascript/moment.js"></script>
	<script type="text/javascript" src="./Javascript/task_managment.js"></script>
	<script type="text/javascript" src="/lib/template/js/bootstrap.js"></script>
	<script type="text/javascript" src="/lib/template/plugins/x-editable/js/bootstrap-editable.js"></script> 
	<script type="text/javascript" src="/lib/template/js/jquery.number.min.js"></script>


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
		var parent_task_name = value[0].parentName;

		if (parent_task_name == null){
			parent_task_name = "N/A";
		}
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
		$('.createDate'). html(task_CD);
		$('.duration'). html(task_Duration);
		$('.ETA'). html(task_ETA);
		$('.status'). html(task_status);
		$('.priority').html(task_priority);
		$('.parentTask'). html(parent_task_name );
		//alert(parent_task_name);
	});

/*********vlaue = ture(parent task) show progress bar else hide ********/

	$.post("./api/task/is_parent_task?taskID=<?php echo $taskID; ?>").then(function (value) {
		var returnValue = value;
		switch(value) {
			case 11:
				$('#progressBar').show();
				$('#parentTaskTable').show();
				break;
			case 01:
			 	$('#progressBar').hide();
				$('#parentTaskTable').show();
				break;
			case 10:
				$('#progressBar').show();
				$('#parentTaskTable').hide();
				break;
			case 00:
				$('#progressBar').hide();
				$('#parentTaskTable').hide();
				break;
			}
	});
		
	
	


/***************X-Editable*********************/
	var parentTaskName =<?php echo json_encode($sourceTaskName) ?>;
	//console.log(parentTaskName);

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

		//var prioritySource = [{'value': 1, text: 'Urgent'}, {'value': 2, text: 'High'},{'value': 3, text: 'Medium'}, {'value': 4, text: 'Low'}];
		//var statusSource = [{value: 1, text: 'Not yet started'}, {value: 2, text: 'Underway'}, {value: 3, text: 'Nearly Completed'}, {value: 4, text: 'Completed'}];

		$('.taskName').editable({
			type: 'text',
			title: 'Edit Task Name',
			pk: 0,
			params: function (params) {
				var data = {};
				data.id = <?=$taskID?>;
				data.action = "update_taskName";
				data.value =  params.value;
				console.log(data);
				return data;
			},	
			url: './api/task_edit_table.json.php',
			success: function(response){
			location.reload();
			}
		});


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


		$('.parentTask').editable ({
			type: 'select',
			title: 'Select a parent task',
			pk: 0,
			source:parentTaskName,
			params: function (params) {
				var data = {};
				data.id = <?=$taskID?>;
				data.staffID = <?=$loginStaffID?>;
				data.value = params.value;
				data.action = "update_parent_task";
				console.log(data);
				if (data.id == data.value){
					alert("Can set Parent task to task itself.")
				} else {
					return data;
				}
			},
			url: './api/task_edit_table.json.php',
			success: function(response){
			location.reload();
			//$("#parentTask").bootstrapTable('refresh');
			//$("#share").bootstrapTable('refresh');
			}

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


	 function formatterRemoveDelegated(value, row) {
	 	return[
	 		'<a class="removeDelegated" herf="javascript:void(0)" title="Remove staff from This Task">',
	 			'<i class="fa fa-trash-o fa-lg"></i>',
	 		'</a>'
	 	].join('');
	 }

	 function formatterRemoveShared(value, row) {
	 	return[
	 		'<a class="removeShared" herf="javascript:void(0)" title="Remove staff from This Task">',
	 			'<i class="fa fa-trash-o fa-lg"></i>',
	 		'</a>'
	 	].join('');

	 }	

	 window.removeDelegatedTask = {'click .removeDelegated' : function(e, value, row, index) {
	 		$.post("./api/task/remove_delegate_task", {staffID : value, taskID : <?=$taskID?>})
	 			.done (function (data) {
	 				console.log(data);
	 				$('#DelegatedDelete').show().delay(1000).fadeOut('Slow');
	 				$("#delegate").bootstrapTable('refresh');

	 			});
	 }}; 

 	window.removeSharedTask = {'click .removeShared' : function(e, value, row, index) {

	 		$.post("./api/task/remove_share_task", {staffID : value, taskID : <?=$taskID?>})
	 			.done (function (data) {
	 				console.log(data);
	 				$('#SharedDelete').show().delay(1000).fadeOut('Slow');
	 				$("#share").bootstrapTable('refresh');

	 			});
	 }}; 


	$('#delegateSubmit').on('click', function(){
		var theStaffID = $('#staffIDDelegate').val();
		var theTaskID = <?=$taskID?>;
		var onwerID = <?=$loginStaffID?>;
		var url = "./api/task/add_delegate_task";
		 if (theStaffID == onwerID) {
			alert("Can not delagate task to yourself");
			document.getElementById("staffIDDelegate").value = null;
			$('#addTaskDelegate').hide();
		} else {
    $.ajax({
           type: "POST",
           url: url,
           data: {taskID : theTaskID , staffID : theStaffID},
           success: function(response)
           {
            console.log(response);
			$('#addTaskDelegate').hide();
			$("#delegate").bootstrapTable('refresh');
			$('#DelegatedSuccess').show().delay(1000).fadeOut('Slow');
			document.getElementById("staffIDDelegate").value = null; 
           }
         });
		}

    return false;
	})
	


	$('#shareSubmit').on('click', function(){
		var theStaffID = $('#staffIDShare').val();
		var theTaskID = <?=$taskID?>;
		var onwerID = <?=$loginStaffID?>;
		var url = "./api/task/add_shared_task";
		if (theStaffID == onwerID) {
			alert("Can not delagate task to yourself");
			document.getElementById("staffIDShare").value = null;
			$('#addTaskShare').hide();
		} else {
    $.ajax({
           type: "POST",
           url: url,
           data: {taskID : theTaskID , staffID : theStaffID},
           success: function(response)
           {
            console.log(response);
			$('#addTaskShare').hide();
			$("#share").bootstrapTable('refresh');
			$('#SharedSuccess').show().delay(1000).fadeOut('Slow');
			document.getElementById("staffIDShare").value = null; 
           }
         });
		}

    return false;
	})
	


	

	</script> 

	
	
