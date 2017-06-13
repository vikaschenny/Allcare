<?php
require "Services/Twilio.php";
// set your AccountSid and AuthToken from www.twilio.com/user/account
$AccountSid = "ACa5482d4602fd6ac1b0b15e6c45ad8cdb";
$AuthToken = "b922b3652854d27aa0c7f91e0b319986";
$client = new Services_Twilio($AccountSid, $AuthToken);
$sms = $client->account->sms_messages->create(
"+1 415-599-2671", // From this number   "YYY-YYY-YYYY"
"+1 757-639-2609", // To this number     "XXX-XXX-XXXX"   
"Test message!"
);
// Display a confirmation message on the screen
echo "Sent message from openemr";

/*
require "/path/to/twilio-php/Services/Twilio.php";
// set your AccountSid and AuthToken from www.twilio.com/user/account
$AccountSid = "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$AuthToken = "YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY";
$client = new Services_Twilio($AccountSid, $AuthToken);
try {
$sms = $client->account->sms_messages->create(
"YYY-YYY-YYYY", // From this number
"XXX-XXX-XXXX", // To this number
"Test message!"
);
} catch (Services_Twilio_RestException $e) {
echo $e->getMessage();
}
*/

?>
