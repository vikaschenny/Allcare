<?php
require_once("../globals.php");
require_once("../../library/acl.inc");
require_once("$srcdir/sql.inc");
require_once("$srcdir/auth.inc");
require_once("$srcdir/formdata.inc.php");
require_once(dirname(__FILE__) . "/../../library/classes/WSProvider.class.php");
require_once ($GLOBALS['srcdir'] . "/classes/postmaster.php");

$alertmsg = '';
$bg_msg = '';
$set_active_msg=0;
$show_message=0;


/* Sending a mail to the admin when the breakglass user is activated only if $GLOBALS['Emergency_Login_email'] is set to 1 */
$bg_count=count($access_group);
$mail_id = explode(".",$SMTP_HOST);
for($i=0;$i<$bg_count;$i++){
if(($_GET['access_group'][$i] == "Emergency Login") && ($_GET['active'] == 'on') && ($_GET['pre_active'] == 0)){
  if(($_GET['get_admin_id'] == 1) && ($_GET['admin_id'] != "")){
	$res = sqlStatement("select username from users where id={$_GET["id"]}");
	$row = sqlFetchArray($res);
	$uname=$row['username'];
	$mail = new MyMailer();
        $mail->SetLanguage("en",$GLOBALS['fileroot'] . "/library/" );
        $mail->From = "admin@".$mail_id[1].".".$mail_id[2];     
        $mail->FromName = "Administrator OpenEMR";
        $text_body  = "Hello Security Admin,\n\n The Emergency Login user ".$uname.
                                                " was activated at ".date('l jS \of F Y h:i:s A')." \n\nThanks,\nAdmin OpenEMR.";
        $mail->Body = $text_body;
        $mail->Subject = "Emergency Login User Activated";
        $mail->AddAddress($_GET['admin_id']);
        $mail->Send();
}
}
}
/* To refresh and save variables in mail frame */
if (isset($_POST["privatemode"]) && $_POST["privatemode"] =="user_admin") {
    if ($_POST["mode"] == "update") {
      if (isset($_POST["username"])) {
        // $tqvar = addslashes(trim($_POST["username"]));
        $tqvar = trim(formData('username','P'));
        $user_data = mysql_fetch_array(sqlStatement("select * from users where id={$_POST["id"]}"));
        sqlStatement("update users set username='$tqvar' where id={$_POST["id"]}");
        sqlStatement("update groups set user='$tqvar' where user='". $user_data["username"]  ."'");
        //echo "query was: " ."update groups set user='$tqvar' where user='". $user_data["username"]  ."'" ;
      }
      if ($_POST["taxid"]) {
        $tqvar = formData('taxid','P');
        sqlStatement("update users set federaltaxid='$tqvar' where id={$_POST["id"]}");
      }
      if ($_POST["state_license_number"]) {
        $tqvar = formData('state_license_number','P');
        sqlStatement("update users set state_license_number='$tqvar' where id={$_POST["id"]}");
      }
      if ($_POST["drugid"]) {
        $tqvar = formData('drugid','P');
        sqlStatement("update users set federaldrugid='$tqvar' where id={$_POST["id"]}");
      }
      if ($_POST["upin"]) {
        $tqvar = formData('upin','P');
        sqlStatement("update users set upin='$tqvar' where id={$_POST["id"]}");
      }
      if ($_POST["npi"]) {
        $tqvar = formData('npi','P');
        sqlStatement("update users set npi='$tqvar' where id={$_POST["id"]}");
      }
      if ($_POST["taxonomy"]) {
        $tqvar = formData('taxonomy','P');
        sqlStatement("update users set taxonomy = '$tqvar' where id= {$_POST["id"]}");
      }
      if ($_POST["lname"]) {
        $tqvar = formData('lname','P');
        sqlStatement("update users set lname='$tqvar' where id={$_POST["id"]}");
      }
      if ($_POST["job"]) {
        $tqvar = formData('job','P');
        sqlStatement("update users set specialty='$tqvar' where id={$_POST["id"]}");
      }
      if ($_POST["mname"]) {
              $tqvar = formData('mname','P');
              sqlStatement("update users set mname='$tqvar' where id={$_POST["id"]}");
      }
      if ($_POST["facility_id"]) {
              $tqvar = formData('facility_id','P');
              sqlStatement("update users set facility_id = '$tqvar' where id = {$_POST["id"]}");
              //(CHEMED) Update facility name when changing the id
              sqlStatement("UPDATE users, facility SET users.facility = facility.name WHERE facility.id = '$tqvar' AND users.id = {$_POST["id"]}");
              //END (CHEMED)
      }
      if ($GLOBALS['restrict_user_facility'] && $_POST["schedule_facility"]) {
          sqlStatement("delete from users_facility
            where tablename='users'
            and table_id={$_POST["id"]}
            and facility_id not in (" . implode(",", $_POST['schedule_facility']) . ")");
          foreach($_POST["schedule_facility"] as $tqvar) {
          sqlStatement("replace into users_facility set
                facility_id = '$tqvar',
                tablename='users',
                table_id = {$_POST["id"]}");
        }
      }
      if ($_POST["fname"]) {
              $tqvar = formData('fname','P');
              sqlStatement("update users set fname='$tqvar' where id={$_POST["id"]}");
      }

      //(CHEMED) Calendar UI preference
      if ($_POST["cal_ui"]) {
              $tqvar = formData('cal_ui','P');
              sqlStatement("update users set cal_ui = '$tqvar' where id = {$_POST["id"]}");

              // added by bgm to set this session variable if the current user has edited
          //   their own settings
          if ($_SESSION['authId'] == $_POST["id"]) {
            $_SESSION['cal_ui'] = $tqvar;
          }
      }
      //END (CHEMED) Calendar UI preference

      if (isset($_POST['default_warehouse'])) {
        sqlStatement("UPDATE users SET default_warehouse = '" .
          formData('default_warehouse','P') .
          "' WHERE id = '" . formData('id','P') . "'");
      }

      if (isset($_POST['irnpool'])) {
        sqlStatement("UPDATE users SET irnpool = '" .
          formData('irnpool','P') .
          "' WHERE id = '" . formData('id','P') . "'");
      }

     if ($_POST["adminPass"] && $_POST["clearPass"]) { 
        require_once("$srcdir/authentication/password_change.php");
        $clearAdminPass=$_POST['adminPass'];
        $clearUserPass=$_POST['clearPass'];
        $password_err_msg="";
        $success=update_password($_SESSION['authId'],$_POST['id'],$clearAdminPass,$clearUserPass,$password_err_msg);
        if(!$success)
        {
            error_log($password_err_msg);    
            $alertmsg.=$password_err_msg;
        }
     }

      // for relay health single sign-on
      if (isset($_POST["ssi_relayhealth"]) && $_POST["ssi_relayhealth"]) {
        $tqvar = formData('ssi_relayhealth','P');
        sqlStatement("update users set ssi_relayhealth = '$tqvar' where id = {$_POST["id"]}");
      }

      $tqvar  = $_POST["authorized"] ? 1 : 0;
      $actvar = $_POST["active"]     ? 1 : 0;
      $calvar = $_POST["calendar"]   ? 1 : 0;
  
      sqlStatement("UPDATE users SET authorized = $tqvar, active = $actvar, " .
        "calendar = $calvar, see_auth = '" . $_POST['see_auth'] . "' WHERE " .
        "id = {$_POST["id"]}");
      //Display message when Emergency Login user was activated 
      $bg_count=count($_POST['access_group']);
      for($i=0;$i<$bg_count;$i++){
        if(($_POST['access_group'][$i] == "Emergency Login") && ($_POST['pre_active'] == 0) && ($actvar == 1)){
         $show_message = 1;
        }
      }
      if(($_POST['access_group'])){
	for($i=0;$i<$bg_count;$i++){
        if(($_POST['access_group'][$i] == "Emergency Login") && ($_POST['user_type']) == "" && ($_POST['check_acl'] == 1) && ($_POST['active']) != ""){
         $set_active_msg=1;
        }
      }
    }	
      if ($_POST["comments"]) {
        $tqvar = formData('comments','P');
        sqlStatement("update users set info = '$tqvar' where id = {$_POST["id"]}");
      }
	$erxrole = formData('erxrole','P');
	sqlStatement("update users set newcrop_user_role = '$erxrole' where id = {$_POST["id"]}");
        
        
         /************ For Signature Image******************/
//        echo "<br>9090";
//  print_r($_POST);
//  echo "<br>9090";
  if($_FILES['imgSignatureEdit'])
  {
      //$tqvar = formData('imgSignatureEdit','P');
      //sqlStatement("update users set signature_image = '$tqvar' where id = {$_POST["id"]}");
      
      if(move_uploaded_file($_FILES['imgSignatureEdit']['tmp_name'],"../pic/user_sign/".$_FILES['imgSignatureEdit']['name']))
      {
        sqlStatement("update users set signature_image = '".$_FILES['imgSignatureEdit']['name']."' where id = {$_POST["id"]}");     
      
        unlink("../pic/user_sign/".$_POST['hdnPreviousImg']);
      }
  }

      if (isset($phpgacl_location) && acl_check('admin', 'acl')) {
        // Set the access control group of user
        $user_data = mysql_fetch_array(sqlStatement("select username from users where id={$_POST["id"]}"));
        set_user_aro($_POST['access_group'], $user_data["username"],
          formData('fname','P'), formData('mname','P'), formData('lname','P'));
      }

      $ws = new WSProvider($_POST['id']);

    }
}

/* To refresh and save variables in mail frame  - Arb*/
if (isset($_POST["mode"])) {
  if ($_POST["mode"] == "new_user") {
    if ($_POST["authorized"] != "1") {
      $_POST["authorized"] = 0;
    }
    // $_POST["info"] = addslashes($_POST["info"]);

    $calvar = $_POST["calendar"] ? 1 : 0;

    $res = sqlStatement("select distinct username from users where username != ''");
    $doit = true;
    while ($row = mysql_fetch_array($res)) {
      if ($doit == true && $row['username'] == trim(formData('rumple'))) {
        $doit = false;
      }
    }

    if ($doit == true) {
    require_once("$srcdir/authentication/password_change.php");

    //if password expiration option is enabled,  calculate the expiration date of the password
    if($GLOBALS['password_expiration_days'] != 0){
    $exp_days = $GLOBALS['password_expiration_days'];
    $exp_date = date('Y-m-d', strtotime("+$exp_days days"));
    }
    
    $insertUserSQL=            
            "insert into users set " .
            "username = '"         . trim(formData('rumple'       )) .
            "', password = '"      . 'NoLongerUsed'                  .
            "', fname = '"         . trim(formData('fname'        )) .
            "', mname = '"         . trim(formData('mname'        )) .
            "', lname = '"         . trim(formData('lname'        )) .
            "', federaltaxid = '"  . trim(formData('federaltaxid' )) .
            "', state_license_number = '"  . trim(formData('state_license_number' )) .
            "', newcrop_user_role = '"  . trim(formData('erxrole' )) .
            "', authorized = '"    . trim(formData('authorized'   )) .
            "', info = '"          . trim(formData('info'         )) .
            "', federaldrugid = '" . trim(formData('federaldrugid')) .
            "', upin = '"          . trim(formData('upin'         )) .
            "', npi  = '"          . trim(formData('npi'          )).
            "', taxonomy = '"      . trim(formData('taxonomy'     )) .
            "', facility_id = '"   . trim(formData('facility_id'  )) .
            "', specialty = '"     . trim(formData('specialty'    )) .
            "', see_auth = '"      . trim(formData('see_auth'     )) .
            "', cal_ui = '"        . trim(formData('cal_ui'       )) .
            "', default_warehouse = '" . trim(formData('default_warehouse')) .
            "', irnpool = '"       . trim(formData('irnpool'      )) .
            "', calendar = '"      . $calvar                         .
            "', pwd_expiration_date = '" . trim("$exp_date") .
            "'";
    
    $clearAdminPass=$_POST['adminPass'];
    $clearUserPass=$_POST['stiltskin'];
    $password_err_msg="";
    $prov_id="";
    $success=update_password($_SESSION['authId'],0,$clearAdminPass,$clearUserPass,$password_err_msg,true,$insertUserSQL,formData('rumple'),$prov_id);
    error_log($password_err_msg);
    $alertmsg .=$password_err_msg;
    if($success)
    {
      //set the facility name from the selected facility_id
      sqlStatement("UPDATE users, facility SET users.facility = facility.name WHERE facility.id = '" . trim(formData('facility_id')) . "' AND users.username = '" . trim(formData('rumple')) . "'");

      sqlStatement("insert into groups set name = '" . trim(formData('groupname')) .
        "', user = '" . trim(formData('rumple')) . "'");

      if (isset($phpgacl_location) && acl_check('admin', 'acl') && trim(formData('rumple'))) {
        // Set the access control group of user
        set_user_aro($_POST['access_group'], trim(formData('rumple')),
          trim(formData('fname')), trim(formData('mname')), trim(formData('lname')));
      }

      $ws = new WSProvider($prov_id);
        
    }

        

    } else {
      $alertmsg .= xl('User','','',' ') . trim(formData('rumple')) . xl('already exists.','',' ');
    }
   if($_POST['access_group']){
	 $bg_count=count($_POST['access_group']);
         for($i=0;$i<$bg_count;$i++){
          if($_POST['access_group'][$i] == "Emergency Login"){
             $set_active_msg=1;
           }
	}
      }
  }
  else if ($_POST["mode"] == "new_group") {
    $res = sqlStatement("select distinct name, user from groups");
    for ($iter = 0; $row = sqlFetchArray($res); $iter++)
      $result[$iter] = $row;
    $doit = 1;
    foreach ($result as $iter) {
      if ($doit == 1 && $iter{"name"} == trim(formData('groupname')) && $iter{"user"} == trim(formData('rumple')))
        $doit--;
    }
    if ($doit == 1) {
      sqlStatement("insert into groups set name = '" . trim(formData('groupname')) .
        "', user = '" . trim(formData('rumple')) . "'");
    } else {
      $alertmsg .= "User " . trim(formData('rumple')) .
        " is already a member of group " . trim(formData('groupname')) . ". ";
    }
  }
}

if (isset($_GET["mode"])) {

  /*******************************************************************
  // This is the code to delete a user.  Note that the link which invokes
  // this is commented out.  Somebody must have figured it was too dangerous.
  //
  if ($_GET["mode"] == "delete") {
    $res = sqlStatement("select distinct username, id from users where id = '" .
      $_GET["id"] . "'");
    for ($iter = 0; $row = sqlFetchArray($res); $iter++)
      $result[$iter] = $row;

    // TBD: Before deleting the user, we should check all tables that
    // reference users to make sure this user is not referenced!

    foreach($result as $iter) {
      sqlStatement("delete from groups where user = '" . $iter{"username"} . "'");
    }
    sqlStatement("delete from users where id = '" . $_GET["id"] . "'");
  }
  *******************************************************************/

  if ($_GET["mode"] == "delete_group") {
    $res = sqlStatement("select distinct user from groups where id = '" .
      $_GET["id"] . "'");
    for ($iter = 0; $row = sqlFetchArray($res); $iter++)
      $result[$iter] = $row;
    foreach($result as $iter)
      $un = $iter{"user"};
    $res = sqlStatement("select name, user from groups where user = '$un' " .
      "and id != '" . $_GET["id"] . "'");

    // Remove the user only if they are also in some other group.  I.e. every
    // user must be a member of at least one group.
    if (sqlFetchArray($res) != FALSE) {
      sqlStatement("delete from groups where id = '" . $_GET["id"] . "'");
    } else {
      $alertmsg .= "You must add this user to some other group before " .
        "removing them from this group. ";
    }
  }
}

$form_inactive = empty($_REQUEST['form_inactive']) ? false : true;

?>
<html>
<head>

<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/css/bootstrap-3.2.0.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/popover/css/jquery.webui-popover.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/jalert/css/jquery-confirm.css" media="screen" />

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/polyfills/flax/flexibility.js"></script>

<script type="text/javascript">
$j = $.noConflict();
$j(document).ready(function(){
    
    // fancy box
    $j(".iframe").fancybox( {
        'overlayOpacity' : 0.0,
        'showCloseButton' : true,
        'centerOnScroll' : false
    });
    // special size for
	$j(".iframe_medium").fancybox( {
		'overlayOpacity' : 0.0,
		'showCloseButton' : true,
		'frameHeight' : 450,
		'frameWidth' : 660
	});
});

</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox_costom-1.2.6.js"></script>
<script language="JavaScript">

function authorized_clicked() {
 var f = document.forms[0];
 f.calendar.disabled = !f.authorized.checked;
 f.calendar.checked  =  f.authorized.checked;
}

</script>
<style>
    .users{        
       margin-top: 5px;
        margin: 5px 5px;
    }
    .newwindowicon{
         margin: 3px;
         
    }
    .modal-lg {
        /*width: 1100px;*/
    }
    .modal-body{
       overflow: auto;
    }
    .webui-popover-content{
        padding:0 !important;
    }

    .panel-heading{
        font-size:15px;
    }
    .panel-body > iframe {
        border: 0 none;
        height: 100%;
        width: 100%;
    }
    .panel-body{
        overflow: auto;
        padding:0px 0px 15px 0px;

    }
    #penalcontenar {
        left: 0;
        padding: 13px;
        position: absolute;
        top: -50px;
        opacity : 0.20;
        width: 100%;
        height:100%;
    }
    .showborder{
        width: 750px;
    }
    
    .showborder td{
        border-bottom: 0px;
    }
    .tdcontent{
     width: 177px;
     -js-display: flex;
     display: flex;
    }
</style>
</head>
<body class="body_top">

<div>
    <div>
        <table >
	  <tr >
		<td><b><?php xl('User / Groups','e'); ?></b></td>
		<td><a href="usergroup_admin_add.php" class="iframe_medium css_button"><span><?php xl('Add User','e'); ?></span></a>
		</td>
		<td><a href="facility_user.php" class="css_button"><span><?php xl('View Facility Specific User Information','e'); ?></span></a>
		</td>
<!--                <td><a href="users_dropdown_1to1.php" class="css_button"><span><?php xl('Add User Custom Attributes','e'); ?></span></a>
		</td>
                <td><a href="user_cred_1ton.php" class="css_button"><span><?php xl('User Credentials','e'); ?></span></a>
		</td>
                <td><a href="payroll_1ton.php" class="css_button"><span><?php xl('Payroll','e'); ?></span></a>
		</td>-->
	  </tr>
	</table>
    </div>
    <div style="width:750px;">
        <div>

<form name='userlist' method='post' action='usergroup_admin.php' onsubmit='return top.restoreSession()'>
    <input type='checkbox' name='form_inactive' value='1' onclick='submit()' <?php if ($form_inactive) echo 'checked '; ?>/>
    <span class='text' style = "margin-left:-3px"> <?php xl('Include inactive users','e'); ?> </span>
</form>
<?php
if($set_active_msg == 1){
echo "<font class='alert'>".xl('Emergency Login ACL is chosen. The user is still in active state, please de-activate the user and activate the same when required during emergency situations. Visit Administration->Users for activation or de-activation.')."</font><br>";
}
if ($show_message == 1){
 echo "<font class='alert'>".xl('The following Emergency Login User is activated:')." "."<b>".$_GET['fname']."</b>"."</font><br>";
 echo "<font class='alert'>".xl('Emergency Login activation email will be circulated only if following settings in the interface/globals.php file are configured:')." \$GLOBALS['Emergency_Login_email'], \$GLOBALS['Emergency_Login_email_id']</font>";
}

?>
<table id="usertb" cellpadding="3" cellspacing="0" class="showborder">
	<tbody><tr height="22" class="showborder_head">
		<th width="180px"><b><?php xl('Username','e'); ?></b></th>
		<th width="180px"><b><?php xl('Real Name','e'); ?></b></th>
		<th width="250px"><b><span class="bold"><?php xl('Additional Info','e'); ?></span></b></th>
                <th><b><?php xl('Authorized','e'); ?>?&nbsp;&nbsp;&nbsp;</b></th>
                <th><b><?php xl('Others','e'); ?></b></th>    
		<?php
$query = "SELECT * FROM users WHERE username != '' ";
if (!$form_inactive) $query .= "AND active = '1' ";
$query .= "ORDER BY username";
$res = sqlStatement($query);
for ($iter = 0;$row = sqlFetchArray($res);$iter++)
  $result4[$iter] = $row;
foreach ($result4 as $iter) {
  if ($iter{"authorized"}) {
    $iter{"authorized"} = xl('yes');
  } else {
      $iter{"authorized"} = "";
  }
  print "<tr height=20  class='text' style='border-bottom: 1px dashed;'>
		<td class='text'><b><a href='user_admin.php?id=" . $iter{"id"} .
    "' class='iframe_medium' onclick='top.restoreSession()'><span>" . $iter{"username"} . "</span></a></b>" ."&nbsp;</td>
	<td><span class='text'>" .$iter{"fname"} . ' ' . $iter{"lname"}."</span>&nbsp;</td>
	<td><span class='text'>" .$iter{"info"} . "</span>&nbsp;</td>
	<td align='left'><span class='text'>" .$iter{"authorized"} . "</span>&nbsp;</td>";
  print "<td><div class='tdcontent'><select class='users' onchange='showPopOver(this)'><option value=''>Select</option><option value='Users Custom Attributes'>Add Custom Attributes</option><option value='add_extrausersdata'>User Credentials</option><option value='add_payroll'>Payroll</option></select><div class='newwindowicon'></div></div></td>";
  print "</tr>\n";
}
?>
	</tbody></table>
<?php
if (empty($GLOBALS['disable_non_default_groups'])) {
  $res = sqlStatement("select * from groups order by name");
  for ($iter = 0;$row = sqlFetchArray($res);$iter++)
    $result5[$iter] = $row;

  foreach ($result5 as $iter) {
    $grouplist{$iter{"name"}} .= $iter{"user"} .
      "(<a class='link_submit' href='usergroup_admin.php?mode=delete_group&id=" .
      $iter{"id"} . "' onclick='top.restoreSession()'>Remove</a>), ";
  }

  foreach ($grouplist as $groupname => $list) {
    print "<span class='bold'>" . $groupname . "</span><br>\n<span class='text'>" .
      substr($list,0,strlen($list)-2) . "</span><br>\n";
  }
}
?>
        </div>
    </div>
</div>
    
    <!-- Modal -->
<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>

<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/popover/js/jquery.webui-popover.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/bootstrap-3.2.0.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/jalert/js/jquery-confirm.js"></script>
<script language="JavaScript">
<?php
  if ($alertmsg = trim($alertmsg)) {
    echo "alert('$alertmsg');\n";
  }
?>
    var settings = {
       trigger:'click',
       title:'Template',
       content:'',						
       multi:true,						
       closeable:false,
       style:'',
       delay:300,
       padding:true,
       backdrop:false,
    };
    popoverSettings = {
           placement:'left-bottom',
           width:500,
           height:300,
           delay:{show:2000,hide:1000},
           closeable:true,
           title:'',
           trigger: 'click',
           dismissible:true,
           type:'iframe',
           url:"",
           fullscreen:true,
           parentwindowheaderheight:50,
           onHide: function($element) {}
    };
    //get href parameters values
    var getUrlParameter = function getUrlParameter(url,sParam) {
       var sPageURL = url.substring(url.indexOf("?")+1,url.length),
           sURLVariables = sPageURL.split('&'),
           sParameterName,
           i;
       for (i = 0; i < sURLVariables.length; i++) {
           sParameterName = sURLVariables[i].split('=');

           if (sParameterName[0] === sParam) {
               return sParameterName[1] === undefined ? true : sParameterName[1];
           }
       }
    };
    //onChange DropDown 
    function showPopOver(target){
        var $self = $(target);
        var modalbodyurl="";
        var modalbody="";
        if($self.val() == ""){
            $self.parents('td').find(".newwindowicon").html("");
        }else if($self.val() == "add_extrausersdata" || $self.val() == "add_payroll"){
            var $selectpharma = $self.parents("tr").find('td:first-child a');
            modalbodyurl =  $self.val()+".php?userid=";
            modalbody = modalbodyurl+getUrlParameter($selectpharma.attr("href"),"id");
            $self.parents('td').find(".newwindowicon").html("<a href='#' class='' data-toggle='modal' data-modalbody='"+modalbodyurl+"' data-target='#Modal'><i class='glyphicon glyphicon-new-window'></i></a>");
            $('#Modal').find('.modal-title').html($selectpharma.text());
            $('#Modal').find('.modal-body').empty();
            loadmodalbody($('#Modal').find('.modal-body'),modalbody);
            $('#Modal').modal({
                show: true
            });
        }else{
            var $selectpharma = $self.parents("tr").find('td:first-child  a');
            $self.parents('td').find(".newwindowicon").html("<a href='#' class='show-pop-async popoverph'  title='Click to see Template' ><i class='glyphicon glyphicon-new-window'></i></a>");
            popoverSettings.url = "users_full_1to1.php?uid1="+getUrlParameter($selectpharma.attr("href"),"id");
            popoverSettings.title = $self.val();
            $self.parents('td').find(".newwindowicon").find('a.popoverph').webuiPopover('destroy').webuiPopover($.extend({},settings,popoverSettings));
            setTimeout(function(){$self.parents('td').find(".newwindowicon").find('a.popoverph').webuiPopover('show')},100);
        }

    }

    function hidepopover(){
        $('a.popoverph').webuiPopover('hide');
    }
    
    function hidepenal(type){
        $("#penalcontenar").animate({opacity:0,top:-50},400,function(){$(this).remove();});
        $("#Modal .modal-body").css("overflow","auto");
        if(type[0]=="save"){
            $("#userdata_div").load(""+type[2]+".php?userid="+type[1],function(){
                $(this).css("display","block");
                $(this).next().remove();
            });
        }
     }
    
    function userstable(event,target,paneltitle){
        event.preventDefault();
        event.stopPropagation();
        $("#Modal .modal-body").css("overflow","hidden");
        var panelheading = paneltitle;
        var frameurl = (function(){
            var oldurl = $(target).attr("href");
            return ""+oldurl;
        })(target);
        $("#Modal .modal-body").append('<div id="penalcontenar"><div class="panel panel-default"><div class="panel-heading">'+panelheading+'<button id="penalclose" type="button" class="close">&times;</button></div><div class="panel-body"><iframe src="'+frameurl+'" width="200" height="200"></iframe></div></div></div>');
        var penalbodyheight = ($('.modal-body').height() - ($(".panel-heading").height()+parseInt($("#penalcontenar").css("padding-bottom"))+parseInt($("#penalcontenar").css("padding-top"))));
        $('.panel-body').css({height:penalbodyheight});
        $('.panel-body > iframe').css({height:penalbodyheight});
        $("#penalcontenar").animate({opacity:1,top:0},400);
        $("#penalclose").click(function(){
           $("#penalcontenar").animate({opacity:0,top:-50},400,function(){$(this).remove();});
           $("#Modal .modal-body").css("overflow","auto");
        });
    }
    

    //modal window load
    $('#Modal').on('show.bs.modal', function (event) {
        var $currentTarget = $(event.relatedTarget);
        var $modal = $(this);
        $(this).find(".modal-body").css("height",($(window.parent.document).height() - 170)+"px");
        if($currentTarget.length !=0){
            $modal.find('.modal-body').empty();
            var modalbodyurl = $currentTarget.data('modalbody')+getUrlParameter($currentTarget.parents("tr").find('td:first-child a').attr("href"),"id");
            $modal.find('.modal-title').html($currentTarget.parents("tr").find('td:first-child a').text());
           loadmodalbody($modal.find('.modal-body'),modalbodyurl);
        }
    });
    function loadmodalbody($target,url){
        $target.load(url);
    }
    
    function deleteusersdatarow(event,target,id){
        event.preventDefault();
        event.stopPropagation();
        var url = "/interface/patient_file/practice_settings_deleter.php?"+id+"="+getUrlParameter($(target).attr("href"),id);
        var alertcontent = "Do you really want to delete "+id+" "+ getUrlParameter($(target).attr("href"),id)+" and all subordinate data?";
        $.confirm({
                title: 'Confirm!',
                content:alertcontent,
                confirm: function () {
                    $.ajax({
                        url:url,
                        success:function(){
                            $(target).parents("tr").remove();
                            $.alert('deleted successfully!');
                        },error:function(){
                            $.alert('Ajax Error not deleted Please Try!');
                        }
                    });
                },
                cancel: function () {
                        //$.alert('Canceled!');
                }
        });

    }
</script>

</body>
</html>
