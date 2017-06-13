<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */

require_once("verify_session.php");
require_once("../library/formdata.inc.php");
require_once("../library/globals.inc.php");

require_once("../library/sqlCentralDB.inc");
global $sqlconfCentralDB;

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

$str1 = "SELECT u.id, u.username, u.fname, u.lname, u.mname, GROUP_CONCAT(gag.name SEPARATOR ',') AS acl_role_name, lp.title as abookType,u.organization as organization, u.facility,
        u.facility_id,u.email as addremail,uc.email,u.street,u.streetb,u.city,u.state,u.zip,u.street2,u.streetb2,u.city2,u.state2,u.zip2,
        u.phone,u.fax,u.phonew1,u.phonew2,u.phonecell
        FROM users u
        LEFT JOIN tbl_user_custom_attr_1to1 uc ON u.id=uc.userid
        LEFT JOIN list_options lp ON u.abook_type = lp.option_id and lp.list_id = 'abook_type'
        LEFT JOIN gacl_aro ga ON ga.value = u.username
        LEFT JOIN gacl_groups_aro_map ggam ON ggam.aro_id = ga.id
        LEFT JOIN gacl_aro_groups gag ON gag.id = ggam.group_id GROUP BY u.id";
$sql1 = sqlStatement($str1);


?>
<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
<script src="js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
<script src="js/responsive_datatable/version1.0/jquery.dataTables.min.js"></script>
<script src="js/responsive_datatable/version1.0/dataTables.bootstrap.min.js"></script>
<script type='text/javascript' src='js/responsive_datatable/dataTables.tableTools.js'></script>
<script src="js/responsive_datatable/version1.0/dataTables.responsive.min.js"></script>
<script type='text/javascript' src='js/responsive_datatable/dataTables.bootstrap.js'></script>
<script>
 $(document).ready(function() {
	 var table = $('#datasync').dataTable({
            "iDisplayLength": 10,
             dom: 'T<\"clear\">lfrtip',
           tableTools: {
                 "sSwfPath": "../interface/swf/copy_csv_xls_pdf.swf",
                aButtons: [
                    {
                        sExtends: "xls",
                        sButtonText: "Save to Excel",
                        sFileName: $('#openemrTitle').val() + " datasync "+ $('#currTime').val() +".csv"
                    }
                ]
            }
        });
 });
</script>

<table cellspacing='0' width='100%' id="datasync" class="table table-striped table-bordered nowrap" style="display:block;">
    <thead>
 <tr>
 <th>
     <?php xl('Object Type','e'); ?>
 </th>   
  <th>
   <?php xl('UserID','e'); ?>
  </th>
  <th>
   <?php xl('Username','e'); ?>
  </th>
  <th>
   <?php xl('Firstname','e'); ?>
  </th>
  <th>
   <?php xl('Middlename','e'); ?>
  </th>
  <th>
   <?php xl('Lastname','e'); ?>
  </th>
  <th>
   <?php xl('User Roles','e'); ?>
  </th>
  <th>
   <?php xl('Address book Type','e'); ?>
  </th>
  <th>
   <?php xl('Organization','e'); ?>
  </th>
  <th>
   <?php xl('User Facility Name','e'); ?>
  </th>
  <th>
   <?php xl('User Facility ID','e'); ?>
  </th>
  <th>
   <?php xl('User Email','e'); ?>
  </th>
   <th>
   <?php xl('Insurance','e'); ?>
  </th>
  <th>
   <?php xl('Street','e'); ?>
  </th>
  <th>
   <?php xl('Streetb','e'); ?>
  </th>
  <th>
   <?php xl('City','e'); ?>
  </th>
  <th>
   <?php xl('State','e'); ?>
  </th>
  <th>
   <?php xl('Zip','e'); ?>
  </th>
  <th>
   <?php xl('Country','e'); ?>
  </th>
  <th>
   <?php xl('Street2','e'); ?>
  </th>
  <th>
   <?php xl('Streetb2','e'); ?>
  </th>
  <th>
   <?php xl('City2','e'); ?>
  </th>
  <th>
   <?php xl('State2','e'); ?>
  </th>
  <th>
   <?php xl('Zip2','e'); ?>
  </th>
  <th>
   <?php xl('Home','e'); ?>
  </th>
  <th>
   <?php xl('Fax','e'); ?>
  </th>
  <th>
   <?php xl('Work Phone','e'); ?>
  </th>
  <th>
   <?php xl('Work Phone2','e'); ?>
  </th>
  <th>
   <?php xl('Mobile','e'); ?>
  </th>
  <th>
   <?php xl('Website','e'); ?>
  </th>
  <th>
   <?php xl('Service Location','e'); ?>
  </th>
  <th>
   <?php xl('Billing Location','e'); ?>
  </th>
  <th>
   <?php xl('POS Code','e'); ?>
  </th>
  <th>
   <?php xl('Attn','e'); ?>
  </th>
  <th>
   <?php xl('Domain Identifier','e'); ?>
  </th>
  <th>
   <?php xl('Facility NPI','e'); ?>
  </th>
  <th>
   <?php xl('Primary Business Entity','e'); ?>
  </th>
  <th>
   <?php xl('Pharmacy','e'); ?>
  </th>
  
  <th>
   <?php xl('Patient ID','e'); ?>
  </th>
  
  <th>
   <?php xl('Patient Mothersname','e'); ?>
  </th>
  <th>
   <?php xl('Patient Guardiansname','e'); ?>
  </th>
  <th>
   <?php xl('Patient DOB','e'); ?>
  </th>
  <th>
   <?php xl('Patient Sex','e'); ?>
  </th>
  <th>
   <?php xl('Patient SS','e'); ?>
  </th>
  <th>
   <?php xl('Patient Pharmacy name','e'); ?>
  </th>
  <th>
   <?php xl('Patient Provider name','e'); ?>
  </th>
  <th>
   <?php xl('Patient Referring Provider name','e'); ?>
  </th>
  <th>
   <?php xl('Patient Folder','e'); ?>
  </th>
  <th>
   <?php xl('Patient Facility','e'); ?>
  </th>
  <th>
   <?php xl('Patient HHAgency','e'); ?>
  </th>
  <th>
   <?php xl('Patient Living Facility','e'); ?>
  </th>
  
</tr>
    </thead>
    <tbody>
        
    <?php
    while($row1 = sqlFetchArray($sql1)):
        if($row1['username'] == '' && $row1['id'] !=0):
            $obj = 'AddressBook';
        else:
            $obj = 'User';
        endif;
        
        // Get user email from user custom attribute table
        $str = "SELECT email,cangroup FROM tbl_user_custom_attr_1to1 WHERE userid='". $row1['id'] ."'";
        $query = sqlStatement($str);
        $useremail = $cangroup = '';
        while($row = sqlFetchArray($query)):
            $useremail = $row['email'];
            $cangroup = $row['cangroup'];
        endwhile;
        
        if($obj == 'AddressBook'):
            $useremail = $row1['addremail'];
            // Check if this user has userid created in tbl_allcare_agencyportal
            $str = "SELECT portal_username FROM tbl_allcare_agencyportal WHERE uid=".$row1['id'];
            $query = sqlStatement($str);
            $rowq = sqlFetchArray($query);
            $agencyusername = '';
            if($rowq['portal_username'] !='') $agencyusername =  $rowq['portal_username'];
            
            // Check if cangroup is allowed or not from table tbl_addrbk_custom_attr_1to1
            $str = "SELECT cangroup FROM tbl_addrbk_custom_attr_1to1 WHERE addrbk_type_id=".$row1['id'];
            $queryq1 = sqlStatement($str);
            $rowq1 = sqlFetchArray($queryq1);
            $cangroup = $rowq1['cangroup'];
        endif;
        
        $sql2 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".$row1['id']." AND objecttype = '".$obj."'";
        $r = sqlStatement($sql2);
        $objref = "";
        while($row2 = sqlFetchArray($r)):
           $objref = $row2['objectref'];
        endwhile;
        $count = sqlNumRows($r);
        if($datasync == 1):
                $usname = '';
                if($obj == 'AddressBook'):
                    $usname = $agencyusername;
                endif;
                if($obj == 'User'):
                    $usname = $row1['username'];
                endif;
            if($count):
                $sql = "UPDATE allcareobjects SET userid=".$row1['id'].", 
                                                  username='".$usname."',
                                                  fname='".$row1['fname']."',
                                                  mname='".$row1['mname']."',
                                                  lname='".$row1['lname']."',
                                                  urole='".$row1['acl_role_name']."',
                                                  organization='".$row1['organization']."',    
                                                  ufacility='".$row1['facility']."',
                                                  ufacilityid='".$row1['facility_id']."',
                                                  uemail='".$useremail."',    
                                                  abookType='".$row1['abookType']."',
                                                  street='".$row1['street']."',
                                                  streetb='".$row1['streetb']."',
                                                  city='".$row1['city']."',
                                                  state='".$row1['state']."',
                                                  zip='".$row1['zip']."',
                                                  street2='".$row1['street2']."',
                                                  streetb2='".$row1['streetb2']."',
                                                  city2='".$row1['city2']."',
                                                  state2='".$row1['state2']."', 
                                                  zip2='".$row1['zip2']."',     
                                                  phone='".$row1['phone']."',
                                                  fax='".$row1['fax']."',
                                                  phonew1='".$row1['phonew1']."',    
                                                  phonew2='".$row1['phonew2']."',
                                                  phonecell='".$row1['phonecell']."',
                                                  cangroup='".$cangroup."',    
                                                  practiceId='".$practiceId."'    
                                                  WHERE id=".$objref;
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute(); 
            else:
                $sql = "INSERT INTO allcareobjects (userid,practiceId,username,fname,mname,lname,urole,organization,abookType,ufacility,ufacilityid,uemail,street,streetb,city,state,zip,street2,streetb2,city2,state2,zip2,phone,fax,phonew1,phonew2,phonecell,cangroup,objecttype) 
                    VALUES(".$row1['id'].",'".$practiceId."','".$usname."','".$row1['fname']."','".$row1['mname']."','".$row1['lname']."','".$row1['acl_role_name']."','".$row1['organization']."','".$row1['abookType']."','".$row1['facility']."','".$row1['facility_id']."','".$useremail."',
                        '".$row1['street']."','".$row1['streetb']."','".$row1['city']."','".$row1['state']."','".$row1['zip']."',
                        '".$row1['street2']."','".$row1['streetb2']."','".$row1['city2']."','".$row1['state2']."','".$row1['zip2']."',
                         '".$row1['phone']."','".$row1['fax']."','".$row1['phonew1']."', '".$row1['phonew2']."', '".$row1['phonecell']."','".$cangroup."',  '".$obj."')";
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute();
                $id = $sqlconfCentralDB->lastInsertId();
            endif;
        endif;    
        
        
    ?>
        <tr>
        <td><?php echo $obj; ?></td>    
        <td><?php echo $row1['id']; ?></td>
        <td><?php echo $row1['username']; ?></td>
        <td><?php echo $row1['fname']; ?></td>
        <td><?php echo $row1['mname']; ?></td>
        <td><?php echo $row1['lname']; ?></td>
        <td><?php echo $row1['acl_role_name']; ?></td>
        <td><?php echo $row1['abookType']; ?></td>
        <td><?php echo $row1['organization']; ?></td>
        <td><?php echo $row1['facility']; ?></td>
        <td><?php echo $row1['facility_id']; ?></td>
        <td><?php echo $row1['email']; ?></td>
        <td>&nbsp;</td>
        <td><?php echo $row1['street']; ?></td>
        <td><?php echo $row1['streetb']; ?></td>
        <td><?php echo $row1['city']; ?></td>
        <td><?php echo $row1['state']; ?></td>
        <td><?php echo $row1['zip']; ?></td>
        <td>&nbsp;</td>
        <td><?php echo $row1['street2']; ?></td>
        <td><?php echo $row1['streetb2']; ?></td>
        <td><?php echo $row1['city2']; ?></td>
        <td><?php echo $row1['state2']; ?></td>
        <td><?php echo $row1['zip2']; ?></td>
        <td><?php echo $row1['phone']; ?></td>
        <td><?php echo $row1['fax']; ?></td>
        <td><?php echo $row1['phonew1']; ?></td>
        <td><?php echo $row1['phonew2']; ?></td>
        <td><?php echo $row1['phonecell']; ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        </tr> 
    <?php
        if($datasync == 1):
            if($count == 0):
                sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".$row1['id'].",".$id.",'".$obj."')");
            endif;
        endif;    
    endwhile;
    
    $str3 = "SELECT inc.id,inc.name,ad.line1,ad.line2,ad.city,ad.state,ad.zip,ad.country FROM insurance_companies inc LEFT JOIN addresses ad ON inc.id = ad.foreign_id";
    $query3 = sqlStatement($str3);
    
    while($row3 = sqlFetchArray($query3)):
        $obj = 'Insurance';
    
        $sql2 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".$row3['id']." AND objecttype = '".$obj."'";
        $r = sqlStatement($sql2);
        $objref = "";
        while($row2 = sqlFetchArray($r)):
           $objref = $row2['objectref'];
        endwhile;
        $count = sqlNumRows($r);
        if($datasync == 1):
            if($count):
                $sql = "UPDATE allcareobjects SET insurance_comp_id='".$row3['id']."',
                                                  insurance_comp='".$row3['name']."',    
                                                  street='".$row3['line1']."',
                                                  streetb='".$row3['line2']."',
                                                  city='".$row3['city']."',
                                                  state='".$row3['state']."',
                                                  zip='".$row3['zip']."',
                                                  country='".$row3['country']."',
                                                  practiceId='".$practiceId."'  
                                                  WHERE id=".$objref;
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute(); 
            else:
                $sql = "INSERT INTO allcareobjects (practiceId,insurance_comp_id,insurance_comp,street,streetb,city,state,zip,country,objecttype) 
                    VALUES('".$practiceId."',".$row3['id'].",'".$row3['name']."','".$row3['line1']."','".$row3['line2']."','".$row3['city']."','".$row3['state']."','".$row3['zip']."',
                        '".$row3['country']."',  '".$obj."')";
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute();
                $id = $sqlconfCentralDB->lastInsertId();
            endif;
        endif;
        ?>
        <tr>
        <td><?php echo $obj; ?></td>    
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?php echo $row3['name']; ?></td>
        <td><?php echo $row3['line1']; ?></td>
        <td><?php echo $row3['line2']; ?></td>
        <td><?php echo $row3['city']; ?></td>
        <td><?php echo $row3['state']; ?></td>
        <td><?php echo $row3['zip']; ?></td>
        <td><?php echo $row3['country']; ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
        <?php
        if($datasync == 1):
            if($count == 0):
                sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".$row3['id'].",".$id.",'".$obj."')");
            endif;
        endif;    
    endwhile;
    
    $str4 = "SELECT * FROM facility";
    $query4 = sqlStatement($str4);
    
    while($row4 = sqlFetchArray($query4)):
        $obj = 'Facility';
    
        $sql2 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".$row4['id']." AND objecttype = '".$obj."'";
        $r = sqlStatement($sql2);
        $objref = "";
        while($row2 = sqlFetchArray($r)):
           $objref = $row2['objectref'];
        endwhile;
        $count = sqlNumRows($r);
        if($datasync == 1):
            if($count):
                $sql = "UPDATE allcareobjects SET facility_id='".$row4['id']."',
                                                  facility_name='".$row4['name']."',
                                                  phone='".$row4['phone']."',
                                                  fax='".$row4['fax']."',    
                                                  street='".$row4['street']."',
                                                  city='".$row4['city']."',
                                                  state='".$row4['state']."',
                                                  zip='".$row4['postal_code']."',
                                                  country='".$row4['country_code']."',
                                                  website='".$row4['website']."',
                                                  uemail='".$row4['email']."',
                                                  service_location='".$row4['service_location']."',
                                                  billing_location='".$row4['billing_location']."',
                                                  pos_code='".$row4['pos_code']."',
                                                  attn='".$row4['attn']."',
                                                  domain_identifier='".$row4['domain_identifier']."',
                                                  facility_npi='".$row4['facility_npi']."',
                                                  primary_business_entity='".$row4['primary_business_entity']."',
                                                  practiceId='".$practiceId."'      
                                                  WHERE id=".$objref;
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute(); 
            else:
                $sql = "INSERT INTO allcareobjects (practiceId,facility_id,facility_name,phone,fax,street,city,state,zip,country,website,uemail,service_location,billing_location,
                                                    pos_code,attn,domain_identifier,facility_npi,primary_business_entity,objecttype) 
                    VALUES('".$practiceId."',".$row4['id'].",'".$row4['name']."','".$row4['phone']."','".$row4['fax']."','".$row4['street']."','".$row4['city']."','".$row4['state']."','".$row4['postal_code']."','".$row4['country_code']."',
                        '".$row4['website']."', '".$row4['email']."', '".$row4['service_location']."', '".$row4['billing_location']."',
                        '".$row4['pos_code']."','".$row4['attn']."','".$row4['domain_identifier']."','".$row4['facility_npi']."','".$row4['primary_business_entity']."','".$obj."')";
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute();
                $id = $sqlconfCentralDB->lastInsertId();
            endif;
        endif;
        ?>
        <tr>
        <td><?php echo $obj; ?></td>    
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?php echo $row4['name']; ?></td>
        <td><?php echo $row4['street']; ?></td>
        <td>&nbsp;</td>
        <td><?php echo $row4['city']; ?></td>
        <td><?php echo $row4['state']; ?></td>
        <td><?php echo $row4['postal_code']; ?></td>
        <td><?php echo $row4['country_code']; ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?php echo $row4['fax']; ?></td>
        <td><?php echo $row4['phone']; ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?php echo $row4['website']; ?></td>
        <td><?php echo $row4['service_location']; ?></td>
        <td><?php echo $row4['billing_location']; ?></td>
        <td><?php echo $row4['pos_code']; ?></td>
        <td><?php echo $row4['attn']; ?></td>
        <td><?php echo $row4['domain_identifier']; ?></td>
        <td><?php echo $row4['facility_npi']; ?></td>
        <td><?php echo $row4['primary_business_entity']; ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
        <?php
        if($datasync == 1):
            if($count == 0):
                sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".$row4['id'].",".$id.",'".$obj."')");
            endif;
        endif;    
    endwhile;
    
    // Pharmacy
    
    $str5 = "SELECT ph.id,ph.name,ph.email,ad.line1,ad.line2,ad.city,ad.state,ad.zip,ad.country FROM pharmacies ph LEFT JOIN addresses ad ON ph.id = ad.foreign_id";
    $query5 = sqlStatement($str5);
    
    while($row5 = sqlFetchArray($query5)):
        $obj = 'Pharmacy';
    
        $sql2 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".$row5['id']." AND objecttype = '".$obj."'";
        $r = sqlStatement($sql2);
        $objref = "";
        while($row2 = sqlFetchArray($r)):
           $objref = $row2['objectref'];
        endwhile;
        $count = sqlNumRows($r);
        if($datasync == 1):
            if($count):
                $sql = "UPDATE allcareobjects SET pharmacy_id='".$row5['id']."',
                                                  pharmacy_name='".$row5['name']."',    
                                                  street='".$row5['line1']."',
                                                  streetb='".$row5['line2']."',
                                                  city='".$row5['city']."',
                                                  state='".$row5['state']."',
                                                  zip='".$row5['zip']."',
                                                  country='".$row5['country']."',
                                                  uemail='".$row5['email']."',
                                                  practiceId='".$practiceId."'      
                                                  WHERE id=".$objref;
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute(); 
            else:
                $sql = "INSERT INTO allcareobjects (practiceId,pharmacy_id,pharmacy_name,street,streetb,city,state,zip,country,uemail,objecttype) 
                    VALUES('".$practiceId."',".$row5['id'].",'".$row5['name']."','".$row5['line1']."','".$row5['line2']."','".$row5['city']."','".$row5['state']."','".$row5['zip']."',
                        '".$row5['country']."', '".$row5['email']."', '".$obj."')";
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute();
                $id = $sqlconfCentralDB->lastInsertId();
            endif;
        endif;
        ?>
        <tr>
        <td><?php echo $obj; ?></td>    
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?php echo $row5['email']; ?></td>
        <td>&nbsp;</td>
        <td><?php echo $row5['line1']; ?></td>
        <td><?php echo $row5['line2']; ?></td>
        <td><?php echo $row5['city']; ?></td>
        <td><?php echo $row5['state']; ?></td>
        <td><?php echo $row5['zip']; ?></td>
        <td><?php echo $row5['country']; ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?php echo $row5['name']; ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
        <?php
        if($datasync == 1):
            if($count == 0):
                sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".$row5['id'].",".$id.",'".$obj."')");
            endif;
        endif;    
    endwhile;
    
    
    // Patient
    
    $str6 = "SELECT pd.*,pm.name as pharname,uh.organization,f.name as facname,u.fname as providerf,u.mname as providerm,u.lname as providerl,us.fname as refproviderf,us.mname as refproviderm,us.lname as refproviderl FROM patient_data pd LEFT JOIN 
            pharmacies pm ON pd.pharmacy_id = pm.id LEFT JOIN 
            users u ON pd.providerID = u.id LEFT JOIN 
            users uh ON pd.hhagency = uh.id LEFT JOIN
            facility f ON pd.patient_facility = f.id LEFT JOIN
            users us ON pd.ref_providerID = us.id";
    $query6 = sqlStatement($str6);
    
    while($row6 = sqlFetchArray($query6)):
        $obj = 'Patient';
    
        $sql2 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".$row6['pid']." AND objecttype = '".$obj."'";
        $r = sqlStatement($sql2);
        $objref = "";
        while($row2 = sqlFetchArray($r)):
           $objref = $row2['objectref'];
        endwhile;
        $count = sqlNumRows($r);
        
        if($datasync == 1):
            if($count):
                // Check if this user has userid created in patient_access_onsite
                $str = "SELECT portal_username FROM patient_access_onsite WHERE pid=".$row6['pid'];
                $query = sqlStatement($str);
                $rowq = sqlFetchArray($query);
                $patientusername = '';
                if($rowq['portal_username'] !='') $patientusername =  $rowq['portal_username'];
                
                $providername = $row6['providerf']." ". $row6['providerm'] . " " . $row6['providerl'];
                $refprovidername = $row6['refproviderf']." ". $row6['refproviderm'] . " " . $row6['refproviderl'];
                $sql = "UPDATE allcareobjects SET pid='".$row6['pid']."',
                                                  username='".$patientusername."',
                                                  fname='".$row6['fname']."',    
                                                  mname='".$row6['mname']."',
                                                  lname='".$row6['lname']."',
                                                  street='".$row6['street']."',    
                                                  city='".$row6['city']."',
                                                  state='".$row6['state']."',
                                                  zip='".$row6['zip']."',
                                                  country='".$row6['country']."',
                                                  uemail='".$row6['email']."',

                                                  phone='".$row6['phone_contact']."',
                                                  phonew1='".$row6['phone_home']."',
                                                  phonew2='".$row6['phone_biz']."',
                                                  phonecell='".$row6['phone_cell']."',

                                                  patientmothername='".$row6['mothersname']."',
                                                  patientguardianname='".$row6['guardiansname']."',
                                                  patientdob='".$row6['DOB']."',
                                                  patientsex='".$row6['sex']."',
                                                  patient_ss='".$row6['ss']."',
                                                  patientpharmacyid='".$row6['pharmacy_id']."',
                                                  patientpharmacyname='".$row6['pharname']."',
                                                  patientproviderid='".$row6['providerID']."',
                                                  patientprovidername='".$providername."',
                                                  patientrefproviderid='".$row6['ref_providerID']."',

                                                  patientrefprovidername='".$refprovidername."',
                                                  patient_folder='".$row6['patient_folder']."',
                                                  patient_facility='".$row6['facname']."',
                                                  patient_hhagency='".$row6['organization']."',      
                                                  patient_livingfacility='".$row6['living_facility']."',
                                                  cangroup='".$row6['cangroup']."',    
                                                  practiceId='".$practiceId."'  
                                                  WHERE id=".$objref;
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute(); 
            else:
                // Check if this user has userid created in patient_access_onsite
                $str = "SELECT portal_username FROM patient_access_onsite WHERE pid=".$row6['pid'];
                $query = sqlStatement($str);
                $rowq = sqlFetchArray($query);
                $patientusername = '';
                if($rowq['portal_username'] !='') $patientusername =  $rowq['portal_username'];
                
               $sql = "INSERT INTO allcareobjects (pid,username,practiceId,fname,mname,lname,street,city,state,zip,country,uemail,patientmothername,patientguardianname,
                                patientdob,patientsex,patient_ss,patientpharmacyid,patientpharmacyname,patientproviderid,patientprovidername,
                                patientrefproviderid,patientrefprovidername,patient_folder,patient_facility,patient_hhagency,patient_livingfacility,phone,phonew1,phonew2,phonecell,cangroup,objecttype) 
                    VALUES(".$row6['pid'].",'".$patientusername."','".$practiceId."','".$row6['fname']."','".$row6['mname']."','".$row6['lname']."','".$row6['street']."','".$row6['city']."','".$row6['state']."','".$row6['zip']."',
                        '".$row6['country']."', '".$row6['email']."', '".$row6['mothersname']."', '".$row6['guardiansname']."',  '".$row6['DOB']."','".$row6['sex']."','".$row6['ss']."','".$row6['pharmacy_id']."','".$row6['pharname']."',
                            '".$row6['providerID']."','".$providername."','".$row6['ref_providerID']."','".$refprovidername."',
                                '".$row6['patient_folder']."','".$row6['facname']."','".$row6['organization']."','".$row6['living_facility']."','".$row6['phone_contact']."','".$row6['phone_home']."','".$row6['phone_biz']."','".$row6['phone_cell']."','".$row6['cangroup']."','".$obj."')";
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute();
                $id = $sqlconfCentralDB->lastInsertId();
            endif;
        endif;
        ?>
        <tr>
        <td><?php echo $obj; ?></td>    
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?php echo $row6['fname']; ?></td>
        <td><?php echo $row6['mname']; ?></td>
        <td><?php echo $row6['lname']; ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?php echo $row6['email']; ?></td>
        <td>&nbsp;</td>
        <td><?php echo $row6['street']; ?></td>
        <td>&nbsp;</td>
        <td><?php echo $row6['city']; ?></td>
        <td><?php echo $row6['state']; ?></td>
        <td><?php echo $row6['zip']; ?></td>
        <td><?php echo $row6['country']; ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        
        <td><?php echo $row6['pid']; ?></td>
        <td><?php echo $row6['mothersname']; ?></td>
        <td><?php echo $row6['guardiansname']; ?></td>
        <td><?php echo $row6['DOB']; ?></td>
        <td><?php echo $row6['sex']; ?></td>
        <td><?php echo $row6['ss']; ?></td>
        <td><?php echo $row6['pharname']; ?></td>
        <td><?php echo $providername; ?></td>
        <td><?php echo $refprovidername; ?></td>
        <td><?php echo $row6['patient_folder']; ?></td>
        <td><?php echo $row6['facname']; ?></td>
        <td><?php echo $row6['organization']; ?></td>
        <td><?php echo $row6['living_facility']; ?></td>
        
        </tr>
        <?php
        if($datasync == 1):
            if($count == 0):
                sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".$row6['pid'].",".$id.",'".$obj."')");
            endif;
        endif;    
    endwhile;
    
    ?>
           
    </tbody>
</table>