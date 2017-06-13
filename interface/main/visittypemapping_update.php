<?php
// +-----------------------------------------------------------------------------+
// Copyright (C) 2012 NP Clinics <info@npclinics.com.au>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//
// Author:   Scott Wakefield <scott@npclinics.com.au>
//
// +------------------------------------------------------------------------------+

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;


//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;


require_once("../globals.php");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/acl.inc");

// Ensure authorized
if (!acl_check('admin', 'users')) {
  die(xlt("Unauthorized"));
}
?>

<?php
    $visitType = (count($_POST['visitType']) > 0)? $_POST['visitType'] : "";
    $feesheetvisit      = (count($_POST['feesheetvisit']) > 0)? $_POST['feesheetvisit'] : "";
    $auditvisit   = (count($_POST['auditvisit']) > 0)? $_POST['auditvisit'] : "" ;
    $fuvid    = ($_POST['fuvid'] != "")? $_POST['fuvid'] : "";
    if($visitType != "" && $feesheetvisit != "" && $auditvisit != ""):
            $feesheetvisitsplit = explode('$$',$feesheetvisit);
            $sql = sqlStatement("SELECT fs_codes FROM fee_sheet_options where fs_category = '".$feesheetvisitsplit[0]."' AND fs_option ='".$feesheetvisitsplit[1]."'");
            $row = sqlFetchArray($sql);
            
            $updateQuery = "UPDATE tbl_allcare_visittypemapping SET visit_type = '". $visitType ."',fee_visit_category ='". $feesheetvisit ."',audit_visit_category='". $auditvisit ."', cpt_code='".$row['fs_codes']."' WHERE visit_category=".$fuvid;
            sqlStatement($updateQuery);
            $updatelogQuery = "INSERT INTO tbl_allcare_visittypemapping_log (visitcategory,userid,timestamp,action) VALUES (".$fuvid.",'".$_SESSION['authUser']."',now(),'updated')";
            sqlStatement($updatelogQuery);
            echo "Visit Type Mapping updated successfully";            
       // exit();
    endif;
   // echo "ji";
//exit();
?>