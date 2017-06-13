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

include_once('../../../interface/globals.php'); 



function display_div_function($gname,$encounter){
        if($gname == 'Patient Information')
            echo "<div style='clear:both'>";
        else
            echo "<div class='page' style='clear:both'>"; ?>
       <center><h3 style="margin:0px !important">Texas Physician House Calls</h3><hr style="border-width: 3px ! important ; margin:0px !important">
                    <table>
                        <tr><td align="center">Sumana Ketha M.D.</td></tr>
                        <tr><td align="center"><i>Board Certified in internal Medicine</i></td></tr>
                        <tr><td align="center">2925 Skyway circle North,</td></tr>
                        <tr><td align="center">Irving, Texas 75038</td></tr>
                        <tr><td align="center">HHSUPPORT@TEXASHOUSECALLS.COM</td></tr>
                    </table>
                </center>
                <h3><?php echo $gname; ?>:</h3>    
      <?php  echo "</div>";
    }
    
function patientdata_not_related_to_encounters($result1Value,$groupName,$pid){
    $j = 0;
    foreach($result1Value as $key => $value){
        $display_title = '';
        $get_display_field_label = sqlStatement("SELECT title FROM layout_options WHERE form_id = 'NONENC' AND field_id = '".substr($value, 5)."'");
        $set_display_field_label = sqlFetchArray($get_display_field_label);
        if(!empty($set_display_field_label))
            $display_title = $set_display_field_label['title'];
        if($value=='form_mobile_allergy' || $value=='form_homehealth_allergy' || $value=='form_payeraudit_allergy' || $value=='form_referral_allergy' || $value=='form_appeal_allergy'){
            if($_REQUEST[$value] == "YES") {
                echo "<div id='show_div_allergy' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name LIKE '%Allergies'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr= sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1)=='Allergies' ) {
                    $idName = str_replace(" ","-",trim($groupName)."-".'Allergies');
                    if($setpagebr_allr['page_break'] == 'YES'){ 
                        display_div_function($display_title,0);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_allergy($setpagebr_allr['layout_type'],$groupName,$pid);
                echo "<ul/></div></div></div>";
            }
        }
        if($value=='form_mobile_surgery' || $value=='form_homehealth_surgery' || $value=='form_payeraudit_surgery' || $value=='form_referral_surgery' || $value=='form_appeal_surgery'){
            if($_REQUEST[$value] == "YES") {
                echo "<div id='show_div_surgery' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr_allr = sqlStatement("SELECT group_name, layout_type, page_break FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name LIKE '%Surgeries'");
                $setpagebr_allr = sqlFetchArray($getpagebr_allr); 
                if(substr($setpagebr_allr['group_name'], 1) == 'Surgeries') {
                    $idName = str_replace(" ","-",trim($groupName)."-".'Surgeries');
                     if($setpagebr_allr['page_break'] == 'YES'){
                        display_div_function($display_title,0);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_surgery($setpagebr_allr['layout_type'],$pid);
                echo "<ul/></div></div></div>";
            }
        }
        if($value=='form_mobile_dental' || $value=='form_homehealth_dental' || $value=='form_payeraudit_dental' || $value=='form_referral_dental' || $value=='form_appeal_dental'){
            if ($_REQUEST[$value] == "YES"){
                echo "<div id='show_div_dental' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name LIKE '%Dental Problems'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Dental Problems') {
                    $idName = str_replace(" ","-",trim($groupName)."-".'Dental Problems');
                    if($setpagebr_allr['page_break'] == 'YES'){
                        display_div_function($display_title,0);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_dental($setpagebr_allr['layout_type'],$pid);
                echo "<ul/></div></div></div>";
            }
         }
        if($value=='form_mobile_mproblem' || $value=='form_homehealth_mproblem' || $value=='form_payeraudit_mproblem' || $value=='form_referral_mproblem' || $value=='form_appeal_mproblem'){
            if ($_REQUEST[$value] == "YES"){
                echo "<div id='show_div_mproblem' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT id,group_name, layout_type,page_break FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name LIKE '%Medical Problems'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Medical Problems') {
                    $idName = str_replace(" ","-",trim($groupName)."-".'Medical Problems');
                    if($setpagebr_allr['page_break'] == 'YES'){
                        display_div_function($display_title,0);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_mproblem($pid,$groupName,$setpagebr_allr['layout_type']);
                echo "<ul/></div></div></div>";
            }
        }
        if($value=='form_mobile_dme' || $value=='form_homehealth_dme' || $value=='form_payeraudit_dme' || $value=='form_referral_dme' || $value=='form_appeal_dme'){
            if ($_REQUEST[$value] == "YES"){
                echo "<div id='show_div_dme' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name LIKE '%DME'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'DME') {
                    $idName = str_replace(" ","-",trim($groupName)."-".'DME');
                    if($setpagebr_allr['page_break'] == 'YES'){
                        display_div_function($display_title,0);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_dme($setpagebr_allr['layout_type'],$pid);
                echo "<ul/></div></div></div>";
            }
        }   
        if($value=='form_mobile_med' || $value=='form_homehealth_med' || $value=='form_payeraudit_med' || $value=='form_referral_med' || $value=='form_appeal_med'){
            if ($_REQUEST[$value] == "YES"){
                echo "<div id='show_div_med' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name LIKE '%Medication'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Medication') {
                    $idName = str_replace(" ","-",trim($groupName)."-".'Medication');
                    if($setpagebr_allr['page_break'] == 'YES'){
                        display_div_function($display_title,0);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_medications(0,$_SESSION['pid'],$groupName,$setpagebr_allr['layout_type']);
                echo "<ul/></div></div></div>";
            }
        }  
        if ($value=='form_mobile_prescript' || $value=='form_homehealth_prescript' || $value=='form_payeraudit_prescript' || $value=='form_referral_prescript' || $value=='form_appeal_prescript'){
            if ($_REQUEST[$value] == "YES"){
                echo "<div id='show_div_prescript' style='display:none'>";
                $setpagebr_allr = '';
                echo "<div style='clear:both;'>";
                $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name LIKE '%Prescription'";
                $getpagebr_allr = sqlStatement($getpagebr);
                $setpagebr_allr = sqlFetchArray($getpagebr_allr);
                if(substr($setpagebr_allr['group_name'], 1) == 'Prescription') {
                    $idName = str_replace(" ","-",trim($groupName)."-".'Prescription');
                    if($setpagebr_allr['page_break'] == 'YES'){
                       display_div_function($display_title,0);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }
                }
                echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
                echo "<ul class='1".$idName.$j."' >"; 
                display_prescription($setpagebr_allr['layout_type'],$pid);
                echo "<ul/></div></div></div>";
            }
        }
        if($value=='form_mobile_immunizations' || $value=='form_homehealth_immunizations' || $value=='form_payeraudit_immunizations' || $value=='form_referral_immunizations' || $value=='form_appeal_immunizations'){
            if ($_REQUEST[$value] == "YES" ){
                echo "<div id='show_div_immunizations' >";
                $setpagebr_allr = '';
                
                    $getpagebr = "SELECT group_name, layout_type, page_break FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name LIKE '%Immunization'";
                    $getpage = sqlStatement($getpagebr);
                    $setpagebr9= sqlFetchArray($getpage);
                    if(substr($setpagebr9['group_name'], 1)=='Immunization') {
                        $idName = $groupName."-".'Immunization';
                        $idName = str_replace(" ","-",$idName);
                        echo "<div style='clear:both;'>"; 
                        if($setpagebr9['group_name'] == 'YES'){
                            display_div_function($display_title,0);
                    }else{
                        echo "<h2> $display_title: </h2>";
                    }  
                    }
                    if($_REQUEST[$value] == "YES" ){
                        echo "<div id='".$idName."' style='clear:both;'>";
                        echo "<ul class='".$idName.$j."' >";
                        $sql = "select  c.code_text_short as Vaccine,i1.administered_date AS Date,CONCAT(i1.amount_administered ,' ',i1.amount_administered_unit	)as Amount ,i1.manufacturer as Manufacturer, i1.lot_number as Lot_Number,i1.administered_by_id as AdministeredBy ,i1.education_date as Education_Date,i1.Route as Route ,i1.administration_site as Administration_Site,substring(i1.note,1,20) as immunization_note
                               from immunizations i1 
                               left join code_types ct on ct.ct_key = 'CVX' 
                               left join codes c on c.code_type = ct.ct_id AND i1.cvx_code = c.code 
                               where i1.patient_id ='".$_SESSION['pid']."'  and i1.added_erroneously = 0 
                               order by administered_date desc";
                        $result = sqlStatement($sql);
                        if(sqlNumRows($result)==0){
                           // echo "<li><label> There are no recorded immunizations for this patient at this time.</label></li>";
                            ?>
                             <script>
                                document.getElementById('show_div_immunizations').style.display = "none";
                            </script> 
                            <?php    
                        }else{
                            while ($row=sqlFetchArray($result)) {
                                $immun=$row;   
                                
                                foreach($immun as $key1 => $value1){
                                    if(!empty($value1)){
                                        if($key1=='AdministeredBy'){
                                            $getporvidername = sqlStatement("SELECT CONCAT(lname,' ',fname) AS name FROM users WHERE id='$value1'" );
                                            $rowName=sqlFetchArray($getporvidername);
                                            $admin1=$rowName['name'];    
                                            echo "<li><b>".$key1.": "."</b>".$admin1."</li>";
                                        }else if($key1=='Amount'){
                                            $string = $value1;
                                            $string = explode(' ', $string);
                                            $getunits = sqlStatement("SELECT title FROM list_options WHERE option_id='$string[1]' AND list_id='drug_units'" );
                                            $rowName = sqlFetchArray($getunits);
                                            $units=$rowName['title'];  
                                            echo "<li><b>".ucwords(str_replace('_',' ',$key1)).": "."</b>". $string[0].' '.$units."</li>";
                                        }else if($key1=='Route'){
                                            $getunits = sqlStatement("SELECT title FROM list_options WHERE list_id='drug_route' AND option_id = '$value1'" );
                                            $rowName = sqlFetchArray($getunits);
                                            $units=$rowName['title'];  
                                            echo "<li><b>".ucwords(str_replace('_',' ',$key1)).":</b>".$units."</li>";
                                        }else {
                                            echo "<li><b>".ucwords(str_replace('_',' ',$key1)).": "."</b>".$value1."</li>";
                                        }
                                    }
                                }
                                echo "<li>&nbsp;</li>";
                            }
                        }
                        echo "<ul/></div></div>\n";
                    }
                
                echo "</div>";
            }
        }
    }  
}    

function static_patient_data($groupName,$result1, $pid,$minValue){ 
    $result_array = $result1; 
    echo "<div class='text dem' id='DEM'>\n";
    $getpagebr = sqlStatement("SELECT DISTINCT(group_name) FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name LIKE '%Who' ");
    $setpagebr= sqlFetchArray($getpagebr);
    display_div_function("Patient Information",0);
    display_demographics($pid, $groupName);
    echo "</div>\n";   
    foreach($result1 as $key => $value) { 
        $display_title = '';
        $get_display_field_label = sqlStatement("SELECT title FROM layout_options WHERE form_id = 'NONENC' AND field_id = '".substr($value, 5)."'");
        $set_display_field_label = sqlFetchArray($get_display_field_label);
        if(!empty($set_display_field_label))
            $display_title = $set_display_field_label['title'];
        if($value=='form_mobile_history' || $value=='form_homehealth_history' || $value=='form_payeraudit_history' || $value=='form_referral_history' || $value=='form_appeal_history'){
            if ($_REQUEST[$value] == "YES"){
                display_history($pid,$groupName);
            }
        }
        if($value=='form_mobile_insurance' || $value=='form_homehealth_insurance' || $value=='form_payeraudit_insurance' || $value=='form_referral_insurance' || $value=='form_appeal_insurance'){  
            if ($_REQUEST[$value] == "YES"){
                echo "<div id='show_div_insurance'>";
                if($key!=$minValue){
                    echo "<div class='text insurance' style='clear:both;'>";
                    echo '<br><h2>'.$display_title.':</h2><br>';
                    display_insurance($pid);
                    echo "</div>"; 
                }else { 
                    $getpagebr = sqlStatement("SELECT DISTINCT(group_name) FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name = '1Who'");
                    $setpagebr= sqlFetchArray($getpagebr);
                    display_div_function($display_title,0);
                    display_insurance($pid);
                }
                echo "</div>";
            }
        }
    }
    patientdata_not_related_to_encounters($result_array,$groupName,$pid);
}

//demographics
function display_demographics($pid, $groupName){
    $getgroupnames = sqlStatement("SELECT DISTINCT(group_name) as group_name from layout_options where form_id='DEM' and uor <> 0 order by group_name");
    while($setgroupnames=sqlFetchArray($getgroupnames)){
        $gettitles =  sqlStatement("SELECT group_concat(field_id) as id, group_concat(title) as title from layout_options where form_id='DEM' and uor <> 0 AND group_name='".$setgroupnames['group_name']."'"  );
        $idName = trim($groupName)."-".trim(substr($setgroupnames['group_name'],1));
        $idName = str_replace(" ","-",$idName);
        echo " <div id='".$idName."' style='clear:both;'><ul class='".$idName."' >";
        while($settitles=sqlFetchArray($gettitles)){
            $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name = '".$setgroupnames['group_name']."' AND option_value = 'YES'");
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
                            $getvalname = sqlStatement("SELECT title FROM list_options WHERE option_id =  '".addslashes($explodeval[$i])."' AND list_id = '$listname'");
                            $setvalname2=sqlFetchArray($getvalname);
                            $getlayoutval = sqlStatement("SELECT layout_col,group_name FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name = '".$setgroupnames['group_name']."'");
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

//history
function display_history($pid, $groupName){
    $getgroupval3 = sqlStatement("SELECT date as Date,date FROM history_data  WHERE pid = $pid order by date desc limit 1");
    $getgroupval3=sqlFetchArray($getgroupval3);
    $getgroupnames = sqlStatement("SELECT DISTINCT(group_name) as group_name from layout_options where form_id='HIS' and uor <> 0 order by group_name");
    while($setgroupnames=sqlFetchArray($getgroupnames)){
        $setpagebr_allr = '';
        echo "<div id='".$setgroupnames['group_name']."' style='clear:both;'>";
        $getpagebr = "SELECT group_name, layout_type,page_break FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name LIKE '".$setgroupnames['group_name']."'";
        $getpagebr_allr = sqlStatement($getpagebr);
        $setpagebr_allr = sqlFetchArray($getpagebr_allr);      
        if($setpagebr_allr['group_name'] == $setgroupnames['group_name']) {
            $idName = str_replace(" ","-",trim($groupName)."-".$setpagebr_allr['group_name']);
            if($setpagebr_allr['page_break'] == 'YES'){
                display_div_function(substr($setpagebr_allr['group_name'], 1),0);
            }else{
                echo "<h2>". substr($setpagebr_allr['group_name'], 1).": </h2>";
            }
        }
        echo " <div id='1".$idName.$j."'  style='clear:both;'>"; 
        echo "<ul class='1".$idName.$j."' >"; 
        $gettitles =  sqlStatement("SELECT group_concat(field_id) as id, group_concat(title) as title from layout_options where form_id='HIS' and uor <> 0 AND group_name='".$setgroupnames['group_name']."'"  );
        while($settitles=sqlFetchArray($gettitles)){
            $datacheck = '';
            $getselectedvales = sqlStatement("SELECT field_id as selectedfield FROM tbl_medrecord_non_encounter WHERE screen_name = '$groupName' AND group_name = '".$setgroupnames['group_name']."' AND option_value = 'YES'");
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

//insurance
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

//medication
function display_medications($eid,$pid,$groupName,$layout_type){
    global $tempvalue;
    $getMedication = sqlStatement('SET SQL_BIG_SELECTS=1');
    $getMedication = sqlStatement("SET NAMES utf8");
    $getMedication = sqlStatement("SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'allergy'
                                    AND l.pid = $pid AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid = $pid ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC");
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
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                                }
                            }
                            echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
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
                        <th width='50%' class = 'tdborder'><b> Description </b></th> 
                        <th width='10%' class = 'tdborder'><b> Status </b></th> 
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
                    echo "<tr><td width='50%' class='firsttd'>";
                    if(!empty($pres_array[$i]['Title']))
                        echo $pres_array[$i]['Title']; 
                    else
                        echo "Not Specified. ";
                    echo "</td></tr>";
                    // multiple icd codes description
                    $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                    $icd_description_value = '';
                    for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                        if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                            $checkstring = "ICD9:";
                            $checkstring1 = 'ICD9';
                            $table = 'icd9_dx_code';
                        }
                        if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                            $checkstring = "ICD10:";
                            $table = 'icd10_dx_order_code';
                            $checkstring1 = 'ICD10';
                        }
                        $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                        $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                        $icd_description = sqlFetchArray($icd_description_sql);
                        if(!empty($icd_description)){
                            $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                        }
                    }
                    if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                        echo "<tr><td class='secondtd'>";
                        echo $icd_description_value;
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
                    echo "<td width='10%' class = 'tdborder'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Begin_date']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['End_date']."";
                    echo "</tr>";
            }
            echo "</table>";
        }
        $tempvalue = 0 ;
        ?>
        <script>
            document.getElementById('show_div_med').style.display = "block";
        </script> 
         <?php
        
    else:
        //echo "No Medications for this patient";
        //if($min == 1)
                //$tempvalue = 1 ;
    endif;
}

//allergy
function display_allergy($layout_type,$groupName,$pid){
    global $tempvalue;
    $allergysql = sqlStatement('SET SQL_BIG_SELECTS=1'); 
    $allergysql = sqlStatement("SET NAMES utf8");
    $allegrysqlquery .= " SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'allergy'
                                    AND l.pid =".$pid." AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =".$pid." ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
    
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
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                                }
                            }
                            echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
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
                        document.getElementById('show_div_allergy').style.display = "block";
                    </script>    
                    <?php
                    $tempvalue = 0 ;
                } 
            } 
        }else{
            echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr>
                    <th class = 'tdborder' width='50%'><b> Description </b></th> 
                    <th class = 'tdborder' width='10%'><b> Status </b></th> 
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
                echo "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    echo $pres_array[$i]['Title']; 
                else
                    echo "Not Specified. ";
                echo "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                    $icd_description = sqlFetchArray($icd_description_sql);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                    }
                }
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    echo "<tr><td class='secondtd'>";
                    echo $icd_description_value;
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
                    echo $pres_array[$i]['Begin_date']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    echo "</td></tr>";
                }
                
                if(!empty($pres_array[$i]['destination'])){
                    echo "<tr><td class='thirdtd'> ";
                    echo $pres_array[$i]['destination'];
                    echo "</td></tr>";
                }
                echo "</table></td>";
                echo "<td width='10%' class = 'tdborder'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Begin_date']."</td><td width='20%' class = 'tdborder''>".$pres_array[$i]['End_date']."</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
        <script>
        document.getElementById('show_div_allergy').style.display = "block";
       </script>
        <?php
        $tempvalue = 0 ;
    }else{
        //if($min == 1)
            //$tempvalue = 1 ;
        //echo "<li> No Allergies for this patient</li>";
    }
}

//surgery
function display_surgery($layout_type,$pid){
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
    $surgerysql2 = sqlStatement('SET SQL_BIG_SELECTS=1');
    $surgerysql2 = sqlStatement("SET NAMES utf8");
    $surgerysql2 = "SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'surgery'
                                    AND l.pid =".$pid." AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =".$pid." ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
   
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
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                                }
                            }
                            echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
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
                    document.getElementById('show_div_surgery').style.display = "block";
                   </script>
                    <?php
                    $tempvalue = 0 ;
                } 
            } 
        }else{
             echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr>
                    <th class = 'tdborder' width='50%'><b> Description </b></th> 
                    <th class = 'tdborder' width='10%'><b> Status </b></th> 
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
                echo "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    echo $pres_array[$i]['Title']; 
                else
                    echo "Not Specified. ";
                echo "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                    $icd_description = sqlFetchArray($icd_description_sql);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                    }
                }
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    echo "<tr><td class='secondtd'>";
                    echo $icd_description_value;
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
                    echo $pres_array[$i]['Begin_date']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['destination'])){
                    echo "<tr><td class='thirdtd'> ";
                    echo $pres_array[$i]['destination'];
                    echo "</td></tr>";
                }
                echo "</table></td>";
                echo "<td width='10%' class = 'tdborder'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Begin_date']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['End_date']."</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
        <script>
            document.getElementById('show_div_surgery').style.display = "block";
        </script>
        <?php
        $tempvalue = 0 ;
    }else{
        //echo "<li>No Surgeries for this patient.</li>";
        //if($min == 1)
            //$tempvalue = 1 ;
    }
}

//dental
function display_dental($layout_type,$pid){
    global $tempvalue;
    $dentalsql = sqlStatement('SET SQL_BIG_SELECTS=1');
    $dentalsql = sqlStatement("SET NAMES utf8");
    $dentalsql = sqlStatement("SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'allergy'
                                    AND l.pid =".$pid." AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =".$pid." ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC");

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
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                                }
                            }
                            echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
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
                        document.getElementById('show_div_dental').style.display = "block";
                    </script>
                    <?php
                
                } 
            }
        }else{
             echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr>
                    <th class = 'tdborder' width='50%'><b> Description </b></th> 
                    <th class = 'tdborder' width='10%'><b> Status </b></th> 
                    <th class = 'tdborder' width='20%'><b> Start Date </b></th> 
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
                echo "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    echo $pres_array[$i]['Title']; 
                else
                    echo "Not Specified. ";
                echo "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                    $icd_description = sqlFetchArray($icd_description_sql);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                    }
                }
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    echo "<tr><td class='secondtd'>";
                    echo $icd_description_value;
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
                    echo $pres_array[$i]['Begin_date']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['destination'])){
                    echo "<tr><td class='thirdtd'> ";
                    echo $pres_array[$i]['destination'];
                    echo "</td></tr>";
                }
                echo "</table></td>";
                echo "<td width='10%' class = 'tdborder'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Begin_date']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['End_date']."</td>";
                echo "</tr>";
            }
            echo "</table>"; 
        }
        ?>
        <script>
            document.getElementById('show_div_dental').style.display = "block";
        </script>
        <?php
        $tempvalue = 0 ;
    }else{
        //echo "<li>No Dental Issues for this patient.</li>";
        //if($min == 1)
            //$tempvalue = 1 ;
    }    
}

//medical problem
function display_mproblem($pid,$groupName,$layout_type){
    global $tempvalue;
    $getMedicalProblems = sqlStatement('SET SQL_BIG_SELECTS=1');
    $getMedicalProblems = sqlStatement("SET NAMES utf8");
    $getMedicalProblems2 = "SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'medical_problem'
                                    AND l.pid =$pid AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =$pid ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
    
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
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                                }
                            }
                            echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
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
                     document.getElementById('show_div_mproblem').style.display = "block";
                    </script> <?php
                } 
            } 
        }else{
             echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr>
                    <th width='50%' class = 'tdborder'><b> Description </b></th> 
                    <th width='10%' class = 'tdborder'><b> Status </b></th> 
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
                echo "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    echo $pres_array[$i]['Title']; 
                else
                    echo "Not Specified. ";
                echo "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                    $icd_description = sqlFetchArray($icd_description_sql);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                    }
                }
                
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    echo "<tr><td class='secondtd'>";
                    echo $icd_description_value;
                    echo "</td></tr>";
                }
                // ======== //
                if($outcome !='Unassigned' ||$occurance !='Unknown'){
                    echo "<tr><td class='thirdtd'> ";
                    echo $occurance;
                    if($outcome != 'Unassigned')
                        echo ", ". $outcome;
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['ReferredBy'])){
                    echo "<tr><td class='thirdtd'>";
                    echo $pres_array[$i]['Begin_date']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['destination'])){
                    echo "<tr><td class='thirdtd'> ";
                    echo $pres_array[$i]['destination'];
                    echo "</td></tr>";
                }
                echo "</table></td>";
                echo "<td width='10%' class = 'tdborder'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Begin_date']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['End_date']."</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        $tempvalue = 0 ;
        ?>
        <script>
            document.getElementById('show_div_mproblem').style.display = "block";
        </script>
        <?php
        
    }else{
        //echo " <li>No Medical Problems for this patient</li>";
        //if($min == 1)
            //$tempvalue = 1 ;
    }
}

//dme
function display_dme($layout_type,$pid){
    global $tempvalue;
    $surgerysql2 = sqlStatement('SET SQL_BIG_SELECTS=1');
    $surgerysql2 = sqlStatement("SET NAMES utf8");
    $surgerysql2 = " SELECT DISTINCT l.id, l.title AS Title, 
                                    CASE 
                                    WHEN enddate IS NULL 
                                    THEN  'Active'
                                    ELSE  'Inactive'
                                    END AS is_Issue_Active, outcome, destination, begdate AS Begin_date, enddate AS End_date, diagnosis AS Codes, occurrence AS Occurrence, referredby AS ReferredBy, comments
                                    FROM lists AS l
                                    WHERE l.type =  'DME'
                                    AND l.pid =".$pid." AND l.id NOT IN ( SELECT DISTINCT list_id FROM issue_encounter WHERE pid =".$pid." ) 
                                    ORDER BY is_Issue_Active ASC , begdate DESC";
    
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
                        }else if($key == 'Codes'){
                            // multiple icd codes description
                            $icdcodesarray = explode(";",$val);
                            $icd_description_value = '';
                            for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                                if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                                    $checkstring = "ICD9:";
                                    $checkstring1 = 'ICD9';
                                    $table = 'icd9_dx_code';
                                }
                                if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                                    $checkstring = "ICD10:";
                                    $table = 'icd10_dx_order_code';
                                    $checkstring1 = 'ICD10';
                                }
                                $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                                $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                                $icd_description = sqlFetchArray($icd_description_sql);
                                if(!empty($icd_description)){
                                    $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                                }
                            }
                            echo "<b>". ucfirst(str_replace('_',' ',$key.":"))."</b>".$icd_description_value;
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
                        document.getElementById('show_div_dme').style.display = "block";
                    </script>
                    <?php
                    $tempvalue = 0 ;
                    echo "<li>&nbsp;</li>";
                } 
            } 
        }else{
            echo "<table width='980px' border= '1' style='border-collapse: collapse'>";
            echo "<tr>
                    <th width='50%' class = 'tdborder'><b> Description </b></th> 
                    <th width='10%' class = 'tdborder'><b> Status </b></th> 
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
                echo "<tr><td width='50%' class='firsttd'>";
                if(!empty($pres_array[$i]['Title']))
                    echo $pres_array[$i]['Title']; 
                else
                    echo "Not Specified. ";
                echo "</td></tr>";
                // multiple icd codes description
                $icdcodesarray = explode(";",$pres_array[$i]['Codes']);
                $icd_description_value = '';
                for($ic = 0; $ic < count($icdcodesarray) ; $ic++){
                    if(strpos($icdcodesarray[$ic], 'ICD9',0)!== false){
                        $checkstring = "ICD9:";
                        $checkstring1 = 'ICD9';
                        $table = 'icd9_dx_code';
                    }
                    if(strpos($icdcodesarray[$ic], 'ICD10',0)!== false || empty($table)){
                        $checkstring = "ICD10:";
                        $table = 'icd10_dx_order_code';
                        $checkstring1 = 'ICD10';
                    }
                    $exploded_code = str_replace($checkstring, "", $icdcodesarray[$ic]);
                    $icd_description_sql = sqlStatement("SELECT long_desc FROM $table WHERE formatted_dx_code ='$exploded_code' and active= 1");
                    $icd_description = sqlFetchArray($icd_description_sql);
                    if(!empty($icd_description)){
                        $icd_description_value .=  "( ".$icdcodesarray[$ic] ." ".$icd_description['long_desc'].")<br />";
                    }
                }
                if(!empty($pres_array[$i]['Codes']) || !empty($icd_description_value)){
                    echo "<tr><td class='secondtd'>";
                    echo $icd_description_value;
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
                    echo $pres_array[$i]['Begin_date']. "<span class='byelement'>  by </span>". $pres_array[$i]['ReferredBy'];
                    echo "</td></tr>";
                }
                if(!empty($pres_array[$i]['destination'])){
                    echo "<tr><td class='thirdtd'> ";
                    echo $pres_array[$i]['destination'];
                    echo "</td></tr>";
                }
                echo "</table></td>";
                echo "<td width='10%' class = 'tdborder'>".$pres_array[$i]['is_Issue_Active']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['Begin_date']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['End_date']."</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
         ?>
        <script>
            document.getElementById('show_div_dme').style.display = "block";
        </script>
        <?php
        $tempvalue = 0 ;
    else:
        //echo " <li>No DME for this patient.</li>";
        //if($min == 1)
            //$tempvalue = 1 ;
    endif;
}

//prescription
function display_prescription($layout_type,$pid){
    global $tempvalue;
    $pressql = sqlStatement("SELECT  * FROM  `prescriptions` WHERE  `patient_id` =".$pid." order by active desc ,date_added desc ");
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
                        if($key=='provider_id'){
                            $getporvidername = sqlStatement("SELECT CONCAT(fname,' ',lname) AS name FROM users WHERE id='$val'" );
                            $rowName=sqlFetchArray($getporvidername);
                            $provider_name=$rowName['name'];
                            echo  "<b> Provider:"."</b>". $provider_name;
                        }else if($key=='Unit'){
                            $getunits = sqlStatement("SELECT title FROM list_options WHERE option_id='$val' AND list_id='drug_units'" );
                            $rowName=sqlFetchArray($getunits);
                            $units=$rowName['title'];   
                            echo  "<b>".  ucfirst(str_replace('_',' ',$key)).":"."</b>".$units;
                        }elseif($key == 'active' || $key == 'substitute' || $key == 'medication'){
                            if($val== 1)
                                $active = "Yes";
                            else
                                 $active = "No";
                            echo  "<b>".  ucfirst(str_replace('_',' ',$key)).":"."</b>". $active;
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
                        document.getElementById('show_div_prescript').style.display = "block";
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
                    <td width='50%' class = 'tdborder'><b> Description </b></td> 
                    <td width='10%' class = 'tdborder'><b> Status </b></td> 
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
                echo "<tr><td class='firsttd' width='50%'>";
                echo $pres_array[$i]['drug']. " ".$pres_array[$i]['quantity']." ".$drugunit. " ".$drugform;
                echo "</td></tr><tr><td class='secondtd'>";
                echo $pres_array[$i]['dosage']. " ". $druginterval;
                echo "</td></tr>";
                echo "<tr><td class='thirdtd'>";
                echo $pres_array[$i]['date_added']. "<span class='byelement'> by </span> ". $provider_name . " ".$pres_array[$i]['quantity']." ".$drugunit. " ".$drugform ."(".$refill.")";
                echo "</td></tr>";
                echo "</table></td>";
                if($pres_array[$i]['active'] == 1)
                    $active = "Active";
                else
                    $active = "Inactive";
                echo "</td><td width='10%' class = 'tdborder'>".$active."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['start_date']."</td><td width='20%' class = 'tdborder'>".$pres_array[$i]['date_modified']."</td>";
                echo "</tr>";
            }
            echo "</td>";
             echo "</table>";
        }
        ?>
        <script>
            document.getElementById('show_div_prescript').style.display = "block";
        </script>

        <?php
        $tempvalue = 0 ;
    }else{
        //echo "<li> No Prescriptions for this patient.</li>";
        //if($min == 1)
            //$tempvalue = 1 ;
    }
}   

$getlayout_fields = sqlStatement("SELECT * FROM layout_options WHERE form_id = 'NONENC' AND uor > 0 AND field_id != ''  AND group_name='$group' ORDER BY seq");
while($setlayout = sqlFetchArray($getlayout_fields)){   
 $result[$setlayout['seq']]=$setlayout['field_id'];
}

foreach($result as $key1 => $value1){
    if($_REQUEST["form_".$value1]=='YES'){
        $keyarr[$key1]="form_".$value1;
        $minvalue[] = $key1;
    }
}
$minValue = min($minvalue);
static_patient_data($group, $keyarr, $patientid, $minValue);
?>