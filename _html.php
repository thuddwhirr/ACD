<?php
/* Include JS for polling calls */
OpenVBX::addJS('script.js');
	
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
