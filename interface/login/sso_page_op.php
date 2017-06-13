<?php
$ignoreAuth=true;
include_once("../globals.php");
?>
<style>
 a:visited {
  color: #000000 !important;
}
</style>
<script type='text/javascript'>

	function submit_by_id() {
		//document.getElementById("ssoform").submit();
                document.ssoform.submit();
	}
	
</script>
<?php 
//Get the Client_id, redirect url and server url 
$sql1= sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id='openEMR_sso'");
$sql1_exc = sqlFetchArray($sql1);
$res = $sql1_exc[title];
if($res == 'enabled'){
	$sql2 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'openEMR_redirect'");
	$sql2_exc = sqlFetchArray($sql2);
	$redirect = $sql2_exc[title];
	
	$sql3 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'openEMR_client_id'");
	$sql3_exc = sqlFetchArray($sql3);
	$client_id = $sql3_exc[title];
	
	$sql4= sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'openEMR_server'");
	$sql4_exc = sqlFetchArray($sql4);
	$server = $sql4_exc[title];

	if(!empty($redirect) && !empty($client_id) && !empty($server)){
		$sql5 = sqlStatement("SELECT title FROM `list_options` where list_id='AllCareSSO' and option_id = 'openEMR_sso_text'");
		$sql5_exc = sqlFetchArray($sql5);
		$text  = $sql5_exc[title];
		if(empty($text)){
			$text = 'Single Sign On';
		}
?>
<!--		<form action="<?php echo $server; ?>/oauth/authorize?response_type=code&client_id=<?php echo $client_id; ?>&redirect_uri=<?php echo $redirect; ?>" method="post" name="ssoform" id="ssoform" target='_parent'>
			<input type="button" value="<?php echo $text;?>" onclick="submit_by_id();"/>
                        <input  type="submit" onClick="submit_by_id()" value="<?php echo $text;?>">
                       
		</form>-->
                <a class="button" href="<?php echo $server; ?>/oauth/authorize?response_type=code&client_id=<?php echo $client_id; ?>&redirect_uri=<?php echo $redirect; ?>" target="_parent"><?php echo $text; ?></a>

<?php
	}
}
?>
