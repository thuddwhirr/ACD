<script>var claimed=false</script>

<?php
function claim($client, $conferenceSid, $phoneNumber,$account)
{
    $twiml = "<Response><Dial><Conference startConferenceOnEnter='true' endConferenceOnExit='true' beep='false'>$conferenceSid</Conference></Dial></Response>";

    $response = $client->request("Accounts/".$account."/Calls",
                                 "POST",
                                 array("Caller" => "6509241936",
                                       "Called" => "$phoneNumber",
                                       "Url" => "http://twimlets.com/echo?Twiml=".urlencode($twiml)));

    if($response->IsError)
		echo "Error starting phone call: {$response->ErrorMessage}\n";

    echo "<script>$.notify('Claiming caller $phoneNumber'); claimed ='$conferenceSid';</script>\n";
}

//Get list of running conferences that have not started
$client = new TwilioRestClient($this->twilio_sid,$this->twilio_token);

$ci = &get_instance();
$user_id = $ci->session->userdata('user_id');
$user = VBX_User::get($user_id);

$base_uri="Accounts/".$this->twilio_sid;

$claiming_conf = "";
if(array_key_exists('claim',$_REQUEST))
{
    $claiming_conf=$_REQUEST['claim'];
    $claim_phone_number=$user->devices[0]->value;
    claim($client, $claiming_conf, $claim_phone_number, $this->twilio_sid);
}

?>

<div class="vbx-plugin">
    <h2>Calls Waiting</h2>
<table>
    <thead>
        <tr>
            <th>Queue</th>
            <th>Caller</th>
            <th>Waiting</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="call_waiting_body">
    </tbody>
</table>
</div>
<div id="cometcatchr"></div>



<script>

var queueData = null;

$(document).ready(function() {

  $.getJSON("acd?json=true",
        function(data){
		if((data!=null)&&(data!='')){
			renderTable(data);
			queueData=data;
		}
	});
  
  timedRefresh();
  timedReload();
});

function timedReload(){
  getData();
  setTimeout(function(){timedReload()},5000);
}

function timedRefresh(){
   if((queueData!=null)&&(queueData!='')) renderTable(queueData);
   setTimeout(function(){timedRefresh()},500);
}



function getData(){
  $.getJSON("acd?json=true",
        function(data){
                if((data!=null)&&(data!='')){
                        renderTable(data);
			queueData=data;
		} else {
			queueData=null;
			clearTable();
		}
        });
}

function clearTable(){
  $("#call_waiting_body").empty();
}

function renderTable(data){
  clearTable();
  var tbody = $("#call_waiting_body");
  $.each(data, function(key, value) {
	var queueName = key;
	$.each(value, function(callsid,call){
		var starttime=Date.parse(call['startTime']);
		var waiting = Math.floor(((new Date()).getTime()-starttime)/1000);
		if(!claimed) tbody.append("<tr><td>"+call['queueName']+"</td><td>"+call['caller']+"</td><td>"+waiting+" s</td><td><div class='vbx-input-container'><form method='POST'><input type='hidden' name='claim' value='"+call['conferenceSid']+"'><input type='submit' value='Claim'></form></div></td></tr>");
		else tbody.append("<tr><td>"+call['queueName']+"</td><td>"+call['caller']+"</td><td>"+waiting+" s</td><td></td></tr>");
	});
});

}

</script>

