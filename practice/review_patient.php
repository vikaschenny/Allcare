<?php
//session_start(); 
//require_once("verify_session.php");
// 
//if(isset($_SESSION['portal_username']) !=''){
//    $provider    = $_SESSION['portal_username'];
//    $refer       = $_REQUEST['refer'];
//    
//    if($_REQUEST['refer']!='')
//        $_SESSION['refer'] = $_REQUEST['refer'];
//}else {
//    $provider                    = $_REQUEST['provider'];
//    $_SESSION['portal_username'] = $_REQUEST['provider'];
//    //for logout
//    $refer                       = $_REQUEST['refer'];
//    if($_REQUEST['refer']!='')
//        $_SESSION['refer'] = $_REQUEST['refer'];
//}

require_once("verify_session.php");

if(isset($_SESSION['portal_username']) !=''){
$provider=$_SESSION['portal_username'];
$refer=$_SESSION['refer']; 
}else {
 $provider=$_REQUEST['provider'];
 $refer=$_REQUEST['refer'];  
 $_SESSION['portal_username']=$_REQUEST['provider'];
 $_SESSION['refer']=$_REQUEST['refer'];
}

//require_once("../interface/globals.php");
require_once("../library/formdata.inc.php"); 
require_once("../library/globals.inc.php");
require_once("$srcdir/forms.inc");
require_once("$srcdir/billing.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once "$srcdir/options.inc.php";
include_once("$srcdir/calendar.inc");
include_once("$srcdir/edi.inc");



//for logout
$refer                      = isset($_REQUEST['refer'])     ? $_REQUEST['refer']    : $_SESSION['refer'];
$_SESSION['refer']          = isset($_REQUEST['refer'])     ? $_REQUEST['refer']    : $_SESSION['refer'];
$_SESSION['portal_username']= isset($_REQUEST['provider'])  ? $_REQUEST['provider'] : $_SESSION['provider'];
$sql = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id         = sqlFetchArray($sql);

?>
<html>
<head>
    <title>Review Patient Data</title>
    <style>
@page { size 8.5in 11in; margin: 2cm; }
div.page { page-break-before: always }
ul
{
    list-style-type: none;
    -webkit-padding-start: 0px !important;

}
li { padding-right:40px; }
ul{float:left;}
</style>
<script type="text/javascript" src="../library/js/jquery-1.9.1.min.js"></script>
<script>
$(window).load(function(){
    function breakList(divid,numOfLists, list){
        var listLength = list.find("li").size();
        var numInRow = Math.ceil(listLength / numOfLists);

        for (var i=0;i<numOfLists;i++){
            var listItems = list.find("li").slice(0, numInRow);
            var newList = $('<ul>').append(listItems);
            $(divid).append(newList);
        }
    }
    <?php
    $getcolval = sqlStatement("SELECT DISTINCT CONCAT(form_id,'-',`group`) as cname,layout_col FROM tbl_reviewpatient_mapping WHERE form_id != 'DEFAULT'");
    while($setcolval = sqlFetchArray($getcolval))
    {   
        $idName = str_replace(" ","-",$setcolval['cname']); 
        ?>  
        breakList('#<?php echo "1".$idName; ?>','<?php echo $setcolval['layout_col']; ?>', $('.<?php echo "1".$idName; ?>'));<?php //        
    }
    ?>    
});

</script>
</head>
<body>
<?php 

$pid = $_REQUEST['pid'];

echo "<div class='page' style='clear:both;'>";

$get_related = sqlStatement("select option_value,field_id from tbl_reviewpatient_mapping where form_id = 'DEFAULT' AND option_value = 'YES' order by id"); 

while($set_related = sqlFetchArray($get_related)){
    if($set_related['field_id'] == 'INSU'){
        if($set_related['option_value'] == 'YES'){
            echo "<div id='show_div_insurance'>";
                echo "<div class='text insurance' style='clear:both;'>";
                    echo '<h2>Insurance:</h2>';
                    display_insurance($pid);
                echo "</div>"; 
            echo "</div>";
        }
    }else{
        // to display demographics
        if($set_related['option_value'] == 'YES' && $set_related['field_id'] == 'DEM'){
            display_div_function("Patient Information",0,$pid);
            display_demographics($pid, 'DEM');
        }

        // to display dyanmic screens
        if($set_related['option_value'] == 'YES' && $set_related['field_id']  != 'DEM'){
            display_dynamic_forms($pid,$set_related['field_id']);
        }     
    }
}
echo "</div>";
?>
</body>
</html>
<?php
function display_demographics($pid, $groupName){
    $getgroupnames = sqlStatement("SELECT DISTINCT(group_name) as group_name from layout_options where form_id='DEM' and uor <> 0 order by group_name");
    while($setgroupnames=sqlFetchArray($getgroupnames)){
        $gettitles =  sqlStatement("SELECT group_concat(field_id) as id, group_concat(title) as title from layout_options where form_id='DEM' and uor <> 0 AND group_name='".$setgroupnames['group_name']."'"  );
        $idName = trim($groupName)."-".trim($setgroupnames['group_name']);
        $idName = str_replace(" ","-",$idName);
        echo " <div id='".trim($setgroupnames['group_name'])."' style='clear:both;'>";
        while($settitles=sqlFetchArray($gettitles)){
            $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_reviewpatient_mapping WHERE `group` = '".str_replace(' ','-',$setgroupnames['group_name'])."' AND option_value = 'YES' AND form_id='DEM'");
            $idlist2 = '';
            while($setselectedvalues= sqlFetchArray($getselectedvales)){
                $selected= explode(',',$settitles['id']);
                for($i=0; $i< count($selected); $i++){
                    if($setselectedvalues['selectedfield'] == $selected[$i] ):
                        if($selected[$i] == 'em_city' || $selected[$i] == 'em_street' || $selected[$i] == 'em_name' || $selected[$i] == 'em_state' || $selected[$i] == 'em_postal_code' || $selected[$i] == 'em_country'):
                            $check = 1;
                            $idlist2 .= "e.".substr($setselectedvalues['selectedfield'],3).",";
                        elseif($selected[$i] == 'title' || $selected[$i] == 'fname' || $selected[$i] == 'lname' || $selected[$i] == 'mname'):
//                            if($selected[$i] == 'title'):
//                                $title = 'p.title,';
//                            elseif($selected[$i] == 'fname'):
//                                $fname = '" ",p.fname,';
//                            elseif($selected[$i] == 'mname'):
//                                $mname = '" ",p.mname,';
//                            elseif($selected[$i] == 'lname'):
//                                $lname = '" ",p.lname';
//                            endif;
                            $title_name = '';
                            if($selected[$i] == 'title'):
                                $title_name = 'p.title,';
                            elseif($selected[$i] == 'fname'):
                                $title_name .= '" ",p.fname,';
                            elseif($selected[$i] == 'mname'):
                                $title_name .= '" ",p.mname,';
                            elseif($selected[$i] == 'lname'):
                                $title_name .= '" ",p.lname';
                            endif;
                            $fullname = rtrim($title_name,",");
//                            $getname = "CONCAT(".$title.$fname.$mname.$lname.") as Name,";
                            $getname = "CONCAT(".$fullname.") as Name,";
                        else:
                            $idlist2 .= "p.".$setselectedvalues['selectedfield'].",";
                        endif;
                    endif;
                }
            }
            $idlist = rtrim($idlist2, ',');
            if($idlist !=''):
                echo "<div id='1".$idName."' style='clear:both;display:none;' ><ul class='1".$idName."' >";
                if(substr($setgroupnames['group_name'], 1) != 'Who' ):
                    $getname = '';
                endif;
                if($check == 1):
                    $getgroupval2 = sqlStatement("SELECT ". $idlist." FROM patient_data p LEFT JOIN employer_data e ON e.pid= p.pid WHERE e.pid = $pid ");
                else:
                    $getgroupval2 = sqlStatement("SELECT ".$getname. $idlist." FROM patient_data p WHERE pid = $pid ");
                endif;
                $getgroupval = sqlFetchArray($getgroupval2);
                if(!empty($getgroupval)){
                    foreach($getgroupval as $key => $val){

                        $explodeval = array();
                        $listname = '';
                        $getlistname = sqlStatement("SELECT list_id, field_id, title, data_type FROM layout_options WHERE field_id = '$key'" );
                        while($setlistname=sqlFetchArray($getlistname)){
                            $listname = $setlistname['list_id'];
                            $field_id = $setlistname['field_id'];
                            $subtitle = $setlistname['title'] ;
                            $datatypeno = $setlistname['data_type'];
                        }
                        if($listname != ''){
                            $explodeval = explode("|", $val);

                            for($i=0; $i< count($explodeval); $i++){
                                $getvalname = sqlStatement("SELECT title FROM list_options WHERE option_id =  '".addslashes($explodeval[$i])."' AND list_id = '$listname'");
                                $setvalname2=sqlFetchArray($getvalname);
                                $getlayoutval = sqlStatement("SELECT layout_col,`group` as group_name FROM tbl_reviewpatient_mapping WHERE `group` = '".str_replace(' ','-',$setgroupnames['group_name'])."' AND form_id='DEM'");
                                $setlayoutval=sqlFetchArray($getlayoutval);
                                 //echo $setlayoutval['layout_col']."===".$setlayoutval['group_name'];

                                if(empty($setvalname2)){
                                    //echo   "<li><b>".$subtitle.": </b></li>";
                                }else{
                                    foreach($setvalname2 as $setvalname){
                                        if(!empty($setvalname) && $setvalname != '0000-00-00 00:00:00' && $setvalname != '0000-00-00')
                                        echo   "<li><b>".$subtitle.": </b>".$setvalname."</li>";
                                    }
                                } 
                            }
                        }else{ 
                           $subtitle2 = '';
                            if($key == 'Name'):
                                $subtitle2 = 'Name';
                            else:
                                if($subtitle != ''):
                                    $subtitle2 = $subtitle;
                                else:
                                    $subtitle2 = $field_id;
                                endif; 
                            endif;    

                            if($key == 'providerID' || $key == 'ref_providerID')
                            {
                                if(!empty($val) && $val != '0000-00-00 00:00:00' && $val != '0000-00-00'){
                                    $getporvidername = sqlStatement("SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$val'" );
                                    $rowName=sqlFetchArray($getporvidername);
                                    $provider_name=$rowName['name'];
                                    echo "<li><b>".$subtitle2.": </b>".$provider_name."</li>";
                                }
                            }elseif($key == 'pharmacy_id' )
                            {
                                if(!empty($val) && $val != '0000-00-00 00:00:00' && $val != '0000-00-00'){ 
                                   $getpharmacyname = sqlStatement("SELECT name FROM pharmacies WHERE id='$val'" );
                                   $rowName=sqlFetchArray($getpharmacyname);
                                   $setpharmacyname=$rowName['name'];
                                    echo "<li><b>".$subtitle2.": </b>".$setpharmacyname."</li>";
                                }    
                            } else{
                                if(!empty($val) && $val != '0000-00-00 00:00:00' && $val != '0000-00-00')
                                    echo "<li><b>".$subtitle2.": </b>".$val."</li>";
                            }
                        }
                    }
                }
                ?>

                <script>
                    if ($('#<?php echo "1$idName"; ?> li').length != 0)
                        document.getElementById('<?php echo "1$idName"; ?>').style.display = "block";
                </script>
                <?php
                
                echo "<ul/></div>";
            endif;
        }
        echo "</div>";
    }
}
function display_insurance($patient_id) {    
    $datacheck = '';
    $left_div_fields_array=array('Payer','Priority','Type','Relationship_to_Insured','Start_Date','End_Date');
    $right_div_fields_array=array('Copay','Group_Number','Insured_ID_Number','Employer_Name');                
    $getInsuranceData="SELECT insd.type AS Priority,
                              insd.copay AS Copay,
                              insd.date AS Start_Date,
                              insd.subscriber_relationship AS Relationship_to_Insured,
                              insd.policy_type AS Type,
                              insd.policy_number AS Insured_ID_Number,
                              insd.group_number AS Group_Number,
                              CONCAT(insd.subscriber_fname,' ',insd.subscriber_lname) AS Employer_Name,
                              insc.name AS Payer 
                       FROM insurance_data insd
                       INNER JOIN insurance_companies insc ON insd.provider=insc.id
                          
                       WHERE insd.pid='".$patient_id."'";        

    $resInsuranceData= sqlStatement($getInsuranceData);                
    while($rowInsuranceData= sqlFetchArray($resInsuranceData)){
        echo "<h3>".$rowInsuranceData['Payer']."</h3> ";
	echo "<table style='width:100%;'><tr>";
        echo "  <td style='width:50%;'>";
        $datacheck = 1 ;
        foreach($rowInsuranceData as $key=>$value) {
            if(in_array($key,$left_div_fields_array))  {
                if($key=='Type'){                        
                        $policy_types = array(
                            ''   => xl('N/A'),
                            '12' => xl('Working Aged Beneficiary or Spouse with Employer Group Health Plan'),
                            '13' => xl('End-Stage Renal Disease Beneficiary in MCP with Employer`s Group Plan'),
                            '14' => xl('No-fault Insurance including Auto is Primary'),
                            '15' => xl('Worker`s Compensation'),
                            '16' => xl('Public Health Service (PHS) or Other Federal Agency'),
                            '41' => xl('Black Lung'),
                            '42' => xl('Veteran`s Administration'),
                            '43' => xl('Disabled Beneficiary Under Age 65 with Large Group Health Plan (LGHP)'),
                            '47' => xl('Other Liability Insurance is Primary'),
                          );// taken from patient.inc
                       $value=$policy_types[$value];  
                    // $policy_types array comes from patient.inc
                    }
                    echo "<label><b>".ucfirst(str_replace('_',' ',$key))." : </b></label>";
                    echo "<label>".ucfirst($value)."</label>";
                    echo "<br>";
                }
        }                
        echo "</td>";
        echo "  <td style='width:50%;'>";
        foreach($rowInsuranceData as $key=>$value) {
            if(in_array($key,$right_div_fields_array)){
                echo "<label><b>".ucfirst(str_replace('_',' ',$key))." : </b></label>";
                echo "<label>".$value."</label>";                
                echo "<br>";
            }                
        }
        echo "</td></tr></table>";
        echo "<br>";
    }
    if($datacheck == ''){
        ?>
        <style type="text/css">
            #show_div_insurance{
                display:none;
            }
        </style>
        <?php
    }                
//        echo "No Insurance data for this patient.";
}
function display_dynamic_forms($pid,$form_name){
    $table_name = '';
    $groupName = $form_name;           
    if($form_name == 'HIS'){
        $patientname = 'pid';
        $table_name = ' history_data ';
        $getgroupval3 = sqlStatement("SELECT date as Date,date FROM $table_name  WHERE $patientname = $pid order by date desc limit 1");
        $getgroupval3=sqlFetchArray($getgroupval3);
    }
    if($form_name == 'SERVICEFAC'){
        $patientname = 'patientid';
        $table_name = ' tbl_patientfacility ';
    }
    if($form_name == 'AGENCY'){
        $patientname = 'patientid';
        $table_name = ' tbl_patientagency ';
    }
    $getgroupnames = sqlStatement("SELECT DISTINCT(group_name) as group_name from layout_options where form_id='$form_name' and uor <> 0 order by group_name");
    while($setgroupnames=sqlFetchArray($getgroupnames)){
        $setpagebr_allr = '';
        echo "<div id='".$setgroupnames['group_name']."' style='clear:both;'>";
        $getpagebr = "SELECT `group` as group_name, layout_type,page_break FROM tbl_reviewpatient_mapping WHERE form_id='$form_name' AND `group` LIKE '".str_replace(' ','-',$setgroupnames['group_name'])."'";
        $getpagebr_allr = sqlStatement($getpagebr);
        $setpagebr_allr = sqlFetchArray($getpagebr_allr);      
        if(str_replace('-',' ',$setpagebr_allr['group_name']) == $setgroupnames['group_name']) {
            $idName = str_replace(" ","-",trim($groupName)."-".$setgroupnames['group_name']);
            if($setpagebr_allr['page_break'] == 'YES'){
                display_div_function(substr($setgroupnames['group_name'], 1),0,$pid);
            }else{
                echo "<h2>". substr($setgroupnames['group_name'], 1).": </h2>";
            }
        }
        echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
        echo "<ul class='1".$idName.$j."' >"; 
        $gettitles =  sqlStatement("SELECT group_concat(field_id) as id, group_concat(title) as title from layout_options where form_id='$form_name' and uor <> 0 AND group_name='".$setgroupnames['group_name']."'"  );
        while($settitles=sqlFetchArray($gettitles)){
            $datacheck = '';
            $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_reviewpatient_mapping WHERE form_id='$form_name' AND `group` = '".str_replace(' ','-',$setgroupnames['group_name'])."' AND option_value = 'YES'");
            if(mysql_num_rows($getselectedvales)== 0){
                ?>
                <script>
                    document.getElementById('<?php echo $setgroupnames['group_name']; ?>').style.display = "none";
                </script>
                <?php
            }
                
            $idlist2 = '';
            while($setselectedvalues= sqlFetchArray($getselectedvales)){
                $selected= explode(',',$settitles['id']);
                for($i=0; $i< count($selected); $i++){
                    if($setselectedvalues['selectedfield'] == $selected[$i] ):
                        $idlist2 .= "`".$setselectedvalues['selectedfield']."`,";
                    endif;
                }
            }
            $idlist = rtrim($idlist2, ',');
            if($idlist !=''){
                $datecheck = '';
                if($form_name == 'HIS')
                    $datecheck = 'order by date desc limit 1';
                $getgroupcheck = sqlStatement("SELECT  ". $idlist."  FROM  $table_name WHERE $patientname = $pid $datecheck");
                $setgroupcheck=sqlFetchArray($getgroupcheck);
                $datacheck= 0; $add=0;
                foreach ($setgroupcheck as $key3 => $value3) { 
                    if(empty($value3) || $value3 == '|0||' || $value3 == '|0|'){
                        $datacheck = $datacheck+1;
                    }else{    
                        $add= $add+1;
                    }    
                }
                $getgroupval = '';
                $getgroupval2 = sqlStatement("SELECT  ". $idlist."  FROM $table_name  WHERE $patientname = $pid $datecheck");
                $getgroupval=sqlFetchArray($getgroupval2);
                if(!empty($getgroupval)){
                   foreach($getgroupval as $key => $val){
                        $explodeval = array();
                        $listname = '';
                        $getlistname = sqlStatement("SELECT list_id, field_id, title, data_type FROM layout_options WHERE field_id = '$key'" );
                        while($setlistname=sqlFetchArray($getlistname)){
                            $listname = $setlistname['list_id'];
                            $subtitle = $setlistname['title'];
                            $datatypeno = $setlistname['data_type'];
                            $field_id = $setlistname['field_id'];
                        }
                        
                        if($listname != ''){
                            if($datatypeno == 23){ 
                                if(!empty($val)){
                                    $explodeval2 = explode("|", $val);
                                    $explodelist2 = array();
                                    for($i= 0; $i< count($explodeval2); $i++){
                                        $explodelist2 = explode(":", $explodeval2[$i]);
                                        $getvalname = sqlStatement("SELECT title FROM list_options WHERE option_id =  '".addslashes($explodelist2[0])."' AND list_id = '$listname'");

                                        while($setvalname=sqlFetchArray($getvalname)){
                                            if($explodelist2[1] == 0){
                                                $type = 'N/A';
                                                //$nastring .= $setvalname['title'].",";
                                            }elseif($explodelist2[1] == 1){ 
                                                $type = 'Normal';
                                                //$normalstring .=  $setvalname['title'].",";
                                            }elseif($explodelist2[1] == 2){
                                                $type = 'Abnormal';
                                                //$abnormalstring .= $setvalname['title'].",";
                                            }
                                            if($explodelist2[1] == 2 || !empty($explodelist2[2])){
                                                echo "<li><b>".$setvalname['title']."</b>&nbsp&nbsp $type&nbsp&nbsp ".$explodelist2[2]."<li>";
                                                //$i++;$datacheck = '';
                                            }    
                                        }
                                    }
                                }    
    //                            echo "<li><b>N/A: </b>".rtrim($nastring, ',');
    //                            if($nastring != '') echo ".</li>";
    //                            
    //                            echo "<li><b>Normal: </b>".rtrim($normalstring, ',');
    //                            if($normalstring != '') echo ".</li>";
    //                            
    //                            echo "<li><b>Abnormal: </b>".rtrim($abnormalstring, ',');
    //                            if($abnormalstring != '') echo ".</li>";

                            }elseif($datatypeno == 32){
                                if(!empty($val) && $val != '|0||'){
                                    $explodeval = explode("|", $val); 
                                    //if($val != '|0||'){
                                        echo "<li><b>".$subtitle.": </b>";
                                        if($datatypeno == 32):
                                            $getvalname = sqlStatement("SELECT title FROM list_options WHERE option_id =  '".addslashes($explodeval[3])."' AND list_id = '$listname'");
                                            while($setvalname=sqlFetchArray($getvalname)){
                                                echo $setvalname['title']."               ";
                                            }
                                        endif;
                                        $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                        foreach($statustypes as $key => $stype){
                                            if($explodeval[1] == $key.$field_id):
                                                $statusname = $stype;
                                            endif;
                                        }

                                        if(!empty($explodeval[0]) || !empty($statusname) || !empty($explodeval[2])){
                                            echo $explodeval[0].str_repeat('&nbsp;', 5)."<b><u>Status</u>:</b> ".$statusname. "  ".$explodeval[2]."</li>";
                                            //$i++;$datacheck = '';
                                        }    
                                    }
                                //}
                            }else{
                                $setedvalname = '';
                                if(!empty($val) && $val !== '|0|'){
                                    $explodeval = explode("|", $val);
                                    //if($val != '|0|'){
                                        for($i=0; $i< count($explodeval); $i++){
                                            $getvalname = sqlStatement("SELECT title FROM list_options WHERE option_id =  '".addslashes($explodeval[$i])."' AND list_id = '$listname'");
                                           while($setvalname=sqlFetchArray($getvalname)){
                                                $setedvalname .=  $setvalname['title'].",";
                                            }
                                        }
                                        if(!empty($setedvalname))
                                            echo "<li><b>".$subtitle.": </b>";
                                            $trimedvalue =  rtrim($setedvalname, ',');
                                            //$i++;$datacheck = '';
                                        if($trimedvalue != ''){
                                            echo $trimedvalue.".";
                                        }
                                        echo "</li>";
                                    }
                                }
                            //}    
                        }else{ 
                            if($datatypeno == 28){
                                if(!empty($val) && $val != '|0|'){
                                    $explodeval = explode("|", $val);
                                    //if($val != '|0|'){
                                        echo "<li><b>".$subtitle.": </b>";

                                        $statustypes = array('current'=>'Current','quit' =>'Quit','never' => 'Never','not_applicable' => 'N/A');
                                        foreach($statustypes as $key => $stype){
                                            if($explodeval[1] == $key.$field_id):
                                                $statusname = $stype;
                                            endif;
                                        }
                                        if(!empty($explodeval[0]) || !empty($statusname) || !empty($explodeval[2])){
                                            echo $explodeval[0].str_repeat('&nbsp;', 5)."<b><u>Status</u>:</b> ".$statusname. "  ".$explodeval[2] . "</li>";
                                            //$i++;$datacheck = '';
                                        }    
                                    }
                                //}    
                            }else{
                                $subtitle2 = '';
                                if($key == 'Date1'){
                                    $subtitle2 = 'Last Recorded On';
                                }else{
                                    if($subtitle != ''):
                                        $subtitle2 = $subtitle;
                                    else:
                                        $subtitle2 = $field_id;
                                    endif;
                                }
                                if(!empty($val)){
                                    if( $add != 0){
                                      echo "<li><b>".$subtitle2.": </b>".$val;  
                                    }
                                }
                                    $valcheck = '';
                                    if($i==0) $valcheck = 1;
                                if($val != ''  && $add >= 1) echo ".";
                                echo "</li>";
                            }
                        }
                    }
                    if($add==0){
                        ?>
                        <script>
                            document.getElementById('<?php echo "1".$idName.$j; ?>').style.display = "none";
                            document.getElementById('<?php echo $setgroupnames['group_name']; ?>').style.display = "none";
                        </script> 
                         <?php
                    }
                }else{
                    //echo "No such related data for this patient.";
                    ?>
                    <script>
                        document.getElementById('<?php echo "1".$idName.$j; ?>').style.display = "none";
                        document.getElementById('<?php echo $setgroupnames['group_name']; ?>').style.display = "none";
                    </script> 
                     <?php
                }    
            }
            echo "<ul/></div></div>";
        }
    } 
}
function display_div_function($gname,$encounter,$pid){
    if($gname == 'Patient Information')
        echo "<div style='clear:both'>";
    else
        echo "<div class='page' style='clear:both'>";
    $getPatientName = sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname ,pid,DATE_FORMAT(DOB,'%m-%d-%Y') as DOB ,ss, providerID, street,city,state,country_code,postal_code FROM patient_data WHERE pid=".$pid."");
    $resPatientName = sqlFetchArray($getPatientName);
    $name           = $resPatientName['pname'];
    $dob            = $resPatientName['DOB'];
    $ssn            = $resPatientName['ss'];
    $pid            = $resPatientName['pid'];
    $provider       = $resPatientName['providerID'];   
    $getporvidername= sqlStatement("SELECT f.name as faclityname, f.street, f.city, f.state, f.postal_code, f.country_code, f.email,f.website,f.fax, f.phone
                                        FROM facility f
                                        WHERE primary_business_entity=1" );
    $rowName        = sqlFetchArray($getporvidername);
    if(!empty($rowName))
        $count_rows = mysql_num_rows($getporvidername);
    else
        $count_rows = 0;
    if(!empty($rowName)){
        //$provider_name  = $rowName['name'];
        $facilityname   = $rowName['faclityname'];
        if(!empty($rowName['website']))
            $website = $rowName['website'].", ";
        if(!empty($rowName['phone']))
            $phone = "Phone:".$rowName['phone'].", ";
        if(!empty($rowName['fax']))
            $fax = "Fax:".$rowName['fax'].", ";
        if(!empty($rowName['fax']))
           $email =  "Email:".$rowName['email'];
        $location       = $rowName['street'].", ".$rowName['city'].", ".$rowName['state'].", ".$rowName['country_code'].", ".$rowName['postal_code'];
        $contact        = str_replace(',,', ' ', $website.$phone.$fax.$email);
    }
     echo "<br><div style='width:980px;height:110px;border:1px solid #000;clear:both;'><table width='980px' height:'110px'><tr><td align='left' width='70%' height= ><b>$name: $gname</b><br>Patient Record Number:$pid</td><td width='70%'><span><b>$facilityname</b><br><font size='2'>$location<br>$contact</font></b></span></td></tr></table></div><br>";
    if($encounter != 0){
        $get_rendering_provider = sqlStatement("SELECT CONCAT(u.fname,' ',u.lname) AS provider_name ,u.id, DATE_FORMAT(f.date,'%d-%M-%Y') as date
                                        FROM users u
                                        INNER JOIN form_encounter f ON f.provider_id = u.id
                                        WHERE f.encounter = $encounter ");
        $set_rendering_provider = sqlFetchArray($get_rendering_provider);
        if(!empty($set_rendering_provider)){
        $getprovider_credentials = sqlStatement("SELECT provider_credentials FROM tbl_patientuser WHERE userid = ".$set_rendering_provider['id']);
        $setprovider_credentials = sqlFetchArray($getprovider_credentials);
        $provider_cred = $setprovider_credentials['provider_credentials'];
        }else{
            $provider_cred = '';
        }
        echo "<div align='right'>";
        echo "<b>Seen by </b>".$set_rendering_provider['provider_name']."&nbsp; <font size='2' >". $provider_cred."</font><br>";
        echo "<b>Seen on </b>".$set_rendering_provider['date'];
        echo "</div>";
    }
    echo "</div>";
}
?>