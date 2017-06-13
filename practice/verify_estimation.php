<?php
    require_once("verify_session.php");

    if(isset($_SESSION['portal_username']) !=''){
        $provider    = $_SESSION['portal_username'];
        $refer       = $_REQUEST['refer'];

        if($_REQUEST['refer']!='')
            $_SESSION['refer'] = $_REQUEST['refer'];
    }else {
        $provider                    = $_REQUEST['provider'];
        $_SESSION['portal_username'] = $_REQUEST['provider'];
        //for logout
        $refer                       = $_REQUEST['refer'];
        if($_REQUEST['refer']!='')
            $_SESSION['refer'] = $_REQUEST['refer'];
    }

    require_once("../interface/globals.php");
    require_once("../library/formdata.inc.php"); 
    require_once("../library/globals.inc.php");
    require_once("$srcdir/api.inc");
    require_once("$srcdir/forms.inc");
    require_once("$srcdir/options.inc.php");
    require_once("$srcdir/patient.inc");
    require_once("$srcdir/formatting.inc.php");

    //for logout
    $refer                      = isset($_REQUEST['refer'])     ? $_REQUEST['refer']    : $_SESSION['refer'];
    $_SESSION['refer']          = isset($_REQUEST['refer'])     ? $_REQUEST['refer']    : $_SESSION['refer'];
    $_SESSION['portal_username']= isset($_REQUEST['provider'])  ? $_REQUEST['provider'] : $_SESSION['provider'];
    $sql = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
          "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
          "AND authorized = 1 AND username='".$provider."'" .
          "ORDER BY lname, fname");
    $id         = sqlFetchArray($sql);
    

    $formateddata =  trim($_REQUEST['posteddata']);//echo "<br>";
    $result     = trim($_REQUEST['estID']);

    $zirmedCustID = trim($_REQUEST['CustID']);
    sleep(20);
//    echo  $formateddata =  'eyJaaXJNZWRDdXN0SUQiOiIxMDEyNDciLCJTZXF1ZW5jZU5vIjoiMCIsIklzRmFicmljYXRlZCI6ImZhbHNlIiwiUGF0aWVudCI6eyJGaXJzdE5hbWUiOiJGcmFuY2VzIiwiTGFzdE5hbWUiOiJBZGFtcyIsIkdlbmRlciI6IkYiLCJEYXRlT2ZCaXJ0aCI6IjA1XC8yNVwvMTk0NyIsIkFjY291bnROdW1iZXIiOiIiLCJFbWFpbEFkZHJlc3MiOiIifSwiSW5zdXJhbmNlIjp7IlBheWVyTmFtZSI6IkFldG5hICIsIlppck1lZFBheWVySUQiOiI2MDA1NCIsIk1lbWJlcklEIjoiTUVCRzlIN1YiLCJSZWxhdGlvbnNoaXBUb1N1YnNjcmliZXIiOiJzZWxmIiwiT3V0T2ZOZXR3b3JrIjoiZmFsc2UiLCJHcm91cE51bWJlciI6IiJ9LCJFbmNvdW50ZXIiOnsiUHJvdmlkZXJOYW1lIjoiS2V0aGEgU3VtYW5hIiwiUHJvdmlkZXJOUEkiOiIxOTYyNDQ3ODA1IiwiVGF4b25vbXlJRCI6IiIsIkRhdGVPZlNlcnZpY2UiOiIwNlwvMTFcLzIwMTUiLCJTdGNDb2RlIjoiOTgifSwiUHJvY2VkdXJlcyI6W3siQ29kZSI6Ijk5Mzk3IiwiUXVhbnRpdHkiOiIxIiwiTW9kMSI6IiIsIk1vZDIiOiIiLCJNb2QzIjoiIiwiTW9kNCI6IiJ9LHsiQ29kZSI6IjY5MjEwIiwiUXVhbnRpdHkiOiIxIiwiTW9kMSI6IiIsIk1vZDIiOiIiLCJNb2QzIjoiIiwiTW9kNCI6IiJ9XX0=';//$_REQUEST['posteddata'];echo "<br>";
//     echo $result     = '229846' ; //$_REQUEST['estID'];
//
//    echo $zirmedCustID = '101247'; //$_REQUEST['CustID'];
    
    //Set the Date header on the HTTP Request to the current UTC date/time expressed in RFC 1123 format (example: Mon, 12 Jan 2015 20:50:07 GMT)
    $date = gmdate(DATE_RFC1123) ; 
    $formatDate = gmdate(DATE_ATOM);
    
    $date = substr($date, 0, -5);
    $formatDate = substr($formatDate, 0, -6);

//  Create a “representation” of the request by concatenating the following values into a single string (in this order, with no separators):
    $posted = "GEThttps://estimationapi.zirmed.com/1.0/document/$result".$formatDate."$zirmedCustID";
    $encoded_signature = base64_encode(hash_hmac('sha256',$posted, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U',true));
   
   
//   echo "<pre>"; print_r($data); echo "</pre>";
   
   $form_url = 'https://estimationapi.zirmed.com/1.0/document/'.$result;
   $curl = curl_init();
   
   //// Set the options
   curl_setopt($curl,CURLOPT_URL, $form_url);
   
   $hmacSignature = 'HMAC '.$zirmedCustID.':'.$encoded_signature;
   
   $headers = array('Content-Type: application/json',sprintf('Date: %s',$date),
                     sprintf('Authorization: %s', $hmacSignature));

   curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


   $result2 = curl_exec($curl);
   if(curl_errno($curl)){   echo 'Curl error: '.curl_error($curl);}
   ////close the connection
   curl_close($curl);
   
   
    $destination = dirname(__FILE__) . '/estdownload.pdf';
    $file = fopen($destination, "w+");
    fputs($file, $result2);
    fclose($file);
    
    
?> 

<embed src="<?php echo 'https://'. $_SERVER['HTTP_HOST'].'/interface/main/allcarereports/estdownload.pdf'; ?>" width="800" height="600" alt="pdf">