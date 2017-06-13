<?php 

require_once("../../globals.php");
require_once("$srcdir/chartoutput_lib.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/amc.php");

$trow = $_REQUEST['id'] ?getChartOutputById($_REQUEST['id'],$_REQUEST['chartgroupshidden'],$_REQUEST['f2ftrans']) : array();
$transtype=$_REQUEST['f2ftrans'];
// Function to generate a drop-list.
//
function generate_select_list_chart($tag_name, $list_id, $currvalue, $dvalue,$title,
  $empty_name=' ', $class='', $onchange='', $tag_id = '', $custom_attributes = null )
{
  
  $s = '';
  $tag_name_esc = htmlspecialchars( $tag_name, ENT_QUOTES);
  $s .= "<select name='$tag_name_esc'";
  $tag_id_esc = $tag_name_esc;
  if ( $tag_id != '' ) {
      $tag_id_esc = htmlspecialchars( $tag_id, ENT_QUOTES);
  }   
  $s .=  " id='$tag_id_esc'";
  if ($class) $s .= " class='$class'";
  if ($onchange) $s .= " onchange='$onchange'";
  if ( $custom_attributes != null && is_array($custom_attributes) ) {
      foreach ( $custom_attributes as $attr => $val ) {
          if ( isset($custom_attributes[$attr] ) ) {
              $s .= " ".htmlspecialchars( $attr, ENT_QUOTES)."='".htmlspecialchars( $val, ENT_QUOTES)."'";
          }
      }
  }
  $selectTitle = htmlspecialchars( $title, ENT_QUOTES);
  $s .= " title='$selectTitle'>";
  $selectEmptyName = htmlspecialchars( xl($empty_name), ENT_NOQUOTES);
  if(substr($tag_name_esc, -13) == '_demographics'){
        $s .= "<option value ='YES' selected > YES </option>";
        $got_selected = TRUE;
  }else{
    if ($empty_name) $s .= "<option value=''>" . $selectEmptyName . "</option>";
    $lres = sqlStatement("SELECT * FROM list_options " .
      "WHERE list_id = ? ORDER BY seq, title", array($list_id) );
    $got_selected = FALSE;
    if($dvalue && strlen($currvalue)==0 ){
            while ($lrow = sqlFetchArray($lres)) {
              $optionValue = htmlspecialchars( $lrow['option_id'], ENT_QUOTES);
                  $s .= "<option value='$optionValue'";

                  if ((strlen($dvalue) == 0 && $lrow['is_default']) ||
                      (strlen($dvalue)  > 0 && $lrow['option_id'] == $dvalue))
                  {
                    $s .= " selected";
                    $got_selected = TRUE;
                  }

              $optionLabel = htmlspecialchars( xl_list_label($lrow['title']), ENT_NOQUOTES);
              $s .= ">$optionLabel</option>\n";
            }
    } else {
        while ($lrow = sqlFetchArray($lres)) {
              $optionValue = htmlspecialchars( $lrow['option_id'], ENT_QUOTES);
              $s .= "<option value='$optionValue'";
                
              if ((strlen($currvalue) == 0 && $lrow['is_default']) ||
                  (strlen($currvalue)  > 0 && $lrow['option_id'] == $currvalue))
              {
                $s .= " selected";
                $got_selected = TRUE;
              }
              
              $optionLabel = htmlspecialchars( xl_list_label($lrow['title']), ENT_NOQUOTES);
              $s .= ">$optionLabel</option>\n";
            }
    }
}
  if (!$got_selected && strlen($currvalue) > 0) {
    $currescaped = htmlspecialchars($currvalue, ENT_QUOTES);
    $s .= "<option value='$currescaped' selected>* $currescaped *</option>";
    $s .= "</select>";
    $fontTitle = htmlspecialchars( xl('Please choose a valid selection from the list.'), ENT_QUOTES);
    $fontText = htmlspecialchars( xl('Fix this'), ENT_NOQUOTES);
    $s .= " <font color='red' title='$fontTitle'>$fontText!</font>";
  }
  else {
    $s .= "</select>";
  }
  return $s;
}


function generate_form_field_chart($frow, $currvalue,$frow1) {
  global $rootdir, $date_init;
//  print_r($frow);
  $currescaped = htmlspecialchars($currvalue, ENT_QUOTES);

  $data_type   = $frow['data_type'];
  $field_id    = $frow['field_id'];
  $list_id     = $frow['list_id'];
  $dvalue=$frow1['option_value'];
  // escaped variables to use in html
  $field_id_esc= htmlspecialchars( $field_id, ENT_QUOTES);
  $list_id_esc = htmlspecialchars( $list_id, ENT_QUOTES);

  // Added 5-09 by BM - Translate description if applicable  
  $description = (isset($frow['description']) ? htmlspecialchars(xl_layout_label($frow['description']), ENT_QUOTES) : '');
      
  // added 5-2009 by BM to allow modification of the 'empty' text title field.
  //  Can pass $frow['empty_title'] with this variable, otherwise
  //  will default to 'Unassigned'.
  // modified 6-2009 by BM to allow complete skipping of the 'empty' text title
  //  if make $frow['empty_title'] equal to 'SKIP'
  $showEmpty = true;
  if (isset($frow['empty_title'])) {
   if ($frow['empty_title'] == "SKIP") {
    //do not display an 'empty' choice
    $showEmpty = false;
    $empty_title = "Select";
   }
   else {     
    $empty_title = $frow['empty_title'];
   }
  }
  else {
   $empty_title = "Select";   
  }
    
  // generic single-selection list
  if ($data_type == 1) {
     
    echo generate_select_list_chart("form_$field_id", $list_id, $currvalue,$dvalue,
      $description, $showEmpty ? $empty_title : '');
  }

 } 

?>

<div id="chart_output">
    <ul class="tabNav">
        <?php 
        $fres = sqlStatement("SELECT * FROM layout_options " .
          "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND group_name = '".$_POST['chartgroupshidden']."'" .
          "ORDER BY group_name, seq");
        $last_group = '';
        $cell_count = 0;
        $item_count = 0;
        $display_style = 'block';

        while ($frow = sqlFetchArray($fres)) {
          $this_group = $frow['group_name'];
          $titlecols  = $frow['titlecols'];
          $datacols   = $frow['datacols'];
          $data_type  = $frow['data_type'];
          $field_id   = $frow['field_id'];
          $list_id    = $frow['list_id'];



        // Handle a data category (group) change.
        if (strcmp($this_group, $last_group) != 0) {
          $group_seq  = substr($this_group, 0, 1);
          $group_name = substr($this_group, 1);
          $last_group = $this_group;
              //if($group_seq==1)
                  echo "<li class='current'>";
              //else				echo "<li class=''>";
              $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
              $group_name_show = htmlspecialchars( xl_layout_label($group_name), ENT_NOQUOTES);
              echo "<a href='' id='div_$group_seq_esc'>".
                  "$group_name_show</a></li>";
        }
        ++$item_count;
      }
      ?>
      </ul>
      <div class="tabContainer">							
       <?php
       if($transtype==2){
            $fres = sqlStatement("SELECT * FROM layout_options " .
                    "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND group_name = '".$_POST['chartgroupshidden']."' and field_id LIKE '%_f2f%'" .
                    "ORDER BY group_name, seq");
       }else {
            $fres = sqlStatement("SELECT * FROM layout_options " .
                    "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND group_name = '".$_POST['chartgroupshidden']."'" .
                    "ORDER BY group_name, seq");
       }
      
      $last_group = '';
      $cell_count = 0;
      $item_count = 0;
      $display_style = 'block';

      while ($frow = sqlFetchArray($fres)) {
             $this_group = $frow['group_name'];
             $titlecols  = $frow['titlecols'];
             $datacols   = $frow['datacols'];
             $data_type  = $frow['data_type'];
             $field_id   = $frow['field_id'];
             $list_id    = $frow['list_id'];

        
        
        $defaultval = sqlStatement("SELECT option_value FROM tbl_chartui_mapping " .
        "WHERE form_id = 'CHARTOUTPUT' AND group_name = '".$this_group."' AND screen_name='".$this_group."' AND field_id='$field_id'");
         $frow1 = sqlFetchArray($defaultval);
//      print_r($frow1);
       // $dvalue=$frow1;
      

       $currvalue  = '';
        if (isset($trow[$field_id])) $currvalue = $trow[$field_id];
        // Handle a data category (group) change.

      // Handle a data category (group) change.
        if (strcmp($this_group, $last_group) != 0) {
          //end_group();
         $group_seq  = substr($this_group, 0, 1);
         $group_name = substr($this_group, 1);
         $last_group = $this_group;
         $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);
              //if($group_seq==7)	
                  echo "<div class='tab current' id='div_$group_seq_esc'>";
              //				echo "<div class='tab' id='div_$group_seq_esc'>";
          echo " <table border='0' cellpadding='0'>\n";
          $display_style = 'none';
        }
        // Handle starting of a new row.
        if (($titlecols > 0 && $cell_count >= $CPR) || $cell_count == 0) {
          end_row();
          echo " <tr>";
        }

        if ($item_count == 0 && $titlecols == 0) $titlecols = 1;

        // Handle starting of a new label cell.
        if ($titlecols > 0) {
          end_cell();
          $titlecols_esc = htmlspecialchars( $titlecols, ENT_QUOTES);
          echo "<td width='70' valign='top' colspan='$titlecols_esc'";
          echo ($frow['uor'] == 2) ? " class='required'" : " class='bold'";
          if ($cell_count == 2) echo " style='padding-left:10pt'";
          echo ">";
          $cell_count += $titlecols;
        }
        ++$item_count;

        echo "<b>";

        // Modified 6-09 by BM - Translate if applicable
        if ($frow['title']) echo (htmlspecialchars( xl_layout_label($frow['title']), ENT_NOQUOTES) . ":"); else echo "&nbsp;";

        echo "</b>";

        // Handle starting of a new data cell.
        if ($datacols > 0) {
          end_cell();
          $datacols_esc = htmlspecialchars( $datacols, ENT_QUOTES);
          echo "<td valign='top' colspan='$datacols_esc' class='text'";
          if ($cell_count > 0) echo " style='padding-left:5pt'";
          echo ">";
          $cell_count += $datacols;
        }

        ++$item_count;
        generate_form_field_chart($frow, $currvalue,$frow1);
        echo "</div>";
      }
    
      //end_group();

    ?>
    </div>
</div>
<?php 
$CPR = 4; // cells per row

function end_cell() {
  global $item_count, $cell_count;
  if ($item_count > 0) {
    echo "</td>";
    $item_count = 0;
  }
}

function end_row() {
  global $cell_count, $CPR;
  end_cell();
  if ($cell_count > 0) {
    for (; $cell_count < $CPR; ++$cell_count) echo "<td></td>";
    echo "</tr>\n";
    $cell_count = 0;
  }
}

function end_group() {
  global $last_group;
  if (strlen($last_group) > 0) {
    end_row();
    echo " </table>\n";
    echo "</div>\n";
  }
}
?>
<script language="JavaScript">
<?php echo $date_init; ?>
</script>
<?php
