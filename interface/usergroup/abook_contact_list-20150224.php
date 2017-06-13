<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false; 
// 

include_once("../globals.php");
//include_once("$srcdir/transactions.inc");
include_once("$srcdir/abook_data_lib.php");
require_once("$srcdir/options.inc.php"); 
$abookuserid = $_REQUEST['abookuserid'];

?>

<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script language="javascript">
// Called by the deleteme.php window on a successful delete.
function imdeleted() {
 top.restoreSession();
 location.href = 'usergroup/add_abook_contact.php';
}
</script>

</head>
<body class="body_top">

<br>
<table class="showborder" cellspacing="0px" cellpadding="2px">
<?php
$field_id=array();
$title=array();
$fres=sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'ABOOKCONTACT' AND uor > 0 AND field_id != '' " .
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

if ($result = getAbookDatabyUserid($abookuserid)) {
    foreach ($result as $iter) {
	echo "<tr height='25'><td>";
        print "<a href='abook_contact.php?abookuserid=$abookuserid&id=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
                "&inmode=edit' onclick='top.restoreSession()' class='css_button_small'><span>".
                htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
        echo "</td><td>";
        if (acl_check('admin', 'super')) {
                echo "<a href='../patient_file/deleter.php?abookcontactid=".
                        htmlspecialchars( $iter{"id"}, ENT_QUOTES).
                        "' onclick='top.restoreSession()' class='css_button_small'><span>".
                        htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";
        }
        else {
            echo "&nbsp;";
        }
        echo "</td>";
        echo "<td><b>&nbsp;</b></td>";
        foreach($field_id as $attr)
            echo " <td style='width:200px;'>".htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
        echo "</tr>\n";
        $notes_count++;
    }
}
?>
</table>

</body>
</html>
