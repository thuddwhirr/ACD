<Response>
<Dial>
<Conference participantLimit="2" startConferenceOnEnter="false" endConferenceOnExit="true" beep="false">
QUEUE_<?php echo AppletInstance::getValue("queue_name")."_".rand(1000,9999)?>
</Conference>
</Dial>
<Pause length="1"/>
</Response>
