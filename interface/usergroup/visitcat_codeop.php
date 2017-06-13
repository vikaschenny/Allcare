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
$cat =  $_POST['catid'];
$options = array();
if(isset($cat) and strlen($cat) > 0){
    $quer="SELECT fs_option FROM fee_sheet_options where fs_category IN ('$cat') ";
    $fuvRows = sqlStatement($quer);
    while($row = sqlFetchArray($fuvRows)):
        $options[$row['fs_option']] = $row['fs_option'];
    endwhile;
}
echo $json = json_encode($options);
?>