
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

include_once("../../globals.php");
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
include_once("$srcdir/acl.inc");


$formid=$_REQUEST['formid'];
//echo $formid;
 
echo "<div id='details'>";
  
        /*if($formid)
        {  
            $res1=sqlstatement("select updated_by,created_by from tbl_form_physical_exam_status");
            $rows=sqlFetchArray($res1);
            $data = mysql_query("SELECT logdate from `tbl_allcare_formflag` WHERE  form_id=".$formid);
            while ($row = mysql_fetch_array($data,MYSQL_ASSOC)) {
                $array =  unserialize($row['logdate']);
                $count= count($array);
            }
            $user_column=($rows['updated_by']!=0)?'updated_by':'created_by';
            $resFinalized = sqlStatement("SELECT  DISTINCT(f.id),f. * ,f.finalized ,f.pending , CONCAT( u.fname,  ' ', u.lname ) AS uname, CONCAT( pd.fname,  ' ', pd.lname ) AS pname
                                    FROM tbl_allcare_formflag f
                                    INNER JOIN tbl_form_physical_exam e ON e.forms_id = f.form_id
                                    INNER JOIN users u ON u.id = $user_column
                                    INNER JOIN patient_data pd ON pd.pid =".$GLOBALS['pid']."
                                    WHERE e.forms_id ='$formid'
                                    ORDER BY f.id DESC  LIMIT 0,10;");
            

        }
       else
       {
           echo "form_id is not valid";
           
       }*/
    if(form_id)
    {
        $res1=sqlstatement("select  * from tbl_allcare_formflag where form_id='$formid' AND form_name='Allcare Physical Exam'  order by id desc");
       // $rows=sqlFetchArray($res1);
       
        //print_r($array);
        
    }
   echo "<table id='details' border='1'>";
   echo  "<tr><th style='width:20'>UserName</th><th style='width:20'>Pending</th><th style='width:20'>Finalized</th><th style='width:20'>Date</th><th style='width:20'>Action</th><th style='width:20'>IP Address</th></tr>";
   while($rows=sqlFetchArray($res1))   
   {
        $array=unserialize($rows['logdate']);
        $count = count($array);
    ?>
      <tr>
       <td style="width:20"><?php echo $array[$count-1]['authuser'];?></td><td style="width:20"><?php echo $array[$count-1]['pending'];?></td><td style="width:20"><?php echo $array[$count-1]['finalized']; ?></td><td style="width:20"><?php echo $array[$count-1]['date']; ?></td>
       <td style="width:20"><?php echo $array[$count-1]['action']; ?></td><td style="width:20"><?php echo $array[$count-1]['ip_address']; ?></td>
   </tr>
<?php } 
echo "</table>";
echo "</div>";
?>