<?php 
//Include OpenEMR globals.php file
include_once("globals.php");
//print_r($_REQUEST);
//Code to be executed in postback for email functionality
/*
 * Desc: Send Email with/without attachment based on the file type of the Document(file_url/web_url) when a user clicks email button with email address provided
*/
$result = null;
sqlQuery("CREATE TABLE IF NOT EXISTS my_documents_email_history(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,email_to VARCHAR(50), email_from VARCHAR(50), date DATE, document_url VARCHAR(500), file_type VARCHAR(50))");
if(isset($_REQUEST['docid']) && isset($_REQUEST['recipient']) && (isset($_REQUEST['pid'])))
{
	//$details = explode('-',$_REQUEST['emaildetails']);	//Split the Document id and Patient id submitted in the form(emaildetails hidden field)
	$details = array($_REQUEST['pid'],$_REQUEST['docid']);
	//Check if emaildetails(docid, patientid) are provided properly
	if(sizeof($details) > 0) {
		//Get the Document and Patient Details from the database to send email
		$docDetails = sqlQuery("SELECT pdata.id, pdata.fname, pdata.lname, docs.id AS docid, docs.url, docs.type FROM patient_data AS pdata INNER JOIN documents AS docs ON pdata.pid = docs.foreign_id WHERE pdata.pid =".$details[0]." AND docs.id =".$details[1]." LIMIT 1 ");
		//Check if any record is fetched
		$email_sender_name = sqlQuery("SELECT gl_value from globals WHERE gl_name='patient_reminder_sender_name'");
		$email_sender_email = sqlQuery("SELECT gl_value from globals WHERE gl_name='patient_reminder_sender_email'");
		//print_r($email_name);print_r($email_email);
		if(isset($docDetails)) {
			include_once("../library/classes/postmaster.php");
			
			$mail = new MyMailer();
			//$mail->From = 'admin@emrdev.risecorp.com';
			$mail->From = $email_sender_email['gl_value'];
			$mail->FromName = $email_sender_name['gl_value'];
			$doctitle = explode('/',$docDetails['url']);
			$mail->Subject = $docDetails['fname'].' '.$docDetails['lname'].' - '.$doctitle[sizeof($doctitle) - 1];
			//$mail->AddAddress( $_REQUEST[recipient]);
			$exploded_mail_addresses  = explode(',',$_REQUEST['recipient']);
			foreach($exploded_mail_addresses as $email){
				$mail->AddAddress($email);
			}
			if ($docDetails['type']=='file_url') {
				$mail->Body = 'Please find the attached Document';
				$status = $mail->AddAttachment($docDetails['url']);
				if($status) {
					$result = 'File attached Successfully!';
				} else {
					$result = 'Could not attach file';
				}
			}
			elseif ($docDetails['type']=='web_url'){
				//Send Document url in the email body
				$mail->Body = 'Click the link to download the Document '.$docDetails['url'];
			}
			//Send email
			if(!$mail->send()){
				$result .= 'Could not send email';
			}
			else{
				$result .= 'Email sent to '.$_REQUEST['recipient'];
				/*$email_to = $_REQUEST['recipient'];
				$email_from = $email_sender_email['gl_value'];
				$document_url = $docDetails['url'];
				$file_type = $docDetails['type'];
				$insert_details = sqlQuery("INSERT into my_documents_email_history(id,email_to,email_from,date,document_url,file_type) VALUES('".$email_to."','".$email_from."','".NOW()."'".$document_url."','".$file_type.")");*/
			}
			
		}
		else {
			$result = 'Could not fetch Document details. Email failed.';
		}
	}
	else {
		$result = 'Insufficient information provided.';
	}
}
echo $result;
return $result;
/*
 * End of Email code
*/
/*if(isset($_REQUEST['emaildetails']) && isset($_REQUEST['recipient']))
{
	echo 'Send the document '.$_REQUEST['emaildetails'].' email to: '.$_REQUEST['recipient'];
}*/
?>