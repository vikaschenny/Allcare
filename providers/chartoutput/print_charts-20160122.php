<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true;

    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong
    $landingpage = "index.php?site=".$_SESSION['site_id'];	
    //

    // kick out if patient not authenticated
    //if ( isset($_SESSION['uid']) && isset($_SESSION['patient_portal_onsite']) ) {
    if ( isset($_SESSION['portal_username']) ) {    
    $provider = $_SESSION['portal_username'];
    }
    else {
            session_destroy();
    header('Location: '.$landingpage.'&w');
            exit;
    }
    //

    $ignoreAuth=true; // ignore the standard authentication for a regular OpenEMR user
include_once('../../interface/globals.php');

require_once("$srcdir/options.inc.php");
include_once("$srcdir/patient.inc");
$template_file = $GLOBALS['OE_SITE_DIR'] . "/chartoutput_template.html";
require_once("$srcdir/globals.inc.php");
require_once("$srcdir/classes/CouchDB.class.php");
require_once("$srcdir/report.inc");
require_once("$srcdir/html2pdf/html2pdf.class.php");

$TEMPLATE_LABELS = array();

$tempvalue = 1;                    
$patient_id=$_GET['pid'];
$encounter_id=$_GET['encounter_id'];
$user_id=$_SESSION['authUserID'];
$trans_form_id=$_GET['coid'];
$group=$_GET['group'];


$getPatientName=sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname ,DATE_FORMAT(DOB,'%m-%d-%Y') as DOB ,ss, providerID, street,city,state,country_code,postal_code FROM patient_data WHERE pid=".$patient_id."");
$resPatientName=sqlFetchArray($getPatientName);
$name=$resPatientName['pname'];
$dob=$resPatientName['DOB'];
$ssn=$resPatientName['ss'];
$provider=$resPatientName['providerID'];   
$location=$resPatientName['street'].", ".$resPatientName['city'].", ".$resPatientName['state'].", ".$resPatientName['country_code'].", ".$resPatientName['postal_code'];
//provider
$getporvidername = sqlStatement("SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$provider'" );
$rowName=sqlFetchArray($getporvidername);
$provider_name=$rowName['name'];

function display_demographics($pid, $groupName){
    $getgroupnames = sqlStatement("SELECT DISTINCT(group_name) as group_name from layout_options where form_id='DEM' and uor <> 0 order by group_name");
    while($setgroupnames=sqlFetchArray($getgroupnames)){
        $gettitles =  sqlStatement("SELECT group_concat(field_id) as id, group_concat(title) as title from layout_options where form_id='DEM' and uor <> 0 AND group_name='".$setgroupnames['group_name']."'"  );
        $idName = trim($groupName)."-".trim(substr($setgroupnames['group_name'],1));
        $idName = str_replace(" ","-",$idName);
        echo " <div id='".$idName."' style='clear:both;'><ul class='".$idName."' >";
        while($settitles=sqlFetchArray($gettitles)){
            $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name = '".$setgroupnames['group_name']."' AND option_value = 'YES'");
            $idlist2 = '';
            while($setselectedvalues= sqlFetchArray($getselectedvales)){
                $selected= explode(',',$settitles['id']);
                for($i=0; $i< count($selected); $i++){
                    if($setselectedvalues['selectedfield'] == $selected[$i] ):
                        if($selected[$i] == 'em_city' || $selected[$i] == 'em_street' || $selected[$i] == 'em_name' || $selected[$i] == 'em_state' || $selected[$i] == 'em_postal_code' || $selected[$i] == 'em_country'):
                            $check = 1;
                            $idlist2 .= "e.".substr($setselectedvalues['selectedfield'],3).",";
                        elseif($selected[$i] == 'title' || $selected[$i] == 'fname' || $selected[$i] == 'lname' || $selected[$i] == 'mname'):
                            if($selected[$i] == 'title'):
                                $title = 'p.title,';
                            elseif($selected[$i] == 'fname'):
                                $fname = '" ",p.fname,';
                            elseif($selected[$i] == 'mname'):
                                $mname = '" ",p.mname,';
                            elseif($selected[$i] == 'lname'):
                                $lname = '" ",p.lname';
                            endif;
                            $getname = "CONCAT(".$title.$fname.$mname.$lname.") as Name,";
                        else:
                            $idlist2 .= "p.".$setselectedvalues['selectedfield'].",";
                        endif;
                    endif;
                }
            }
            $idlist = rtrim($idlist2, ',');
            if($idlist !=''):
                if(substr($setgroupnames['group_name'], 1) != 'Who' ):
                    $getname = '';
                endif;
                if($check == 1):
                    $getgroupval2 = sqlStatement("SELECT ". $idlist." FROM patient_data p LEFT JOIN employer_data e ON e.pid= p.pid WHERE e.pid = $pid ");
                else:
                    $getgroupval2 = sqlStatement("SELECT ".$getname. $idlist." FROM patient_data p WHERE pid = $pid ");
                endif;
                $getgroupval = sqlFetchArray($getgroupval2);
               
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
                            $getvalname = sqlStatement("SELECT title FROM list_options WHERE option_id =  '$explodeval[$i]' AND list_id = '$listname'");
                            $setvalname2=sqlFetchArray($getvalname);
                            $getlayoutval = sqlStatement("SELECT layout_col,group_name FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name = '".$setgroupnames['group_name']."'");
                            $setlayoutval=sqlFetchArray($getlayoutval);
                             //echo $setlayoutval['layout_col']."===".$setlayoutval['group_name'];
                             
                            if(empty($setvalname2)){
                                //echo   "<li><b>".$subtitle.": </b></li>";
                            }else{
                                foreach($setvalname2 as $setvalname){
                                    if(!empty($setvalname) && $setvalname != '0000-00-00 00:00:00')
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
                            if(!empty($val) && $val != '0000-00-00 00:00:00'){
                                $getporvidername = sqlStatement("SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$val'" );
                                $rowName=sqlFetchArray($getporvidername);
                                $provider_name=$rowName['name'];
                                echo "<li><b>".$subtitle2.": </b>".$provider_name."</li>";
                            }
                        }elseif($key == 'pharmacy_id' )
                        {
                            if(!empty($val) && $val != '0000-00-00 00:00:00'){ 
                               $getpharmacyname = sqlStatement("SELECT name FROM pharmacies WHERE id='$val'" );
                               $rowName=sqlFetchArray($getpharmacyname);
                               $setpharmacyname=$rowName['name'];
                                echo "<li><b>".$subtitle2.": </b>".$setpharmacyname."</li>";
                            }    
                        } else{
                            if(!empty($val) && $val != '0000-00-00 00:00:00')
                                echo "<li><b>".$subtitle2.": </b>".$val."</li>";
                        }
                    }
                }
            endif;
        }
        echo "<ul/></div>";
    }
}
function display_history($pid, $groupName){
    $getgroupval3 = sqlStatement("SELECT date as Date,date FROM history_data  WHERE pid = $pid order by date desc limit 1");
    $getgroupval3=sqlFetchArray($getgroupval3);
    $getgroupnames = sqlStatement("SELECT DISTINCT(group_name) as group_name from layout_options where form_id='HIS' and uor <> 0 order by group_name");
    while($setgroupnames=sqlFetchArray($getgroupnames)){
        $setpagebr_allr = '';
        echo "<div id='".$setgroupnames['group_name']."' style='clear:both;'>";
        $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '".$setgroupnames['group_name']."'";
        $getpagebr_allr = sqlStatement($getpagebr);
        $setpagebr_allr = sqlFetchArray($getpagebr_allr);      
        if($setpagebr_allr['group_name'] == $setgroupnames['group_name']) {
            $idName = str_replace(" ","-",trim($groupName)."-".$setpagebr_allr['group_name']);
            if($setpagebr_allr['page_break'] == 'YES'){
                display_div_function(substr($setpagebr_allr['group_name'], 1),0,$pid);
            }else{
                echo "<h2>". substr($setpagebr_allr['group_name'], 1).": </h2>";
            }
        }
        echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
        echo "<ul class='1".$idName.$j."' >"; 
        $gettitles =  sqlStatement("SELECT group_concat(field_id) as id, group_concat(title) as title from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$setgroupnames['group_name']."'"  );
        while($settitles=sqlFetchArray($gettitles)){
            $datacheck = '';
            $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name = '".$setgroupnames['group_name']."' AND option_value = 'YES'");
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
                        $idlist2 .= $setselectedvalues['selectedfield'].",";
                    endif;
                }
            }
            $idlist = rtrim($idlist2, ',');
            if($idlist !=''){
                $getgroupcheck = sqlStatement("SELECT  ". $idlist."  FROM history_data  WHERE pid = $pid order by date desc limit 1");
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
                $getgroupval2 = sqlStatement("SELECT  DATE_FORMAT(date,'%m-%d-%Y') as Date1 ,". $idlist."  FROM history_data  WHERE pid = $pid order by date desc limit 1");
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
                                        $getvalname = sqlStatement("SELECT title FROM list_options WHERE option_id =  '$explodelist2[0]' AND list_id = '$listname'");

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
                                            $getvalname = sqlStatement("SELECT title FROM list_options WHERE option_id =  '$explodeval[3]' AND list_id = '$listname'");
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
                                            $getvalname = sqlStatement("SELECT title FROM list_options WHERE option_id =  '$explodeval[$i]' AND list_id = '$listname'");
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
function display_insurance($patient_id) 
{    
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
function display_medications($eid,$pid,$groupName,$layout_type){
    global $tempvalue;
    if($eid != 0)
        $getMedication = sqlStatement("SELECT title AS Title,begdate as Begdate,enddate as Enddate, outcome,destination,diagnosis AS Codes, occurrence AS Occurrence, referredby as ReferredBy, comments FROM lists l INNER JOIN issue_encounter i ON i.list_id = l.id WHERE l.pid = $pid AND i.encounter =$eid AND l.type='medication'");
    else 
        $getMedication = sqlStatement("SELECT 
                                        CASE 
                                        WHEN li.enddate IS NULL 
                                        THEN  'Active'
                                        ELSE  'Inactive'
                                        END AS isIssueActive,outcome,destination, li.id, li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, li.reaction AS reaction, li.occurrence AS Occurrence, li.referredby AS ReferredBy, li.comments, (
                                        SELECT COUNT( list_id ) FROM issue_encounter WHERE list_id = li.id ) AS enc
                                        FROM  `icd9_dx_code` i
                                        RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                                        WHERE li.type =  'medication' AND li.pid =$pid AND li.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =$pid ) GROUP BY li.id");
        while ($frow = sqlFetchArray($getMedication)) {
            $data2[] = $frow;
            $datakey = $frow;
        }
        if(count($data2)>0):
            if($layout_type == 'list'){
                foreach($data2 as $key => $value){
                    foreach ($value as $key => $val) {
                        echo "<li>";
                        if($key == 'Occurrence'){
                            $occsql =  sqlStatement("select title from list_options  where list_id='occurrence' AND option_id='$val'");
                            $frow1 = sqlFetchArray($occsql);
                            if(!empty($frow1['title']))
                                echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1['title'];
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc'){
                            if(!empty($val))
                                echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        echo "</li>";
                    }
                } 
                echo "<li>&nbsp;</li>";
            }else{
                echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
                echo "<tr>
                        <th width='60%' class = 'tdborder'><b> Description </b></th> 
                        <th width='20%' class = 'tdborder'><b> Start Date </b></th> 
                        <th width='20%' class = 'tdborder'><b> End Date </b></th> 
                     </tr>";
                    foreach($data2 as $key =>$value){
                        $pres_array[$key] = $value;
                    }
                for($i=0; $i< count($pres_array); $i++){
                    if($pres_array[$i]['Occurrence'] != ''){
                        $getoccu = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'");
                        $setoccu = sqlFetchArray($getoccu);
                        if(!empty($setoccu)){
                            $occurance = $setoccu['title']; 
                        }else{
                            $occurance = '';
                        }    
                    }
                    if($pres_array[$i]['outcome'] != ''){
                        $getoutcome = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'");
                        $setoutcome = sqlFetchArray($getoutcome);
                        if(!empty($setoutcome)){
                            $outcome = $setoutcome['title']; 
                        }else{
                            $outcome = '';
                        }    
                    }
                    echo"<tr><td class = 'tdborder'><table border = '0'>";
                    echo "<tr><td width='60%' class='firsttd'>";
                    if(!empty($pres_array[$i]['Title']))
                        echo $pres_array[$i]['Title']; 
                    else
                        echo "Not Specified. ";
                    echo "</td></tr>";
                    if(!empty($pres_array[$i]['Codes']) || !empty($pres_array[$i]['Description'])){
                        echo "<tr><td class='secondtd'>";
                        echo "( ".$pres_array[$i]['Codes'] ." ".$pres_array[$i]['Description'].")";
                        echo "</td></tr>";
                    }
                    if($outcome !='Unassigned' ||$occurance !='Unknown'){
                        echo "<tr><td class='thirdtd'> ";
                        echo $occurance;
                        if($outcome != 'Unassigned')
                            echo ", ". $outcome;
                        echo "</td></tr>";
                    }
                    if(!empty($pres_array[$i]['ReferredBy'])){
                        echo "<tr><td class='thirdtd'> ";
                        echo "<spanclass='byelement'> Referred by </span> ". $pres_array[$i]['ReferredBy'];
                        echo "</td></tr>";
                    }
                    if(!empty($pres_array[$i]['destination'])){
                        echo "<tr><td class='thirdtd'> ";
                        echo $pres_array[$i]['destination'];
                        echo "</td></tr>";
                    }
                    echo "</table></td>";
                    echo "<td width='20%' class = 'tdborder'>".$pres_array[$i]['Begdate']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Enddate']."";
                    echo "</tr>";
            }
            echo "</table>";
        }
        $tempvalue = 0 ;
        ?>
        <script>
            document.getElementById('show_div_med<?php echo $eid; ?>').style.display = "block";
        </script> 
         <?php
        
    else:
        //echo "No Medications for this patient";
        //if($min == 1)
                //$tempvalue = 1 ;
    endif;
}

function display_vitals($encounter,$groupName,$patient_id){
    global $tempvalue;
    $getVitals="SELECT DATE( v.date ) AS Service_Date, v.bps AS BPS, v.bpd AS BPD, v.weight AS Wt, v.height AS Ht, v.temperature, v.respiration AS RR, note, v.BMI, v.head_circ
                FROM form_vitals v
                INNER JOIN forms f ON v.id = f.form_id
                AND f.pid = v.pid
                WHERE v.pid='".$patient_id."'
                AND encounter = ($encounter) AND f.deleted = 0";
    $resVitals=sqlStatement($getVitals);
    $vitaldata=sqlStatement($getVitals);
    $get_rendering_provider = sqlStatement("SELECT CONCAT(u.fname,' ',u.lname) AS provider_name ,DATE_FORMAT(f.date,'%m-%d-%Y') as date
                                        FROM users u
                                        INNER JOIN form_encounter f ON f.provider_id = u.id
                                        WHERE f.encounter = $encounter ");
    $set_rendering_provider = sqlFetchArray($get_rendering_provider);
    while ($frow = sqlFetchArray($vitaldata)) {
        $data2[] = $frow; 
    }
    if(count($data2)>0):
        if($layout_type == 'list'){
            foreach($data2 as $value){
                foreach ($value as $key => $val) {
                    if(!empty($val))
                        echo "<li><b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val."</li>";
                }
//                if(!empty($set_rendering_provider['provider_name']))
//                    echo "<li><b> Seen by: </b> ".$set_rendering_provider['provider_name']."</li>";
                echo "<li>&nbsp;</li>";
            }
        }else{
            echo "<hr />";
            echo "<div class=''>\n";
            echo "<style>
                  .tbl_vitals {border:1px solid #C3E7F5;background-color: #E0E0E0; }
                  .tbl_vitals_th
                  {border:0px 1px 1px 0px solid  #C3E7F5;;text-align:center; background-color:  #E0E0E0;}
                  .tbl_vitals_td 
                  {border:0px 1px 1px 0px solid  ;text-align:center; background:#FFFFFF;}
                 </style>";
            echo "<table width='980px' class='tbl_vitals'>";
            echo "<tr>";
            $rowVitalFields=sqlFetchArray($resVitals);
            foreach($rowVitalFields as $key=>$value){
                echo "<th class='tbl_vitals_th'>". ucfirst(str_replace('_',' ',$key))."</th>";
            }
            //echo "<th> Seen by </th>";
            echo "</tr>";
            foreach($data2 as $value){
                echo "<tr>";
                foreach ($value as $val) {
                    echo "<td class='tbl_vitals_td'>".$val."</td>";
                }
                //echo "<td class='tbl_vitals_td'>".$set_rendering_provider['provider_name']."</td>";
               echo "</tr>";
            }
            echo "</table>";
            echo "</div>\n";
        }
        $tempvalue = 0 ;
        ?>
        <script>
            document.getElementById('show_div_vitals<?php echo $encounter; ?>').style.display = "block";
        </script> 
        
<!--        <style type="text/css">
            #show_div_vitals<?php echo $encounter; ?>{
                display:block;
            }
        </style>-->
        <?php
        
    else:
//       echo "No vital form for this date of service.";
        //if($min == 1)
            //$tempvalue = 1 ;
        
    endif;
}
function display_ros($encounter,$groupName,$patient_id){
    global $tempvalue;
    $getROS="SELECT r.*
                FROM  tbl_form_allcare_ros r
                INNER JOIN forms f ON r.id = f.form_id
                WHERE r.pid=".$patient_id."
                AND encounter = ($encounter) AND f.deleted = 0 and formdir='allcare_ros'";

    
    $resROS=sqlStatement($getROS);
    $rosdata=sqlStatement($getROS);
    $datacheck = '';
    if(mysql_num_rows($rosdata)>0){
       while ($frow = sqlFetchArray($rosdata)) {
          $data3[] = $frow; 
        }
        $rowROSFields=sqlFetchArray($resROS);
        $datacheck = '';
        $checkingval  = 0;
        foreach ($rowROSFields as $key=>$value){
            if($key == 'constitutional'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        } 
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='constitutional$encounter' style='display: none;'><b>Constitutional: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                    if($key == 'weight_change' || $key == 'weakness' || $key == 'fatigue' || $key == 'anorexia' || $key == 'fever' || $key == 'chills' || $key == 'night_sweats' || $key == 'insomnia' || $key == 'irritability' || $key == 'heat_or_cold' || $key == 'intolerance' || $key == 'change_in_appetite'){
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                    }elseif($key == 'constitutional_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('constitutional<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }
            if($checkval == 1)echo "<br>";
        }
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'eyes'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='eyes$encounter' style='display: none;'><b>Eyes: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                    if($key == 'change_in_vision' || $key == 'glaucoma_history' || $key == 'eye_pain' || $key == 'irritation' || $key == 'redness' || $key == 'excessive_tearing' || $key == 'double_vision' || $key == 'blind_spots' || $key == 'photophobia' || $key == 'glaucoma' || $key == 'cataract' || $key == 'injury' || $key == 'ha' || $key =='coryza' || $key == 'obstruction'){
                        if ($key == "glaucoma_history") { $key = "Glaucoma Family History"; }
                        if ($key == "irritation") { $key = "Eye Irritation"; }
                        if ($key == "redness") { $key = "Eye Redness"; }
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                         $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                         $checkval  = 1;
                        endif;
                    }elseif($key == 'eyes_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('eyes<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        } 
        foreach ($rowROSFields as $key=>$value){
            if($key == 'ent'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='ent$encounter' style='display: none;'><b>Ears, Nose, Mouth, Throat: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                    if($key == 'hearing_loss' || $key == 'discharge' || $key == 'pain' || $key == 'vertigo' || $key == 'tinnitus' || $key == 'frequent_colds' || $key == 'sore_throat' || $key == 'sinus_problems' || $key == 'post_nasal_drip' || $key == 'nosebleed' || $key == 'snoring' || $key == 'apnea' || $key == 'bleeding_gums' || $key =='hoarseness' || $key == 'dental_difficulties' || $key == 'use_of_dentures'){
                        if ($key == "discharge") { $key = "ENT Discharge"; }
                        if ($key == "pain") { $key = "ENT Pain"; }
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                         $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                         $checkval  = 1;
                        endif;
                    }elseif($key == 'ent_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }   
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('ent<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }   
            if($checkval == 1)echo "<br>";
        }
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'neck'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='neck$encounter' style='display: none;'><b>Neck: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if( $key == 'stiffness' || $key == 'neck_pain' || $key == 'masses' || $key == 'tenderness'){
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'neck_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('neck<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'breast'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='breast$encounter' style='display: none;'><b>Breast: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if( $key == 'breast_mass' || $key == 'breast_discharge' || $key == 'biopsy' || $key == 'abnormal_mammogram'){
                        if ($key == "biopsy") { $key = "Breast Biopsy"; }
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'breast_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('breast<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'respiratory'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='respiratory$encounter' style='display: none;'><b>Respiratory: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if( $key == 'cough' || $key == 'sputum' || $key == 'shortness_of_breath' || $key == 'wheezing' || $key == 'hemoptsyis' || $key == 'asthma' || $key == 'copd' ){
                        if ($key == "hemoptsyis") { $key = "Hemoptysis"; }
                        if ($key == "copd") { $key = "COPD"; }
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'respiratory_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }    
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('respiratory<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'cardiovascular'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='cardiovascular$encounter' style='display: none;'><b>Cardiovascular: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if($key == 'chest_pain' || $key == 'palpitation' || $key == 'syncope' || $key == 'pnd' || $key == 'doe' || $key == 'orthopnea' || $key == 'peripheal' || $key == 'edema' || $key == 'legpain_cramping' || $key == 'history_murmur' || $key == 'arrythmia' || $key == 'heart_problem'){
                        if ($key == "pnd") { $key = "PND"; }
                        if ($key == "doe") { $key = "DOE"; }
                        if ($key == "peripheal") { $key = "Peripheral"; }
                        if ($key == "legpain_cramping") { $key = "Leg Pain/Cramping"; } 
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'cardiovascular_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }   
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('cardiovascular<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'gastrointestinal'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='gastrointestinal$encounter' style='display: none;'><b>Gastrointestinal: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if( $key == 'dysphagia' || $key == 'heartburn' || $key == 'bloating' || $key == 'belching' || $key == 'flatulence' || $key == 'nausea' || $key == 'vomiting' || $key == 'hematemesis' || $key == 'gastro_pain' || $key == 'food_intolerance' || $key == 'hepatitis' || $key == 'jaundice' || $key == 'hematochezia' || $key =='changed_bowel' || $key == 'diarrhea' || $key == 'constipation' || $key == 'blood_in_stool'){
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'gastrointestinal_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('gastrointestinal<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'genitourinary'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='genitourinary$encounter' style='display: none;'><b>Genitourinary General: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if( $key == 'polyuria' || $key == 'polydypsia' || $key == 'dysuria' || $key == 'hematuria' || $key == 'frequency' || $key == 'urgency' || $key == 'incontinence' || $key == 'renal_stones' || $key == 'utis' || $key == 'blood_in_urine' || $key == 'urinary_retention' || $key == 'change_in_nature_of_urine' ){
                        if ($key == "frequency") { $key = "Urine Frequency"; }
                        if ($key == "urgency") { $key = "Urine Urgency"; }
                        if ($key == "utis") { $key = "UTIs"; }
                       
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'genitourinary_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('genitourinary<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'genitourinarymale'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='genitourinarymale$encounter' style='display: none;'><b>Genitourinary Male: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if($key == 'hesitancy' || $key == 'dribbling' || $key == 'stream' || $key == 'nocturia' || $key == 'erections' || $key == 'ejaculations' ){
                        if ($key == "hesitancy") { $key = "Urine Hesitancy"; }
                        if ($key == "dribbling") { $key = "Urine Dribbling"; }
                        if ($key == "stream") { $key = "Urine Stream"; }
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'genitourinarymale_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('genitourinarymale<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'genitourinaryfemale'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='genitourinaryfemale$encounter' style='display: none;'><b>Genitourinary Female: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if($key == 'g' || $key == 'p' || $key == 'ap' || $key == 'lc' || $key == 'mearche' || $key == 'menopause' || $key == 'lmp' || $key == 'f_frequency' || $key == 'f_flow' || $key == 'f_symptoms' || $key == 'abnormal_hair_growth' || $key == 'f_hirsutism' ){
                        if ($key == "g") { $key = "Female G"; }
                        if ($key == "p") { $key = "Female P"; }
                        if ($key == "lc") { $key = "Female LC"; }
                        if ($key == "ap") { $key = "Female AP"; }
                        if ($key == "mearche") { $key = "Menarche"; }
                        if ($key == "lmp") { $key = "LMP"; }
                        if ($key == "f_frequency") { $key = "Menstrual Frequency"; }
                        if ($key == "f_flow") { $key = "Menstrual Flow"; }
                        if ($key == "f_symptoms") { $key = "Female Symptoms"; }
                        if ($key == "f_hirsutism") { $key = "Hirsutism/Striae"; }
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'genitourinaryfemale_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('genitourinaryfemale<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'musculoskeletal'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='musculoskeletal$encounter' style='display: none;'><b>Musculoskeletal: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if( $key == 'joint_pain' || $key == 'swelling' || $key == 'm_redness' || $key == 'm_warm' || $key == 'm_stiffness' || $key == 'm_aches' || $key == 'fms' || $key == 'arthritis' || $key == 'gout' || $key == 'back_pain' || $key == 'paresthesia' || $key == 'muscle_pain' || $key =='limitation_in_range_of_motion' ){
                        if ($key == "swelling") { $key = "Musc Swelling"; }
                        if ($key == "m_redness") { $key = "Musc Redness"; }
                        if ($key == "m_warm") { $key = "Musc Warm"; }
                        if ($key == "m_stiffness") { $key = "Musc Stiffness"; }
                        if ($key == "m_aches") { $key = "Musc Aches"; }
                        if ($key == "fms") { $key = "FMS"; }
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'musculoskeletal_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('musculoskeletal<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'extremities'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='extremities$encounter' style='display: none;'><b>Extremities: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if( $key == 'spasms' || $key == 'extreme_tremors'){
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'extremities_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('extremities<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'neurologic'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='neurologic$encounter' style='display: none;'><b>Neurologic: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if($key == 'loc' || $key == 'seizures' ||  $key == 'stroke'  || $key == 'tia' || $key == 'n_numbness' || $key == 'n_weakness' || $key == 'paralysis' || $key == 'intellectual_decline' || $key == 'memory_problems' || $key == 'dementia' || $key == 'n_headache' || $key == 'dizziness_vertigo' || $key == 'slurred_speech' || $key =='tremors' || $key == 'migraines' || $key == 'changes_in_mentation' ){
                        if ($key == "loc") { $key = "LOC"; }
                        if ($key == "tia") { $key = "TIA"; }
                        if ($key == "n_numbness") { $key = "Neuro Numbness"; }
                        if ($key == "n_weakness") { $key = "Neuro Weakness"; }
                        if ($key == "n_headache") { $key = "Headache"; } 
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'neurologic_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('neurologic<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'skin'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='skin$encounter' style='display: none;'><b>Skin: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if($key == 's_cancer' || $key == 'psoriasis' || $key == 's_acne' || $key == 's_other' || $key == 's_disease' || $key == 'rashes' || $key == 'dryness' || $key == 'itching' ){
                        if ($key == "s_cancer") { $key = "Skin Cancer"; }
                        if ($key == "s_acne") { $key = "Acne"; }
                        if ($key == "s_other") { $key = "Skin Other"; }
                        if ($key == "s_disease") { $key = "Skin Disease"; } 
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'skin_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('skin<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'psychiatric'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='psychiatric$encounter' style='display: none;'><b>Psychiatric: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if($key == 'p_diagnosis' || $key == 'p_medication' || $key == 'depression' || $key == 'anxiety' || $key == 'social_difficulties' || $key == 'alcohol_drug_dependence' || $key == 'suicide_thoughts' || $key == 'use_of_antideprassants' || $key == 'thought_content' ){
                        if ($key == "p_diagnosis") { $key = "Psych Diagnosis"; }
                        if ($key == "p_medication") { $key = "Psych Medication"; }
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'psychiatric_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                } 
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('psychiatric<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }    
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'endocrine'  ){
                 $datacheck = 1;
                $sub = 1;
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='endocrine$encounter' style='display: none;'><b>Endocrine: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if($key == 'thyroid_problems' || $key == 'diabetes' || $key == 'abnormal_blood' || $key == 'goiter' || $key == 'heat_intolerence' || $key == 'cold_intolerence' || $key == 'increased_thirst' || $key == 'excessive_sweating' || $key == 'excessive_hunger' ){
                        if ($key == "abnormal_blood") { $key = "Endo Abnormal Blood"; }
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'endocrine_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('endocrine<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            }        
            if($checkval == 1)echo "<br>";
        }    
        $datacheck = '';
        foreach ($rowROSFields as $key=>$value){
            if($key == 'hai'  ){
                $datacheck = 1;
                $sub = 1;
//                if($value == 'Normal'){
//                    $datacheck = 1;
//                    $sub = 0;
//                }elseif($value == 'Select Details'){
//                    $datacheck = 1;
//                    $sub = 1;
//                }elseif($value == 'Not Examined'){
//                    $datacheck = 0;
//                }
            }
        }
        if($datacheck == 1){
            $checkval  = 0;
            echo "<li id='hai$encounter' style='display: none;'><b>Hematologic/Allergic/Immunologic: </b>";
            if($sub == 0){
                echo "Normal.</li>";
            }elseif($sub == 1){
                echo "<li style='margin-left: 1cm;'>";
                foreach ($rowROSFields as $key=>$value) {
                   if($key == 'anemia' || $key == 'fh_blood_problems' || $key == 'bleeding_problems' || $key == 'allergies' || $key == 'frequent_illness' || $key == 'hiv' || $key == 'hai_status' || $key == 'hay_fever' || $key == 'positive_ppd' ){
                        if ($key == "fh_blood_problems") { $key = "FH Blood Problems"; }
                        if ($key == "hiv") { $key = "HIV"; }
                        if ($key == "hai_status") { $key = "HAI Status"; }
                        if($value == 'YES'):
                            echo ucwords(str_replace('_',' ',$key)) ." YES<br>";
                            $checkval  = 1;
                            //$constitutional_val .=  ucwords(str_replace('_',' ',$key)).",";
                        elseif($value == 'NO'):
                            echo "No ".ucwords(str_replace('_',' ',$key))."<br>";
                            $checkval  = 1;
                        endif;
                   }elseif($key == 'hai_text' ){
                        if(!empty($value)){
                            echo "<b>Other Details:</b>".$value;
                            $checkval  = 1;
                        }
                    }
                }  
                if($checkval == 1){
                    //echo "Not Recorded.";
                    ?><script>
                        document.getElementById('hai<?php echo $encounter; ?>').style.display = "block";
                    </script>
                    <?php
                    $checkingval = 1;
                }
                echo "</li>";
            } 
            if($checkval == 1)
                echo "<br>";
        }   
        $tempvalue = 0 ;
        if($checkingval == 1){
            ?>
            <script>
                document.getElementById('show_div_ros<?php echo $encounter; ?>').style.display = "block";
            </script> 
            <?php
        }
    }else{
//         echo "No Review of System form for this date of service.";
        //if($min == 1)
            //$tempvalue = 1 ;
       } 
}
function display_physical_exam($encounter,$groupName,$patient_id){
    global $tempvalue;
    $getphysicalexam="SELECT r.*
                FROM  tbl_form_physical_exam r
                INNER JOIN forms f ON r.forms_id = f.form_id
                WHERE f.pid=".$patient_id."
                AND encounter = ($encounter) AND f.deleted = 0 and formdir='allcare_physical_exam'";
    $resphysicalexam=sqlStatement($getphysicalexam);
    $physicalexamdata=sqlStatement($getphysicalexam);
    $pelines = array(
            'GEN' => array(
                    'GENWELL'  =>'Appearance',
                    'GENAWAKE' =>'Awake, Alert, Oriented, in No Acute Distress'),
            'HEAD' =>array(
                    'HEADNORM'  =>'Normocephalic, Autramatic',
                    'HEADLESI'  =>'Lesions' ),
            'EYE' => array(
                    'EYECP'    =>'Conjuntiva, Pupils',
                    'EYECON'   =>'Conjuctive Clear, Tms Intact,Discharge, Wax, Oral Lesions, Gums pink, Bilateral Nasal Turbinates',
                    'EYEPER'  =>'PERRLA, EOMI'),
            'ENT' => array(
                    'ENTTM'    =>'TMs/EAMs/EE, Ext Nose',
                    'ENTNASAL' =>'Nasal Mucosa Pink, Septum Midline',
                    'ENTORAL'  =>'Oral Mucosa Pink, Throat Clear',
                    'ENTSEPT'  =>'Septum Midline'),
            'NECK'=> array(
                    'NECKSUP'    =>'Supple,Thyromegaly, Carotid of the Nasal Septum,  JVD,  lymphadenopathy'),
            'BACK'=> array(
                    'BACKCUR'    =>'Normal Curvature, Tenderness'),
            'CV' => array(
                    'CVRRR'    =>'RRR',
                    'CVNTOH'   =>'Thrills or Heaves',
                    'CVCP'     =>'Cartoid Pulsations, Pedal Pulses',
                    'CVNPE'    =>'Peripheral Edema',
                    'CVNMU'    =>'Murmur, Rubs,Gallops'),
            'CHEST' => array(
                    'CHNSD'    =>'Skin Dimpling or Breast Nodules'),
            'RESP' => array(
                    'RECTAB'   =>'Lungs CTAB',
                    'REEFF'    =>'Respirator Effort Unlabored',
                    'RELUN'    =>'Lungs Clear,Rales,Rhonchi,Wheezes'),
            'GI' => array(
                    'GIOG'     =>'Ogrganomegoly',
                    'GIHERN'   =>'Hernia',
                    'GIRECT'   =>'Anus, Rectal Tenderness/Mass',
                    'GISOFT'   =>'Soft, Non Tender, Non Distended, Masses',
                    'GIBOW'   =>'Bowel Sounds present in all four quadrants'),
            'GU' => array(
                    'GUTEST'   =>'Testicular Tenderness, Masses',
                    'GUPROS'   =>'Prostate w/o Enlrgmt, Nodules, Tender',
                    'GUEG'     =>'Ext Genitalia, Vag Mucosa, Cervix',
                    'GUAD'     =>'Adnexal Tenderness/Masses',
                    'GULES'    =>'Normal. Lesions, Discharge, Hernias Noted, Deferred'),

        'EXTREMITIES'=> array(
                    'EXTREMIT'  =>'Edema, Cyanosis or Clubbing',
                    'EXTREDEF'  =>'Deformities'),
            'LYMPH' => array(
                    'LYAD'     =>'Adenopathy (2 areas required)'),
            'MUSC' => array(
                    'MUSTR'    =>'Strength',
                    'MUROM'    =>'ROM',
                    'MUSTAB'   =>'Stability',
                    'MUINSP'   =>'Inspection'),
            'NEURO' => array(
                    'NEUCN2'   =>'CN2-12 Intact',
                    'NEUREF'   =>'Reflexes Normal',
                    'NEUSENS'  =>'Sensory Exam Normal',
                    'NEULOCAL'  =>'Physiological, Localizing Findings'),
            'PSYCH' => array(
                    'PSYAFF'   =>'Affect Normal',
                    'PSYJUD'   =>'Normal Affect, Judgement and Mood, Alert and Oriented X3',
                    'PSYDEP'   =>'Depressive Symptoms',
                    'PSYSLE'   =>'Change In Sleeping Habit',
                    'PSYTHO'   =>'Change In Thought Content',
                    'PSYAPP'   =>'Patient Appears To Be In Good Mood',
                    'PSYABL'   =>'Able To Answer Questions Qppropriately'),
            'SKIN' => array(
                    'SKRASH'   =>'Rash or Abnormal Lesions',
                    'SKCLEAN'   =>'Clean & Intact with Good Skin Turgor'),
            'OTHER' => array(
                    'OTHER'    =>'Other'),
    );
    if(mysql_num_rows($physicalexamdata)>0):
        while ($frow = sqlFetchArray($physicalexamdata)) {
          $data4[] = $frow;
        }
        foreach ($pelines as $sysname => $sysarray) {
            $sysnamedisp = $sysname;
            if ($sysname == '*') {
              
            }
            else {
              $sysnamedisp = $sysname;
            }
            $datacheck = 0;
            $check = 0;
            $namevalue = $sysnamedisp;
            foreach ($sysarray as $line_id => $description) {
                foreach($data4 as $value){
                    if((!empty($value['line_id']) || !empty($value['wnl']) || !empty($value['abn']) || $value['comments'])  && $line_id == $value['line_id']){
                        if($value['wnl']!=0 || $value['abn']!=0){  
                             if($sysnamedisp == '' && $check == 1){
                                  echo "<br> &nbsp;&nbsp;&nbsp;&nbsp;";
                            }else{
                                echo "<li>";
                                echo "<b>".$namevalue.":</b> <br>&nbsp;&nbsp;&nbsp;&nbsp;";
                                $check = 1;
                            }    
                            $wnl = '';$abn = '';
                            if($value['wnl']== 1):
                                $wnl = "Within Normal Limits";
                            endif;
                            if($value['abn']== 1):
                                $abn = "Abnormal Limits";
                            endif;
                            if($line_id == $value['line_id']):
                                echo $description."-".$wnl. " ".$abn.".".$value['comments'];
                                $datacheck = 1;
                                if($value['comments'] != ''):
                                    echo ".";
                                endif;
                                break;
                            endif;
                            echo "</li>";
                        }
                    }    
                }
                $sysnamedisp = '';
            } 
//            if($datacheck == 0):
//                echo  "Not Recorded</li>";
//            endif;
        }
        $tempvalue = 0 ;
        ?>
        <script>
            document.getElementById('show_div_physical_exam<?php echo $encounter; ?>').style.display = "block";
        </script> 
        
        <?php
        
     else:
        //echo "No Physical form for this date of service.";
         //if($min == 1)
            //$tempvalue = 1 ;
    endif;
}

function display_cc($encounterid,$groupName){
    global $tempvalue;
    $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%Chief Complaint' AND option_value = 'YES'");
    while($setselectedvalues= sqlFetchArray($getselectedvales)){
        $listselected[] = $setselectedvalues['selectedfield'];
    }
    $getCC = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Chief Complaint'");
    while($getCCResult = sqlFetchArray($getCC)){ 
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getCCResult['field_id']):
                $fieldid[$getCCResult['title']] = $getCCResult['field_id'];
            endif;
        }
    }
    if(!empty($fieldid)){
        $getformid = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ");
        $setformid=sqlFetchArray($getformid);
        if(!empty($setformid)){
            for($i=0; $i< count($setformid); $i++){
                $formid = $setformid['form_id'];
                $datacheck = '';
                foreach($fieldid as $fkey => $fid){
                    $getCCdata = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'");
                    $getCCdataResult2=sqlFetchArray($getCCdata);
                    if(!empty($getCCdataResult2['field_value'])){
                        echo "<li>";
                        if(strcmp($fkey, 'Chief Complaint Text') != 0):
                            echo "<b>".$fkey.":</b>";
                        endif;
                        $datacheck = 1;
                        if(!empty($getCCdataResult2)):
                            if(strcmp($fkey, 'Chief Complaint Status') == 0):
                                echo   ucwords(str_replace('|', ',', $getCCdataResult2['field_value']))."</li>";
                            else:
                                echo $getCCdataResult2['field_value']."</li>";
                            endif;
                        endif;
                    }
                }
                if($datacheck == ''){
                    //if($min == 1)
                        //$tempvalue = 1 ;
                }else{
                    $tempvalue = 0 ;
                    ?>
                    <script>
                        document.getElementById('show_div_cc<?php echo $encounterid; ?>').style.display = "block";
                    </script> 
                    <?php
                }
            }
        } else{
            //echo " No Chief Complaint for this date of service.";
            //if($min == 1)
                //$tempvalue = 1 ;
        }
    } 
}

function display_progress($encounterid,$groupName){
    global $tempvalue;
    $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE  '%Progress Note' AND option_value = 'YES'");
    while($setselectedvalues= sqlFetchArray($getselectedvales)){
        $listselected[] = $setselectedvalues['selectedfield'];
    }
    $getCC = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Progress Note'");
    while($getCCResult = sqlFetchArray($getCC)){
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getCCResult['field_id']):
                $fieldid[$getCCResult['title']] = $getCCResult['field_id'];
            endif;
        }
    }
    if(!empty($fieldid)):
    $getformid = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ");
        $setformid=sqlFetchArray($getformid);
        if(!empty($setformid)):
            for($i=0; $i< count($setformid); $i++){
                $formid = $setformid['form_id'];
                $datacheck = '';
                foreach($fieldid as $fkey => $fid){
                    $getCCdata = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'");
                    $getCCdataResult2=sqlFetchArray($getCCdata);
                    if(!empty($getCCdataResult2['field_value'])){
                        echo "<li>";
                        if(strcmp($fkey, 'Progress Note Text') != 0):
                            echo "<b>".$fkey.":</b>";
                        endif;
                        $datacheck = 1;
                        if(!empty($getCCdataResult2)):
                            if(strcmp($fkey, 'Progress Note Status') == 0):
                                echo   ucwords(str_replace('|', ',', $getCCdataResult2['field_value']));
                            else:
                                echo $getCCdataResult2['field_value'];
                            endif;
                        endif;
                        echo "</li>";
                    }
                }
                if($datacheck == ''){
                    //if($min == 1)
                        //$tempvalue = 1 ;
                }else{
                    $tempvalue = 0 ;
                    ?>
                    <script>
                        document.getElementById('show_div_progress<?php echo $encounterid; ?>').style.display = "block";
                    </script> 
                    <?php
                }
          }
        else:
            //echo "No Progress note for this date of service";
            //if($min == 1)
                //$tempvalue = 1 ;
        endif;
    endif;
}
function display_hpi($encounterid,$pid,$groupName){
    global $tempvalue;
    $getCC = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%History of Present illness'");
    
    $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%History of Present illness' AND option_value = 'YES'");
    while($setselectedvalues= sqlFetchArray($getselectedvales)){
        $listselected[] = $setselectedvalues['selectedfield'];
    }
    while($getCCResult = sqlFetchArray($getCC)){
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getCCResult['field_id']):
                $fieldid[$getCCResult['title']] = $getCCResult['field_id'];
            endif;
        }
    }
    if(!empty($fieldid)):
        $getformid = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ");
        $setformid=sqlFetchArray($getformid);
        if(!empty($setformid)):
        for($i=0; $i< count($setformid); $i++){
            $formid = $setformid['form_id'];
            $datacheck = '';
            foreach($fieldid as $fkey => $fid){
                $getCCdata = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'");
                $getCCdataResult2=sqlFetchArray($getCCdata);
                if(!empty($getCCdataResult2['field_value'])){
                    $datacheck = 1;
                    echo "<li>";
                    if(strcmp($fkey, 'HPI Text') != 0):
                        echo "<b>".$fkey.":</b>";
                    endif;
                    if(!empty($getCCdataResult2)):
                        if($fkey == 'HPI Status'):
                            echo  ucwords(str_replace('|', ',', $getCCdataResult2['field_value']))."</li>";
                        else:
                            echo $getCCdataResult2['field_value']."</li>";
                        endif;
                    endif;
                }
            }
//            if($datacheck == '')
//            echo "<h2>HPI Data</h2>";
            $check = '';
            echo "<li>";
            $gethistorypast = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = 're_up_patient1'");
            $sethistorypast=sqlFetchArray($gethistorypast);
            if(!empty($sethistorypast['field_value'])){
                echo "<b> Past Medical History:</b>".$sethistorypast['field_value'];
                $check = 1;
            }    
            $getfamilyhistory = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = 're_up_patient2'");
            $setfamilyhistory=sqlFetchArray($getfamilyhistory);
            if(!empty($setfamilyhistory['field_value'])){
                echo "<br><b> Family History:</b>".$setfamilyhistory['field_value'];
                $check = 1;
            }   
            $getprimaryfamilyhistory = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = 're_up_patient3'");
            $setprimaryfamilyhistory=sqlFetchArray($getprimaryfamilyhistory);
            if(!empty($setprimaryfamilyhistory['field_value'])){
                echo "<br><b> Primary Family Med Conditions:</b>".$setprimaryfamilyhistory['field_value'];
                $check = 1;
            }   
//            $gettestexams = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = 're_up_patient4'");
//            $settestexams=sqlFetchArray($gettestexams);
//            if(!empty($settestexams['field_value'])){
//                echo "<br><b> Tests and Exams:</b>".$settestexams['field_value'];
//                $check = 1;
//            }  
            $getsocialhistory = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = 're_up_patient5'");
            $setsocialhistory=sqlFetchArray($getsocialhistory);
            if(!empty($setsocialhistory['field_value'])){
                echo "<br><b> Social History:</b>".$setsocialhistory['field_value'];
                $check = 1;
            } 
            echo "</li>";
            if($datacheck == '' && $check == ''){
                //if($min == 1)
                //$tempvalue = 1 ;
            }else{
                $tempvalue = 0 ;
                ?>
                <script>
                    document.getElementById('show_div_hpi<?php echo $encounterid; ?>').style.display = "block";
                </script> 
                <?php
            }
        }
        else:
            //echo "No History of Present illness for this date of service.";
            //if($min == 1)
                //$tempvalue = 1 ;
        endif;
    endif;
}

function display_assess($encounterid,$groupName){
    global $tempvalue;
    $getassessment = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE  '%Assessment Note'");
    $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE  '%Assessment Note' AND option_value = 'YES'");
    while($setselectedvalues= sqlFetchArray($getselectedvales)){
        $listselected[] = $setselectedvalues['selectedfield'];
    }
    while($getassessmentResult = sqlFetchArray($getassessment)){
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getassessmentResult['field_id']):
                $fieldid[$getassessmentResult['title']] = $getassessmentResult['field_id'];
            endif;
        }
    }
    if(!empty($fieldid)):
        
            $getformid = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ");
            $setformid=sqlFetchArray($getformid);
            if(!empty($setformid)):
            for($i=0; $i< count($setformid); $i++){
                $formid = $setformid['form_id'];
                $datacheck = '';
                foreach($fieldid as $fkey => $fid){
                    echo "<li>";
                    $getassessmentdata = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'");
                    $getassessmentdataResult2=sqlFetchArray($getassessmentdata);
                    if(!empty($getassessmentdataResult2['field_value'])){
                        echo "</li>";
                        if(strcmp($fkey, 'Assessment Note Text') != 0):
                            echo "<b>".$fkey.":</b>";
                        endif;
                        $datacheck = 1;
                        if(!empty($getassessmentdataResult2)):
                            if(strcmp($fkey, 'Assessment Note Status') == 0):
                                echo ucwords(str_replace('|', ',', $getassessmentdataResult2['field_value']));
                            else:
                                echo $getassessmentdataResult2['field_value'];
                            endif;
                        endif;
                        echo "</li>";
                    }    
                }
                 if($datacheck == ''){
                    //echo "No Assessment data for this date of service.";
                    //if($min == 1)
                        //$tempvalue = 1 ;
                }else{
                    $tempvalue = 0 ;
                    ?>
                    <script>
                     document.getElementById('show_div_assess<?php echo $encounterid; ?>').style.display = "block";
                    </script> 
                
                <?php
                }
            }
        else:
           //echo "No Assessment data for this date of service.";
           //if($min == 1)
                //$tempvalue = 1 ;
        endif;
    endif;
}

function display_plan($encounterid,$groupName){
    global $tempvalue;
    $getplan = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Plan Note'");
    
    $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%Plan Note' AND option_value = 'YES'");
    while($setselectedvalues= sqlFetchArray($getselectedvales)){
        $listselected[] = $setselectedvalues['selectedfield'];
    }
    while($getplanResult = sqlFetchArray($getplan)){
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getplanResult['field_id']):
                $fieldid[$getplanResult['title']] = $getplanResult['field_id'];
            endif;
        }
    }
     
    if(!empty($fieldid)):
        $getformid = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ");
        $setformid=sqlFetchArray($getformid);
        if(!empty($setformid)):
        for($i=0; $i< count($setformid); $i++){
            $formid = $setformid['form_id'];
            $datacheck = '';
            foreach($fieldid as $fkey => $fid){

                $getplandata = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'");
                $getplandataResult2=sqlFetchArray($getplandata);
                if(!empty($getplandataResult2['field_value'])){    
                    echo "<li>";
                    if(strcmp($fkey, 'Plan Note Text') != 0):
                        echo "<b>".$fkey.":</b>";
                    endif;
                    $datacheck = 1;
                    if(!empty($getplandataResult2)):
                        if(strcmp($fkey, 'Plan Note Status') == 0):
                            echo ucwords(str_replace('|', ',', $getplandataResult2['field_value']));
                        else:
                            echo $getplandataResult2['field_value'];
                        endif;
                    endif;
                    echo "</li>";
                }
            }
            if($datacheck == ''){
               // echo " No Plan Note for this date of service";
               //if($min == 1)
                    //$tempvalue = 1 ;
            }else{
                $tempvalue = 0 ;
                ?>
                 <script>
                     document.getElementById('show_div_plan<?php echo $encounterid; ?>').style.display = "block";
                    </script> 
                <?php
            }
        }
        else:
            //echo " No Plan Note for this date of service";
            //if($min == 1)
                //$tempvalue = 1 ;
        endif;
    endif;
}
function display_mproblem($eid,$pid,$groupName,$layout_type){
    global $tempvalue;
    if($eid != 0){
        $getMedicalProblems2 = "SELECT title as Title,outcome,destination,begdate as Begdate,enddate as Enddate, diagnosis as Codes, occurrence  AS Occurrence,(SELECT DISTINCT i.long_desc FROM `icd9_dx_code` i WHERE l.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                ) as Description, referredby as ReferredBy, comments FROM lists l INNER JOIN issue_encounter i ON i.list_id = l.id WHERE l.pid = $pid AND  l.type='medical_problem' AND i.encounter =$eid";
    }else{
        $getMedicalProblems2 = "SELECT 
                                CASE 
                                WHEN li.enddate IS NULL 
                                THEN  'Active'
                                ELSE  'Inactive'
                                END AS isIssueActive,outcome,destination, li.id, li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, li.reaction AS reaction, li.occurrence AS Occurrence, li.referredby AS ReferredBy, li.comments, (
                                SELECT COUNT( list_id ) FROM issue_encounter WHERE list_id = li.id ) AS enc
                                FROM  `icd9_dx_code` i
                                RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                                WHERE li.type =  'medical_problem' AND li.pid =$pid AND li.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =$pid ) GROUP BY li.id";
    }
    $getMedicalProblems = sqlStatement($getMedicalProblems2);
    while ($frow = sqlFetchArray($getMedicalProblems)) {
      $data2[] = $frow;
      $datakey = $frow;
    }
    $datacheck = '';
    if(count($data2)>0){
        if($layout_type == 'list'){
            foreach($data2 as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        $datacheck = 1;
                        echo "<li>";
                        if($key == 'Occurrence'){

                            $occsql =  sqlStatement("select title from list_options  where list_id='occurrence' AND option_id='$val'");
                            $frow1 = sqlFetchArray($occsql);
                            if(!empty($frow1['title']))
                                echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1['title'];
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc'){
                            echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        echo "</li>";
                    }
                }
                if($datacheck == ''){
                    //echo " <li>No Medical Problems for this patient</li>";
                    //if($min == 1)
                        //$tempvalue = 1 ;
                }else{
                    $tempvalue = 0 ;
                    echo "<li>&nbsp;</li>";
                    ?> <script>
                     document.getElementById('show_div_mproblem<?php echo $eid; ?>').style.display = "block";
                    </script> <?php
                } 
            } 
        }else{
             echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr>
                    <th width='60%' class = 'tdborder'><b> Description </b></th> 
                    <th width='20%' class = 'tdborder'><b> Start Date </b></th> 
                    <th width='20%' class = 'tdborder'><b> End Date </b></th> 
                 </tr>";
                foreach($data2 as $key =>$value){
                    $pres_array[$key] = $value;
                }
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'");
                    $setoccu = sqlFetchArray($getoccu);
                    if(!empty($setoccu)){
                        $occurance = $setoccu['title']; 
                    }else{
                        $occurance = '';
                    }    
                }
                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'");
                    $setoutcome = sqlFetchArray($getoutcome);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                echo"<tr><td class = 'tdborder'><table border = '0'>";
                echo "<tr><td width='60%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    echo $pres_array[$i]['Title']; 
                else
                    echo "Not Specified. ";
                echo "</td></tr>";
                if(!empty($pres_array[$i]['Codes']) || !empty($pres_array[$i]['Description'])){
                    echo "<tr><td class='secondtd'>";
                    echo "( ".$pres_array[$i]['Codes'] ." ".$pres_array[$i]['Description'].")";
                    echo "</td></tr>";
                }
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    echo "<tr><td class='thirdtd'> ";
                    echo $occurance;
                    if($outcome != 'Unassigned')
                        echo ", ". $outcome;
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    echo "<tr><td class='thirdtd'>";
                    echo $pres_array[$i]['Begdate']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['destination'])){
                    echo "<tr><td class='thirdtd'> ";
                    echo $pres_array[$i]['destination'];
                    echo "</td></tr>";
                }
                echo "</table></td>";
                echo "<td width='20%' class = 'tdborder'>".$pres_array[$i]['Begdate']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Enddate']."";
                echo "</tr>";
            }
            echo "</table>";
        }
        $tempvalue = 0 ;
        ?>
        <script>
            document.getElementById('show_div_mproblem<?php echo $eid; ?>').style.display = "block";
        </script>
        <?php
        
    }else{
        //echo " <li>No Medical Problems for this patient</li>";
        //if($min == 1)
            //$tempvalue = 1 ;
    }
}
function display_allergy($encounterid,$layout_type,$groupName,$patient_id){
    global $tempvalue;
    $allergysql = sqlStatement('SET SQL_BIG_SELECTS = 1');
    if($encounterid != 0){
        $allegrysqlquery .= "SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS isIssueActive, li.id,outcome,destination,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, 
                    li.reaction as reaction,li.occurrence AS Occurrence, li.referredby AS ReferredBy,ie.encounter, li.comments,
                    (SELECT COUNT( list_id ) 
                    FROM issue_encounter
                    WHERE list_id = li.id
                    ) AS enc
                FROM  `icd9_dx_code` i
                RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                INNER JOIN issue_encounter ie ON ie.list_id = li.id
                INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                WHERE li.type ='allergy' AND li.pid =".$patient_id." AND ie.encounter = $encounterid ";
    }else{
       $allegrysqlquery .= " SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS isIssueActive, outcome,destination,li.id,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, 
                    li.reaction as reaction,li.occurrence AS Occurrence, li.referredby AS ReferredBy, li.comments,
                    (SELECT COUNT( list_id ) 
                    FROM issue_encounter
                    WHERE list_id = li.id
                    ) AS enc
                FROM  `icd9_dx_code` i
                RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                WHERE li.type ='allergy' And li.pid = ".$patient_id."
                AND li.id NOT IN (
                                SELECT DISTINCT list_id
                                FROM issue_encounter
                                WHERE pid =".$patient_id.") group by li.id";
    }
    $allegrysql = sqlStatement($allegrysqlquery);
    while ($frow = sqlFetchArray($allegrysql)) {
        $dataallergy[] = $frow; 
    }
    $datacheck = '';
    if(count($dataallergy)>0){
        if($layout_type == 'list'){
            foreach($dataallergy as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        echo "<li>";
                        $datacheck = 1;
                        if($key == 'Occurrence'){
                            $occsql =  sqlStatement("select title from list_options  where list_id='occurrence' AND option_id='$val'");
                            $frow1 = sqlFetchArray($occsql);
                             if(!empty($frow1['title']))   
                                 echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1['title'];
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc'){
                            echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        echo "</li>";
                    }
                }
                if($datacheck == ''){
                    $tempvalue = 1 ;
                }else{
                    echo "<li>&nbsp;</li>";
                    ?>
                    <script>
                        document.getElementById('show_div_allergy<?php echo $encounterid; ?>').style.display = "block";
                    </script>    
                    <?php
                    $tempvalue = 0 ;
                } 
            } 
        }else{
            echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr>
                    <th class = 'tdborder' width='60%'><b> Description </b></th> 
                    <th class = 'tdborder' width='20%'><b> Start Date </b></th> 
                    <th class = 'tdborder' width='20%'><b> End Date </b></th> 
                </tr>";
                foreach($dataallergy as $key =>$value){
                    $pres_array[$key] = $value;
                }
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'");
                    $setoccu = sqlFetchArray($getoccu);
                    if(!empty($setoccu)){
                        $occurance = $setoccu['title']; 
                    }else{
                        $occurance = '';
                    }    
                }
                
                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'");
                    $setoutcome = sqlFetchArray($getoutcome);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                echo"<tr ><td class = 'tdborder'><table border = '0'>";
                echo "<tr><td width='60%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    echo $pres_array[$i]['Title']; 
                else
                    echo "Not Specified. ";
                echo "</td></tr>";
                if(!empty($pres_array[$i]['Codes']) || !empty($pres_array[$i]['Description'])){
                    echo "<tr><td class='secondtd'>";
                    echo "( ".$pres_array[$i]['Codes'] ." ".$pres_array[$i]['Description'].")";
                    echo "</td></tr>";
                }
                
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    echo "<tr><td class='thirdtd'> ";
                    echo $occurance;
                    if($outcome != 'Unassigned')
                        echo ", ". $outcome;
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    echo "<tr><td class='thirdtd'>";
                    echo $pres_array[$i]['Begdate']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    echo "</td></tr>";
                }
                
                if(!empty($pres_array[$i]['destination'])){
                    echo "<tr><td class='thirdtd'> ";
                    echo $pres_array[$i]['destination'];
                    echo "</td></tr>";
                }
                echo "</table></td>";
                echo "<td width='20%' class = 'tdborder'>".$pres_array[$i]['Begdate']."</td><td width='20%' class = 'tdborder''>".$pres_array[$i]['Enddate']."";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
        <script>
        document.getElementById('show_div_allergy<?php echo $encounterid; ?>').style.display = "block";
       </script>
        <?php
        $tempvalue = 0 ;
    }else{
        //if($min == 1)
            //$tempvalue = 1 ;
        //echo "<li> No Allergies for this patient</li>";
    }
}
function display_surgery($encounterid,$layout_type,$patient_id){
    global $tempvalue;
    echo "<br>";
    if($layout_type == 'grid'){
        echo "<style>
            .tbl_surgery {border:1px solid #C3E7F5;background-color: #E0E0E0 ; }
            .tbl_surgery_th
            {border:0px 1px 1px 0px solid  #C3E7F5;;text-align:center; background-color:  #E0E0E0;}
            .tbl_surgery_td 
            {border:0px 1px 1px 0px solid  ;text-align:center; background:#FFFFFF ;}
           </style>";
    }
    $surgerysql2 = sqlStatement('SET SQL_BIG_SELECTS = 1');
    if($encounterid != 0){
        $surgerysql2 = "SELECT 
                    CASE 
                    WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS isIssueActive,outcome,destination, li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, 
                    li.diagnosis AS Codes, li.reaction as reaction,li.occurrence AS Occurrence, li.referredby AS ReferredBy,ie.encounter, li.comments
                FROM  lists li 
                INNER JOIN issue_encounter ie ON ie.list_id = li.id
                INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                WHERE li.type ='surgery' AND li.pid =". $patient_id." AND ie.encounter = $encounterid";
    }else{
       $surgerysql2 = " SELECT CASE WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS isIssueActive,outcome,destination,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes,(SELECT DISTINCT i.long_desc FROM `icd9_dx_code` i WHERE li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                ) as Description, li.reaction AS reaction, li.occurrence AS Occurrence, li.referredby AS ReferredBy, li.comments
                    FROM lists li
                    WHERE li.type =  'surgery'
                    AND li.pid =".$patient_id." AND li.id NOT IN (
                                SELECT DISTINCT list_id
                                FROM issue_encounter
                                WHERE pid =".$patient_id
                            .") group by li.id";
    }
    $surgerysql = sqlStatement($surgerysql2);
    while ($frow2 = sqlFetchArray($surgerysql)) {
        $datasurgery[] = $frow2; 
    }
    $datacheck = '';
    if(count($datasurgery)>0){
        if($layout_type == 'list'){
            foreach($datasurgery as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        echo "<li>";
                        $datacheck = 1;
                        if($key == 'Occurrence'){
                            $occsql =  sqlStatement("select title from list_options  where list_id='occurrence' AND option_id='$val'");
                            $frow1 = sqlFetchArray($occsql);
                            if(!empty($frow1['title']))
                                echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1['title'];
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc') {
                           echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        echo "</li>";
                    }
                }
                
                if($datacheck == ''){
                    //echo "<li>No Surgeries for this patient.</li>";
                    //if($min == 1)
                        //$tempvalue = 1 ;
                }else{
                    echo "<li>&nbsp;</li>";
                    ?>
                    <script>
                    document.getElementById('show_div_surgery<?php echo $encounterid; ?>').style.display = "block";
                   </script>
                    <?php
                    $tempvalue = 0 ;
                } 
            } 
        }else{
             echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr>
                    <th class = 'tdborder' width='60%'><b> Description </b></th> 
                    <th class = 'tdborder' width='20%'><b> Start Date </b></th> 
                    <th class = 'tdborder' width='20%'><b> End Date </b></th> 
                 </tr>";
            foreach($datasurgery as $key =>$value){
                $pres_array[$key] = $value;
            }
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'");
                    $setoccu = sqlFetchArray($getoccu);
                    if(!empty($setoccu)){
                        $occurance = $setoccu['title']; 
                    }else{
                        $occurance = '';
                    }    
                }
                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'");
                    $setoutcome = sqlFetchArray($getoutcome);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                echo"<tr><td class = 'tdborder'><table border = '0'>";
                echo "<tr><td width='60%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    echo $pres_array[$i]['Title']; 
                else
                    echo "Not Specified. ";
                echo "</td></tr>";
                if(!empty($pres_array[$i]['Codes']) || !empty($pres_array[$i]['Description'])){
                    echo "<tr><td class='secondtd'>";
                    echo "( ".$pres_array[$i]['Codes'] ." ".$pres_array[$i]['Description'].")";
                    echo "</td></tr>";
                }
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    echo "<tr><td class='thirdtd'> ";
                    echo $occurance;
                    if($outcome != 'Unassigned')
                        echo ", ". $outcome;
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    echo "<tr><td class='thirdtd'>";
                    echo $pres_array[$i]['Begdate']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['destination'])){
                    echo "<tr><td class='thirdtd'> ";
                    echo $pres_array[$i]['destination'];
                    echo "</td></tr>";
                }
                echo "</table></td>";
                echo "<td width='20%' class = 'tdborder'>".$pres_array[$i]['Begdate']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Enddate']."";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
        <script>
            document.getElementById('show_div_surgery<?php echo $encounterid; ?>').style.display = "block";
        </script>
        <?php
        $tempvalue = 0 ;
    }else{
        //echo "<li>No Surgeries for this patient.</li>";
        //if($min == 1)
            //$tempvalue = 1 ;
    }
}
function display_dental($encounterid,$layout_type,$patient_id){
    global $tempvalue;
    if($encounterid != 0){       
        $dentalsql = sqlStatement("SELECT 
                        CASE 
                        WHEN li.enddate IS NULL 
                        THEN  'Active'
                        ELSE  'Inactive'
                        END AS isIssueActive,outcome,destination,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, 
                        li.occurrence AS Occurrence, li.referredby AS ReferredBy
                    FROM  `icd9_dx_code` i
                    RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                    INNER JOIN issue_encounter ie ON ie.list_id = li.id
                    INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
                    WHERE ie.encounter = $encounterid
                    AND li.type ='dental'");
    }else{
        $dentalsql = sqlStatement(" SELECT CASE WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS isIssueActive,outcome,destination,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes,(SELECT DISTINCT i.long_desc FROM `icd9_dx_code` i WHERE li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                ) as Description, li.reaction AS reaction, li.occurrence AS Occurrence, li.referredby AS ReferredBy, li.comments
                    FROM lists li
                    WHERE li.type =  'dental'
                    AND li.pid =".$patient_id." AND li.id NOT IN (
                                SELECT DISTINCT list_id
                                FROM issue_encounter
                                WHERE pid =".$patient_id
                            .") group by li.id");
    }
    while ($frow3 = sqlFetchArray($dentalsql)) {
        $datadental[] = $frow3; 
    }
    $datacheck = '';
    if(count($datadental)>0){
        if($layout_type == 'list'){
            foreach($datadental as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        $datacheck = 1;
                        echo "<li>";
                        if($key == 'Occurrence'){
                            $occsql =  sqlStatement("select title from list_options  where list_id='occurrence' AND option_id='$val'");
                            $frow1 = sqlFetchArray($occsql);
                            if(!empty($frow1['title']))
                                echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1['title'];
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc') {
                           echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        echo "</li>";
                    }
                }
                $tempvalue = 0 ;
                if($datacheck == ''){
                    //if($min == 1)
                        //$tempvalue = 1 ;
                }else{
                    echo "<li>&nbsp;</li>";
                    ?>
                    <script>
                        document.getElementById('show_div_dental<?php echo $encounterid; ?>').style.display = "block";
                    </script>
                    <?php
                
                } 
            }
        }else{
             echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr>
                    <th class = 'tdborder' width='60%'><b> Description </b></th> 
                    <th  class = 'tdborder' width='20%'><b> Start Date </b></th> 
                    <th class = 'tdborder' width='20%'><b> End Date </b></th> 
                 </tr>";
            foreach($datadental as $key =>$value){
                $pres_array[$key] = $value;
            }
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'");
                    $setoccu = sqlFetchArray($getoccu);
                    if(!empty($setoccu)){
                        $occurance = $setoccu['title']; 
                    }else{
                        $occurance = '';
                    }    
                }
                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'");
                    $setoutcome = sqlFetchArray($getoutcome);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                echo"<tr><td  class = 'tdborder'><table border = '0'>";
                echo "<tr><td width='60%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    echo $pres_array[$i]['Title']; 
                else
                    echo "Not Specified. ";
                echo "</td></tr>";
                if(!empty($pres_array[$i]['Codes']) || !empty($pres_array[$i]['Description'])){
                    echo "<tr><td class='secondtd'>";
                    echo "( ".$pres_array[$i]['Codes'] ." ".$pres_array[$i]['Description'].")";
                    echo "</td></tr>";
                }
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    echo "<tr><td class='thirdtd'> ";
                    echo $occurance;
                    if($outcome != 'Unassigned')
                        echo ", ". $outcome;
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    echo "<tr><td class='thirdtd'>";
                    echo $pres_array[$i]['Begdate']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['destination'])){
                    echo "<tr><td class='thirdtd'> ";
                    echo $pres_array[$i]['destination'];
                    echo "</td></tr>";
                }
                echo "</table></td>";
                echo "<td width='20%' class = 'tdborder'>".$pres_array[$i]['Begdate']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Enddate']."";
                echo "</tr>";
            }
            echo "</table>"; 
        }
        ?>
        <script>
            document.getElementById('show_div_dental<?php echo $encounterid; ?>').style.display = "block";
        </script>
        <?php
        $tempvalue = 0 ;
    }else{
        //echo "<li>No Dental Issues for this patient.</li>";
        //if($min == 1)
            //$tempvalue = 1 ;
    }    
}


//face to face
function display_f2f($encounter1,$groupName1){
    global $tempvalue;
    $getCC = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Face to Face HH Plan' order by seq");
    
    $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName1' AND form_id = 'LBF2' AND group_name LIKE '%Face to Face HH Plan' AND option_value = 'YES'");
    while($setselectedvalues= sqlFetchArray($getselectedvales)){
        $listselected[] = $setselectedvalues['selectedfield'];
    }
    while($getCCResult = sqlFetchArray($getCC)){
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getCCResult['field_id']):
                $fieldid[$getCCResult['title']] = $getCCResult['field_id'];
            endif;
        }
    }
    if(!empty($fieldid)):
        $getformid = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounter1 AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ");
            $setformid=sqlFetchArray($getformid);
            if(!empty($setformid)):
                for($i=0; $i< count($setformid); $i++){
                    $formid = $setformid['form_id'];
                    $datacheck = '';
                    foreach($fieldid as $fkey => $fid){

                        $getCCdata = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'");// AND (SELECT count(*) FROM lbf_data WHERE form_id = '$formid' AND field_id = 'f2f_stat' AND field_value='finalized')>0");
                        $getCCdataResult2=sqlFetchArray($getCCdata);
                        echo "<li>";
                        if(!empty($getCCdataResult2)):
                            echo "<b>".$fkey.": "."</b>".str_replace('|',',',$getCCdataResult2['field_value']);
                            $datacheck = 1;  
                        else:
                          //echo "<b>".$fkey.":"."</b>";
                        endif;
                       echo "</li>";
                    }
                    $tempvalue = 0 ;
                    if($datacheck == ''){
                        //echo " No Face to Face Data for this patient";
                        //if($min == 1)
                            //$tempvalue = 1 ;
                    }else{
                        ?>
                        <script>
                            document.getElementById('show_div_f2f<?php echo $encounter1; ?>').style.display = "block";
                        </script>
                        <?php
                    }
                }
                
            else:
//                echo " No Face to Face Data for this patient";
                //if($min == 1)
                    //$tempvalue = 1 ;
            endif;
    endif;
            
}

function display_dme($encounterid,$layout_type,$patient_id){
    global $tempvalue;
    $surgerysql2 = sqlStatement('SET SQL_BIG_SELECTS = 1');
    if($encounterid != 0){
        $surgerysql2 = "SELECT 
            CASE 
            WHEN li.enddate IS NULL 
            THEN  'Active'
            ELSE  'Inactive'
            END AS isIssueActive, outcome,destination,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes, i.long_desc AS Description, 
            li.occurrence AS Occurrence, li.referredby AS ReferredBy,li.comments AS Comments
        FROM  `icd9_dx_code` i
        RIGHT JOIN lists li ON li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
        INNER JOIN issue_encounter ie ON ie.list_id = li.id
        INNER JOIN form_encounter fe ON fe.encounter = ie.encounter
        WHERE ie.encounter =$encounterid
        AND li.type ='DME'";
    }else{
        $surgerysql2 = " SELECT CASE WHEN li.enddate IS NULL 
                    THEN  'Active'
                    ELSE  'Inactive'
                    END AS isIssueActive,outcome,destination,li.title AS Title, li.begdate AS Begdate, li.enddate AS Enddate, li.diagnosis AS Codes,(SELECT DISTINCT i.long_desc FROM `icd9_dx_code` i WHERE li.diagnosis = CONCAT(  'ICD9:', i.formatted_dx_code ) 
                ) as Description, li.reaction AS reaction, li.occurrence AS Occurrence, li.referredby AS ReferredBy, li.comments
                    FROM lists li
                    WHERE li.type =  'DME'
                    AND li.pid =".$patient_id." AND li.id NOT IN (
                                SELECT DISTINCT list_id
                                FROM issue_encounter
                                WHERE pid =".$patient_id
                            .") group by li.id";
    }
    $allegrysql = sqlStatement($surgerysql2);
    while ($frow = sqlFetchArray($allegrysql)) {
        $datadme[] = $frow; 
    }
    $datacheck = '';
    if(count($datadme)>0):
        if($layout_type == 'list'){
             foreach($datadme as $key => $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        echo "<li>";
                        $datacheck = 1;
                        if($key == 'Occurrence'){
                            $occsql =  sqlStatement("select title from list_options  where list_id='occurrence' AND option_id='$val'");
                            $frow1 = sqlFetchArray($occsql);
                            if(!empty($frow1['title']))
                                echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$frow1['title'];
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc') {
                           echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$val;
                        }
                        echo "</li>";
                    }    
                }
                if($datacheck == ''){
                    //echo " <li>No DME for this patient.</li>";
                    //if($min == 1)
                        //$tempvalue = 1 ;
                }else{
                    ?>
                    <script>
                        document.getElementById('show_div_dme<?php echo $encounterid; ?>').style.display = "block";
                    </script>
                    <?php
                    $tempvalue = 0 ;
                    echo "<li>&nbsp;</li>";
                } 
            } 
        }else{
            echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr>
                    <th width='60%' class = 'tdborder'><b> Description </b></th> 
                    <th width='20%' class = 'tdborder'><b> Start Date </b></th> 
                    <th width='20%' class = 'tdborder'><b> End Date </b></th> 
                 </tr>";
            foreach($datadme as $key =>$value){
                    $pres_array[$key] = $value;
            }    
            for($i=0; $i< count($pres_array); $i++){
                if($pres_array[$i]['Occurrence'] != ''){
                    $getoccu = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['Occurrence']."' AND list_id = 'Occurrence'");
                    $setoccu = sqlFetchArray($getoccu);
                    if(!empty($setoccu)){
                        $occurance = $setoccu['title']; 
                    }else{
                        $occurance = '';
                    }    
                }
                if($pres_array[$i]['outcome'] != ''){
                    $getoutcome = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['outcome']."' AND list_id = 'Outcome'");
                    $setoutcome = sqlFetchArray($getoutcome);
                    if(!empty($setoutcome)){
                        $outcome = $setoutcome['title']; 
                    }else{
                        $outcome = '';
                    }    
                }
                echo"<tr><td  class = 'tdborder'><table border = '0'>";
                echo "<tr><td width='60%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    echo $pres_array[$i]['Title']; 
                else
                    echo "Not Specified. ";
                echo "</td></tr>";
                if(!empty($pres_array[$i]['Codes']) || !empty($pres_array[$i]['Description'])){
                    echo "<tr><td class='secondtd'>";
                    echo "( ".$pres_array[$i]['Codes'] ." ".$pres_array[$i]['Description'].")";
                    echo "</td></tr>";
                }
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    echo "<tr><td class='thirdtd'> ";
                    echo $occurance;
                    if($outcome != 'Unassigned')
                        echo ", ". $outcome;
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    echo "<tr><td class='thirdtd'>";
                    echo $pres_array[$i]['Begdate']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['destination'])){
                    echo "<tr><td class='thirdtd'> ";
                    echo $pres_array[$i]['destination'];
                    echo "</td></tr>";
                }
                echo "</table></td>";
                echo "<td width='20%' class = 'tdborder'>".$pres_array[$i]['Begdate']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Enddate']."";
                echo "</tr>";
            }
            echo "</table>";
        }
         ?>
        <script>
            document.getElementById('show_div_dme<?php echo $encounterid; ?>').style.display = "block";
        </script>
        <?php
        $tempvalue = 0 ;
    else:
        //echo " <li>No DME for this patient.</li>";
        //if($min == 1)
            //$tempvalue = 1 ;
    endif;
}
function display_prescription($layout_type,$patient_id){
    global $tempvalue;
    $pressql = sqlStatement("SELECT  * FROM  `prescriptions` WHERE  `patient_id` =".$patient_id."");
    while ($frow = sqlFetchArray($pressql)) {
        $datapre[] = $frow; 
    }
    $datacheck = '';
    if(count($datapre)>0){
        if($layout_type == 'list'){
            foreach($datapre as $value){
                foreach ($value as $key => $val) {
                    if(!empty($val)){
                        $datacheck = 1;
                        echo "<li>";
                        if($key=='Provider'){
                            $getporvidername = sqlStatement("SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$provider'" );
                            $rowName=sqlFetchArray($getporvidername);
                            $provider_name=$rowName['name'];
                            echo  "<b>". ucfirst(str_replace('_',' ',$key)).":"."</b>". $provider_name;
                        }else if($key=='Unit'){
                            $getunits = sqlStatement("SELECT title FROM list_options WHERE option_id='$val' AND list_id='drug_units'" );
                            $rowName=sqlFetchArray($getunits);
                            $units=$rowName['title'];   
                            echo  "<b>".  ucfirst(str_replace('_',' ',$key)).":"."</b>".$units;
                        }else if($key != 'id' && $key != 'encounter' && $key != 'enc'){
                            echo  "<b>".  ucfirst(str_replace('_',' ',$key)).":"."</b>". $val;
                        }
                        echo "</li>"; 
                    }
                }
                if($datacheck == ''){
                    //echo "<li> No Prescriptions for this patient.</li>";
                    //if($min == 1)
                        //$tempvalue = 1 ;
                }else{
                    echo "<li>&nbsp;</li>"; 
                    ?>
                    <script>
                        document.getElementById('show_div_prescript0').style.display = "block";
                    </script>
                    
                    <?php
                    $tempvalue = 0 ;
                } 
            } 
        }else{
            echo "<style>
                .tbl_pre {border:1px solid #C3E7F5;background-color: #E0E0E0; }
                .tbl_pre_th
                {border:0px 1px 1px 0px solid  #C3E7F5;;text-align:center; background-color:  #E0E0E0;}
                .tbl_pre_td 
                {border:0px 1px 1px 0px solid  ;text-align:center; background:#FFFFFF;}
               </style>";
            echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr> 
                    <td width='60%' class = 'tdborder'><b> Description </b></td> 
                    <td width='20%' class = 'tdborder'><b> Start Date </b></td> 
                    <td width='20%' class = 'tdborder'><b> End Date </b></td> 
                  </tr>";
            foreach($datapre as $key =>$value){
                $pres_array[$key] = $value;
            }  
            for($i=0; $i< count($pres_array); $i++){
                if(!empty($pres_array[$i]['unit'])){
                    $getdrugunit = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['unit']."' AND list_id = 'drug_units'");
                    $setdrugunit = sqlFetchArray($getdrugunit);
                    $drugunit = $setdrugunit['title'];
                }else{
                    $drugunit = '';
                } 
                if(!empty($pres_array[$i]['form'])){
                    $getdrugform = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['form']."' AND list_id = 'drug_form'");
                    $setdrugform = sqlFetchArray($getdrugform);
                    $drugform = $setdrugform['title']; 
                }else{
                    $drugform = '';
                } 
                if(!empty($pres_array[$i]['interval'])){
                    $getdruginterval = sqlStatement("SELECT title FROM list_options WHERE option_id = '".$pres_array[$i]['interval']."' AND list_id = 'drug_interval'");
                    $setdruginterval = sqlFetchArray($getdruginterval);
                    $druginterval = $setdruginterval['title']; 
                }else{
                    $druginterval = '';
                } 
                if(!empty($pres_array[$i]['provider_id'])){
                    $getporvidername = sqlStatement("SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='".$pres_array[$i]['provider_id']."'" );
                    $setporvidername = sqlFetchArray($getporvidername);
                    $provider_name = $setporvidername['name'];
                }
                if(!empty($pres_array[$i]['refills']) || !empty($pres_array[$i]['per_refill'])){
                   $refill = (!empty($pres_array[$i]['refills'])? $pres_array[$i]['refills'] : 0 )." # of tablets ". $pres_array[$i]['per_refill'];
                }else{
                    $refill = "No refills";
                }

                echo"<tr><td  class = 'tdborder'><table border = '0'>";
                echo "<tr><td class='firsttd' width='60%'>";
                echo $pres_array[$i]['drug']. " ".$pres_array[$i]['quantity']." ".$drugunit. " ".$drugform;
                echo "</td></tr><tr><td class='secondtd'>";
                echo $pres_array[$i]['dosage']. " ". $druginterval;
                echo "</td></tr>";
                echo "<tr><td class='thirdtd'>";
                echo $pres_array[$i]['date_added']. "<span class='byelement'> by </span> ". $provider_name . " ".$pres_array[$i]['quantity']." ".$drugunit. " ".$drugform ."(".$refill.")";
                echo "</td></tr>";
                echo "</table></td>";
                echo "</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['date_added']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['date_modified'];
                echo "</tr>";
            }
            echo "</td>";
             echo "</table>";
        }
        ?>
        <script>
            document.getElementById('show_div_prescript0').style.display = "block";
        </script>

        <?php
        $tempvalue = 0 ;
    }else{
        //echo "<li> No Prescriptions for this patient.</li>";
        //if($min == 1)
            //$tempvalue = 1 ;
    }
}   
function display_procedure($encounter, $layout_type,$patient_id){
    $getProc = "SELECT procedure_order_id FROM procedure_order where encounter_id=$encounter
                and patient_id='".$patient_id."'";
    $orderid = '';
    $Procdata=sqlStatement($getProc);
    while ($frow = sqlFetchArray($Procdata)) {
        $orderid =$frow['procedure_order_id'];
    
        if($orderid !== ''){
            if($layout_type =='list'){
                $get_procedure = sqlStatement("SELECT " .
                    "po.procedure_order_id as Order_Id, po.date_ordered as Order_Date, " .
                    "po.order_status as Order_Status, po.specimen_type as Specimen_type, " .
                    "pd.pubpid as Patient_Id, CONCAT(pd.lname,' ', pd.fname,' ', pd.mname) as Patient_Name ," .
                    "fe.date as Date, " .
                    "pp.name as Lab_Name, " .
                    "CONCAT(u.lname , ' ',u.fname ,' ',u.mname )AS Name " .
                    "FROM procedure_order AS po " .
                    "LEFT JOIN patient_data AS pd ON pd.pid = po.patient_id " .
                    "LEFT JOIN procedure_providers AS pp ON pp.ppid = po.lab_id " .
                    "LEFT JOIN users AS u ON u.id = po.provider_id " .
                    "LEFT JOIN form_encounter AS fe ON fe.pid = po.patient_id AND fe.encounter = po.encounter_id " .
                    "WHERE po.procedure_order_id = $orderid");
                $procedure = sqlFetchArray($get_procedure);
                foreach($procedure as $pkey =>$pvalue){
                    echo "<li>";
                    echo ucfirst(str_replace('_',' ',$pkey.":")) . $pvalue;
                    echo "</li>";
                }
                $query = sqlStatement("SELECT " .
                      "po.date_ordered as Date , CONCAT(pc.procedure_code ,':',pc.procedure_name ) as Procedure_Name, " .
                      "pr.procedure_report_id, pr.date_report as Reported, pr.date_collected, pr.specimen_num , " .
                      "pr.report_status as Specimen, pr.review_status as Status, pr.report_notes as Note " .
                      "FROM procedure_order AS po " .
                      "JOIN procedure_order_code AS pc ON pc.procedure_order_id = po.procedure_order_id " .
                      "LEFT JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id AND " .
                      "pr.procedure_order_seq = pc.procedure_order_seq " .
                      "WHERE po.procedure_order_id = $orderid " .
                      "ORDER BY pc.procedure_order_seq, pr.procedure_report_id");
                $procedure_result = sqlFetchArray($query);
                if(!empty($procedure_result)){
                    foreach($procedure_result as $pkey1 =>$pvalue1){
                        if(!empty($pvalue1)){
                            echo "<li>";

                            echo ucfirst(str_replace('_',' ',$pkey1.":")) . $pvalue1;
                            echo "</li>";
                        }
                    }
                }
                $query1 = sqlStatement("SELECT " .
                    "ps.result_code as Code, ps.result_text as Name, ps.abnormal as Abn, ps.result as Value, " .
                    "ps.range as _Range, ps.result_status as Status,  ps.facility as Facility, ps.units as Units, ps.comments as Note " .
                    "FROM procedure_result AS ps " .
                    "WHERE ps.procedure_report_id = $orderid " .
                    "ORDER BY ps.result_code, ps.procedure_result_id");
                $procedure_result1 = sqlFetchArray($query1);
                if(!empty($procedure_result1)){
                    foreach($procedure_result1 as $pkey2 =>$pvalue2){
                        if(!empty($pvalue2)){
                            echo "<li>";
                            echo ucfirst(str_replace('_',' ',$pkey2.":")) . $pvalue2;
                            echo "</li>";
                        }
                    }
                }
                ?><script>
                    document.getElementById('show_div_procedure<?php echo $encounter; ?>').style.display = "block";
                </script>

                <?php
                $tempvalue = 0 ;
            }else{    
                $input_form=false;
                global $aNotes;

                // Check authorization.
                $thisauth = acl_check('patients', 'med');
                if (!$thisauth) return xl('Not authorized');

                $orow = sqlQuery("SELECT " .
                  "po.procedure_order_id, po.date_ordered, " .
                  "po.order_status, po.specimen_type, " .
                  "pd.pubpid, pd.lname, pd.fname, pd.mname, " .
                  "fe.date, " .
                  "pp.name AS labname, " .
                  "u.lname AS ulname, u.fname AS ufname, u.mname AS umname " .
                  "FROM procedure_order AS po " .
                  "LEFT JOIN patient_data AS pd ON pd.pid = po.patient_id " .
                  "LEFT JOIN procedure_providers AS pp ON pp.ppid = po.lab_id " .
                  "LEFT JOIN users AS u ON u.id = po.provider_id " .
                  "LEFT JOIN form_encounter AS fe ON fe.pid = po.patient_id AND fe.encounter = po.encounter_id " .
                  "WHERE po.procedure_order_id = ?",
                  array($orderid));
                  ?>

                  <style>

                  .labres tr.head   { font-size:10pt; text-align:center; }
                  .labres tr.detail { font-size:10pt; }
                  .labres a, .labres a:visited, .labres a:hover { color:#0000cc; }

                  .labres table {
                   border-style: solid;
                   border-width: 1px 0px 0px 1px;
                   border-color: black;
                  }

                  .labres td, .labres th {
                   border-style: solid;
                   border-width: 0px 1px 1px 0px;
                   border-color: black;
                  }

                  </style>

                  <?php if ($input_form) { ?>
                  <script type="text/javascript" src="../../library/dialog.js"></script>
                  <script type="text/javascript" src="../../library/textformat.js"></script>
                  <?php } // end if input form ?>

                  <?php if (empty($GLOBALS['PATIENT_REPORT_ACTIVE'])) { ?>
                  <script language="JavaScript">
                  var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';
                  // Called to show patient notes related to this order in the "other" frame.
                  function showpnotes(orderid) {
                   // Look for the top or bottom frame that contains this document, return if none.
                   var w;
                   for (w = window; w.name != 'RTop' && w.name != 'RBot'; w = w.parent) {
                    if (w.parent == w) return false;
                   }
                   var othername = (w.name == 'RTop') ? 'RBot' : 'RTop';
                   w.parent.left_nav.forceDual();
                   w.parent.left_nav.setRadio(othername, 'pno');
                   w.parent.left_nav.loadFrame('pno1', othername, 'patient_file/summary/pnotes_full.php?orderid=' + orderid);
                   return false;
                  }
                  </script>
                  <?php } // end if not patient report ?>

                  <?php if ($input_form) { ?>
                  <form method='post' action='single_order_results.php?orderid=<?php echo $orderid; ?>'>
                  <?php } // end if input form ?>

                  <div class='labres'>

                  <table width='100%' cellpadding='2' cellspacing='0'>
                   <tr>
                    <td width='5%' nowrap><?php echo xlt('Patient ID'); ?></td>
                    <td width='45%'><?php echo myCellText($orow['pubpid']); ?></td>
                    <td width='5%' nowrap><?php echo xlt('Order ID'); ?></td>

                    <td width='45%'>
                  <?php
                    echo myCellText($orow['procedure_order_id']);
                  ?>
                    </td>
                   </tr>
                   <tr>
                    <td nowrap><?php echo xlt('Patient Name'); ?></td>
                    <td><?php echo myCellText($orow['lname'] . ', ' . $orow['fname'] . ' ' . $orow['mname']); ?></td>
                    <td nowrap><?php echo xlt('Ordered By'); ?></td>
                    <td><?php echo myCellText($orow['ulname'] . ', ' . $orow['ufname'] . ' ' . $orow['umname']); ?></td>
                   </tr>
                  <tr>
                    <td nowrap><?php echo xlt('Order Date'); ?></td>
                    <td><?php echo myCellText(oeFormatShortDate($orow['date_ordered'])); ?></td>
                    <td nowrap><?php echo xlt('Print Date'); ?></td>
                    <td><?php echo oeFormatShortDate(date('Y-m-d')); ?></td>
                   </tr>
                   <tr>
                    <td nowrap><?php echo xlt('Order Status'); ?></td>
                    <td><?php echo myCellText($orow['order_status']); ?></td>
                    <td nowrap><?php echo xlt('Encounter Date'); ?></td>
                    <td><?php echo myCellText(oeFormatShortDate(substr($orow['date'], 0, 10))); ?></td>
                   </tr>
                   <tr>
                    <td nowrap><?php echo xlt('Lab'); ?></td>
                    <td><?php echo myCellText($orow['labname']); ?></td>
                    <td nowrap><?php echo xlt('Specimen Type'); ?></td>
                    <td><?php echo myCellText($orow['specimen_type']); ?></td>
                   </tr>
                  </table>

                  &nbsp;<br />

                  <table width='100%' cellpadding='2' cellspacing='0'>

                   <tr class='head'>
                    <td rowspan='2' valign='middle'><?php echo xlt('Ordered Procedure'); ?></td>
                    <td colspan='4'><?php echo xlt('Report'); ?></td>
                    <td colspan='7'><?php echo xlt('Results'); ?></td>
                   </tr>

                   <tr class='head'>
                    <td><?php echo xlt('Reported'); ?></td>
                    <td><?php echo xlt('Specimen'); ?></td>
                    <td><?php echo xlt('Status'); ?></td>
                    <td><?php echo xlt('Note'); ?></td>
                    <td><?php echo xlt('Code'); ?></td>
                    <td><?php echo xlt('Name'); ?></td>
                    <td><?php echo xlt('Abn'); ?></td>
                    <td><?php echo xlt('Value'); ?></td>
                    <td><?php echo xlt('Range'); ?></td>
                    <td><?php echo xlt('Units'); ?></td>
                    <td><?php echo xlt('Note'); ?></td>
                   </tr>

                  <?php 
                    $query = "SELECT " .
                      "po.date_ordered, pc.procedure_order_seq, pc.procedure_code, " .
                      "pc.procedure_name, " .
                      "pr.procedure_report_id, pr.date_report, pr.date_collected, pr.specimen_num, " .
                      "pr.report_status, pr.review_status, pr.report_notes " .
                      "FROM procedure_order AS po " .
                      "JOIN procedure_order_code AS pc ON pc.procedure_order_id = po.procedure_order_id " .
                      "LEFT JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id AND " .
                      "pr.procedure_order_seq = pc.procedure_order_seq " .
                      "WHERE po.procedure_order_id = ? " .
                      "ORDER BY pc.procedure_order_seq, pr.procedure_report_id";

                    $res = sqlStatement($query, array($orderid));

                    $lastpoid = -1;
                    $lastpcid = -1;
                    $lastprid = -1;
                    $encount = 0;
                    $lino = 0;
                    $extra_html = '';
                    $aNotes = array();
                    $sign_list = '';

                    while ($row = sqlFetchArray($res)) {
                      $order_type_id  = empty($row['order_type_id'      ]) ? 0 : ($row['order_type_id' ] + 0);
                      $order_seq      = empty($row['procedure_order_seq']) ? 0 : ($row['procedure_order_seq'] + 0);
                      $report_id      = empty($row['procedure_report_id']) ? 0 : ($row['procedure_report_id'] + 0);
                      $procedure_code = empty($row['procedure_code'  ]) ? '' : $row['procedure_code'];
                      $procedure_name = empty($row['procedure_name'  ]) ? '' : $row['procedure_name'];
                      $date_report    = empty($row['date_report'     ]) ? '' : $row['date_report'];
                      $date_collected = empty($row['date_collected'  ]) ? '' : substr($row['date_collected'], 0, 16);
                      $specimen_num   = empty($row['specimen_num'    ]) ? '' : $row['specimen_num'];
                      $report_status  = empty($row['report_status'   ]) ? '' : $row['report_status']; 
                      $review_status  = empty($row['review_status'   ]) ? 'received' : $row['review_status'];

                      if ($review_status != 'reviewed' && $report_id) {
                        if ($sign_list) $sign_list .= ',';
                        $sign_list .= $report_id;
                      }

                      $report_noteid ='';
                      if (!empty($row['report_notes'])) {
                        $report_noteid = 1 + storeNote($row['report_notes']);
                      }

                      $query = "SELECT " .
                        "ps.result_code, ps.result_text, ps.abnormal, ps.result, " .
                        "ps.range, ps.result_status, ps.facility, ps.units, ps.comments " .
                        "FROM procedure_result AS ps " .
                        "WHERE ps.procedure_report_id = ? " .
                        "ORDER BY ps.result_code, ps.procedure_result_id";

                      $rres = sqlStatement($query, array($report_id));
                      $rrows = array();
                      while ($rrow = sqlFetchArray($rres)) {
                        $rrows[] = $rrow;
                      }
                      if (empty($rrows)) {
                        $rrows[0] = array('result_code' => '');
                      }

                      foreach ($rrows as $rrow) {
                        $result_code      = empty($rrow['result_code'     ]) ? '' : $rrow['result_code'];
                        $result_text      = empty($rrow['result_text'     ]) ? '' : $rrow['result_text'];
                        $result_abnormal  = empty($rrow['abnormal'        ]) ? '' : $rrow['abnormal'];
                        $result_result    = empty($rrow['result'          ]) ? '' : $rrow['result'];
                        $result_units     = empty($rrow['units'           ]) ? '' : $rrow['units'];
                        $result_facility  = empty($rrow['facility'        ]) ? '' : $rrow['facility'];
                        $result_comments  = empty($rrow['comments'        ]) ? '' : $rrow['comments'];
                        $result_range     = empty($rrow['range'           ]) ? '' : $rrow['range'];
                        $result_status    = empty($rrow['result_status'   ]) ? '' : $rrow['result_status'];

                        $result_comments = trim($result_comments);
                        $result_noteid = '';
                        if (!empty($result_comments)) {
                          $result_noteid = 1 + storeNote($result_comments);
                        }

                        if ($lastpoid != $order_id || $lastpcid != $order_seq) {
                          ++$encount;
                        }
                        //$bgcolor = "#" . (($encount & 1) ? "ddddff" : "ffdddd");

                        echo " <tr class='detail' bgcolor='FFFFFF'>\n";

                        if ($lastpcid != $order_seq) {
                          $lastprid = -1; // force report fields on first line of each procedure
                          echo "  <td>" . text("$procedure_code: $procedure_name") . "</td>\n";
                        }
                        else {
                          echo "  <td style='background-color:transparent'>&nbsp;</td>";
                        }

                        // If this starts a new report or a new order, generate the report fields.
                        if ($report_id != $lastprid) {
                          echo "  <td>";
                          echo myCellText(oeFormatShortDate($date_report));
                          echo "</td>\n";

                          echo "  <td>";
                          echo myCellText($specimen_num);
                          echo "</td>\n";

                          echo "  <td title='" . xla('Check mark indicates reviewed') . "'>";
                          echo myCellText(getListItem('proc_rep_status', $report_status));
                          if ($row['review_status'] == 'reviewed') {
                            echo " &#x2713;"; // unicode check mark character
                          }
                          echo "</td>\n";

                          echo "  <td align='center'>";
                          echo myCellText($report_noteid);
                          echo "</td>\n";
                        }
                        else {
                          echo "  <td colspan='4' style='background-color:transparent'>&nbsp;</td>\n";
                        }

                        if ($result_code !== '') {
                          echo "  <td>";
                          echo myCellText($result_code);
                          echo "</td>\n";
                          echo "  <td>";
                          echo myCellText($result_text);
                          echo "</td>\n";
                          echo "  <td>";
                          echo myCellText(getListItem('proc_res_abnormal', $result_abnormal));
                          echo "</td>\n";
                          echo "  <td>";
                          echo myCellText($result_result);
                          echo "</td>\n";
                          echo "  <td>";
                          echo myCellText($result_range);
                          echo "</td>\n";
                          echo "  <td>";
                          echo myCellText($result_units);
                          echo "</td>\n";
                          echo "  <td align='center'>";
                          echo myCellText($result_noteid);
                          echo "</td>\n";
                        }
                        else {
                          echo "  <td colspan='7' style='background-color:transparent'>&nbsp;</td>\n";
                        }

                        echo " </tr>\n";

                        $lastpoid = $order_id;
                        $lastpcid = $order_seq;
                        $lastprid = $report_id;
                        ++$lino;
                      }
                    }
                  ?>

                  </table>

                  &nbsp;<br />
                  <table width='100%' style='border-width:0px;'>
                   <tr>
                    <td style='border-width:0px;'>
                  <?php
                    if (!empty($aNotes)) {
                      echo "<table cellpadding='3' cellspacing='0'>\n";
                      echo " <tr>\n";
                      echo "  <th align='center' colspan='2'>" . xlt('Notes') . "</th>\n";
                      echo " </tr>\n";
                      foreach ($aNotes as $key => $value) {
                        echo " <tr>\n";
                        echo "  <td valign='top'>" . ($key + 1) . "</td>\n";
                        echo "  <td>" . nl2br(text($value)) . "</td>\n";
                        echo " </tr>\n";
                      }
                      echo "</table>\n";
                    }
                  ?>
                   </tr>
                  </table>

                  </div>

                  <?php if ($input_form) { ?>
                  </form>
                  <?php } // end if input form ?>

                  <?php  

            ?><script>
                    document.getElementById('show_div_procedure<?php echo $encounter; ?>').style.display = "block";
                </script>

                <?php
                $tempvalue = 0 ;
            }
        }else{
            //echo "<li>No Procedure order form for this date of service.</li>";
            //if($min == 1)
                //$tempvalue = 1 ;
        }
    }
}
function display_cert_recert($encounterid,$groupName){
    global $tempvalue;
    $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%Certification_Recertification' AND option_value = 'YES'");
    while($setselectedvalues= sqlFetchArray($getselectedvales)){
        $listselected[] = $setselectedvalues['selectedfield'];
    }
    $getCC = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%Certification_Recertification'");
    while($getCCResult = sqlFetchArray($getCC)){ 
        for($i=0; $i<count($listselected); $i++){
            if($listselected[$i] == $getCCResult['field_id']):
                $fieldid[$getCCResult['title']] = $getCCResult['field_id'];
            endif;
        }
    }
    if(!empty($fieldid)){
        $getformid = sqlStatement("SELECT form_id FROM forms WHERE encounter = $encounterid AND form_name = 'Allcare Encounter Forms' AND deleted = 0 ");
        $setformid=sqlFetchArray($getformid);
        if(!empty($setformid)){
            for($i=0; $i< count($setformid); $i++){
                $formid = $setformid['form_id'];
                $datacheck = '';
                foreach($fieldid as $fkey => $fid){
                    $getCCdata = sqlStatement("SELECT field_value FROM lbf_data WHERE form_id = '$formid' AND field_id = '".$fid."'");
                    $getCCdataResult2=sqlFetchArray($getCCdata);
                    if(!empty($getCCdataResult2['field_value'])){
                        echo "<li>";
//                        if(strcmp($fkey, 'Certification/Recertification Text') != 0):
                            echo "<b>".$fkey.":</b>";
//                        endif;
                        $datacheck = 1;
                        if(!empty($getCCdataResult2)):
                            if(strcmp($fkey, 'Certification/Recertification Status') == 0):
                                echo   ucwords(str_replace('|', ',', $getCCdataResult2['field_value']))."</li>";
                            else:
                                echo nl2br($getCCdataResult2['field_value'])."</li>";
                            endif;
                        endif;
                    }
                }
                if($datacheck == ''){
                    //if($min == 1)
                        //$tempvalue = 1 ;
                }else{
                    $tempvalue = 0 ;
                    ?>
                    <script>
                        document.getElementById('show_div_cert_recert<?php echo $encounterid; ?>').style.display = "block";
                    </script> 
                    <?php
                }
            }
        } else{
            //echo " No Chief Complaint for this date of service.";
            //if($min == 1)
                //$tempvalue = 1 ;
        }
    } 
}
function display_cpo($encounterid,$groupName,$patient_id){
    global $tempvalue;
    $getCC = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id = 'LBF2' AND group_name LIKE '%CPO'");
    $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND form_id = 'LBF2' AND group_name LIKE '%CPO' AND option_value = 'YES'");
    while($setselectedvalues= sqlFetchArray($getselectedvales)){
         $listselected[] = $setselectedvalues['selectedfield'];
    }
    while($getCCResult = sqlFetchArray($getCC)){
        for($i=0; $i<count($listselected); $i++){
        if($listselected[$i] == $getCCResult['field_id']):
           $fieldid[$getCCResult['title']] = $getCCResult['field_id'];
        endif;
        }
    }
    $date3=sqlStatement("select cp.cpo_data, cp.provider_id  from tbl_form_cpo cp INNER JOIN form_encounter fe on fe.pid=cp.pid INNER JOIN forms f on fe.encounter = f.encounter AND f.form_id=cp.id  where cp.pid=".$patient_id." AND fe.encounter='$encounterid' AND form_name='cpo' and deleted=0 group by cp.id ");
    $fdate2=sqlFetchArray($date3);
    if(!empty($fdate2)){
        foreach($fdate2 as $key=> $value){
            if($key == 'cpo_data'){
                $cpoarray = unserialize($value);
                for($i=0; $i< count($cpoarray); $i++){
                    foreach($cpoarray[$i] as $key1 => $value1){
                        if(!empty($value1)){
                            if($key1 == 'cpotype') {    
                                $ctype=sqlStatement("select title from list_options where list_id='CPO_types' AND option_id='$value1'");
                                $crow = sqlFetchArray($ctype);
                                echo  "<li><b>".ucfirst('Type').":"."</b>".$crow['title']."</li>";
                            }elseif($key1 == 'timeinterval') {    
                                $time=sqlStatement("select title from list_options where list_id='Time_Interval' AND option_id='$value1'");
                                $time2 = sqlFetchArray($time);
                                echo  "<li><b>".ucwords('Duration').":"."</b>".$time2['title']."</li>";
                            }else{
                                echo  "<li><b>".ucwords(str_replace('_',' ',$key1)).":"."</b>".$value1."</li>";
                            }
                        }
                    }
                }
            }else{
                if($key== 'provider_id'):
                    $getporvidername = sqlStatement("SELECT CONCAT(lname,' ',fname) AS name FROM users WHERE id='".$value."'" );
                    $rowName=sqlFetchArray($getporvidername);
                    $provider1=$rowName['name'];
                    if(!empty($provider1)):
                        echo  "<li><b>".ucwords(str_replace('_',' ',$key)).":"."</b>".$provider1."</li> ";
                    endif;
                else:
                    if(!empty($value)):
                        echo  "<li><b>".ucwords(str_replace('_',' ',$key)).":"."</b>".$value."</li> ";
                    endif;
                endif;
            }
        }
        ?>
        <script>
            document.getElementById('show_div_cpo<?php echo $encounterid; ?>').style.display = "block";
        </script>
        <?php
        $tempvalue = 0 ;
    }else{
        //echo "<li>"."No CPO Log for this date of service."."</ii>";
       //if($min == 1)
            //$tempvalue = 1 ;
    }
 }

$s = '';
$fh = fopen($template_file, 'r');
while (!feof($fh)) $s .= fread($fh, 8192);
fclose($fh);
$getChartOutputTrans="SELECT *  FROM tbl_form_chartoutput_transactions
                      WHERE pid='$patient_id'
                      AND id='$trans_form_id'";

$resChartOutputTrans=sqlStatement($getChartOutputTrans) or die(mysql_error());

$rowChartOutputTrans=sqlFetchArray($resChartOutputTrans);

$exploded = explode(',', $rowChartOutputTrans['date_of_service']);
foreach($exploded as $dos){
   $date_of_service .= "'".$dos."',";
}
$date_of_service2 = rtrim($date_of_service, ',');
?>
<html>
<head>
    <title>Medical Record</title>
     <style>
@page { size 8.5in 11in; margin: 2cm; }
div.page { page-break-before: always }
.tdborder {font-size: 15; padding-right: 100px;border:none;border-bottom-style: solid;border-width: 1px;}
.firsttd{
    font-size: 15;
}
.secondtd{
    font-size: 12;
}
.thirdtd{
    font-size: 12;
}
.byelement{
    color: gray;
}
ul
{
    list-style-type: none;
    -webkit-padding-start: 0px !important;

}
li { padding-right:40px; }
ul{float:left;}
</style>
<script type="text/javascript" src="../../../library/js/jquery-1.9.1.min.js"></script>
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
    $getcolval = sqlStatement("SELECT DISTINCT CONCAT(screen_name,'-',substring(group_name FROM 2)) as cname,layout_col FROM tbl_chartui_mapping where form_id!='LBF2'");
    while($setcolval = sqlFetchArray($getcolval)){   
        $idName = str_replace(" ","-",$setcolval['cname']); 
        ?> breakList('#<?php echo $idName; ?>','<?php echo $setcolval['layout_col']; ?>', $('.<?php echo $idName; ?>')); 
        breakList('#<?php echo "1".$idName; ?>','<?php echo $setcolval['layout_col']; ?>', $('.<?php echo "1".$idName; ?>'));<?php
    }
    $getcolval1 = sqlStatement("SELECT DISTINCT CONCAT(screen_name,'-',substring(group_name FROM 2)) as cname,substring(group_name FROM 2) as groupname,layout_col,screen_name FROM tbl_chartui_mapping where form_id='LBF2'");
    while($setcolval1 = sqlFetchArray($getcolval1)){           
        $geteid1 = sqlStatement("SELECT count(*) as count FROM forms WHERE DATE( date ) IN (".$date_of_service2.") AND form_name = 'New Patient Encounter' AND deleted = 0 AND pid = ".$patient_id ); 
        $eidval1 = sqlFetchArray($geteid1); 
        //$group_check = $setcolval1['groupname'];
        if($setcolval1['groupname']!== 'Immunization'){
            for($i=0;$i<=$eidval1['count'];$i++) {
                $idName1 = str_replace(" ","-",$setcolval1['cname']).$i; 
                ?>breakList('#<?php echo $idName1; ?>','<?php echo $setcolval1['layout_col']; ?>', $('.<?php echo $idName1; ?>')); 
                breakList('#<?php echo "1".$idName1; ?>','<?php echo $setcolval1['layout_col']; ?>', $('.<?php echo "1".$idName1; ?>'));       <?php 
//                if($group_check == 'History of Present illness'){
//                    $getgroupnames = sqlStatement("SELECT DISTINCT(l.group_name) as group_names ,t.layout_col, t.screen_name as screen_name from layout_options l
//                            inner join tbl_chartui_mapping t on  t.group_name = l.group_name  where l.form_id='HIS' and uor <> 0 and screen_name ='".$setcolval1['screen_name']."' order by l.group_name");
//                    while($setgroupnames = sqlFetchArray($getgroupnames)){
//                        ?> breakList('#<?php echo "1".str_replace(" ", "-",$setgroupnames['screen_name']." ".$setgroupnames['group_names'].$i); ?>','<?php echo $setgroupnames['layout_col']; ?>', $('.<?php echo "1".str_replace(" ", "-",$setgroupnames['screen_name']." ".$setgroupnames['group_names'].$i); ?>'));<?php
//                    } 
//                }  
            } 
        }else{
            $idName2 = str_replace(" ","-",$setcolval1['cname']);?>
            breakList('#<?php echo $idName2; ?>','<?php echo $setcolval1['layout_col']; ?>', $('.<?php echo $idName2; ?>'));  
            breakList('#<?php echo "1".$idName2; ?>','<?php echo $setcolval1['layout_col']; ?>', $('.<?php echo "1".$idName2; ?>'));  
            <?php 
        } 
    } ?>    
});
</script>
</head>
<body>
<div>
<?php
$getlayout_fields = sqlStatement("SELECT * FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != ''  AND group_name='$group'  ORDER BY seq");
while($setlayout = sqlFetchArray($getlayout_fields)){   
    $result[$setlayout['seq']]=$setlayout['field_id'];
}
foreach($result as $key1 => $value1){
    $getselected = sqlStatement("SELECT $value1   FROM tbl_form_chartoutput_transactions WHERE pid='$patient_id' AND $value1='YES' AND id='$trans_form_id'");
    $setselected[] = sqlFetchArray($getselected);
}
$resultant = array();
for($i=0;$i<count($setselected);$i++){
    foreach($setselected as $key=>$value){
         foreach($value as $key=> $val){
            $getlayout_fields1 = sqlStatement("SELECT * FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND uor > 0 AND field_id != ''  AND group_name='$group' AND field_id='$key'  ORDER BY seq");
            $setlayout1 = sqlFetchArray($getlayout_fields1);
            $result1[$setlayout1['seq']]=$setlayout1['field_id'];
            $minvalue[] = $setlayout1['seq'];
         }
    }
}
$minValue = min($minvalue);
static_patient_data($group, $result1, $patient_id, $minValue, $rowChartOutputTrans);
ksort($result1);$j = 1;
$groupName = $group;
$geteid = sqlStatement("SELECT fe.encounter,DATE_FORMAT(fe.date, '%Y-%m-%d') as date,openemr_postcalendar_categories.pc_catname FROM form_encounter AS fe left join openemr_postcalendar_categories on fe.pc_catid=openemr_postcalendar_categories.pc_catid  WHERE fe.pid = ".$patient_id." AND  DATE( date ) IN (".$date_of_service2.") order by fe.date desc");
while ($eidval = sqlFetchArray($geteid)){ 
    $i = 1;
    $tempvalue  = 1;
    echo "<div class='page' style='clear:both;'>";
    foreach($result1 as $key => $value) {
        $display_title = '';
        $get_display_field_label = sqlStatement("SELECT title FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND field_id = '".$value."'");
        $set_display_field_label = sqlFetchArray($get_display_field_label);
        if(!empty($set_display_field_label))
            $display_title = $set_display_field_label['title'];
        if($value=='mobile_allergy' || $value=='homehealth_allergy' || $value=='payeraudit_allergy' || $value=='referral_allergy' || $value=='appeal_allergy'){
            if($rowChartOutputTrans[$value] == "YES") {
                echo "<div id='show_div_allergy".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Allergies'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr= sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1)=='Allergies' ) {
                    $idName = str_replace(" ","-",$groupName."-".'Allergies');
                    if($setpagebr_allr['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){ 
                        display_div_function($display_title,$eidval['encounter'],$patient_id);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }  
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_allergy($eidval['encounter'],$setpagebr_allr['layout_type'],$groupName,$patient_id);
                echo "<ul/></div></div>";
                $i++;
                echo "</div>";
            }
        }
        if($value=='mobile_surgery' || $value=='homehealth_surgery' || $value=='payeraudit_surgery' || $value=='referral_surgery' || $value=='appeal_surgery'){
            if($rowChartOutputTrans[$value] == "YES") {
                echo "<div id='show_div_surgery".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';    
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr_allr = sqlStatement("SELECT group_name, layout_type, page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Surgeries'");
                $setpagebr_allr = sqlFetchArray($getpagebr_allr); 
                if(substr($setpagebr_allr['group_name'], 1) == 'Surgeries') {
                    $idName = str_replace(" ","-",$groupName."-".'Surgeries');
                    if($setpagebr_allr['page_break'] == 'YES' || $i == 1 || $tempvalue == 1) {
                        display_div_function($display_title,$eidval['encounter'],$patient_id);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }  
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_surgery($eidval['encounter'],$setpagebr_allr['layout_type'],$patient_id);
                echo "<ul/></div></div>";
                $i++;
                echo "</div>";
            }   
        }    
        if($value=='mobile_dental' || $value=='homehealth_dental' || $value=='payeraudit_dental' || $value=='referral_dental' || $value=='appeal_dental'){
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_dental".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Dental Problems'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Dental Problems') {
                    $idName = str_replace(" ","-",$groupName."-".'Dental Problems');
                    if($setpagebr_allr['page_break'] == 'YES'|| $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }  
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_dental($eidval['encounter'],$setpagebr_allr['layout_type'],$patient_id);
                echo "<ul/></div></div>";
                $i++;
                echo "</div>";
            }
        }
        if($value=='mobile_mproblem' || $value=='homehealth_mproblem' || $value=='payeraudit_mproblem' || $value=='referral_mproblem' || $value=='appeal_mproblem'){
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_mproblem".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT id,group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Medical Problems'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Medical Problems') {
                    $idName = str_replace(" ","-",trim($groupName)."-".'Medical Problems');
                    if($setpagebr_allr['page_break'] == 'YES'|| $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }  
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_mproblem($eidval['encounter'],$patient_id,$groupName,$setpagebr_allr['layout_type']);
                echo "<ul/></div></div>";
                $i++;
                echo "</div>";
            }
        }
        if($value=='mobile_med' || $value=='homehealth_med' || $value=='payeraudit_med' || $value=='referral_med' || $value=='appeal_med'){
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_med".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Medication'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Medication') {
                    $idName = str_replace(" ","-",$groupName."-".'Medication');
                    if($setpagebr_allr['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }  
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_medications($eidval['encounter'],$patient_id,$groupName,$setpagebr_allr['layout_type']);
                echo "<ul/></div></div>";
                $i++;
                echo "</div>";
            }
        }  
        if($value=='mobile_dme' || $value=='homehealth_dme' || $value=='payeraudit_dme' || $value=='referral_dme' || $value=='appeal_dme'){
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_dme".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%DME'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'DME') {
                    $idName = str_replace(" ","-",$groupName."-".'DME');
                     if($setpagebr_allr['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }  
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_dme($eidval['encounter'],$setpagebr_allr['layout_type'],$patient_id);
                echo "<ul/></div></div>";
                $i++;
                echo "</div>";
            }
        }
        if ($value=='mobile_vitals'|| $value=='homehealth_vitals'|| $value=='payeraudit_vitals' || $value=='referral_vitals'|| $value=='appeal_vitals'){
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_vitals".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Vitals'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Vitals') {
                    $idName = str_replace(" ","-",$groupName."-".'Vitals');
                    if($setpagebr_allr['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }  
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_vitals($eidval['encounter'],$setpagebr_allr['layout_type'],$patient_id);
                echo "<ul/></div></div>";
                $i++;
                echo "</div>";
            }
        }
        if ($value=='mobile_ros' || $value=='homehealth_ros'|| $value=='payeraudit_ros'|| $value=='referral_ros'|| $value=='appeal_ros'){
            if($rowChartOutputTrans[$value] == "YES" ){
                echo "<div id='show_div_ros".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr4 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Review Of Systems'");
                $setpagebr4 = sqlFetchArray($getpagebr4);
                if(substr($setpagebr4['group_name'],1)=='Review Of Systems'){
                    $idName = str_replace(" ","-",$groupName."-".'Review Of Systems');
                    if($setpagebr4['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }               
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_ros($eidval['encounter'],$groupName,$patient_id);
                    echo "<ul/></div></div>";
                    $i++;
                } 
                echo "</div>";
            }
        }
        if ($value=='mobile_physical_exam'  || $value=='homehealth_physical_' || $value=='payeraudit_physical_' || $value=='referral_physical_ex' || $value=='appeal_physical_exam'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_physical_exam".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Physical Exam'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='Physical Exam'){
                    $idName = str_replace(" ","-",$groupName."-".'Physical Exam');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }               
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_physical_exam($eidval['encounter'],$groupName,$patient_id);
                    echo "<ul/></div></div>";
                    $i++;
                } 
                echo "</div>";
            }
        } 
        if ($value=='mobile_cc' || $value=='homehealth_cc' || $value=='payeraudit_cc' || $value=='referral_cc' || $value=='appeal_cc'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_cc".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Chief Complaint'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='Chief Complaint'){
                    $idName = str_replace(" ","-",$groupName."-".'Chief Complaint');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }                
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_cc($eidval['encounter'],$groupName);
                    echo "<ul/></div></div>";
                    
                    $i++;
                } 
                echo "</div>";
            }    
        }
        if ($value=='mobile_hpi' || $value=='homehealth_hpi' || $value=='payeraudit_hpi' || $value=='referral_hpi' || $value=='appeal_hpi'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_hpi".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%History of Present illness'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='History of Present illness'){
                    $idName = str_replace(" ","-",$groupName."-".'History of Present illness');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }               
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_hpi($eidval['encounter'],$patient_id,$groupName);
                    echo "<ul/></div></div>";
                    $i++;
                } 
                echo "</div>";
            }  
        }
        if($value=='mobile_assess' || $value=='homehealth_assess' || $value=='payeraudit_assess' || $value=='referral_assess' || $value=='appeal_assess'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_assess".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Assessment Note'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='Assessment Note'){
                    $idName = str_replace(" ","-",$groupName."-".'Assessment Note');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }              
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_assess($eidval['encounter'],$groupName);
                    echo "<ul/></div></div>";
                    $i++;
                } 
                echo "</div>";
            } 
        }
        if($value=='mobile_plan' || $value=='homehealth_plan' || $value=='payeraudit_plan' || $value=='referral_plan' || $value=='appeal_plan'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_plan".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Plan Note'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='Plan Note'){
                    $idName = str_replace(" ","-",$groupName."-".'Plan Note');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }               
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_plan($eidval['encounter'],$groupName);
                    echo "<ul/></div></div>";
                    $i++;
                } 
                echo "</div>";
            } 
        }
        if ($value=='mobile_progress' || $value=='homehealth_progress' || $value=='payeraudit_progress' || $value=='referral_progress' || $value=='appeal_progress'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_progress".$eidval['encounter']."' style='display:none'>";
                echo "<div style='clear:both;'>";
                $setpagebr5 = '';
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Progress Note'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='Progress Note' ){
                    $idName = str_replace(" ","-",$groupName."-".'Progress Note');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }               
                    echo " <div id='".$idName.$j."'  style='clear:both;  '>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_progress($eidval['encounter'],$groupName);
                    echo "<ul/></div></div>";
                    $i++;
                } 
                echo "</div>";
            }  
        }
        if ($value=='mobile_cert_recert' || $value=='homehealth_cert_rece' || $value=='payeraudit_cert_rece' || $value=='referral_cert_recert' || $value=='appeal_cert_recert'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_cert_recert".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Certification_Recertification'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='Certification_Recertification'){
                    $idName = str_replace(" ","-",$groupName."-".'Certification_Recertification');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'] );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }                
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_cert_recert($eidval['encounter'],$groupName);
                    echo "<ul/></div></div>";
                    
                    $i++;
                } 
                echo "</div>";
            }    
        }
        if($value=='mobile_cpo' || $value=='homehealth_cpo' || $value=='payeraudit_cpo' || $value=='referral_cpo' || $value=='appeal_cpo'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_cpo".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%CPO'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='CPO'){
                    $idName = str_replace(" ","-",$groupName."-".'CPO');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }               
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_cpo($eidval['encounter'],$groupName,$patient_id);
                    echo "<ul/></div></div>";
                    $i++;
                } 
                echo "</div>";
            }    
        }
        if($value=='mobile_audit' || $value=='homehealth_audit' || $value=='payeraudit_audit' || $value=='referral_audit' || $value=='appeal_audit'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_audit".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%CPO'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='CPO'){
                    $idName = str_replace(" ","-",$groupName."-".'CPO');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }               
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_audit($eidval['encounter'],$groupName);
                    echo "<ul/></div></div>";
                    $i++;
                } 
                echo "</div>";
            }  
        }
        if($value=='mobile_f2f' || $value=='homehealth_f2f' || $value=='payeraudit_f2f' || $value=='referral_f2f' || $value=='appeal_f2f'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_f2f".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Face to Face HH Plan'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='Face to Face HH Plan'){
                    $idName = str_replace(" ","-",$groupName."-".'Face to Face HH Plan');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                       display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }                
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_f2f($eidval['encounter'],$groupName);
                    echo "<ul/></div></div>";
                    $i++;
                } 
                echo "</div>";
            }  
        }
        if($value=='mobile_procedure' || $value=='homehealth_procedure'|| $value=='payeraudit_procedure'|| $value=='referral_procedure' || $value=='appeal_procedure'){
            if($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_procedure".$eidval['encounter']."' style='display:none'>";
                $setpagebr5 = '';
                echo "<div style='clear:both;'>";
                $getpagebr5 = sqlStatement("SELECT DISTINCT(group_name) as group_name,layout_type, page_break  FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Procedure'");
                $setpagebr5= sqlFetchArray($getpagebr5);
                if(substr($setpagebr5['group_name'],1)=='Procedure'){
                    $idName = str_replace(" ","-",$groupName."-".'Procedure');
                    if($setpagebr5['page_break'] == 'YES' || $i == 1 || $tempvalue == 1){
                        display_div_function($display_title,$eidval['encounter'],$patient_id );
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }               
                    echo " <div id='".$idName.$j."'  style='clear:both;'>";   
                    echo "<ul class='".$idName.$j."' >";
                    display_procedure($eidval['encounter'],$setpagebr5['layout_type'],$patient_id);
                    echo "<ul/></div></div>"; 
                    $i++;
                } 
                echo "</div>";
            }  
        }
    }
    $j++;
    echo "</div>";
}   
?>

</div>
<footer style='clear:both;' align="right">
  <p><?php
  $date = new DateTime('now', new DateTimeZone('America/New_York'));
  echo $date->format('d-M-Y H:i:s a').".";
  ?></p>
</footer>
</body></html>
<?php
$logo="../../../images/tphc.jpg";
$s = str_replace("{logo}", $logo, $s);

foreach ($TEMPLATE_LABELS as $key => $value) {
  $s = str_replace("{".$key."}", $value, $s);
}

echo $s;
function patientdata_not_related_to_encounters($rowChartOutputTrans,$result1,$minValue,$groupName,$patient_id){
    $j = 0;
    foreach($result1 as $key => $value){
        $display_title = '';
        $get_display_field_label = sqlStatement("SELECT title FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND field_id = '".$value."'");
        $set_display_field_label = sqlFetchArray($get_display_field_label);
        if(!empty($set_display_field_label))
            $display_title = $set_display_field_label['title'];
        if($value=='mobile_allergy' || $value=='homehealth_allergy' || $value=='payeraudit_allergy' || $value=='referral_allergy' || $value=='appeal_allergy'){
            if($rowChartOutputTrans[$value] == "YES") {
                echo "<div id='show_div_allergy0' style='display:none'> ";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Allergies'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr= sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1)=='Allergies' ) {
                    $idName = str_replace(" ","-",$groupName."-".'Allergies');
                    if($setpagebr_allr['page_break'] == 'YES'){ 
                         display_div_function($display_title,0,$patient_id);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_allergy(0,$setpagebr_allr['layout_type'],$groupName,$patient_id);
                echo "<ul/></div></div></div>";
            }
        }
        if($value=='mobile_surgery' || $value=='homehealth_surgery' || $value=='payeraudit_surgery' || $value=='referral_surgery' || $value=='appeal_surgery'){
            if($rowChartOutputTrans[$value] == "YES") {
                echo "<div id='show_div_surgery0' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr_allr = sqlStatement("SELECT group_name, layout_type, page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Surgeries'");
                $setpagebr_allr = sqlFetchArray($getpagebr_allr); 
                if(substr($setpagebr_allr['group_name'], 1) == 'Surgeries') {
                    $idName = str_replace(" ","-",$groupName."-".'Surgeries');
                     if($setpagebr_allr['page_break'] == 'YES'){
                         display_div_function($display_title,0,$patient_id);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_surgery(0,$setpagebr_allr['layout_type'],$patient_id);
                echo "<ul/></div></div></div>";
            }
        }
        if($value=='mobile_dental' || $value=='homehealth_dental' || $value=='payeraudit_dental' || $value=='referral_dental' || $value=='appeal_dental'){
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_dental0' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Dental Problems'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Dental Problems') {
                    $idName = str_replace(" ","-",$groupName."-".'Dental Problems');
                    if($setpagebr_allr['page_break'] == 'YES'){
                         display_div_function($display_title,0,$patient_id);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_dental(0,$setpagebr_allr['layout_type'],$patient_id);
                echo "<ul/></div></div></div>";
            }
         }
        if($value=='mobile_mproblem' || $value=='homehealth_mproblem' || $value=='payeraudit_mproblem' || $value=='referral_mproblem' || $value=='appeal_mproblem'){
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_mproblem0' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT id,group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Medical Problems'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Medical Problems') {
                    $idName = str_replace(" ","-",$groupName."-".'Medical Problems');
                     if($setpagebr_allr['page_break'] == 'YES'){
                         display_div_function($display_title,0,$patient_id);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_mproblem(0,$patient_id,$groupName,$setpagebr_allr['layout_type']);
                echo "<ul/></div></div></div>";
            }
        }
        if($value=='mobile_dme' || $value=='homehealth_dme' || $value=='payeraudit_dme' || $value=='referral_dme' || $value=='appeal_dme'){
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_dme0'  style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%DME'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'DME') {
                    $idName = str_replace(" ","-",$groupName."-".'DME');
                     if($setpagebr_allr['page_break'] == 'YES'){
                         display_div_function($display_title,0,$patient_id);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_dme(0,$setpagebr_allr['layout_type'],$patient_id);
                echo "<ul/></div></div></div>";
            }
        }   
        if($value=='mobile_med' || $value=='homehealth_med' || $value=='payeraudit_med' || $value=='referral_med' || $value=='appeal_med'){
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_med0' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Medication'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Medication') {
                    $idName = str_replace(" ","-",$groupName."-".'Medication');
                    if($setpagebr_allr['page_break'] == 'YES'){
                         display_div_function($display_title,0,$patient_id);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_medications(0,$patient_id,$groupName,$setpagebr_allr['layout_type']);
                echo "<ul/></div></div></div>";
            }
        }  
        if ($value=='mobile_prescript' || $value=='homehealth_prescript' || $value=='payeraudit_prescript'|| $value=='referral_prescript' || $value=='appeal_prescript'){
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_prescript0' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Prescription'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Prescription') {
                    $idName = str_replace(" ","-",$groupName."-".'Prescription');
                    if($setpagebr_allr['page_break'] == 'YES'){
                       display_div_function($display_title,0,$patient_id);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_prescription($setpagebr_allr['layout_type'],$patient_id);
                echo "<ul/></div></div></div>";
            }
        }
        if($value=='mobile_immunizations' || $value=='homehealth_immunizations' || $value=='payeraudit_immunizations' || $value=='referral_immunizations' || $value=='appeal_immunizations'){
            if ($rowChartOutputTrans[$value] == "YES" ){
                echo "<div id='show_div_immunizations'>";
                $setpagebr_allr = '';
                if (acl_check('patients', 'med')) {
                    $getpagebr = "SELECT group_name, layout_type, page_break FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Immunization'";
                    $getpage = sqlStatement($getpagebr);
                    $setpagebr9= sqlFetchArray($getpage);
                    if(substr($setpagebr9['group_name'], 1)=='Immunization') {
                        $idName = $groupName."-".'Immunization';
                        $idName = str_replace(" ","-",$idName);
                        echo "<div style='clear:both;'>"; 
                        if($setpagebr9['group_name'] == 'YES'){
                            display_div_function($display_title,0,$patient_id);
                        }else{
                             echo '<h2>'.$display_title.':</h2>';
                        }    
                    }
                    if($rowChartOutputTrans[$value] == "YES" ){
                        echo "<div id='".$idName."' style='clear:both;'>";
                        echo "<ul class='".$idName.$j."' >";
                        $sql = "select  c.code_text_short as Vaccine,i1.administered_date AS Date,CONCAT(i1.amount_administered ,' ',i1.amount_administered_unit	)as Amount ,i1.manufacturer as Manufacturer, i1.lot_number as Lot_Number,i1.administered_by_id as AdministeredBy ,i1.education_date as Education_Date,i1.Route ,i1.administration_site as Administration_Site,substring(i1.note,1,20) as immunization_note
                               from immunizations i1 
                               left join code_types ct on ct.ct_key = 'CVX' 
                               left join codes c on c.code_type = ct.ct_id AND i1.cvx_code = c.code 
                               where i1.patient_id ='".$patient_id."'  and i1.added_erroneously = 0 
                               order by administered_date desc";
                        $result = sqlStatement($sql);
                        if(sqlNumRows($result)==0){
                            ?>
                            <script>
                                document.getElementById('show_div_immunizations').style.display = "none";
                            </script> 
                            <?php
                        }else{
                            while ($row=sqlFetchArray($result)) {
                                $immun=$row;   
                            
                                foreach($immun as $key1 => $value1){
                                    if($key1=='AdministeredBy'){
                                        $getporvidername = sqlStatement("SELECT CONCAT(lname,' ',fname) AS name FROM users WHERE id='$value1'" );
                                        $rowName=sqlFetchArray($getporvidername);
                                        $admin1=$rowName['name'];    
                                        echo "<li><b>".$key1.": "."</b>".$admin1."</li>";
                                    }else if($key1=='Amount'){
                                        $string = $value1;
                                        $string = explode(' ', $string);
                                        $getunits = sqlStatement("SELECT title FROM list_options WHERE option_id='$string[1]' AND list_id='drug_units'" );
                                        $rowName=sqlFetchArray($getunits);
                                        $units=$rowName['title'];  
                                        echo "<li><b>".ucwords(str_replace('_',' ',$key1)).": "."</b>". $string[0].' '.$units."</li>";
                                    }else {
                                        echo "<li><b>".ucwords(str_replace('_',' ',$key1)).": "."</b>".$value1."</li>";
                                    }
                                }
                                echo "<li>&nbsp;</li>";
                            }
                        }
                        echo "<ul/></div></div>\n";
                    }
                } 
                echo "</div>";
            }
        }
    }  
}
function static_patient_data($groupName,$result1, $pid,$minValue,$rowChartOutputTrans){
    $result_array = $result1;
    echo "<div class='text dem' id='DEM'>\n";
    $getcred1="SELECT provider_credentials AS pcred FROM tbl_patientuser WHERE userid='$provider' AND provider_credentials!=''"; 
    $resCred1=sqlStatement($getcred1);
    $rowCred1=sqlFetchArray($resCred1);
    $credential= $rowCred1['pcred'];
    $getpagebr = sqlStatement("SELECT DISTINCT(group_name) FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name LIKE '%Who' ");
    $setpagebr= sqlFetchArray($getpagebr);
    display_div_function("Patient Information",0,$pid);
    display_demographics($pid, $groupName);
    echo "</div>\n";   
    foreach($result1 as $key => $value) { 
        $display_title = '';
        $get_display_field_label = sqlStatement("SELECT title FROM layout_options WHERE form_id = 'CHARTOUTPUT' AND field_id = '".$value."'");
        $set_display_field_label = sqlFetchArray($get_display_field_label);
        if(!empty($set_display_field_label))
            $display_title = $set_display_field_label['title'];
        if($value=='mobile_history' || $value=='homehealth_history' || $value=='payeraudit_history' || $value=='referral_history' || $value=='appeal_history'){
            if ($rowChartOutputTrans[$value] == "YES"){
                display_history($pid,$groupName);
            }
        }
        if($value=='mobile_insurance' || $value=='homehealth_insurance' || $value=='payeraudit_insurance' || $value=='referral_insurance' || $value=='appeal_insurance'){  
            if ($rowChartOutputTrans[$value] == "YES"){
                echo "<div id='show_div_insurance'>";
                if($key!=$minValue){
                    echo "<div class='text insurance' style='clear:both;'>";
                    echo '<br><h2>'.$display_title.':</h2><br>';
                    display_insurance($pid);
                    echo "</div>"; 
                }else { 
                    $getpagebr = sqlStatement("SELECT DISTINCT(group_name) FROM tbl_chartui_mapping WHERE screen_name = '$groupName' AND group_name = '1Who'");
                    $setpagebr= sqlFetchArray($getpagebr);
                    display_div_function($display_title,0,$pid);
                    display_insurance($pid);
                }
                echo "</div>";
            }
        }
    }
    patientdata_not_related_to_encounters($rowChartOutputTrans,$result_array,$minValue,$groupName,$pid);
}
function display_div_function($gname,$encounter,$patient_id){
    if($gname == 'Patient Information')
        echo "<div style='clear:both'>";
    else
        echo "<div class='page' style='clear:both'>";
    
    $getPatientName = sqlStatement("SELECT CONCAT(fname,' ',lname) AS pname ,pid,DATE_FORMAT(DOB,'%m-%d-%Y') as DOB ,ss, providerID, street,city,state,country_code,postal_code FROM patient_data WHERE pid=".$patient_id."");
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
//    else{
//        $getporvider= sqlStatement("SELECT CONCAT( fname,  ' ', lname ) AS name,f.name as faclityname, f.street, f.city, f.state, f.postal_code, f.country_code,f.website,f.email,f.phone,f.fax
//                                        FROM users u
//                                        INNER JOIN facility f ON u.facility_id = f.id
//                                        WHERE u.id =  '$provider'" );
//        $setprovider        = sqlFetchArray($getporvider);
//        $facilityname       = $setprovider['faclityname'];
//        if(!empty($setprovider['website']))
//            $website = $setprovider['website'].", ";
//        if(!empty($setprovider['phone']))
//            $phone = "Phone:".$setprovider['phone'].", ";
//        if(!empty($setprovider['fax']))
//            $fax = "Fax:".$setprovider['fax'].", ";
//        if(!empty($setprovider['fax']))
//           $email =  "Email:".$setprovider['email'];
//        //$provider_name  = $setprovider['name'];
//        $location       = str_replace(',,',' ',$setprovider['street'].", ".$setprovider['city'].", ".$setprovider['state'].", ".$setprovider['country_code'].", ".$setprovider['postal_code']);
//        $contact        = str_replace(',,', ' ', $website.$phone.$fax.$email);
//    }
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
function getListItem($listid, $value) {
  $lrow = sqlQuery("SELECT title FROM list_options " .
    "WHERE list_id = ? AND option_id = ?",
    array($listid, $value));
  $tmp = xl_list_label($lrow['title']);
  if (empty($tmp)) $tmp = (($value === '') ? '' : "($value)");
  return $tmp;
}

function myCellText($s) {
  if ($s === '') return '&nbsp;';
  return text($s);
}

// Check if the given string already exists in the $aNotes array.
// If not, stores it as a new entry.
// Either way, returns the corresponding key which is a small integer.
function storeNote($s) {
  global $aNotes;
  $key = array_search($s, $aNotes);
  if ($key !== FALSE) return $key;
  $key = count($aNotes);
  $aNotes[$key] = $s;
  return $key;
}
?>
