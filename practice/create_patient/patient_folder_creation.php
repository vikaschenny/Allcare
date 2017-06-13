<?php
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

    $ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');

 // to get configured email
    $sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
    $row = sqlFetchArray($sql);

    $selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "' order by id desc");
    $sel_rows = sqlFetchArray($selection);
    
    if($sel_rows['user_parent_folder']!='')
     $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['parent_folder']);
    else
     $parentid='root';   

    if($sel_rows['patient_folder']!='')
    {
        $query = $sel_rows['patient_folder'] . " where pid=" . $_REQUEST['pid'] ;
        $fsql = sqlStatement("$query");
        $frow = sqlFetchArray($fsql);
        $folder_name = str_replace(" ", "", $frow['Patient_folder']);  
    }
    $curl = curl_init();
    $form_url2 = 'https://'.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes'].'/'.$parentid.'/'.$folder_name;
    curl_setopt($curl,CURLOPT_URL, $form_url2);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
    $result = curl_exec($curl);
    $resultant = $result;
    curl_close($curl);
    $val= explode(':',$resultant);
    if($val[0]!=''){
         $link='https://drive.google.com/drive/folders/'.$val[0];
             $ins=sqlStatement("update patient_data SET patient_folder='$link' where pid=".$_REQUEST['pid']);
             $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID)values(now(),'".$_SESSION['portal_username']."','".$row['notes']."','".$_REQUEST['encounter']."','".$_REQUEST['pid']."','$link','','','folder_created(during patient Creation in provider portal)','')");
        echo "sucess";
    }
?>