<?php
include_once("../../interface/globals.php");
include_once("$srcdir/patient.inc");
include_once("$srcdir/acl.inc");
include_once("$srcdir/options.inc.php");
include_once("$srcdir/formdata.inc.php");
require_once("$srcdir/addrbk_attr.inc.php");


// Update address book custom attributes 
//
$newdata = array();
$newdata[' tbl_addrbk_custom_attr_1to1']['addrbk_type_id'] = $_POST['db_addrid'];
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'ADDRCA' AND uor > 0 AND field_id != '' " .
  "ORDER BY group_name, seq");
while ($frow = sqlFetchArray($fres)) {
  $data_type = $frow['data_type'];
  $field_id  = $frow['field_id'];
  // $value  = '';
  $colname = $field_id;
  $table = 'tbl_addrbk_custom_attr_1to1';
  
  // if (isset($_POST["form_$field_id"])) $value = $_POST["form_$field_id"];
  $value = get_layout_form_value($frow);

  $newdata[$table][$colname] = $value;
}

//$id=$_POST['db_id'];
$addrid=$_POST['db_addrid'];
$sql=sqlStatement("select * from tbl_addrbk_custom_attr_1to1 where addrbk_type_id='".$addrid."'");
$rowpha=sqlFetchArray($sql);
if($addrid==$rowpha['addrbk_type_id'])
{
    updateAddrbkAttr($addrid, $newdata['tbl_addrbk_custom_attr_1to1'] ,$create=false);
}
else
{
    updateAddrbkAttr($addrid, $newdata['tbl_addrbk_custom_attr_1to1'],$create=true);

}
$portalStatus = sqlQuery("SELECT agencyportal FROM tbl_addrbk_custom_attr_1to1 WHERE addrbk_type_id=?",array($addrid));
$providerlognstates ="";
if ($portalStatus['agencyportal']=='YES') {
    $portalLogin = sqlQuery("SELECT uid FROM `tbl_allcare_agencyportal` WHERE `uid`=?", array($addrid));
    $providerlognstates .= "<a class=\"css_button iframe small_modal\" href=\"create_agencyportallogin.php?portalsite=on&userid=";
    $providerlognstates .= htmlspecialchars($addrid,ENT_QUOTES);
    $providerlognstates .= "\" onclick=\"top.restoreSession()\">";
        
    if(empty($portalLogin)) {
        $providerlognstates .= "<span>".htmlspecialchars(xl('Create'),ENT_NOQUOTES)."</span></a>";
    }else{
         $providerlognstates .= "<span>".htmlspecialchars(xl('Reset'),ENT_NOQUOTES)."</span></a>";
    }
}else {
   $providerlognstates .=  "User Not Authorized";
} 
?>
<script>
  window.parent.hidepopover('<?php echo $providerlognstates; ?>','<?php echo $addrid; ?>');
</script>
<?php 
    if ($GLOBALS['concurrent_layout']) {
     include "addrbook_attr_dropdown_1to1.php" ;
    } else {
     include_once("addrbk_attr_full_1to1.php");
    }
?>