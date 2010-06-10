<?php 
$ci = &get_instance();
  $confname="";

  //Get list of running conferences that have not started
  $client = new TwilioRestClient($ci->twilio_sid, $ci->twilio_token);

  $conferences = $client->request("Accounts/".$ci->twilio_sid."/Conferences?Status=1","GET");
  
  $queue_conferences = array();
  foreach($conferences->ResponseXml->Conferences->Conference as $conference){
     if(substr($conference->FriendlyName,0,strlen("QUEUE_".AppletInstance::getValue("queue_name"))) == "QUEUE_".AppletInstance::getValue("queue_name")) 
       $queue_conferences[]=$conference;
  }

  foreach($queue_conferences as $queue_conf){
    $participants = $client->request("Accounts/".$ci->twilio_sid."/Conferences/{$queue_conf->Sid}/Participants");
    if(count($participants->ResponseXml->Participants->Participant)) {
      $confname=$queue_conf->FriendlyName;
      break;
    };
  }

  //get the next conference
  if($confname!=""){
?>

<Response>
  <Dial action="" hangupOnStar="true">
    <Conference beep="false" participantLimit="2" endConferenceOnExit="true" startConferenceOnEnter="true">
      <?php echo $confname?>
    </Conference>
  </Dial>
</Response>
<?php 

} else {
?>
<Response><Say>No Calls Waiting.  Retrying soon</Say><Pause length="15"/><Redirect/></Response>
<?php
}
?>
