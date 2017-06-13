<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../../verify_session.php");

$pagename = "plist"; 
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
   $provider=$_REQUEST['provider'];
   $refer=$_REQUEST['refer']; 
   $_SESSION['refer']=$_REQUEST['refer'];
   $_SESSION['portal_username']=$_REQUEST['provider'];
} 

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';

 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

?>
<label> Select Payer</label>
<select id='payerslist' name='payerslist' onchange="get_payer_plan_info();">
    <option value=''>Select</option>
    <?php
    // to get emr payers
    $get_emr_payers = sqlStatement("SELECT id, name, payer_id FROM insurance_companies ORDER BY name");
    while($set_emr_payers = sqlFetchArray($get_emr_payers)){
        ?>
        <option value='<?php echo $set_emr_payers['id']; ?>'><?php echo $set_emr_payers['name']; ?></option>
        <?php
    }
    ?>
</select>
<div id='payerplaninfo' name='payerplaninfo'>

</div>