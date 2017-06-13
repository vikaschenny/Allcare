<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../../globals.php");
//include_once("$srcdir/transactions.inc");
include_once("$srcdir/agency_lib.php");
require_once("$srcdir/options.inc.php");

//print_r($_GET);
//print_r($_POST);
//print_r($_SESSION);

?>

<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script language="javascript">
// Called by the deleteme.php window on a successful delete.
function imdeleted() {
 top.restoreSession();
 location.href = 'summary/add_agency.php';
}
</script>

</head>
<body class="body_top">

<br>
<table class="showborder" cellspacing="0px" cellpadding="2px">
<?php

$field=array();
$title=array();
$fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'AGENCY' AND uor > 0 AND field_id != '' " .
    "ORDER BY group_name, seq");
  while ($frow = sqlFetchArray($fres)) {
    $data_type = $frow['data_type'];
    $field_id[]  = $frow['field_id'];
    $title[] = $frow['title'];
   
  }

// Print Heading .. to have better Understanding of the Listed Transactions -- starts here Dec 07,09
	 print "<tr class='showborder_head'><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th><th style='width=10px;'>&nbsp;</th>";
	 
		// Print Heading .. to have better Understanding of the Listed Transactions   -- ends here

         foreach($title as $label)
                        { 
                        echo "<th style='width:180px;'>".htmlspecialchars( xl($label), ENT_NOQUOTES)."</th>" ;
                        }
          echo "</tr>\n";
          
if ($result = getAgencyByPid($pid)) {
	foreach ($result as $iter) {
		
		echo "<tr height='25'><td>";
		print "<a href='agency.php?aid=".htmlspecialchars( $iter{"id"}, ENT_NOQUOTES).
			"&inmode=edit' onclick='top.restoreSession()' class='css_button_small'><span>".
			htmlspecialchars( xl('Edit'), ENT_NOQUOTES)."</span></a>";
		echo "</td><td>";
		if (acl_check('admin', 'super')) {
			echo "<a href='../deleter.php?aid=".
				htmlspecialchars( $iter{"id"}, ENT_QUOTES).
				"' onclick='top.restoreSession()' class='css_button_small'><span>".
				htmlspecialchars( xl('Delete'), ENT_NOQUOTES)."</span></a>";
		}
		else {
			echo "&nbsp;";
		}
		echo "</td>";
		echo "<td><b>&nbsp;" .
			"</b></td>" ;
                        foreach($field_id as $attr)
                        { 
                         echo " <td style='width:150px;'>" .
                        htmlspecialchars( ($iter{$attr}), ENT_NOQUOTES). "&nbsp;</td>";
                        }
                        echo "</tr>\n";
		$notes_count++;
	}
}
?>
</table>
</body>
</html>
