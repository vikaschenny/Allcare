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
global $sqlconf;

$str1 = "SELECT u.id, u.username, u.fname, u.lname, u.mname, GROUP_CONCAT(gag.name SEPARATOR ',') AS acl_role_name, u.facility,
        u.facility_id,uc.email,uc.processproviders,uc.mobilebillingaccess,uc.processmobilef2f,uc.business_support,uc.technical_support,
        uc.provider_portal,uc.practice_portal,uc.processaudit,uc.process_templatefrom,uc.emraccess,uc.contextedit
        FROM users u
        LEFT JOIN tbl_user_custom_attr_1to1 uc ON u.id=uc.userid
        INNER JOIN gacl_aro ga ON ga.value = u.username
        INNER JOIN gacl_groups_aro_map ggam ON ggam.aro_id = ga.id
        INNER JOIN gacl_aro_groups gag ON gag.id = ggam.group_id GROUP BY u.id";
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
   <?php xl('User Facility Name','e'); ?>
  </th>
  <th>
   <?php xl('User Facility ID','e'); ?>
  </th>
  <th>
   <?php xl('User Email','e'); ?>
  </th>
 </tr>
    </thead>
    <tbody>
        
    <?php
    while($row1 = sqlFetchArray($sql1)):
        
        $sql = "INSERT INTO allcareobjects (userid,username,fname,mname,lname,urole,ufacility,ufacilityid,uemail,objecttype) 
                VALUES(".$row1['id'].",'".$row1['username']."','".$row1['fname']."','".$row1['mname']."','".$row1['lname']."','".$row1['acl_role_name']."','".$row1['facility']."','".$row1['facility_id']."','".$row1['email']."','user')";
            $stmt = $sqlconf->prepare($sql) ;
            $stmt->execute();
            $id = $sqlconf->lastInsertId();
        
        
//        $sql1 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".$row1['id']." AND objecttype = 'user'";
//        $r = sqlStatement($sql1);
//        $objref = "";
//        while($row2 = sqlFetchArray($r)):
//           $objref = $row2['objectref'];
//        endwhile;
//        $count = sqlNumRows($r);
//        if($count):
//            $sql = "UPDATE allcareobjects SET userid=".$row1['id'].", 
//                                              username='".$row1['username']."',
//                                              fname='".$row1['fname']."',
//                                              mname='".$row1['mname']."',
//                                              lname='".$row1['lname']."',
//                                              urole='".$row1['acl_role_name']."',
//                                              ufacility='".$row1['facility']."',
//                                              ufacilityid='".$row1['facility_id']."',
//                                              uemail='".$row1['email']."' WHERE id=".$objref;
//            $stmt = $sqlconf->prepare($sql) ;
//            $stmt->execute();
//        else:
//            $sql = "INSERT INTO allcareobjects (userid,username,fname,mname,lname,urole,ufacility,ufacilityid,uemail,objecttype) 
//                VALUES(".$row1['id'].",'".$row1['username']."','".$row1['fname']."','".$row1['mname']."','".$row1['lname']."','".$row1['acl_role_name']."','".$row1['facility']."','".$row1['facility_id']."','".$row1['email']."','user')";
//            $stmt = $sqlconf->prepare($sql) ;
//            $stmt->execute();
//            $id = $sqlconf->lastInsertId();
//        endif;
        
        
    ?>
        <tr>
        <td><?php xl('User','e'); ?></td>    
        <td><?php echo $row1['id']; ?></td>
        <td><?php echo $row1['username']; ?></td>
        <td><?php echo $row1['fname']; ?></td>
        <td><?php echo $row1['mname']; ?></td>
        <td><?php echo $row1['lname']; ?></td>
        <td><?php echo $row1['acl_role_name']; ?></td>
        <td><?php echo $row1['facility']; ?></td>
        <td><?php echo $row1['facility_id']; ?></td>
        <td><?php echo $row1['email']; ?></td>
        </tr> 
    <?php
       // if($count = 0):
            sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".$row1['id'].",".$id.",'user')");
       // endif;
    endwhile;
    ?>
           
    </tbody>
</table>