<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false; 
// 

include_once("../globals.php");
//include_once("$srcdir/transactions.inc");
include_once("$srcdir/extra_usersdata_lib.php");
require_once("$srcdir/options.inc.php"); 
$userid = $_REQUEST['userid'];



?>

<html>
<head>
<?php html_header_show();?>
<script language="javascript">
// Called by the deleteme.php window on a successful delete.
function imdeleted() {
 top.restoreSession();
 location.href = 'usergroup/add_payroll.php';
}
</script>
<style>
    .showborder_head th{
        border-bottom: 1px solid #ddd !important;
    }
    table-responsive > .table tr:last-child {
        border-bottom: 1px solid #ddd;
    }
</style>
</head>
<body class="body_top">

<br>
<div class="table-responsive">
<table class="table" cellspacing="0px" cellpadding="2px">
<?php
$field_id=array();
$title=array();
$fres=sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'PAYROLL' AND uor > 0 AND field_id != '' " .
    "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
    $data_type = $frow['data_type'];
    $field_id[]  = $frow['field_id'];
    $value = $_POST["form_$field_id"];
    $sets .=  add_escape_custom($field_id);
    $title[] = $frow['title'];
}

// Print Heading .. to have better Understanding of the Listed Transactions -- starts here Dec 07,09
    print "<tr class='showborder_head'><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th>";
    foreach($title as $label)
    { 
        echo "<th style='width:200px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."</th>" ;
    }
    echo "</tr>\n";
// Print Heading .. to have better Understanding of the Listed Transactions   -- ends here

if ($result = getPayrollDatabyUserid($userid)) {
    foreach ($result as $iter) {
	echo "<tr height='25'><td>";
        print "<a href='payroll_data.php?userid=$userid&id=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
                "&inmode=edit' onclick='userstable(event,this,\"User Payroll\")' class='css_button_small'><span>".
                htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
        echo "</td><td>";
        if (acl_check('admin', 'super')) {
                echo "<a href='../patient_file/deleter.php?payrollid=".
                        htmlspecialchars( $iter{"id"}, ENT_QUOTES).
                        "' onclick='deleteusersdatarow(event,this,\"userid\")' class='css_button_small'><span>".
                        htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";
        }
        else {
            echo "&nbsp;";
        }
        echo "</td>";
        echo "<td><b>&nbsp;</b></td>";
        foreach($field_id as $attr){
            $fres2=sqlStatement("SELECT data_type FROM layout_options WHERE form_id = 'PAYROLL' AND uor > 0 AND field_id = '$attr'");
            while ($row2 = mysql_fetch_row($fres2)) {
                if($row2[0] == 37): 
                    if( htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES) != '' &&  htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES) != '0'):
                        $fieldname = sqlStatement ("SELECT name FROM insurance_companies WHERE id =". htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES));
                        while ($row = mysql_fetch_row($fieldname)) {
                           echo " <td style='width:200px;'>".htmlspecialchars( $row[0], ENT_NOQUOTES). "&nbsp;</td>"; 
                        }
                    else:
                        echo " <td style='width:200px;'>".htmlspecialchars( '', ENT_NOQUOTES). "&nbsp;</td>"; 
                    endif;
                elseif($row2[0] == 35):
                    if( htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES) != '' &&  htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES) != '0'):
                        $fieldname = sqlStatement ("SELECT name FROM facility WHERE id =". htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES));
                        while ($row = mysql_fetch_row($fieldname)) {
                           echo " <td style='width:200px;'>".htmlspecialchars( $row[0], ENT_NOQUOTES). "&nbsp;</td>"; 
                        }
                    else:
                        echo " <td style='width:200px;'>".htmlspecialchars( '', ENT_NOQUOTES). "&nbsp;</td>"; 
                    endif;     
                elseif(($row2[0] == 10 || $row2[0] == 11)):
                    if( htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES) != '' &&  htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES) != '0'):
                        $fieldname = sqlStatement ("SELECT CONCAT(fname,' ', lname) FROM users WHERE id =". htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES));
                        while ($row = mysql_fetch_row($fieldname)) {
                           echo " <td style='width:200px;'>".htmlspecialchars( $row[0], ENT_NOQUOTES). "&nbsp;</td>"; 
                        } 
                    else:
                        echo " <td style='width:200px;'>".htmlspecialchars( '', ENT_NOQUOTES). "&nbsp;</td>"; 
                    endif;    
                elseif($row2[0] == 12):
                    if( htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES) != '' &&  htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES) != '0'):
                        $fieldname = sqlStatement ("SELECT name FROM pharmacies WHERE id =". htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES));
                        while ($row = mysql_fetch_row($fieldname)) {
                           echo " <td style='width:200px;'>".htmlspecialchars( $row[0], ENT_NOQUOTES). "&nbsp;</td>"; 
                        }
                    else:
                        echo " <td style='width:200px;'>".htmlspecialchars( '', ENT_NOQUOTES). "&nbsp;</td>"; 
                    endif;
                elseif($row2[0] == 14 &&  htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES) != ''):
                    if (strpos($frow['edit_options'], 'L') !== FALSE)
                        $tmp = "abook_type = 'ord_lab'";
                    else if (strpos($frow['edit_options'], 'O') !== FALSE)
                        $tmp = "abook_type LIKE 'ord\\_%'";
                    else if (strpos($frow['edit_options'], 'V') !== FALSE)
                        $tmp = "abook_type LIKE 'vendor%'";
                    else if (strpos($frow['edit_options'], 'R') !== FALSE)
                        $tmp = "abook_type LIKE 'dist'";
                    else
                        $tmp = "( username = '' OR authorized = 1 )";
                    $ures = sqlStatement("SELECT fname, lname, organization, username FROM users " .
                            "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                            "AND $tmp  AND id = ".htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES));
                    while ($urow = sqlFetchArray($ures)) {
                        $uname = $urow['organization'];
                        if (empty($uname) || substr($uname, 0, 1) == '(') {
                            $uname = $urow['lname'];
                            if ($urow['fname']) $uname .= ", " . $urow['fname'];
                        }
                        $optionLabel = htmlspecialchars( $uname, ENT_NOQUOTES);
                    }
                    echo " <td style='width:200px;'>".htmlspecialchars( $optionLabel, ENT_NOQUOTES). "&nbsp;</td>";      
                else:
                    echo " <td style='width:200px;'>".htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                endif;
            }
        }  
//        foreach($field_id as $attr)
//            echo " <td style='width:200px;'>".htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
        echo "</tr>\n";
        $notes_count++;
    }
}
?>
</table>
</div>
</body>
</html>
