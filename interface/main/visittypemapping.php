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
<html>
<head>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/css/bootstrap-3.2.0.min.css" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/css/bootstrap-example.css" type="text/css">


<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/bootstrap-3.2.0.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/docs/js/prettify.js"></script>

<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/css/bootstrap-multiselect.css" type="text/css">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/bootstrap/dist/js/bootstrap-multiselect.js"></script>

<style>
    .inputtext-control{width:"20px;"}
    #facuservisit{background-color: #fe5301;width: 1100px;height: 600px;overflow: scroll;}
</style>

<script type="text/javascript">
  $(document).ready(function() {
        jQuery('#visittypemap').load('visittypemapping_add.php');
    });
    
    
</script>

</head>
<body class="body_top">
    <div id="visittypemap"></div>
</body>
</html>
