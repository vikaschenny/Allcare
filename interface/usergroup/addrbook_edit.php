<?php
 // Copyright (C) 2006-2010 Rod Roark <rod@sunsetsystems.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

 //SANITIZE ALL ESCAPES
 $sanitize_all_escapes=true;
 //

 //STOP FAKE REGISTER GLOBALS
 $fake_register_globals=false;
 //

 include_once("../globals.php");
 include_once("$srcdir/acl.inc");
 require_once("$srcdir/options.inc.php");
 require_once("$srcdir/formdata.inc.php");
 require_once("$srcdir/htmlspecialchars.inc.php");
 
 require_once("{$GLOBALS['srcdir']}/sqlCentralDB.inc"); // This is to connect central db to insert/update patient data in central db
 global $sqlconfCentralDB; // This is declared in central db connection

 // Collect user id if editing entry
 $userid = $_REQUEST['userid']; 
 
 // Collect type if creating a new entry
 $type = $_REQUEST['type'];

 $info_msg = "";

 function invalue($name) {
  $fld = add_escape_custom(trim($_POST[$name]));
  return "'$fld'";
 }
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'https://'; 
?>
<html>
<head>
<title><?php echo $userid ? xlt('Edit') : xlt('Add New') ?> <?php echo xlt('Person'); ?></title>
<link rel="stylesheet" href='<?php echo $css_header ?>' type='text/css'>
<script type="text/javascript" src="../../library/js/jquery.1.3.2.js"></script>

<style>
td { font-size:10pt; }

.inputtext {
 padding-left:2px;
 padding-right:2px;
}

.button {
 font-family:sans-serif;
 font-size:9pt;
 font-weight:bold;
}
</style>

<script language="JavaScript">

 var type_options_js = Array();
 <?php
  // Collect the type options. Possible values are:
  // 1 = Unassigned (default to person centric)
  // 2 = Person Centric
  // 3 = Company Centric
  $sql = sqlStatement("SELECT option_id, option_value FROM list_options WHERE " .
   "list_id = 'abook_type'");
  while ($row_query = sqlFetchArray($sql)) {
   echo "type_options_js"."['" . attr($row_query['option_id']) . "']=" . attr($row_query['option_value']) . ";\n";
  }
 ?>

 // Process to customize the form by type
 function typeSelect(a) {
  if (type_options_js[a] == 3) {
   // Company centric:
   //   1) Hide the person Name entries
   //   2) Hide the Specialty entry
   //   3) Show the director Name entries
   document.getElementById("nameRow").style.display = "none";
   document.getElementById("specialtyRow").style.display = "none";
   document.getElementById("nameDirectorRow").style.display = "";
  }
  else {
   // Person centric:
   //   1) Hide the director Name entries
   //   2) Show the person Name entries
   //   3) Show the Specialty entry
   document.getElementById("nameDirectorRow").style.display = "none";
   document.getElementById("nameRow").style.display = "";
   document.getElementById("specialtyRow").style.display = "";
  }
 }
</script>

</head>

<body class="body_top">
<?php
 // If we are saving, then save and close the window.
 //
 if ($_POST['form_save']) {

 // Collect the form_abook_type option value
 //  (ie. patient vs company centric)
 $type_sql_row = sqlQuery("SELECT `option_value` FROM `list_options` WHERE `list_id` = 'abook_type' AND `option_id` = ?", array(trim($_POST['form_abook_type'])));
 $option_abook_type = $type_sql_row['option_value'];
 // Set up any abook_type specific settings
 if ($option_abook_type == 3) {
  // Company centric
  $form_title = invalue('form_director_title');
  $form_fname = invalue('form_director_fname');
  $form_lname = invalue('form_director_lname');
  $form_mname = invalue('form_director_mname');
 }
 else {
  // Person centric
  $form_title = invalue('form_title');
  $form_fname = invalue('form_fname');
  $form_lname = invalue('form_lname');
  $form_mname = invalue('form_mname');
 }
 
    // Get practice ID
    $practiceId = '';
    $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practiceID'");
    while($row = sqlFetchArray($query)){
    $practiceId = $row['title'];
    }
    
    //Data sync flag
    $datasync = 1; // 1 = practice could be in sync with central db; 0 = This is a standalone practice which should not be in sync with central db
    $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practicetocentral'");
    while($row = sqlFetchArray($query)){
        $datasync = $row['title'];
    }
    
    $sql2 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".add_escape_custom($userid)." AND objecttype = 'AddressBook'";
    $r = sqlStatement($sql2);
    $objref = "";
    while($row2 = sqlFetchArray($r)):
       $objref = $row2['objectref'];
    endwhile;
    $count = sqlNumRows($r);   

  if ($userid) 
      {

   $query = "UPDATE users SET " .
    "abook_type = "   . invalue('form_abook_type')   . ", " .
    "title = "        . $form_title                  . ", " .
    "fname = "        . $form_fname                  . ", " .
    "lname = "        . $form_lname                  . ", " .
    "mname = "        . $form_mname                  . ", " .
    "specialty = "    . invalue('form_specialty')    . ", " .
    "organization = " . invalue('form_organization') . ", " .
    "valedictory = "  . invalue('form_valedictory')  . ", " .
    "assistant = "    . invalue('form_assistant')    . ", " .
    "federaltaxid = " . invalue('form_federaltaxid') . ", " .
    "upin = "         . invalue('form_upin')         . ", " .
    "npi = "          . invalue('form_npi')          . ", " .
    "taxonomy = "     . invalue('form_taxonomy')     . ", " .
    "email = "        . invalue('form_email')        . ", " .
    "url = "          . invalue('form_url')          . ", " .
    "street = "       . invalue('form_street')       . ", " .
    "streetb = "      . invalue('form_streetb')      . ", " .
    "city = "         . invalue('form_city')         . ", " .
    "state = "        . invalue('form_state')        . ", " .
    "zip = "          . invalue('form_zip')          . ", " .
    "street2 = "      . invalue('form_street2')      . ", " .
    "streetb2 = "     . invalue('form_streetb2')     . ", " .
    "city2 = "        . invalue('form_city2')        . ", " .
    "state2 = "       . invalue('form_state2')       . ", " .
    "zip2 = "         . invalue('form_zip2')         . ", " .
    "phone = "        . invalue('form_phone')        . ", " .
    "phonew1 = "      . invalue('form_phonew1')      . ", " .
    "phonew2 = "      . invalue('form_phonew2')      . ", " .
    "phonecell = "    . invalue('form_phonecell')    . ", " .
    "fax = "          . invalue('form_fax')          . ", " .
    "notes = "        . invalue('form_notes')        . " "  .
    "WHERE id = '" . add_escape_custom($userid) . "'";
    sqlStatement($query);
  
    if($datasync == 1):
        
        // Check if this user has userid created in tbl_allcare_agencyportal
        $str = "SELECT portal_username FROM tbl_allcare_agencyportal WHERE uid=".add_escape_custom($userid);
        $query = sqlStatement($str);
        $row = sqlFetchArray($query);
        $agencyusername = '';
        if($row['portal_username'] !='') $agencyusername =  $row['portal_username'];
        
        // Check if cangroup is allowed or not from table tbl_addrbk_custom_attr_1to1
        $str = "SELECT cangroup FROM tbl_addrbk_custom_attr_1to1 WHERE addrbk_type_id=".add_escape_custom($userid);
        $queryq1 = sqlStatement($str);
        $rowq1 = sqlFetchArray($queryq1);
        $cangroup = $rowq1['cangroup'];
        
        $sql = "UPDATE allcareobjects SET userid=".add_escape_custom($userid).",
                                          username='".$agencyusername."',
                                          fname=".$form_fname.",
                                          mname=".$form_mname.",
                                          lname=".$form_lname.",
                                          organization=".invalue('form_organization').",    
                                          abookType=".invalue('form_abook_type').",
                                          street=".invalue('form_street').",
                                          streetb=".invalue('form_streetb').",
                                          city=".invalue('form_city').",
                                          state=".invalue('form_state').",
                                          zip=".invalue('form_zip').",
                                          street2=".invalue('form_street2').",
                                          streetb2=".invalue('form_streetb2').",
                                          city2=".invalue('form_city2').",
                                          state2=".invalue('form_state2').", 
                                          zip2=".invalue('form_zip2').",     
                                          phone=".invalue('form_phone').",
                                          fax=".invalue('form_fax').",
                                          phonew1=".invalue('form_phonew1').",    
                                          phonew2=".invalue('form_phonew2').",
                                          phonecell=".invalue('form_phonecell').",
                                          uemail=".invalue('form_email').",
                                          cangroup = '".$cangroup."',    
                                          practiceId=".$practiceId."    
                                          WHERE id=".$objref;
        $stmt = $sqlconfCentralDB->prepare($sql);
        $stmt->execute();
    endif;
    
  } 
  else {

   $userid = sqlInsert("INSERT INTO users ( " .
    "username, password, authorized, info, source, " .
    "title, fname, lname, mname,  " .
    "federaltaxid, federaldrugid, upin, facility, see_auth, active, npi, taxonomy, " .
    "specialty, organization, valedictory, assistant, billname, email, url, " .
    "street, streetb, city, state, zip, " .
    "street2, streetb2, city2, state2, zip2, " .
    "phone, phonew1, phonew2, phonecell, fax, notes, abook_type "            .
    ") VALUES ( "                        .
    "'', "                               . // username
    "'', "                               . // password
    "0, "                                . // authorized
    "'', "                               . // info
    "NULL, "                             . // source
    $form_title                   . ", " .
    $form_fname                   . ", " .
    $form_lname                   . ", " .
    $form_mname                   . ", " .
    invalue('form_federaltaxid')  . ", " .
    "'', "                               . // federaldrugid
    invalue('form_upin')          . ", " .
    "'', "                               . // facility
    "0, "                                . // see_auth
    "1, "                                . // active
    invalue('form_npi')           . ", " .
    invalue('form_taxonomy')      . ", " .
    invalue('form_specialty')     . ", " .
    invalue('form_organization')  . ", " .
    invalue('form_valedictory')   . ", " .
    invalue('form_assistant')     . ", " .
    "'', "                               . // billname
    invalue('form_email')         . ", " .
    invalue('form_url')           . ", " .
    invalue('form_street')        . ", " .
    invalue('form_streetb')       . ", " .
    invalue('form_city')          . ", " .
    invalue('form_state')         . ", " .
    invalue('form_zip')           . ", " .
    invalue('form_street2')       . ", " .
    invalue('form_streetb2')      . ", " .
    invalue('form_city2')         . ", " .
    invalue('form_state2')        . ", " .
    invalue('form_zip2')          . ", " .
    invalue('form_phone')         . ", " .
    invalue('form_phonew1')       . ", " .
    invalue('form_phonew2')       . ", " .
    invalue('form_phonecell')     . ", " .
    invalue('form_fax')           . ", " .
    invalue('form_notes')         . ", " .
    invalue('form_abook_type')    . " "  .
   ")");
    if($datasync == 1):
        // Check if this user has userid created in tbl_allcare_agencyportal
        $str = "SELECT portal_username FROM tbl_allcare_agencyportal WHERE uid=".add_escape_custom($userid);
        $query = sqlStatement($str);
        $row = sqlFetchArray($query);
        $agencyusername = '';
        if($row['portal_username'] !='') $agencyusername =  $row['portal_username'];
        
        // Check if cangroup is allowed or not from table tbl_addrbk_custom_attr_1to1
        $str = "SELECT cangroup FROM tbl_addrbk_custom_attr_1to1 WHERE addrbk_type_id=".add_escape_custom($userid);
        $queryq1 = sqlStatement($str);
        $rowq1 = sqlFetchArray($queryq1);
        $cangroup = $rowq1['cangroup'];
        
        $sql = "INSERT INTO allcareobjects (userid,username,practiceId,fname,mname,lname,uemail,organization,abookType,street,streetb,city,state,zip,street2,streetb2,city2,state2,zip2,phone,fax,phonew1,phonew2,phonecell,cangroup,objecttype) 
            VALUES(".add_escape_custom($userid).",'".$agencyusername."','".$practiceId."',".$form_fname.",".$form_mname.",".$form_lname.",".invalue('form_email').",".invalue('form_organization').",".invalue('form_abook_type').",
                ".invalue('form_street').",".invalue('form_streetb').",".invalue('form_city').",".invalue('form_state').",".invalue('form_zip').",
                ".invalue('form_street2').",".invalue('form_streetb2').",".invalue('form_city2').",".invalue('form_state2').",".invalue('form_zip2').",
                 ".invalue('form_phone').",".invalue('form_fax').",".invalue('form_phonew1').", ".invalue('form_phonew2').", ".invalue('form_phonecell').", '".$cangroup."', 'AddressBook')";
        $stmt = $sqlconfCentralDB->prepare($sql); 
        $stmt->execute();
        $id = $sqlconfCentralDB->lastInsertId();

        if($count == 0):
           sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".add_escape_custom($userid).",".$id.",'Addressbook')");
        endif;
    endif;    
 }
  
  
  //Address book folder Creation
$sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'"); 
$row = sqlFetchArray($sql);

$selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "' order by id desc");
$sel_rows = sqlFetchArray($selection);
    if($sel_rows['addrbk_folder_trigger']=='yes'){
      
        if($sel_rows['addrbk_parent_folder']!='')
            $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['addrbk_parent_folder']);
        else
            $parentid='root';   
                
  
        $sel_user=sqlStatement("select id from users where abook_type='".trim($_POST['form_abook_type'])."' order by id desc limit 0,1");
        $data1=sqlFetchArray($sel_user);

        $sel_user1=sqlStatement("select u.id from users u inner join tbl_addrbk_custom_attr_1to1 c on c.addrbk_type_id=u.id where abook_type='".trim($_POST['form_abook_type'])."' and addressbook_folder!='' order by id asc limit 0,1 ");
        $data2=sqlFetchArray($sel_user1);

        $sql=sqlStatement("select addressbook_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$data2['id']."' and addressbook_folder!=''  order by id desc limit 0,1");
        $data_fetch=sqlFetchArray($sql);

        if($data_fetch['addressbook_folder']==''){
            $sql12 = sqlStatement("select * from list_options where list_id='abook_type' and option_id='".$_POST['form_abook_type']."'");
            $row12 = sqlFetchArray($sql12); 
            $name=str_replace("/","_",str_replace(" ","",$row12['title']));
            $curl = curl_init();
            $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes'].'/'.$parentid.'/'.trim($name); 
            curl_setopt($curl,CURLOPT_URL, $form_url2); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   
            $result = curl_exec($curl);
            $resultant = $result;
            $val= explode(':',$resultant);
            curl_close($curl);
            if($val[0]!=''){
                $link='https://drive.google.com/drive/folders/'.$val[0];
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                 //   echo  "UPDATE `tbl_addrbk_custom_attr_1to1` SET `updated_date`='$today', addressbook_folder='".$val[0]."' WHERE addrbk_type_id= $value";  echo "<br>"; 
                       $update=sqlStatement("UPDATE `tbl_addrbk_custom_attr_1to1` SET `updated_date`='$today', addressbook_folder='".$val[0]."' WHERE addrbk_type_id= '".$data1['id']."'");
                }else {
                       // echo "insert into tbl_facility_custom_attr_1to1 (facilityid,facilityfolder,created_date)values($user_id,'".$val[0]."','$today')"; echo "<br>";
                        $ins=sqlStatement("insert into tbl_addrbk_custom_attr_1to1 (addrbk_type_id,created_date,addressbook_folder)values('".$data1['id']."','$today','".$val[0]."')");
                }
                $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$data1['id']."','$link','','','abook folder_created(during address book in EMR)','','agency')");
            }
            echo $folder_id=$val[0];
        }
        else {
             echo $folder_id=$data_fetch['addressbook_folder']; 
        }
        //organisation folder               
        if($sel_rows['org_folder_query']!='')
        {
            $query = $sel_rows['org_folder_query'] . " where abook_type='".trim($_POST['form_abook_type'])."' and id=".$data1['id'] ;
            $fsql = sqlStatement("$query");
            $frow = sqlFetchArray($fsql);
            $folder_name = str_replace(" ", "_", $frow['org']);  
        }
        if($folder_name!=''){
            $curl = curl_init();
            $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes'].'/'.$folder_id.'/'.$folder_name;
            curl_setopt($curl,CURLOPT_URL, $form_url2);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
            $result = curl_exec($curl);
            $resultant = $result;
            curl_close($curl);
            $val1= explode(':',$resultant);
            if($val1[0]!=''){
                $link='https://drive.google.com/drive/folders/'.$val1[0];
                $sel_user=sqlStatement("select id from users where abook_type='".trim($_POST['form_abook_type'])."' order by id desc limit 0,1");
                $data1=sqlFetchArray($sel_user);
                $sql=sqlStatement("select addressbook_folder from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$data1['id']."'");
                $data_fetch=sqlFetchArray($sql);
                $today = date("Y-m-d"); 
                if(!empty($data_fetch)){
                    $update=sqlStatement("UPDATE `tbl_addrbk_custom_attr_1to1` SET `updated_date`='$today', addressbook_folder='".$val[0]."' WHERE addrbk_type_id= '".$data1['id']."'");
                }else {
                   $ins=sqlStatement("insert into tbl_addrbk_custom_attr_1to1 (addrbk_type_id,created_date,addressbook_folder)values('".$data1['id']."','$today','".$val[0]."')");
                }
                $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$data1['id']."','$link','','','abook folder_created(during address book in EMR)','','agency')");
                //subfolders
                if($sel_rows['addrbk_sub_folder']=='yes'){
                    $subsql = sqlStatement("select * from list_options where list_id='AllcareAddrbkSubfolders'");
                    $link1='';
                    $rownum=mysql_num_rows($subsql);
                    if($rownum!=0){
                        while($subrow = sqlFetchArray($subsql)){
                            $curl = curl_init();
                            $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes'].'/'.$val1[0].'/'.str_replace(" ","_",$subrow['title']);
                            curl_setopt($curl,CURLOPT_URL, $form_url2);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                            $result = curl_exec($curl);
                            $subresultant = $result;
                            curl_close($curl);
                            $subval= explode(':',$subresultant);
                            $link1.='https://drive.google.com/drive/folders/'.$subval[0]."||";
                        }
                         $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$data1['id']."','$link1','','','subfolder_created(during Agency Creation in EMR)','','agency')");
                    }
                }
            }
        }
    }
 }

 else  if ($_POST['form_delete']) {

  if ($userid) {
   // Be careful not to delete internal users.
   sqlStatement("DELETE FROM users WHERE id = ? AND username = ''", array($userid));
  }

 }

 if ($_POST['form_save'] || $_POST['form_delete']) {
  // Close this window and redisplay the updated list.
  echo "<script language='JavaScript'>\n";
  if ($info_msg) echo " alert('".addslashes($info_msg)."');\n";
  echo " window.close();\n";
  echo " if (opener.refreshme) opener.refreshme();\n";
  echo "</script></body></html>\n";
  exit();
 }

 if ($userid) {
  $row = sqlQuery("SELECT * FROM users WHERE id = ?", array($userid));
 }

 if ($type) { // note this only happens when its new
  // Set up type
  $row['abook_type'] = $type;
 }

?>

<script language="JavaScript">
 $(document).ready(function() {
  // customize the form via the type options
  typeSelect("<?php echo attr($row['abook_type']); ?>");
 });
</script>

<form method='post' name='theform' action='addrbook_edit.php?userid=<?php echo attr($userid) ?>'>
<center>

<table border='0' width='100%'>

<?php if (acl_check('admin', 'practice' )) { // allow choose type option if have admin access ?>
 <tr>
  <td width='1%' nowrap><b><?php echo xlt('Type'); ?>:</b></td>
  <td>
<?php
 echo generate_select_list('form_abook_type', 'abook_type', $row['abook_type'], '', 'Unassigned', '', 'typeSelect(this.value)');
?>
  </td>
 </tr>
<?php } // end of if has admin access ?>

 <tr id="nameRow">
  <td width='1%' nowrap><b><?php echo xlt('Name'); ?>:</b></td>
  <td>
<?php
 generate_form_field(array('data_type'=>1,'field_id'=>'title','list_id'=>'titles','empty_title'=>' '), $row['title']);
?>
   <b><?php echo xlt('Last'); ?>:</b><input type='text' size='10' name='form_lname' class='inputtext'
     maxlength='50' value='<?php echo attr($row['lname']); ?>'/>&nbsp;
   <b><?php echo xlt('First'); ?>:</b> <input type='text' size='10' name='form_fname' class='inputtext'
     maxlength='50' value='<?php echo attr($row['fname']); ?>' />&nbsp;
   <b><?php echo xlt('Middle'); ?>:</b> <input type='text' size='4' name='form_mname' class='inputtext'
     maxlength='50' value='<?php echo attr($row['mname']); ?>' />
  </td>
 </tr>

 <tr id="specialtyRow">
  <td nowrap><b><?php echo xlt('Specialty'); ?>:</b></td>
  <td>
   <input type='text' size='40' name='form_specialty' maxlength='250'
    value='<?php echo attr($row['specialty']); ?>'
    style='width:100%' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('Organization'); ?>:</b></td>
  <td>
   <input type='text' size='40' name='form_organization' maxlength='250'
    value='<?php echo attr($row['organization']); ?>'
    style='width:100%' class='inputtext' />
  </td>
 </tr>

 <tr id="nameDirectorRow">
  <td width='1%' nowrap><b><?php echo xlt('Director Name'); ?>:</b></td>
  <td>
<?php
 generate_form_field(array('data_type'=>1,'field_id'=>'director_title','list_id'=>'titles','empty_title'=>' '), $row['title']);
?>
   <b><?php echo xlt('Last'); ?>:</b><input type='text' size='10' name='form_director_lname' class='inputtext'
     maxlength='50' value='<?php echo attr($row['lname']); ?>'/>&nbsp;
   <b><?php echo xlt('First'); ?>:</b> <input type='text' size='10' name='form_director_fname' class='inputtext'
     maxlength='50' value='<?php echo attr($row['fname']); ?>' />&nbsp;
   <b><?php echo xlt('Middle'); ?>:</b> <input type='text' size='4' name='form_director_mname' class='inputtext'
     maxlength='50' value='<?php echo attr($row['mname']); ?>' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('Valedictory'); ?>:</b></td>
  <td>
   <input type='text' size='40' name='form_valedictory' maxlength='250'
    value='<?php echo attr($row['valedictory']); ?>'
    style='width:100%' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('Home Phone'); ?>:</b></td>
  <td>
   <input type='text' size='11' name='form_phone' value='<?php echo attr($row['phone']); ?>'
    maxlength='30' class='inputtext' />&nbsp;
   <b><?php echo xlt('Mobile'); ?>:</b><input type='text' size='11' name='form_phonecell'
    maxlength='30' value='<?php echo attr($row['phonecell']); ?>' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('Work Phone'); ?>:</b></td>
  <td>
   <input type='text' size='11' name='form_phonew1' value='<?php echo attr($row['phonew1']); ?>'
    maxlength='30' class='inputtext' />&nbsp;
   <b><?php echo xlt('2nd'); ?>:</b><input type='text' size='11' name='form_phonew2' value='<?php echo attr($row['phonew2']); ?>'
    maxlength='30' class='inputtext' />&nbsp;
   <b><?php echo xlt('Fax'); ?>:</b> <input type='text' size='11' name='form_fax' value='<?php echo attr($row['fax']); ?>'
    maxlength='30' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('Assistant'); ?>:</b></td>
  <td>
   <input type='text' size='40' name='form_assistant' maxlength='250'
    value='<?php echo attr($row['assistant']); ?>'
    style='width:100%' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('Email'); ?>:</b></td>
  <td>
   <input type='text' size='40' name='form_email' maxlength='250'
    value='<?php echo attr($row['email']); ?>'
    style='width:100%' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('Website'); ?>:</b></td>
  <td>
   <input type='text' size='40' name='form_url' maxlength='250'
    value='<?php echo attr($row['url']); ?>'
    style='width:100%' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('Main Address'); ?>:</b></td>
  <td>
   <input type='text' size='40' name='form_street' maxlength='60'
    value='<?php echo attr($row['street']); ?>'
    style='width:100%' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap>&nbsp;</td>
  <td>
   <input type='text' size='40' name='form_streetb' maxlength='60'
    value='<?php echo attr($row['streetb']); ?>'
    style='width:100%' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('City'); ?>:</b></td>
  <td>
   <input type='text' size='10' name='form_city' maxlength='30'
    value='<?php echo attr($row['city']); ?>' class='inputtext' />&nbsp;
   <b><?php echo xlt('State')."/".xlt('county'); ?>:</b> <input type='text' size='10' name='form_state' maxlength='30'
    value='<?php echo attr($row['state']); ?>' class='inputtext' />&nbsp;
   <b><?php echo xlt('Postal code'); ?>:</b> <input type='text' size='10' name='form_zip' maxlength='20'
    value='<?php echo attr($row['zip']); ?>' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('Alt Address'); ?>:</b></td>
  <td>
   <input type='text' size='40' name='form_street2' maxlength='60'
    value='<?php echo attr($row['street2']); ?>'
    style='width:100%' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap>&nbsp;</td>
  <td>
   <input type='text' size='40' name='form_streetb2' maxlength='60'
    value='<?php echo attr($row['streetb2']); ?>'
    style='width:100%' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('City'); ?>:</b></td>
  <td>
   <input type='text' size='10' name='form_city2' maxlength='30'
    value='<?php echo attr($row['city2']); ?>' class='inputtext' />&nbsp;
   <b><?php echo xlt('State')."/".xlt('county'); ?>:</b> <input type='text' size='10' name='form_state2' maxlength='30'
    value='<?php echo attr($row['state2']); ?>' class='inputtext' />&nbsp;
   <b><?php echo xlt('Postal code'); ?>:</b> <input type='text' size='10' name='form_zip2' maxlength='20'
    value='<?php echo attr($row['zip2']); ?>' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('UPIN'); ?>:</b></td>
  <td>
   <input type='text' size='6' name='form_upin' maxlength='6'
    value='<?php echo attr($row['upin']); ?>' class='inputtext' />&nbsp;
   <b><?php echo xlt('NPI'); ?>:</b> <input type='text' size='10' name='form_npi' maxlength='10'
    value='<?php echo attr($row['npi']); ?>' class='inputtext' />&nbsp;
   <b><?php echo xlt('TIN'); ?>:</b> <input type='text' size='10' name='form_federaltaxid' maxlength='10'
    value='<?php echo attr($row['federaltaxid']); ?>' class='inputtext' />&nbsp;
   <b><?php echo xlt('Taxonomy'); ?>:</b> <input type='text' size='10' name='form_taxonomy' maxlength='10'
    value='<?php echo attr($row['taxonomy']); ?>' class='inputtext' />
  </td>
 </tr>

 <tr>
  <td nowrap><b><?php echo xlt('Notes'); ?>:</b></td>
  <td>
   <textarea rows='3' cols='40' name='form_notes' style='width:100%'
    wrap='virtual' class='inputtext' /><?php echo text($row['notes']) ?></textarea>
  </td>
 </tr>

</table>

<br />

<input type='submit' name='form_save' value='<?php echo xla('Save'); ?>' />

<?php if ($userid && !$row['username']) { ?>
&nbsp;
<input type='submit' name='form_delete' value='<?php echo xla('Delete'); ?>' style='color:red' />
<?php } ?>

&nbsp;
<input type='button' value='<?php echo xla('Cancel'); ?>' onclick='window.close()' />
</p>

</center>
</form>
</body>
</html>
