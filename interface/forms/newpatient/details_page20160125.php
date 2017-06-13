<style type="text/css">
#details, #template_details {
	font-family: verdana,arial,sans-serif;
	font-size:11px;
	color:#333333;
	border-width: 1px;
	border-color: #999999;
	border-collapse: collapse;
}
#details th , #template_details th{
	background-color:#c3dde0;
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #a9c6c9;
}
#details tr, #template_details tr {
	background-color:#ffffcc;
}
#details td, #template_details td {
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


$formid=$_REQUEST['id'];
//echo $formid;
$get_encounterdate = sqlStatement("select encounter from forms where form_id='$formid' AND form_name = 'New Patient Encounter' AND formdir='newpatient' AND deleted =0");
$encounterdate = '';
while($set_encounterdate = sqlFetchArray($get_encounterdate)){
    $encounterdate = $set_encounterdate['encounter'];
}
echo "<div id='details'>";
echo "<b>Patient Encounter Form Logs Table</b>";
if(form_id)
    {
        $res1=sqlStatement("select  * from tbl_allcare_formflag where form_id='$formid' AND form_name = 'Patient Encounter'");
       // $rows=sqlFetchArray($res1);
       
        
        
    }
   echo "<table id='details' border='1'>";
   echo  "<tr>
            <th style='width:20'>UserName</th>
            <th style='width:20'>Status</th>
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
                <td style="width:20"><?php echo $value['Status'];?></td>
                <td style="width:20"><?php echo $value['date']; ?></td>
                <td style="width:20"><?php echo $value['action']; ?></td>
                <td style="width:20"><?php echo $value['ip_address']; ?></td>
           </tr>
<?php } 
   }
echo "</table>";
echo "</div>";
echo "<br>";
// Table for Template From data
echo "<div id='template_details'>";
echo "<b>Template From Logs Table</b>";
if(form_id)
    {
        $res1=sqlstatement("select t.user,t.copy_from_enc,t.date,t.form_name,DATE_FORMAT(f.date,'%m-%d-%Y') as dos from tbl_allcare_template t 
            INNER JOIN form_encounter f ON f.encounter = t.copy_to_enc 
            where copy_to_enc = '$encounterdate'");
        
       // $rows=sqlFetchArray($res1);
       
        
        
    }
   echo "<table id='template_details' border='1'>";
   echo  "<tr>
            <th style='width:20'>UserName</th>
            <th style='width:20'>Copied From Encounter Id</th>
            <th style='width:20'>Copied From Date of Service</th>
            <th style='width:20'>Copied On</th>
            <th style='width:20'>Form Name</th>
          </tr>";
   while($rows=sqlFetchArray($res1))   
   {
        
        
            ?>
            <tr>
                <td style="width:20"><?php echo $rows['user'];?></td>
                <td style="width:20"><?php echo $rows['copy_from_enc'];?></td>
                <td style="width:20"><?php echo $rows['dos'];?></td>
                <td style="width:20"><?php echo $rows['date']; ?></td>
                <td style="width:20"><?php echo $rows['form_name']; ?></td>
                <!--<td style="width:20"><?php // echo $rows['ip_address']; ?></td>-->
           </tr>
<?php  
   }
echo "</table>";
echo "</div>";
?>