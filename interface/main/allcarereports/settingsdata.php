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
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../../globals.php");
require_once("../../../library/formdata.inc.php");
require_once("../../../library/globals.inc.php");


 
  
function display_db_query($query_string) {
	// perform the database query
	$result_id = mysql_query($query_string)
	or die("display_db_query:" . mysql_error());
	// find out the number of columns in result
	$column_count = mysql_num_fields($result_id)
	or die("display_db_query:" . mysql_error());
	// Here the table attributes from the $table_params variable are added
	print("<table border='1' id='patient_appt' class='display'>\n");
	// optionally print a bold header at top of table
	
		print("<thead style='background-color:#CDFFCF;'><tr>");
		for($column_num = 0; $column_num < $column_count; $column_num++) {
			$field_name = mysql_field_name($result_id, $column_num);
			print("<th>$field_name</th>");
		}
		print("</tr></thead>\n");
	
	// print the body of the table
	while($row = mysql_fetch_row($result_id)) {
		print("<tr ALIGN=LEFT VALIGN=TOP>");
		for($column_num = 0; $column_num < $column_count; $column_num++) {
			print("<td>$row[$column_num]</td>\n");
		}
		print("</tr>\n");
	}
	print("</table>\n"); 
}
function display_db_table() {
	$query_string = "CALL settings()";
	display_db_query($query_string);
}
?>
    <head>
           <style>
        .css_button_small {
        -moz-font-feature-settings: normal;
        -moz-font-language-override: normal;
        -moz-text-decoration-color: -moz-use-text-color;
        -moz-text-decoration-line: none;
        -moz-text-decoration-style: solid;
        -x-system-font: none;
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("../../images/bg_button_a_small.gif");
        background-origin: padding-box;
        background-position: right top;
        background-repeat: no-repeat;
        background-size: auto auto;
        color: #444;
        display: block;
        float: left;
        font-family: arial,sans-serif;
        font-size: 9px;
        font-size-adjust: none;
        font-stretch: normal;
        font-style: normal;
        font-variant: normal;
        font-weight: bold;
        height: 19px;
        line-height: normal;
        margin-right: 3px;
        padding-right: 10px;
        }

        .css_button_small span {
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("../../images/bg_button_span_small.gif");
        background-origin: padding-box;
        background-position: 0 0;
        background-repeat: no-repeat;
        background-size: auto auto;
        display: block;
        line-height: 20px;
        padding-bottom: 0;
        padding-left: 10px;
        padding-right: 0;
        padding-top: 0;
        }
    </style>
<link rel='stylesheet' type='text/css' href='../css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='../css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='../css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='../css/dataTables.colReorder.css'>
<style>
div.DTTT_container {
	float: none;
}
</style>
<script type='text/javascript' src='../js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='../js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='../js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='../js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='../js/dataTables.colVis.js'></script>
    
    </head>
<body style="background-color:#FFFFCC;" >

<?php
display_db_table();
?>
<!--    <table id="patient_data2" class="display" border="1">
         <thead><tr><th>test333</th><th>test22</th></tr></thead>
        <tr><td>test</td><td>test2</td></tr>
    </table>-->
<script type='text/javascript'>
            
            $(document).ready( function () {
                $('#patient_appt').DataTable( {
                    dom: 'T<"clear">lfrtip',
                    "tableTools": {
                        "sSwfPath": "../../swf/copy_csv_xls_pdf.swf",
                        "aButtons": [
                            {
                                "sExtends": "xls",
                                "sButtonText": "Save to Excel"
                            }
                        ]
                    }
                } );
            } );
    </script>
</body>