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
    $facilities = (count($_POST['facilities']) > 0)? serialize($_POST['facilities']) : "";
    $users      = (count($_POST['users']) > 0)? serialize($_POST['users']) : "";
    $visitcat   = (count($_POST['visitcat']) > 0)? serialize($_POST['visitcat']) : "" ;
    //$screens    = (count($_POST['screens']) > 0)? serialize($_POST['screens']) : "";
    $screens=$_POST['screens'];
   //$screenlinks    = ($_POST['screenlinks'] != "")? $_POST['screenlinks'] : "";
    $groupname    = ($_POST['groupname'] != "")? $_POST['groupname'] : "";
    $fuvid    = ($_POST['fuvid'] != "")? $_POST['fuvid'] : "";
   //$medicalgroup   = $_POST['medicalgroup']? $_POST['medicalgroup'] : "" ;
   $screennames=(count($_POST['result_arr']) > 0)? serialize($_POST['result_arr']) : "";
    
    //echo $facilities . " +++ " . $users . " +++ " . $visitcat . " +++ " . $screens . " +++ " . $screenlinks . " +++ " . $groupname . " +++ " . $fuvid."===".$screennames;
    
    if($facilities != "" && $users != "" && $visitcat != "" && $screens != ""    && $fuvid !=""  && $screennames != ""):
        
           $updateQuery = "UPDATE tbl_allcare_facuservisit SET facilities = '". $facilities ."',users ='". $users ."',visit_categories='". $visitcat ."',screen_group= '". $screens ."', screen_names='".$screennames."' WHERE id=".$fuvid;
            sqlStatement($updateQuery);
            $updatelogQuery = "INSERT INTO tbl_allcare_facuservisit_log (fuvid,userid,timestamp,action) VALUES (".$fuvid.",'".$_SESSION['authUser']."',now(),'updated')";
            sqlStatement($updatelogQuery);
            echo "Group updated successfully";            
       // exit();
    endif;
   // echo "ji";
//exit();
?>