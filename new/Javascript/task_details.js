	

/************Date Time picker for EAT only can select future time and interval set to 15 mins************/
	$(document).ready(function(){
			$('#eta').appendDtpicker({
				"futureOnly": true,
				"closeOnSelected": true,
				"minuteInterval": 15,
				"minTime":"08:30",
				"maxTime":"20:09",
				"dateFormat": "DD-MM-YYYY hh:mm",
				"autodateOnStart": "false"
			});
		});

/************twist data for display in the field of duration***************/
    
		function durationFormater(value) {
			if (value == 0) {

				value = "--";
			} else {
				value + "hrs";
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
		

	