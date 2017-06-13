 <?php
 /* Creates the full url string for the purpose of creating the data representation */

    $form_url = 'https://estimationapi.zirmed.com/1.0/estimate/1234';

       
    $custId = 1234;
    $body = '';
    
//   Set the Date header on the HTTP Request to the current UTC date/time expressed in RFC 1123 format (example: Mon, 12 Jan 2015 20:50:07 GMT)
    $date = gmdate(DATE_RFC1123) ; 

//  Create a “representation” of the request by concatenating the following values into a single string (in this order, with no separators):
    $posted = "GEThttps://estimationapi.zirmed.com/1.0/estimate/1234$date101246";

    // remove delimiters "&" and "="
    $exploded_string = explode("&", $posted);

    $posted_array = array();

    foreach($exploded_string as $ekey => $evalue){
        $value = explode("=", $evalue);
        $posted_array[trim($value[0])] = $value[1];
    }

    $sorted_array = array();

    //remove empty key/value pairs
    $filterd_array   = array_filter($posted_array);

    // sort key and value 
    ksort($filterd_array,SORT_STRING | SORT_FLAG_CASE);


    $sorted_string = '';
    foreach($filterd_array as $skey => $salue){
        $sorted_string .= $skey.$salue;
    }

echo $encoded_signature = base64_encode(hash_hmac('sha1', $sorted_string, 'f6sdnmV1ItrAoOzlR1QZRSGSnGng5HV0KQZtSR4U',true));
//  $representation = sprintf("{0}{1}{2}{3}{4}",
//
//       'GET',
//
//       $form_url,
//
//       $formated_date,
//
//       $custId,
//
//       base64_encode($body));

//    $representation = sprintf('GET'.$form_url.$formated_date.$custId.base64_encode($body));


/* Create the HMAC hash based off of the supplied key and representation */

//  HMACSHA256 hmac = new HMACSHA256();
//
//  hmac.Key = Encoding.UTF8.GetBytes(API_KEY);
//
//  $authHash = Convert.ToBase64String(hmac.ComputeHash(Encoding.UTF8.GetBytes(representation)));

//  $s = hash_hmac('sha256', 'Message', 'secret', true);
//  echo base64_encode($s);
//
//    $s = hash_hmac('sha256', $representation, 'secret', true);
//    $authHash =  base64_encode($s);
//    /* Build the auth string */
//
//    $authString = sprintf("$custId:$authHash");
//
//    echo $authString;

//  return new Base64 ("HMAC", authString);

?>
