<?php 
$base_uri="Accounts/".$this->twilio_sid;

$client = new TwilioRestClient($this->twilio_sid,$this->twilio_token);
$conferences = $client->request($base_uri."/Conferences?Status=1","GET");
$queues = array();
foreach($conferences->ResponseXml->Conferences->Conference as $conference){
    if(substr($conference->FriendlyName,0,strlen("QUEUE_")) == "QUEUE_"){
        if(preg_match('/^QUEUE_([A-Za-z0-9_]+)_.+$/', $conference->FriendlyName, $matches)){
            $queue_name = "{$matches[1]}";
            if(!array_key_exists($queue_name,$queues)){
                $queues[$queue_name]=array();
            }
            $participants = $client->request($base_uri."/Conferences/{$conference->Sid}/Participants","GET");
            if(count($participants->ResponseXml->Participants->Participant)==1)
                foreach($participants->ResponseXml->Participants->Participant as $participant){
                    $call= $client->request($base_uri."/Calls/$participant->CallSid","GET");
                    $queues[$queue_name][($participant->CallSid).""]=array("call"=>$call->ResponseXml->Call,"participant"=>$participant);
                }
        }
    }
}

$out_queue = array();;
foreach($queues as $i=>$queue){
  $out_queue[$i]= array();
  foreach($queue as $c=>$call){
    $entry = array();
    $entry['queueName'] = $i; 
    $entry['callSid'] = $c;
    $entry['caller'] = (string)$call['call']->Caller;
    $entry['startTime'] = (string)$call['call']->StartTime;
    $entry['conferenceSid'] = (string)$call['participant']->ConferenceSid;
    $out_queue[$i][$c]=$entry; 
  }
}

header("Content-Type: application/json");
echo json_encode($out_queue);
exit();
