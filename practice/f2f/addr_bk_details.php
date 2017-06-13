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
$provider=$_REQUEST['provider'];
$facility=$_REQUEST['facility'];
$pharmacy=$_REQUEST['pharmacy'];
$payer=$_REQUEST['payer'];


?>
<html>
    <head>
    </head>
    <body>
        <?php if($org!='' && $org!='0') {
            //for address book details
            $users=sqlStatement("select abook_type,organization,assistant,title,fname,lname,mname,phone,phonecell,phonew1,phonew2,fax,email,street,city  from users where id=$org");
            $res=sqlFetchArray($users); 
        ?>
        <div> 
            <fieldset style="width:400px;">
                <legend>Address Book Details:</legend>
            <table>
                <tr><td><b>Address book Type:</b></td><td><?php  $type_sql_row = sqlQuery("SELECT `title` FROM `list_options` WHERE `list_id` = 'abook_type' AND `option_id` = ?", array(trim($res['abook_type'])));echo $type_sql_row['title']; ?></td></tr>
                <?php if($res['organization']!='') { ?><tr><td><b>Organization:</b></td><td><?php  echo $res['organization']; ?></td></tr> <?php } ?>
                <tr><td><b>Name:</b></td><td><?php echo $res['title']." ".$res['fname']." ".$res['lname']." ".$res['mname']; ?></td></tr>
                <?php if($res['phone']!='') { ?><tr><td><b>Home Phone:</b></td><td><?php echo $res['phone']; ?></td></tr><?php } ?>
                <?php if($res['phonecell']!='') { ?><tr><td><b>Mobile:</b></td><td><?php echo $res['phonecell']; ?></td></tr><?php } ?>
                <?php if($res['phonew1']!='') { ?><tr><td><b>Work Phone:</b></td><td><?php echo $res['phonew1']; ?></td></tr><?php } ?>
                <?php if($res['assistant']!='') { ?><tr><td><b>Assistant:</b></td><td><?php echo $res['assistant']; ?></td></tr><?php } ?>
                <?php if($res['email']!='') { ?><tr><td><b>Email:</b></td><td><?php echo $res['email']; ?></td></tr><?php } ?>
                <?php if($res['fax']!='') { ?><tr><td><b>Fax:</b></td><td><?php echo $res['fax']; ?></td></tr><?php } ?>
                <?php if($res['street']!='') { ?><tr><td><b>Street:</b></td><td><?php echo $res['street']; ?></td></tr><?php } ?>
                <?php if($res['city']!='') { ?><tr><td><b>City:</b></td><td><?php echo $res['city']; ?></td></tr><?php } ?>
                
            </table>
           </fieldset>     
          </div>
        <?php }else if($provider!='' && $provider!='0'){ 
            //for providers
            $pro=sqlStatement("SELECT id, fname, lname, specialty,fax, email,street,city,phone,phonecell,phonew1 FROM users " .
            "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
            "AND authorized = 1 AND id=$provider " .
            "ORDER BY lname, fname");
            $pro_res=sqlFetchArray($pro);
        ?>
        <div> 
            <fieldset style="width:400px;">
                <legend>Provider Details:</legend>
            <table>
                <tr><td><b>Name:</b></td><td><?php echo $pro_res['title']." ".$pro_res['fname']." ".$pro_res['lname']." ".$pro_res['mname']; ?></td></tr>
                 <?php if($pro_res['specialty']!='') { ?><tr><td><b>Specialty:</b></td><td><?php echo $pro_res['specialty']; ?></td></tr><?php } ?>
                <?php if($pro_res['phone']!='') { ?><tr><td><b>Home Phone:</b></td><td><?php echo $pro_res['phone']; ?></td></tr><?php } ?>
                <?php if($pro_res['phonecell']!='') { ?><tr><td><b>Mobile:</b></td><td><?php echo $pro_res['phonecell']; ?></td></tr><?php } ?>
                <?php if($pro_res['phonew1']!='') { ?><tr><td><b>Work Phone:</b></td><td><?php echo $pro_res['phonew1']; ?></td></tr><?php } ?>
                <?php if($pro_res['email']!='') { ?><tr><td><b>Email:</b></td><td><?php echo $pro_res['email']; ?></td></tr><?php } ?>
                <?php if($pro_res['fax']!='') { ?><tr><td><b>Fax:</b></td><td><?php echo $pro_res['fax']; ?></td></tr><?php } ?>
                <?php if($pro_res['street']!='') { ?><tr><td><b>Street:</b></td><td><?php echo $pro_res['street']; ?></td></tr><?php } ?>
                <?php if($pro_res['city']!='') { ?><tr><td><b>City:</b></td><td><?php echo $pro_res['city']; ?></td></tr><?php } ?>
                
            </table>
           </fieldset>     
          </div>
        <?php }else if($facility!='' && $facility!='0'){ 
            $query =sqlStatement( "SELECT name,phone,fax,street, city,state,postal_code,country_code,email FROM facility where id=$facility ORDER BY name");
            $fres=sqlFetchArray($query);
        ?>
        <div> 
            <fieldset style="width:400px;">
                <legend>Facility Details:</legend>
            <table>
                <?php if($fres['name']!='') { ?><tr><td><b>Name:</b></td><td><?php echo $fres['name']; ?></td></tr><?php } ?>
                <?php if($fres['phone']!='') { ?><tr><td><b>Mobile:</b></td><td><?php echo $fres['phone']; ?></td></tr><?php } ?>
                <?php if($fres['email']!='') { ?><tr><td><b>Email:</b></td><td><?php echo $fres['email']; ?></td></tr><?php } ?>
                <?php if($fres['fax']!='') { ?><tr><td><b>Fax:</b></td><td><?php echo $fres['fax']; ?></td></tr><?php } ?>
                <?php if($fres['street']!='') { ?><tr><td><b>Street:</b></td><td><?php echo $fres['street']; ?></td></tr><?php } ?>
                <?php if($fres['city']!='') { ?><tr><td><b>City:</b></td><td><?php echo $fres['city']; ?></td></tr><?php } ?>
                <?php if($fres['state']!='') { ?><tr><td><b>State:</b></td><td><?php echo $fres['state']; ?></td></tr><?php } ?>
                <?php if($fres['postal_code']!='') { ?><tr><td><b>Postal Code:</b></td><td><?php echo $fres['postal_code']; ?></td></tr><?php } ?>
                <?php if($fres['country_code']!='') { ?><tr><td><b>Country Code:</b></td><td><?php echo $fres['country_code']; ?></td></tr><?php } ?>
                
            </table>
           </fieldset>     
          </div>
        <?php }else if($pharmacy!='' && $pharmacy!='0') { 
             $pres =sqlStatement("SELECT d.id, d.name, d.email,a.line1, a.city, a.state,a.zip,a.country, " .
             "p.area_code, p.prefix, p.number FROM pharmacies AS d " .
             "LEFT OUTER JOIN addresses AS a ON a.foreign_id = d.id " .
             "LEFT OUTER JOIN phone_numbers AS p ON p.foreign_id = d.id " .
             "AND p.type = 2 where d.id=$pharmacy " .
             "ORDER BY name, area_code, prefix, number");
             $phres=sqlFetchArray($pres);
        ?>
        <div> 
            <fieldset style="width:400px;">
                <legend>Pharmacy Details:</legend>
            <table>
                <?php if($phres['name']!='') { ?><tr><td><b>Name:</b></td><td><?php echo $phres['name']; ?></td></tr><?php } ?>
                <?php if($phres['email']!='') { ?><tr><td><b>Email:</b></td><td><?php echo $phres['email']; ?></td></tr><?php } ?>
                <?php if($phres['city']!='') { ?><tr><td><b>City:</b></td><td><?php echo $phres['city']; ?></td></tr><?php } ?>
                <?php if($phres['state']!='') { ?><tr><td><b>State:</b></td><td><?php echo $phres['state']; ?></td></tr><?php } ?>
                <?php if($phres['zip']!='') { ?><tr><td><b>Postal Code:</b></td><td><?php echo $phres['zip']; ?></td></tr><?php } ?>
                <?php if($phres['country']!='') { ?><tr><td><b>Country Code:</b></td><td><?php echo $phres['country']; ?></td></tr><?php } ?>
                
            </table>
           </fieldset>     
         </div>
        <?php } else if($payer!='' && $payer!='0') { 
            $query1 = sqlStatement("SELECT id, name ,x12_default_partner_id FROM insurance_companies where id=$payer");
            $ires = sqlFetchArray($query1);
        ?>
        <div> 
            <fieldset style="width:400px;">
                <legend>Payer Details:</legend>
            <table>
                <?php if($ires['name']!='') { ?><tr><td><b>Name:</b></td><td><?php echo $ires['name']; ?></td></tr><?php } ?>
                <?php if($ires['x12_default_partner_id']!='') {  $query2 = sqlStatement("SELECT name FROM x12_partners where id='".$ires['x12_default_partner_id']."' ");
                      $xres = sqlFetchArray($query2);
                ?><tr><td><b>x12 Default Partner:</b></td><td><?php echo $xres['name']; ?></td></tr><?php } ?>
            </table>
           </fieldset>     
         </div>
        <?php } ?>
    </body>
</html>