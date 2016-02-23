
<?php
define('NAV', 'reports');
include($_SERVER['DOCUMENT_ROOT']."/lib/template/header.php");
?>			
		
	
	<link rel = "stylesheet" href = "/lib/css/bootstrap-table.css"/>
	<link rel = "stylesheet" href = "./CSS/main.css"/>
	<link rel = "stylesheet" type = "./CSS/bootstrap.min.css"/>
	<link rel = "stylesheet" href = "./CSS/jquery.simple-dtpicker.css" />

	
	


<div class="row">
	<div class="col-md-9">
		<div class="panel panel-back-9">
			<div class="panel-heading-9">
				<div class="alert alert-success">
  					<h3 class= "panel-title"> Task List for <strong><?=$loginStaffName?>!</strong></h3> 
				</div>
			</div>
			<div class="panel-body">		
			<div>	
				<ul class="nav nav-tabs">
			    <li class="active"><a class="alert alert-info" data-toggle="tab" href="#CreateBy" ><strong <i class="fa fa-lg fa-user-plus">&nbsp</i>Owned</strong></a></li>
			    <li><a class="alert alert-info" data-toggle="tab" href="#DelegateTo"><strong <i class="fa fa-lg fa-crosshairs">&nbsp</i>Delegated </strong></a></li>
			    <li><a class="alert alert-info" data-toggle="tab" href="#SharedTo" ><strong <i class="fa fa-lg fa-share-alt-square">&nbsp</i>Shared</strong></a></li>
			    </ul>
			</div>
			<div class="tab-content">
				<div id="CreateBy" class="tab-pane fade in active">
			  		<div class="alert alert-info">
	  					<h5><strong>Created By <?php echo $loginStaffName?> :: TOP LEVEL TASK</strong></h5> 
					</div>
					<div>
						<select name="show" onchange="showTopLevelTable(this.value)"  class ="form-control" >
							<option value="unCompleted&staffID=<?php echo $loginStaffID; ?>">Top Level Task (current)</option> 
							<option value="completed&staffID=<?php echo $loginStaffID; ?>">Top Level Task (Completed)</option>
						</select>
					</div> <br>
					<table  class="table" id="parentList" data-toggle="table" data-url="./api/task/get_top_level_task?sort=unCompleted&staffID=<?php echo $loginStaffID; ?>">																 
						<thead>
							<tr>
								<th data-field="" data-formatter ="infromationFormater_owner" data-halign="center" data-align="center">Info</th>
								<th data-field="TaskName" data-sortable="true">Task Name</th>
								<th data-field="ETA" data-formatter ="eatFormater" data-sortable="true">ETA</th>
								<th data-field="duration" data-formatter = "durationFormater" data-sortable="true">Duration</th>
								<th data-field="Status" data-formatter = "statusFormater" data-sortable="true" >Status</th>
								<th data-field="Priority"  data-formatter = "priorityFomater" data-sortable="true" >Priority</th>	
								<th data-field="numberOfSub" data-sortable="true">Subs</th> 
								<th data-field="completedLevel"  data-formatter = "completedLevelFomater">Completed Level</th>
								<th data-field="TaskID" data-formatter ="hirachyDisplayFormater" data-halign="center" data-align="center" data-events="expandTopLevelTask">Expand</th>
							</tr>
						</thead>
					</table> <br>

					
					<div id="TaskHierarchy" style="display:none">
						<div class="alert alert-info" >
							<!-----------=-->
		  					<h5><strong><i class="taskLevel" id='taskLevel'></i></strong></h5> 
						</div>
				 		<table  class="table" id="HierarchyTable" data-toggle="table" data-url="./api/task/get_expand_task?taskID=-1&staffID=<?php echo $loginStaffID; ?>">																 
							<thead>
								<tr>
									<th data-field="" data-formatter ="infromationFormater_owner" data-halign="center" data-align="center">Info</th>
									<th data-field="TaskName" data-sortable="true">Task Name</th>
									<th data-field="ETA" data-formatter ="eatFormater" data-sortable="true">ETA</th>
									<th data-field="duration" data-formatter = "durationFormater" data-sortable="true">Duration</th>
									<th data-field="Status" data-formatter = "statusFormater" data-sortable="true" >Status</th>
									<th data-field="Priority"  data-formatter = "priorityFomater" data-sortable="true" >Priority</th>
									<th data-field="numberOfSub" data-sortable="true">Subs</th>
									<th data-field="TaskID" data-formatter ="hirachyDisplayFormater" data-halign="center" data-align="center" data-events="expandSubTask">Expand</th>
								</tr>
							</thead>
						</table> <br>
						 <a class="btn btn-success" id="HideTaskHirearchy"><i class="fa fa-lg fa-times-circle"></i> Hide Task Hirerarchy Table</a>
					</div>

			</div>

			
			<div id="DelegateTo" class="tab-pane fade">
				<div class="alert alert-info">
  					<h5><strong>DELEGATED TO <?php echo $loginStaffName?></strong></h5> 
				</div>
				<div class= "form-group" >
					<select name="show" onchange="showDelegatedTable(this.value)"  class ="form-control" >
						<option value="unCompleted&staffID=<?php echo $loginStaffID; ?>">Current Task</option>
						<option value="completed&staffID=<?php echo $loginStaffID; ?>">Completed Task</option>
					</select>
				</div>

				<table class="table" id="delegateTaskList" data-toggle="table" data-url="./api/task/get_delegate_task?sort=&staffID=<?php echo $loginStaffID; ?>">															 
					<thead>
						<tr>
							<th data-field="" data-formatter="infromationFormater_delegate" data-halign="center" data-align="center">Info</th>
							<th data-field="CreateBy" data-sortable="true">Create By</th>
							<th data-field="TaskName" data-sortable="true">Task Name</th>
							<th data-field="ETA" data-formatter ="eatFormater" data-sortable="true">ETA</th>
							<th data-field="duration" data-formatter = "durationFormater" data-sortable="true">Duration</th>
							<th data-field="Status" data-formatter = "statusFormater" data-sortable="true" >Status</th>
							<th data-field="Priority" data-formatter = "priorityFomater" data-sortable="true">Priority</th>	
							<th data-field="ParentTask" data-sortable="true">Parent Task</th>					
						</tr>
					</thead>
				</table><br>
			</div>


			<div id="SharedTo" class="tab-pane fade">
				<div class="alert alert-info">
  					<h5><strong>SHARED TO <?php echo $loginStaffName?></strong></h5> 
				</div>
						<div class= "form-group" id = "sorting">
					<select name="show" onchange="showSharedTable(this.value)" class ="form-control" >
						<option value="unCompleted&staffID=<?php echo $loginStaffID; ?>">Current Task</option>
						<option value="completed&staffID=<?php echo $loginStaffID; ?>">Completed Task</option>
					</select>
				</div>
				<table class="table" id="sharedTaskList" data-toggle="table" data-url="./api/task/get_share_task?sort=&staffID=<?php echo $loginStaffID; ?>">														 
					<thead>
						<tr> 
							<th data-field="CreateBy" data-sortable="true">Create By</th>
							<th data-field="TaskName" data-sortable="true">Task Name</th>
							<th data-field="ETA" data-formatter ="eatFormater" data-sortable="true">ETA</th>
							<th data-field="duration" data-formatter = "durationFormater" data-sortable="true">Duration</th>
							<th data-field="Status" data-formatter = "statusFormater" data-sortable="true" >Status</th>
							<th data-field="Priority" data-formatter = "priorityFomater" data-sortable="true" >Priority</th>
							<th data-field="ParentTask" data-sortable="true">Parent Task</th>
							<th data-field="TaskID" data-formatter="fomatterArchive" data-halign="center" data-align="center" data-events="addToArchive">Archive</th>
						</tr>
					</thead>
				</table><br>
				<a class="btn btn-primary" id ="showArchiveTB" ><i class="fa fa-eye fa-lg"></i> Show Archive Table</a>


				<div id ="archiveTable" style="display: none">
					<div class="alert alert-info">
	  					<h5><strong>ARCHIVED TO<?php echo $loginStaffName?></strong></h5> 
					</div>
					<table class="table" id="ArchivedTaskList" data-toggle="table" data-url="./api/task/get_archive_task?staffID=<?php echo $loginStaffID; ?>">																 
						<thead>
							<tr> 
								<th data-field="CreateBy" data-sortable="true">Create By</th>
								<th data-field="TaskName" data-sortable="true">Task Name</th>
								<th data-field="ETA" data-formatter ="eatFormater" data-sortable="true">ETA</th>
								<th data-field="duration" data-formatter = "durationFormater" data-sortable="true">Duration</th>
								<th data-field="Status" data-formatter = "statusFormater" data-sortable="true" >Status</th>
								<th data-field="Priority" data-formatter = "priorityFomater" data-sortable="true">Priority</th>
								<th data-field="TaskID" data-formatter="fomatterUnarchive" data-halign="center" data-align="center" data-events="removeFromArchive">unArchive</th>
							</tr>
						</thead>
					</table><br>
					<a class="btn btn-success" id ="hideArchiveTB" ><i class="fa fa-eye-slash fa-lg"></i> Hide Archive Table</a>
				</div>
			</div>

				</div>
    		</div>
    	</div>
    </div>


	<div>
		<div class="col-sm-3">
			<div class="panel panel-back-3">
				<div class="panel-heading-3">
					<div class="alert alert-success">
  						<h3 class= "panel-title">Create New Tasks!</strong></h3> 
					</div>	
				</div>
				<form action="./api/task/add_task" method="post" class="ajax" name="taskform" data-toggle="validator" role="form"> 
					
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

						<div class= "form-group" style="display:none;">
							<label for = "parentTask"> Parent Task: </label>
							<select  class = "form-control" name='parent' id='parent'>
								<option value = "0"> Not a sub task </option>
								<option value = "1" selected = "selected" > Has parent task </option>
							</select>
	 					</div><br>

						<div class="form-group" id ="hide" style="display:none;" >
							<label for="ParentTaskID" > Parent Task name: </label>
							<select class="form-control" name='parentID' id='parentTaskID'>
							<option  value="0" disabled>Please select parent task name</option>
							<?php 
								foreach ($taskName as $key => $value) {
								echo '<option value="'.$value['ID'].'">'.$value['Name'].'</option>';
								}
							?>
							</select>
						</div>	
				
						<div class="form-group">
							<input type="submit" class="btn btn-primary btn-block" value = "Submit New Task">	
						</div>
						
						<div style="display:none" id="taskNotificationMain">
							<input type="Button" class="btn btn-danger btn-block" value = "Task Created">
						</div>

							
						
						
				</form>	

			</div>
		</div>	
	</div>
</div>




<?php
	include($_SERVER['DOCUMENT_ROOT']."/lib/template/footer.php");
?>


	<script type="text/javascript" src="./Javascript/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="/lib/template/js/bootstrap-table.js"></script>
	<script type="text/javascript" src="./Javascript/sugar.js"></script>
	<script type="text/javascript" src="./Javascript/moment.js"></script>
	<script type="text/javascript" src="./Javascript/jquery.simple-dtpicker.js"></script>
	<script type="text/javascript" src="./Javascript/task_managment.js"></script>


	<script type="text/javascript">

	function fomatterArchive(value, row){
		return[
			'<a class="addArchive" herf="javascript:void(0)" title="Archive ">',
				'<i class="fa fa-archive fa-lg"></i>',
			'</a>'].join('');
		}

	window.addToArchive = {'click .addArchive' : function (e, value, row, index) {

		$.post("./api/task/task_archive", {taskID : value, staffID : <?=$loginStaffID?> })
			.done(function (data) {
				$("#sharedTaskList").bootstrapTable('refresh');
				$("#ArchivedTaskList").bootstrapTable('refresh');
			});
		}};




	function fomatterUnarchive(value, row) {
		return[
			'<a class="removeArchive" herf="javascript:void(0)" title="UnArchive">',
				'<i class="fa fa-retweet fa-lg fa-spin"></i>',
			'</a>'].join('');
		}

	window.removeFromArchive = {'click .removeArchive' : function (e, value, row, index) {

		$.post("./api/task/task_unarchive", {taskID : value, staffID : <?=$loginStaffID?> })
			.done(function (data) {
				$("#sharedTaskList").bootstrapTable('refresh');
				$("#ArchivedTaskList").bootstrapTable('refresh');
			});
		}};



	function hirachyDisplayFormater(value, row) {
		var countSub = row['numberOfSub'];
		if (countSub == 0) {
			return [
			'<a class="btn btn-link btn-block  btn-sm disabled" id="hirachyDis" herf="javascript:void(0)">',
			'<i class="fa fa-lg fa-ban"></i>',		
			'</a>'].join('');
		} else {
			return [
			'<a class="btn btn-info btn-block  btn-sm active" id="hirachyDis" herf="javascript:void(0)">',
			'<i class="fa fa-lg fa-arrows-alt"></i>',		
			'</a>'].join('');
		}
	}

	window.expandTopLevelTask = {'click #hirachyDis' : function (e, value, row, index) {
		var taskName = row['TaskName'];
		console.log(value);

		$("#TaskHierarchy").show();
		$("#HierarchyTable").bootstrapTable('refresh', {
				   url: './api/task/get_expand_task?taskID='+ value+'& staffID=' + "<?=$loginStaffID?>"
				});
		//$("#taskLevel").html( taskName );
		$("#taskLevel").html( "<a href='#' style='color:blue' onclick='taskRedirect(" +value+ ");'>" + taskName + "</a>");

	}};


	window.expandSubTask = {'click #hirachyDis' : function (e, value, row, index) {
		var taskName = row['TaskName'];
		console.log(value);

		$("#TaskHierarchy").show();
		$("#HierarchyTable").bootstrapTable('refresh', {
				   url: './api/task/get_expand_task?taskID='+ value+'& staffID=' + "<?=$loginStaffID?>"
				});

		//$("#taskLevel").append( " >>> " + "<span class='taskLabel' data-events='taskRedirect' data-id="+value+">" + taskName + "</span>");
		
		$("#taskLevel").append( " >>> " + "<a href='#' onclick='taskRedirect(" +value+ ");'id='taskLabel' data-id=" +value+ ">" + taskName + "</a>");
	}};


	function taskRedirect(value) {

		console.log(value);
		console.log(taskName);
		$("#TaskHierarchy").show();
		$("#HierarchyTable").bootstrapTable('refresh', {
				   url: './api/task/get_expand_task?taskID='+ value +'& staffID=' + "<?=$loginStaffID?>"
				});
		//$("#taskLevel").append( " >>> " + "<a href='#' onclick='taskRedirect("+ value +");'class='taskLabel' data-id="+value+">" + task_name + "</a>");
	}
	
	</script>
