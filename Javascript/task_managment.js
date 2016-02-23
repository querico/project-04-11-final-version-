
	
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

/*********hide the task expand table********/


	$('#HideTaskHirearchy').on('click',function() {
		$('#TaskHierarchy').hide();
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

		function statusFormater(value){
			var status = "";
		switch(value){
				case 1:  
				status = 'Not Yet Start';
				return[
					'<a style="color:LightSlateGray " herf="javascript:void(0)" title="status ">',
						'<i class="fa fa-lg fa-hourglass-o">&nbsp</i>' +status+  '</a>'].join('');		
				break;
				case 2:
				status = 'Underway';
				return[
					'<a style="color:SeaGreen " herf="javascript:void(0)" title="status ">',
						'<i class="fa fa-lg fa-hourglass-start">&nbsp</i>' +status+  '</a>'].join('');	
				break;
				case 3:
				status = 'Nearly Completed';
				return[
					'<a style="color:Tomato " herf="javascript:void(0)" title="status ">',
						'<i class="fa fa-lg fa-hourglass-half">&nbsp</i>' +status+  '</a>'].join('');	
				break;
				case 4:
				status = 'Completed';
				return[
					'<a style="color:red" herf="javascript:void(0)" title="status ">',
						'<i class="fa fa-lg fa-hourglass-end">&nbsp</i>' +status+  '</a>'].join('');	
				break;	
			}
		
		}

/*************twist data for display in the field of Priority***************/

	function priorityFomater(value){
		var priority = "";
			switch(value){
				case 1:  
				priority = 'Urgent';
				return[
					'<a style="color:Crimson" herf="javascript:void(0)" title="priority ">',
						'<i class="fa fa-lg fa-battery-full">&nbsp</i>' +priority+  '</a>'].join('');	
				break;
				case 2:
				priority = 'High';
				return[
					'<a style="color:DarkOrange" herf="javascript:void(0)" title="priority ">',
						'<i class="fa fa-lg fa-battery-three-quarters">&nbsp</i>' +priority+  '</a>'].join('');	
				break;
				case 3:
				priority = 'Medium';
				return[
					'<a style="color:ForestGreen" herf="javascript:void(0)" title="priority ">',
						'<i class="fa fa-lg fa-battery-half">&nbsp</i>' +priority+  '</a>'].join('');	
				break;
				case 4:
				priority = 'Low';
				return[
					'<a style="color:DimGray" herf="javascript:void(0)" title="priority ">',
						'<i class="fa fa-lg fa-battery-quarter">&nbsp</i>' +priority+  '</a>'].join('');	
				break;	
			}	
		}
		

/**********open new page for details of a single task(owned)*********/

	function infromationFormater_owner(value, row ) {
			var newString = row.TaskID; 
			return [
            '<a href="task_display_owner.php?id='+newString+'" title="Task Details" target="_blank">',
                '<i class="fa fa-info-circle fa-lg"></i>',
            '</a>'
        ].join('');
		}

/*********open new page for details of a single task(delegated)***********/

	
	function infromationFormater_delegate(value, row ) {
			var newString = row.TaskID;
			return [
            '<a href="task_display_delegated.php?id='+newString+'" title="Task Details" target="_blank">',
                '<i class="fa fa-info-circle fa-lg"></i>',
            '</a>'
        ].join('');
	}



		
	function completedLevelFomater(value ) {
		var percentage = (value * 100)+'%'
		
		if (value == 1.00) {
			return [
			'<div class="progress">',
			'<div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" style="width:' +percentage+'">',
	   		'<span>'+percentage+'</span>',
	 		'</div>',							
	 		'</div>'].join('');
		} else {
			return [
			'<div class="progress">',
			'<div class="progress-bar progress-bar-striped active" role="progressbar" style="width:' +percentage+'">',
	   		'<span>'+percentage+'</span>',
	 		'</div>',							
	 		'</div>'].join('');
		}
	}			
/******************* toggle between different sorting method for create by given user*****************/


		function showTopLevelTable(val) {
			$("#parentList").bootstrapTable('refresh', {
				   url: './api/task/get_top_level_task?sort='+ val
				});
		}

	
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
			var now = moment().format('DD/MM/YYYY');
			//var booleanTime = moment(now).isBefore(newTime);
			//console.log(now, newTime, booleanTime);

			if (moment(newTime).isSame(moment(now))) {
				return[
					'<a style="color:DarkOrange" herf="javascript:void(0)" title="ETA ">',
						'<i class="fa fa-lg fa-calendar-o">&nbsp</i>' +newTime+  '</a>'].join('');	

				} /*else if (moment(newTime).isAfter(moment(now))) {
					return[
						'<a style="color:Green" herf="javascript:void(0)" title="ETA ">',
							'<i class="fa fa-lg fa-calendar-o">&nbsp</i>' +newTime+  '</a>'].join('');	

					}*/ else {
						return[
							'<a style="color:black" herf="javascript:void(0)" title="ETA ">',
								'<i class="fa fa-lg fa-calendar-o">&nbsp</i>' +newTime+  '</a>'].join('');
							} 

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

	
	
		
		
		
		
		

