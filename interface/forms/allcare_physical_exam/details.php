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

include_once("../../globals.php");
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
include_once("$srcdir/acl.inc");


$formid=$_REQUEST['formid'];
//echo $formid;
 
echo "<div id='details'>";
if($formid!='' && $formid!='undefined'){
//echo "select  * from tbl_allcare_formflag where form_id='$formid' AND form_name='Allcare Physical Exam'";
        $res1=sqlstatement("select  * from tbl_allcare_formflag where form_id='$formid' AND form_name='Allcare Physical Exam'");
       // $rows=sqlFetchArray($res1);
       
}      
   
   echo "<table id='details' border='1'>";
   echo  "<tr>
            <th style='width:20'>UserName</th>
            <th style='width:20'>Pending</th>
            <th style='width:20'>Finalized</th>
            <th style='width:20'>Date</th>
            <th style='width:20'>Action</th>
            <th style='width:20'>IP Address</th>
          </tr>";
   while($rows=sqlFetchArray($res1))   
   {
        $array=unserialize($rows['logdate']);
        $count = count($array);
        foreach ($array as $value) {
            ?>
            <tr>
                <td style="width:20"><?php echo $value['authuser'];?></td>
                <td style="width:20"><?php echo $value['pending'];?></td>
                <td style="width:20"><?php echo $value['finalized']; ?></td>
                <td style="width:20"><?php echo $value['date']; ?></td>
                <td style="width:20"><?php echo $value['action']; ?></td>
                <td style="width:20"><?php echo $value['ip_address']; ?></td>
           </tr>
<?php } 
   }
echo "</table>";
echo "</div>";
?>