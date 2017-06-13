<?php

/*
echo "<br>post 55 ";
        print_r($_POST);
        echo "<br>post 77 ";die;*/

 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true;

    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong
    $landingpage = "index.php?site=".$_SESSION['site_id'];	
    //

    // kick out if patient not authenticated
    //if ( isset($_SESSION['uid']) && isset($_SESSION['patient_portal_onsite']) ) {
    if ( isset($_SESSION['portal_username']) ) {    
    $provider = $_SESSION['portal_username'];
    }
    else {
            session_destroy();
    header('Location: '.$landingpage.'&w');
            exit;
    }
    //
    $newdata_stat=unserialize($_REQUEST['data1']);
print_r($newdata_stat);
$ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
 include_once('../../interface/globals.php');
 require_once("$srcdir/options.inc.php");
 echo $grp_stat=$_REQUEST['grpstat'];
echo $form_id=$_REQUEST['form_id'];
echo $encounter=$_REQUEST['encounter'];
if($_REQUEST['mode']=='save'){
  
 function updateENC_forms($id, $new, $create,$ecounter1,$pid1)
{
 
  $db_id = 0;
  print_r($new);
  
  if ($create) {
    /*$sql = "INSERT INTO lbf_data SET  form_id = $id";
    foreach ($new as $key => $value) {
      if ($key == 'id') continue;
      $sql .= ", `$key` = " . pdValueOrNull($key, $value);
    }*/

        
     foreach ($new as $key => $value) {
      
        if($value!=''){ 
         
          
         sqlInsert("INSERT INTO lbf_data (form_id,field_id,field_value) VALUES (?,?,?)", array($id,$key,$value));
        }
      } 
    
    $db_id = 1;
  
    
  }
  else {
       //echo $db_id = $new['id'];
        foreach ($new as $key => $value) {
            if($value!=''){
              //echo "UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,$key,$id);
               sqlStatement("UPDATE lbf_data SET  `field_value` = ? WHERE  field_id= ? AND form_id = ?",array($value,$key,$id));
            }else {
               sqlStatement("delete  from lbf_data where form_id=$id AND field_id='$key'");
            }
        }
        $db_id = 1;
        
    }
  return $db_id;
}
 
 





 $res1=sqlstatement("select * from lbf_data lb INNER JOIN layout_options l ON l.field_id=lb.field_id where lb.form_id='$form_id' AND l.form_id='LBF2' AND l.group_name LIKE '%$grp_stat%' AND lb.field_id LIKE '%$field_id%' order by seq");
 $res_row1=sqlFetchArray($res1);
 if(!empty($res_row1)){
    
     updateENC_forms($form_id, $newdata_stat['lbf_data'] ,$create=false,$encounter,$pid);
 }else{
     
      updateENC_forms($form_id, $newdata_stat['lbf_data'] ,$create=true,$encounter,$pid);
 }
}

?>