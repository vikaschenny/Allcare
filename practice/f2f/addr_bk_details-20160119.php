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

$org=$_REQUEST['org'];
if($org!=''){
$users=sqlStatement("select abook_type,organization,assistant,title,fname,lname,mname,phone,phonecell,phonew1,phonew2,fax,email,street,city  from users where id=$org");
$res=sqlFetchArray($users);
}
?>
<html>
    <head>
    </head>
    <body>
        <div>
            <fieldset style="width:400px;">
                <legend>Address Book Details:</legend>
            <table>
                <tr><td><b>Address book Type:</b></td><td><?php $type_sql_row = sqlQuery("SELECT `title` FROM `list_options` WHERE `list_id` = 'abook_type' AND `option_id` = ?", array(trim($res['abook_type'])));echo $type_sql_row['title'];  ?></td></tr>
                <tr><td><b>Organization:</b></td><td><?php echo $res['organization']; ?></td></tr>
                <tr><td><b>Name:</b></td><td><?php echo $res['title']." ".$res['fname']." ".$res['lname']." ".$res['mname']; ?></td></tr>
                <tr><td><b>Home Phone:</b></td><td><?php echo $res['phone']; ?></td></tr>
                <tr><td><b>Mobile:</b></td><td><?php echo $res['phonecell']; ?></td></tr>
                <tr><td><b>Work Phone:</b></td><td><?php echo $res['phonew1']; ?></td></tr>
                <tr><td><b>Assistant:</b></td><td><?php echo $res['assistant']; ?></td></tr>
                <tr><td><b>Email:</b></td><td><?php echo $res['email']; ?></td></tr>
                <tr><td><b>Fax:</b></td><td><?php echo $res['fax']; ?></td></tr>
                <tr><td><b>Street:</b></td><td><?php echo $res['street']; ?></td></tr>
                <tr><td><b>City:</b></td><td><?php echo $res['city']; ?></td></tr>
                
            </table>
           </fieldset>     
            
        </div>
    </body>
</html>