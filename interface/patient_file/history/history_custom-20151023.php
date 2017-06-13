<?php

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

 require_once("../../globals.php");
 require_once("$srcdir/patient.inc");
 require_once("history.inc.php");
 require_once("$srcdir/options.inc.php");
 require_once("$srcdir/acl.inc");
?>
<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript" src="../../../library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="../../../library/js/common.js"></script>

<script type="text/javascript">
$(document).ready(function(){
    tabbify();
});
</script>

<style type="text/css">
    #dvLoading3 {
                    background: url(../../pic/ajax-loader-large.gif) no-repeat center center;
                    height: 100px;
                    width: 500px;
                    position: fixed;
                    z-index: 1000;
                    left: 0%;
                    top: 50%;
                    margin: -25px 0 0 -25px;
                }
</style>
</head>
<body class="body_top">

<?php
$pid=$_REQUEST['pid'];
$provider=$_REQUEST['provider'];
$location=$_REQUEST['location'];
$form_id=$_REQUEST['form_id'];
$encounter=$_REQUEST['encounter'];
 if (acl_check('patients','med')) {
  $tmp = getPatientData($pid, "squad");
  if ($tmp['squad'] && ! acl_check('squads', $tmp['squad'])) {
   echo "<p>(".htmlspecialchars(xl('History not authorized'),ENT_NOQUOTES).")</p>\n";
   echo "</body>\n</html>\n";
   exit();
  }
 }
 else {  
  echo "<p>(".htmlspecialchars(xl('History not authorized'),ENT_NOQUOTES).")</p>\n";
  echo "</body>\n</html>\n";
  exit();
 }

 $result = getHistoryData($pid);
 if (!is_array($result)) {
  newHistoryData($pid);
  $result = getHistoryData($pid);
 }
?>

<?php if (acl_check('patients','med','',array('write','addonly') )) { ?>
<div>
    <span class="title"><?php echo htmlspecialchars(xl('Patient History / Lifestyle'),ENT_NOQUOTES); ?></span>
</div>
<div style='float:left;margin-right:10px'>
<?php echo htmlspecialchars(xl('for'),ENT_NOQUOTES);?>&nbsp;<span class="title"><?php echo htmlspecialchars(getPatientName($pid),ENT_NOQUOTES) ?></span>
</div>
<div>
    <a href="history_full_custom.php?pid=<?php echo $pid; ?>&grpname=<?php echo $_REQUEST['grpname']; ?>&grp_stat=<?php echo $_REQUEST['grp_stat']; ?>&form_id=<?php echo $_REQUEST['form_id']; ?>&encounter=<?php echo $encounter ; ?>" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?>
     class="css_button"
     onclick="top.restoreSession()">
    <span><?php echo htmlspecialchars(xl("Edit"),ENT_NOQUOTES);?></span>
    </a>

    <a href="" <?php if (!$GLOBALS['concurrent_layout']) echo "target='Main'"; ?> class="css_button" onclick="window.close();">
        <span><?php echo htmlspecialchars(xl('Back'),ENT_NOQUOTES);?></span>
    </a>
    
</div>
<br/>
<?php } 

$grname=str_replace("_", " " ,$_REQUEST['grpname']);
function display_layout_tabs_custom($formtype, $grname,$result1, $result2='') {
  global $item_count, $cell_count, $last_group, $CPR;

  
  $fres = sqlStatement("SELECT distinct group_name FROM layout_options " .
    "WHERE form_id = ? AND uor > 0  AND group_name LIKE '%$grname%' " .
    "ORDER BY seq", array($formtype) );

  //$first = true;
 // while ($frow = sqlFetchArray($fres)) {
      $frow = sqlFetchArray($fres);
	  $this_group = $frow['group_name'];
      $group_name = substr($this_group, 1);
      ?>
		<li>
			<a href="/play/javascript-tabbed-navigation/" id="header_tab_<?php echo ".htmlspecialchars($group_name,ENT_QUOTES)."?>">
                        <?php echo htmlspecialchars(xl_layout_label($group_name),ENT_NOQUOTES); ?></a>
		</li>
	  <?php
	  //$first = false;
 // }
  
  ?>
<!--                <li>
                    <a href="/play/javascript-tabbed-navigation/" id="header_tab_.htmlspecialchars(POS,ENT_QUOTES).">
                    Patient's Attributes</a>
                </li>-->
                <?php
}

function display_layout_tabs_data_custom($formtype, $grname, $result1, $result2='') {
  global $item_count, $cell_count, $last_group, $CPR;

  if($formtype=='LBF2'){
      $fres = sqlStatement("SELECT distinct group_name FROM layout_options " .
    "WHERE form_id = ? AND uor > 0 AND group_name LIKE '%$grname%' AND field_id LIKE '%_stat%' " .
    "ORDER BY seq", array($formtype));
  }else {
      $fres = sqlStatement("SELECT distinct group_name FROM layout_options " .
        "WHERE form_id = ? AND uor > 0 AND group_name LIKE '%$grname%' " .
        "ORDER BY seq", array($formtype));
  }
	$first = true;
	while ($frow = sqlFetchArray($fres)) {
		$this_group = isset($frow['group_name']) ? $frow['group_name'] : "" ;
		$titlecols  = isset($frow['titlecols']) ? $frow['titlecols'] : "";
		$datacols   = isset($frow['datacols']) ? $frow['datacols'] : "";
		$data_type  = isset($frow['data_type']) ? $frow['data_type'] : "";
		$field_id   = isset($frow['field_id']) ? $frow['field_id'] : "";
		$list_id    = isset($frow['list_id']) ? $frow['list_id'] : "";
		$currvalue  = '';

		$group_fields_query = sqlStatement("SELECT * FROM layout_options " .
		"WHERE form_id = ? AND uor > 0 AND group_name = ? " .
		"ORDER BY seq", array($formtype, $this_group) );
	?>

		<div class="tab <?php echo $first ? 'current' : '' ?>">
			<table border='0' cellpadding='0'>

			<?php
				while ($group_fields = sqlFetchArray($group_fields_query)) {

					$titlecols  = $group_fields['titlecols'];
					$datacols   = $group_fields['datacols'];
					$data_type  = $group_fields['data_type'];
					$field_id   = $group_fields['field_id'];
					$list_id    = $group_fields['list_id'];
					$currvalue  = '';

					if ($formtype == 'DEM') {
					  if ($GLOBALS['athletic_team']) {
						// Skip fitness level and return-to-play date because those appear
						// in a special display/update form on this page.
						if ($field_id === 'fitness' || $field_id === 'userdate1') continue;
					  }
					  if (strpos($field_id, 'em_') === 0) {
					// Skip employer related fields, if it's disabled.
						if ($GLOBALS['omit_employers']) continue;
						$tmp = substr($field_id, 3);
						if (isset($result2[$tmp])) $currvalue = $result2[$tmp];
					  }
					  else {
						if (isset($result1[$field_id])) $currvalue = $result1[$field_id];
					  }
					}
					else {
					  if (isset($result1[$field_id])) $currvalue = $result1[$field_id];
					}

					// Handle a data category (group) change.
					if (strcmp($this_group, $last_group) != 0) {
					  $group_name = substr($this_group, 1);
					  // totally skip generating the employer category, if it's disabled.
					  if ($group_name === 'Employer' && $GLOBALS['omit_employers']) continue;
					  $last_group = $this_group;
					}

					// Handle starting of a new row.
					if (($titlecols > 0 && $cell_count >= $CPR) || $cell_count == 0) {
					  disp_end_row();
					  echo "<tr>";
					}

					if ($item_count == 0 && $titlecols == 0) {
						$titlecols = 1;
					}

					// Handle starting of a new label cell.
					if ($titlecols > 0) {
					  disp_end_cell();
					  $titlecols_esc = htmlspecialchars( $titlecols, ENT_QUOTES);
					  echo "<td class='label' colspan='$titlecols_esc' ";
					  echo ">";
					  $cell_count += $titlecols;
					}
					++$item_count;

					// Added 5-09 by BM - Translate label if applicable
					if ($group_fields['title']) echo htmlspecialchars(xl_layout_label($group_fields['title']).":",ENT_NOQUOTES); else echo "&nbsp;";

					// Handle starting of a new data cell.
					if ($datacols > 0) {
					  disp_end_cell();
					  $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
					  echo "<td class='text data' colspan='$datacols_esc'";
					  echo ">";
					  $cell_count += $datacols;
					}

					++$item_count;
					echo generate_display_field($group_fields, $currvalue);
				  }
        disp_end_row();
			?>
                                    
			</table>
		</div>

 	 <?php

	$first = false;

	}
        ?>
        <div class="tab ">
            <table border='0' cellpadding='0' width="100%" align="center">
                <tr>
                <td>
                <?php 
                        echo allcare1t01po($pid);                
                ?>
                </td>
                </tr>
            </table>
        </div>
                
                            
        <?php

}
$grp_stat=$_REQUEST['grp_stat'];?>

<div id='div_stat' ><div id="dvLoading3" style="display:none; "></div></div>
<script>
    $(document).ready(function() {
         $('#dvLoading3').show();    
         $("#div_stat").load("history_status.php?grp_stat=<?php echo $grp_stat; ?>&form_id=<?php echo $form_id; ?>");
         $('#dvLoading3').hide();
    });
</script>    
<div style='float:none; margin-top: 10px; margin-right:20px'>
    <table>
    <tr>
        <td>
            <!-- Demographics -->
            <div id="HIS">
                <ul class="tabNav">
                   <?php display_layout_tabs_custom('HIS', $grname,$result, $result2); ?>
                </ul>
                <div class="tabContainer">
                   <?php display_layout_tabs_data_custom('HIS',$grname, $result, $result2); ?>
                </div>
            </div>
        </td>
    </tr>
    </table>
</div>

</body>
</html>
