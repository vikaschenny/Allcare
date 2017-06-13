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
//print_r($_POST);
if($_POST['save']!=''){
    
     $cat=sqlStatement("SELECT * FROM  `openemr_postcalendar_categories` order by pc_catid asc");
      while($res2=sqlFetchArray($cat)){ 
          $catid1=$res2['pc_catid'];
          $chartgrp=$_POST["chartgroups_$catid1"];
          $sel=sqlStatement("select * from tbl_visitcat_chartgrp_mapping where visit_category=$catid1");
          $sel_res=sqlFetchArray($sel);
          if(!empty($sel_res)){
              $update=sqlStatement("update tbl_visitcat_chartgrp_mapping set chart_group='$chartgrp' where visit_category=$catid1");
          }else {
              $insert=sqlStatement("insert into tbl_visitcat_chartgrp_mapping (visit_category,chart_group) values ($catid1,'$chartgrp')");
          }
          
        }
}

?>
<html>
    <head>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.js"></script>
        <style>
            th, td {
            border-bottom: 1px solid #ddd;
            padding:2px;
            }
        </style>
    </head>
    <body style="background-color: #fefdcf;">
        <h3>Visit Category - ChartGroup Mapping</h3>
        <form action="" method="POST">
            <input type="submit" name="save" id="save" value="Save"/><br><br>
            <table>
                <tr><th>Visit Category</th>
                    <th>Chart Group</th></tr>
                
                <?php
                    $sql=sqlStatement("SELECT * FROM  `openemr_postcalendar_categories` order by pc_catid asc");
                    while($res=sqlFetchArray($sql)){
                        $catid=$res['pc_catid'];
                        echo  "<tr><td>".$res['pc_catname'] .":"."</td>";
                        $sql1=sqlStatement("SELECT DISTINCT(group_name ) as group_name FROM layout_options " .
                                           "WHERE form_id = 'CHARTOUTPUT' AND uor > 0 " .
                                           "ORDER BY group_name");
                        echo  "<td><select id ='chartgroups_$catid'  name='chartgroups_$catid'>
                               <option value=''> Select </option>";
                        while($res1=sqlFetchArray($sql1)){ 
                           
                            $sel1=sqlStatement("select * from tbl_visitcat_chartgrp_mapping where visit_category=$catid");
                            $sel_res1=sqlFetchArray($sel1);
                            echo "<option value=".$res1['group_name']."";
				  if ($sel_res1['chart_group']==$res1['group_name']) echo " selected";
                            echo ">" .substr($res1['group_name'],1). "</option>";
                        
                        }
                       echo"</select></td></tr>";
                    }
                ?>
            </table>   
        </form>
    </body>
</html>