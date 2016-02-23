
	
/************Date Time picker for EAT only can select future time and interval set to 15 mins************/
	
		$(document).ready(function(){
			$('#eta').appendDtpicker({
				"futureOnly": true,
				"dateOnly": true,
				"closeOnSelected": true,
				//"minuteInterval": 15,
				//"minTime":"08:30",
				//"maxTime":"20:09",
				"dateFormat": "DD-MM-YYYY hh:mm",
				//"dateFormat": "DD-MM-YYYY",
				"autodateOnStart": "false"
			});
		});

/******************allow to hide parent ID column unless select option"Has parent task"********************/		
		
		$('#parent').on('change', function() {
			var selection = $(this).val();
			switch(selection) {
				case "0":
				$("#hide").hide()
				break;
				case "1":
				$("#hide").show()

			}
		});


/******************Show & hide Archive Table*********************/

	$('#showArchiveTB').on('click',function() {
		$('#archiveTable').show();
		$('#showArchiveTB').hide();

	})

	$('#hideArchiveTB').on('click',function() {
		$('#archiveTable').hide();
		$('#showArchiveTB').show();

	})

/************show the share and delegate staff*************/
	$('#showDelegateTask').on('click', function(){
		$('#addTaskDelegate').toggle();
	})

	$('#showShareTask').on('click', function() {
		$('#addTaskShare').toggle();
	})



/************twist data for display in the field of duration***************/
    
		function durationFormater(value) {
			if (value == 0) {

				value = "--";
			} else {
				value = value + "hrs";
			}
			return value;
		}
		
		
/*************twist data for display in the field of status***************/

		function starusFormater(value){
		var status = "";
		switch(value){
				case 1:  
				status = 'Not yet started';
				break;
				case 2:
				status = 'Underway';
				break;
				case 3:
				status = 'Nearly Completed';
				break;
				case 4:
				status = 'Completed';
				break;	
			}
		return status;
			
		}

/*************twist data for display in the field of status***************/

	function priorityFomater(value){
		var priority = "";
			switch(value){
				case 1:  
				priority = 'Urgent';
				break;
				case 2:
				priority = 'High';
				break;
				case 3:
				priority = 'Medium';
				break;
				case 4:
				priority = 'Low';
				break;	
			}
		return priority;	
		}
		

/**********open new page for details of a single task(owned)*********/

	function infromationFormater_owner(value, row ) {
			var theID = row.TaskID;
			//var theName = row.TaskName;
			var newString = theID; //+'&TaskName='+theName;
			return [
            '<a href="task_display_owner.php?id='+newString+'" title="Task Details" target="_blank">',
                '<i class="fa fa-info-circle fa-lg"></i>',
            '</a>'
        ].join('');
		}

/*********open new page for details of a single task(delegated)***********/

	
	function infromationFormater_delegate(value, row ) {
			var theID = row.TaskID;
			//var theName = row.TaskName;
			var newString = theID;//+'&TaskName='+theName;
			return [
            '<a href="task_display_delegated.php?id='+newString+'" title="Task Details" target="_blank">',
                '<i class="fa fa-info-circle fa-lg"></i>',
            '</a>'
        ].join('');
	}



				
/******************* toggle between different sorting method for create by given user*****************/

		function showCreateByTable(val) {
			$("#taskList").bootstrapTable('refresh', {
				   url: './api/task/get_created_tasks?sort='+ val
				});
		}
	

/******************* toggle between different sorting method for shared task*****************/

		function showSharedTable(val) {
			$("#sharedTaskList").bootstrapTable('refresh', {
				   url: './api/task/get_share_task?sort='+ val
				});
		}
	

/******************* toggle between different sorting method for delegate tasks*****************/

		function showDelegatedTable(val) {
			$("#delegateTaskList").bootstrapTable('refresh', {
				   url: './api/task/get_delegate_task?sort='+ val
				});
		}
	
	
/***********change the date foramted to be readeble formate************/

		function eatFormater (value, row) {
			var newTime = moment(value).format('DD/MM/YYYY');
			return newTime;
		}


/*****************Form Validation for taskform -- task name must be filled**********************/

	$('form.ajax').submit(function(e){
		e.preventDefault();

		var x = document.forms["taskform"]["taskName"].value;
		if (x == null || x == "") {
			return false;
		} else {
			var task = $(this),
			url = task.attr('action'),
			type = task.attr('method'),
			data = {};
			
			task.find('[name]').each(function (index,value) {
				var element = $(this),
				name = element.attr('name'),
				value = element.val();
				data[name] = value;
			});


			$.ajax({
				url: url,
				type: type,
				data: data,
				success: function(response) {
					console.log(response);	
					$("#taskNotificationMain").show().delay(1000).fadeOut('Slow');
					$("#taskList").bootstrapTable('refresh');
					$("#parentList").bootstrapTable('refresh');
					$("#subTask").bootstrapTable('refresh');
					$("#taskNotification").show().delay(1000).fadeOut('Slow');
					document.getElementById("taskName").value = null;	
					document.getElementById("taskDis").value = null;
					document.getElementById("eta").value = null;
					document.getElementById("Duration").value = null;
					document.getElementById("status").value = 1;	
					document.getElementById("priority").value = 3;
					document.getElementById("parent").value = 0;
					document.getElementById("parentTaskID").value = 0;
					$("#hide").hide();
				}	
			
			});		
			return false;
		}
	});

	
	
		
		
		
		
		

