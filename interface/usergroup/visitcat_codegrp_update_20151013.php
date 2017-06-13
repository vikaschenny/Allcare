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
    echo  $facilities = $_POST['facilities'];
    echo  $visitcat   = $_POST['visitcat'];
    echo  $codegrps   = $_POST['codegroups'];
    
    
    if($_POST['codegroupsopt']!="")   
      echo $codegrpopt   = (count($_POST['codegroupsopt']) > 0)? serialize($_POST['codegroupsopt']) : "";
    if($_POST['codegroupsopt2']!="")   
     echo  $codegrpopt   = (count($_POST['codegroupsopt2']) > 0)? serialize($_POST['codegroupsopt2']) : "";
    if($_POST['codegroupsopt3']!="")   
      echo $codegrpopt   = (count($_POST['codegroupsopt3']) > 0)? serialize($_POST['codegroupsopt3']) : "";
    if($_POST['codegroupsopt4']!="")   
      echo  $codegrpopt   = (count($_POST['codegroupsopt4']) > 0)? serialize($_POST['codegroupsopt4']) : "";
    if($_POST['codegroupsopt5']!="")   
      echo $codegrpopt   = (count($_POST['codegroupsopt5']) > 0)? serialize($_POST['codegroupsopt5']) : "";
    if($_POST['codegroupsopt6']!="")   
      echo $codegrpopt   = (count($_POST['codegroupsopt6']) > 0)? serialize($_POST['codegroupsopt6']) : "";
    if($_POST['codegroupsopt7']!="")   
      echo $codegrpopt   = (count($_POST['codegroupsopt7']) > 0)? serialize($_POST['codegroupsopt7']) : "";
    if($_POST['codegroupsopt8']!="")   
      echo $codegrpopt   = (count($_POST['codegroupsopt8']) > 0)? serialize($_POST['codegroupsopt8']) : "";
      
   echo $fuvid    = ($_POST['fuvid'] != "")? $_POST['fuvid'] : "";
   
    
    echo $facilities . " +++ " . $users . " +++ " . $visitcat . " +++ " . $screens . " +++ " . $screenlinks . " +++ " . $groupname . " +++ " . $fuvid;
    
    if($facilities != ""  && $visitcat != "" && $codegrps != "" && $codegrpopt != ""):
       
        $result = sqlStatement("SELECT * FROM  tbl_allcare_vistcat_codegrp WHERE    code_groups='".$codegrps."' AND  code_options='".$codegrpopt."' AND facility='".$facilities."' AND visit_category='".$visitcat."'");
       
        if(sqlNumRows($result) > 0 ):
            ?><script> alert("facility and visit_category with same selection already exists"); </script>
            
            <?php
        else:
            echo $updateQuery = "UPDATE tbl_allcare_vistcat_codegrp SET facility = '". $facilities ."',visit_category= '". $visitcat ."',code_groups= '". $codegrps ."', code_options= '". $codegrpopt ."' WHERE id=".$fuvid;
            sqlStatement($updateQuery);
            $updatelogQuery = "INSERT INTO tbl_allcare_vistcat_codegrp_log (vcgrpid,userid,timestamp,action) VALUES (".$fuvid.",'".$_SESSION['authUser']."',now(),'updated')";
            sqlStatement($updatelogQuery);
            echo "Group updated successfully";            
         endif;
    else:
        echo "All fields are required.";
    endif;
    ?>