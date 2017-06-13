<?php
function curl_download($Url){
 
    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
 
    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();
 
    // Now set some options (most are optional)
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
 
    // Set a referer
    curl_setopt($ch, CURLOPT_REFERER, "http://test.schemax.in/DriveSync/getAuthURL");
 
    // User agent
    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
    return $output;
}


$response = curl_download('http://test.schemax.in/DriveSync/getAuthURL');

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Drive Sync Authentication</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

  </head>
  <body>

    <div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<h3>
				Hello,
			</h3>
			<p>
				To access the file in drive, please authenticate...
			</p> 
			<div id="DivCode" style="display:none;">
			<label for="inputcode" class="col-sm-2 control-label" >
						Google Code
					</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="inputcode" placeHolder="Paste your google code here"/>
					</div>
					
			</div>
			
			</div>
			<br>
			<br>
			<div class="col-md-12">
			<button type="button" class="btn btn-primary btn-block active" onClick="loadAuth('<?php echo $response;?>')">
				<span id="authText">Click here to Authenticate</span>
			</button>
		</div>
		
		<br><br>
					
	</div>
</div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
  </body>
</html>

<script>
function loadAuth(URL)
{
	if(document.getElementById('inputcode').value=='')
	{
		document.getElementById('DivCode').style.display='block';
		document.getElementById('authText').innerHTML="Paste your google code and Click here";
		window.open(URL,'_blank');
	}
	else
	{
		document.getElementById('authText').innerHTML="Saved , Your authorise to access your drive..";
	}
	
	
}
</script>