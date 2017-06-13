
<style type="text/css">
#details {
 font-family: verdana,arial,sans-serif;
 font-size:11px;
 color:#333333;
 border-width: 1px;
 border-color: #999999;
 border-collapse: collapse;
}
#details th {
 background-color:#c3dde0;
 border-width: 1px;
 padding: 8px;
 border-style: solid;
 border-color: #a9c6c9;
}
#details tr {
 background-color:#ffffcc;
}
#details td {
 border-width: 1px;
 padding: 8px;
 border-style: solid;
 border-color: #a9c6c9;
}
</style>
<?php
// Copyright (C) 2006, 2010 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

include_once("../globals.php");
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
include_once("$srcdir/acl.inc");


$formid=$_REQUEST['formid'];
//echo $formid;
 
echo "<div id='details'>";
  
       
   
        $res1=sqlstatement("select  * from tbl_allcare_formflag where  form_name='Allcare Encounter Forms' order by id desc");
       
        
 
   echo "<table id='details' border='1'>";
   echo  "<tr><th style='width:20'>UserName</th><th style='width:20'>FormId</th><th style='width:20'>Encounter</th><th style='width:20'>Date</th><th style='width:20'>Action</th><th style='width:20'>Form</th></tr>";
   while($rows=sqlFetchArray($res1))   
   {
        $array=unserialize($rows['logdate']);
        $count = count($array);
    ?>
      <tr>
        <td style="width:20"><?php echo $array[$count-1]['authuser'];?></td> <td style="width:20"><?php echo $rows['form_id'];?></td> 
       <td style="width:20"><?php echo $rows['encounter_id'];?></td><td style="width:20"><?php echo $array[$count-1]['date']; ?></td>
       <td style="width:20"><?php echo $array[$count-1]['action']; ?></td><td style="width:20"><?php echo $array[$count-1]['formName']; ?></td>
   </tr>
<?php } 
echo "</table>";
echo "</div>";
?>