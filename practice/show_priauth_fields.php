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
require_once("verify_session.php");

if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}else {
    $provider                    = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}

require_once("../interface/globals.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php"); 
 
$form_id = $_REQUEST['form_id'];

?>
<html>
    <body>
    
     <form name="preauth_forms" id="preauth_forms" method="POST" action="">    
        <div id='f2fdiv'>
   
                <?php
                $fres = sqlStatement("SELECT * FROM layout_options " .
                  "WHERE form_id = 'USERS' AND uor > 0 AND group_name LIKE '%Preauth'" .
                  "ORDER BY  seq");
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
                        //if($group_seq==6)	
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
                $fres = sqlStatement("SELECT * FROM layout_options " .
                  "WHERE form_id = 'USERS' AND uor > 0 AND group_name LIKE '%Preauth'" .
                  "ORDER BY  seq");
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

                    $currvalue= '';

                    $res=sqlstatement("select * from tbl_patientuser where id='".$form_id."'");
                    $frow1 = sqlFetchArray($res);
                    $currvalue=$frow1[$field_id];

                // Handle a data category (group) change.
                  if (strcmp($this_group, $last_group) != 0) {
                    end_group();
                    $group_seq  = substr($this_group, 0, 1);
                    $group_name = substr($this_group, 1);
                    $last_group = $this_group;
                    $group_seq_esc = htmlspecialchars( $group_seq, ENT_QUOTES);

                    echo "<div class='tab current' id='div_$group_seq_esc'>";
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
                 generate_form_field($frow, $currvalue);
                  echo "</div>";

                  }
                end_group();

                ?>
                </div>
         <center><input class="round-button" type='submit' id='ok' value='OK'></center>
        </div>
</form>          
 <!--<input type='checkbox'/>testing-->   
 </body>
</html>   
<style>
        #savealert{
            background-color: #616161;
            border-radius: 4px;
            color: #fff;
            display: none;
            height: 20px;
            left: 50%;
            margin-left: -75px;
            padding: 5px 14px 5px;
            position: fixed;
            text-align: center;
            top: 10px;
            width: 65px;
            display: none;

        }
        .section-header {
            border-bottom: 1px solid;
            margin-bottom: 5px;
            width: 100%;
        }
        div.tab {
            background: #ffffff none repeat scroll 0 0;
            margin-bottom: 10px;
            min-height: auto;
            width: 100%;
        }

        div.tabContainer{
            width: 99%;
        }
        div.tabContainer div.tab {
            padding: 10px 0 10px 10px;
        }
        div.tab table td[class=bold] {
            padding-bottom: 0;
            padding-right: 1px;
            width: auto;
        }

        #info fieldset {
            display: inline;
            height: 79px;
            margin-bottom: 10px;
            min-width: 249px;
            vertical-align: top;
            border-radius: 8px;
        }

        #ptinfo {
            font-size: 15px;
            margin-top: 5px;
        }
        </style>
    <?php 
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

    $last_group = '';
    $cell_count = 0;
    $item_count = 0;
    $display_style = 'block'; 

    $group_seq=0; // this gives the DIV blocks unique IDs

    ?>
    <br>

    <script language="JavaScript">
    <?php echo $date_init; ?>
    </script>
