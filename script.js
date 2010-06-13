$(document).ready(function() {
	var ACD = {
		claimed : false,
		queueData : false,
		clearTable : function() {
			$("#call_waiting_body").empty();
		},
		renderTable : function (data) {
			ACD.clearTable();
			var tbody = $("#call_waiting_body");
			$.each(data, function(key, value) {
				var queueName = key;
				$.each(value, function(callsid,call){
					var starttime=Date.parse(call['startTime']);
					var waiting = Math.floor(((new Date()).getTime()-starttime)/1000);
					if(!ACD.claimed) tbody.append("<tr><td>"+call['queueName']+"</td><td>"+call['caller']+"</td><td>"+waiting+" s</td><td><div class='vbx-input-container'><form method='POST'><input type='hidden' name='claim' value='"+call['conferenceSid']+"'><input type='submit' value='Claim'></form></div></td></tr>");
					else tbody.append("<tr><td>"+call['queueName']+"</td><td>"+call['caller']+"</td><td>"+waiting+" s</td><td></td></tr>");
				});
			});
		},
		timedRefresh : function timedRefresh() {
			if((ACD.queueData!=null) && (ACD.queueData!='')) 
				ACD.renderTable(ACD.queueData);
			
			setTimeout(function() {
				ACD.timedRefresh()
			}, 500);
		},
		timedReload : function() {
			ACD.getData();
			setTimeout(function() {
				ACD.timedReload()
			}, 5000);
		},
		getData : function() {
			$.ajax({
				url : OpenVBX.home + "/plugins/p/acd?json=true",
				type : 'GET',
				dataType : 'json',
				success : function(data){
					if((data!=null) && (data!='')) {
						ACD.renderTable(data);
						ACD.queueData=data;
					} else {
						ACD.queueData=null;
						ACD.clearTable();
					}
				}
			});
		}
	};

	$.ajax({
		success : function(data){
			if((data != null) && (data != '')){
				ACD.renderTable(data);
				ACD.queueData=data;
			}
		},
		url : OpenVBX.home + "p/acd?json=true",
		type : 'GET',
		dataType : 'json'
	});
		   
	ACD.timedRefresh();
	ACD.timedReload();
});

