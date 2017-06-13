<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("verify-session.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

// Show today's appointments 

// Visit Type
$patientVisitType = $_POST['patientVisitType'];
$currentVisitCategories = "";

// Scheduling Visit Categories from allcareConfig lists
$visit_list = '';
$get_visit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingVisitCategories'");
while($setvisit = sqlFetchArray($get_visit_categories)){
    $visit_list = $setvisit['title'];
}

// AWV Visit Categories
$awvvisit_list = '';
$get_awvvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingAWVVisits'");
while($setvisit = sqlFetchArray($get_awvvisit_categories)){
    $awvvisit_list = $setvisit['title'];
}

// H&P Visit Categories
$hpvisit_list = '';
$get_hpvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingHPVisits'");
while($setvisit = sqlFetchArray($get_hpvisit_categories)){
    $hpvisit_list = $setvisit['title'];
}

// Super Vision Visit Categories
$spvisit_list = '';
$get_spvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingSuperVisionVisits'");
while($setvisit = sqlFetchArray($get_spvisit_categories)){
    $spvisit_list = $setvisit['title'];
}

// Cert Visit Categories
$ctvisit_list = '';
$get_ctvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingCertVisits'");
while($setvisit = sqlFetchArray($get_ctvisit_categories)){
    $ctvisit_list = $setvisit['title'];
}

// CCM Visit Categories
$ccmvisit_list = '';
$get_ccmvisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingCCMVisits'");
while($setvisit = sqlFetchArray($get_ccmvisit_categories)){
    $ccmvisit_list = $setvisit['title'];
}

// Sudo Visit Categories
$sudovisit_list = '';
$get_sudovisit_categories = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='SchedulingSudoVisits'");
while($setvisit = sqlFetchArray($get_sudovisit_categories)){
    $sudovisit_list = $setvisit['title'];
}

if($patientVisitType == 1):
    $currentVisitCategories = $visit_list;
    if($visit_list == ""):
        ?>
        <script> 
            alert("Please configure New/Establised Visit Categories"); </script>
        <?php
    endif;
elseif($patientVisitType == 2):
    $currentVisitCategories = $awvvisit_list;
    if($awvvisit_list == ""):
        ?>
        <script> alert("Please configure AWV Visit Categories"); </script>
        <?php
    endif;    
elseif($patientVisitType == 3):
    $currentVisitCategories = $hpvisit_list;
    if($hpvisit_list == ""):
        ?>
        <script> alert("Please configure H & P Visit Categories"); </script>
        <?php
    endif; 
elseif($patientVisitType == 4):
    $currentVisitCategories = $spvisit_list;
    if($spvisit_list == ""):
        ?>
        <script> alert("Please configure CPO/Supervision Visit Categories"); </script>
        <?php
    endif;
elseif($patientVisitType == 5):
    $currentVisitCategories = $ctvisit_list;
    if($ctvisit_list == ""):
        ?>
        <script> alert("Please configure Cert Visit Categories"); </script>
        <?php
    endif;
elseif($patientVisitType == 6):
    $currentVisitCategories = $ccmvisit_list;
    if($ccmvisit_list == ""):
        ?>
        <script> alert("Please configure CCM Visit Categories"); </script>
        <?php
    endif;
elseif($patientVisitType == 7):    
    $currentVisitCategories = $sudovisit_list;
    if($sudovisit_list == ""):
        ?>
        <script> alert("Please configure Sudo Visit Categories"); </script>
        <?php
    endif;
endif;

$getOpenAppQuery = sqlStatement("SELECT pc_pid, MAX( pc_eid ),pc_eventDate 
                                FROM openemr_postcalendar_events
                                WHERE pc_catid
                                IN ($currentVisitCategories ) 
                                AND pc_eventDate > CURDATE( ) -1
                                GROUP BY pc_pid");
$openAppId = array();
if(sqlNumRows($getOpenAppQuery)>0)
{
    while($rowTemp=sqlFetchArray($getOpenAppQuery))
    {
        if($rowTemp['pc_pid'] != ""){
            $openAppId[] = $rowTemp['pc_pid'];
        }
    }
    $openAppIdStr = implode(",",$openAppId);
    if($openAppIdStr != ""){
        $openAppIdStrInClause = " AND pd.pid NOT IN (".$openAppIdStr.")";
        $openSetAppIdStrInClause = " AND pd.pid IN (".$openAppIdStr.")";
    }    
}
/* Update openAppdate attribute by next future appointment date*/
if($patientVisitType == 1):
    $getOpenAppQuery2 = sqlStatement("SELECT pc_pid, MAX( pc_eid ),pc_eventDate 
                                    FROM openemr_postcalendar_events
                                    WHERE pc_catid
                                    IN ($visit_list ) 
                                    AND pc_eventDate > CURDATE( ) -1
                                    GROUP BY pc_pid");
    while($rowTemp=sqlFetchArray($getOpenAppQuery2)){
        sqlStatement("UPDATE patient_data SET openAppdate = '".$rowTemp['pc_eventDate']."' WHERE pid=".$rowTemp['pc_pid']);
    }
endif;    

/* Update AWV openAppdate attribute by next future appointment date*/
if($patientVisitType == 2):
    $getOpenAppQuery3 = sqlStatement("SELECT pc_pid, MAX( pc_eid ),pc_eventDate 
                                    FROM openemr_postcalendar_events
                                    WHERE pc_catid
                                    IN ($awvvisit_list ) 
                                    AND pc_eventDate > CURDATE( ) -1
                                    GROUP BY pc_pid");
    while($rowTemp=sqlFetchArray($getOpenAppQuery3)){
        sqlStatement("UPDATE patient_data SET awvopenAppDate = '".$rowTemp['pc_eventDate']."' WHERE pid=".$rowTemp['pc_pid']);
    }
endif;    

/* Update H&P openAppdate attribute by next future appointment date*/
if($patientVisitType == 3):
    $getOpenAppQuery4 = sqlStatement("SELECT pc_pid, MAX( pc_eid ),pc_eventDate 
                                    FROM openemr_postcalendar_events
                                    WHERE pc_catid
                                    IN ($hpvisit_list ) 
                                    AND pc_eventDate > CURDATE( ) -1
                                    GROUP BY pc_pid");
    while($rowTemp=sqlFetchArray($getOpenAppQuery4)){
        sqlStatement("UPDATE patient_data SET hpopenAppdate = '".$rowTemp['pc_eventDate']."' WHERE pid=".$rowTemp['pc_pid']);
    }
endif;    

/* Update Super Vision openAppdate attribute by next future appointment date*/
if($patientVisitType == 4):
    $getOpenAppQuery5 = sqlStatement("SELECT pc_pid, MAX( pc_eid ),pc_eventDate 
                                    FROM openemr_postcalendar_events
                                    WHERE pc_catid
                                    IN ($spvisit_list ) 
                                    AND pc_eventDate > CURDATE( ) -1
                                    GROUP BY pc_pid");
    while($rowTemp=sqlFetchArray($getOpenAppQuery5)){
        sqlStatement("UPDATE patient_data SET spopenAppdate = '".$rowTemp['pc_eventDate']."' WHERE pid=".$rowTemp['pc_pid']);
    }
endif;    

/* Update Cert openAppdate attribute by next future appointment date*/
if($patientVisitType == 5):
    $getOpenAppQuery5 = sqlStatement("SELECT pc_pid, MAX( pc_eid ),pc_eventDate 
                                    FROM openemr_postcalendar_events
                                    WHERE pc_catid
                                    IN ($ctvisit_list ) 
                                    AND pc_eventDate > CURDATE( ) -1
                                    GROUP BY pc_pid");
    while($rowTemp=sqlFetchArray($getOpenAppQuery5)){
        sqlStatement("UPDATE patient_data SET ctopenAppdate = '".$rowTemp['pc_eventDate']."' WHERE pid=".$rowTemp['pc_pid']);
    }
endif; 

/* Update CCM openAppdate attribute by next future appointment date*/
if($patientVisitType == 6):
    $getOpenAppQuery5 = sqlStatement("SELECT pc_pid, MAX( pc_eid ),pc_eventDate 
                                    FROM openemr_postcalendar_events
                                    WHERE pc_catid
                                    IN ($ccmvisit_list ) 
                                    AND pc_eventDate > CURDATE( ) -1
                                    GROUP BY pc_pid");
    while($rowTemp=sqlFetchArray($getOpenAppQuery5)){
        sqlStatement("UPDATE patient_data SET ccmopenAppdate = '".$rowTemp['pc_eventDate']."' WHERE pid=".$rowTemp['pc_pid']);
    }
endif; 

/* Update Sudo openAppdate attribute by next future appointment date*/
if($patientVisitType == 7):
    $getOpenAppQuery5 = sqlStatement("SELECT pc_pid, MAX( pc_eid ),pc_eventDate 
                                    FROM openemr_postcalendar_events
                                    WHERE pc_catid
                                    IN ($sudovisit_list ) 
                                    AND pc_eventDate > CURDATE( ) -1
                                    GROUP BY pc_pid");
    while($rowTemp=sqlFetchArray($getOpenAppQuery5)){
        sqlStatement("UPDATE patient_data SET sudoopenAppdate = '".$rowTemp['pc_eventDate']."' WHERE pid=".$rowTemp['pc_pid']);
    }
endif; 

$selectisactive = $_POST['selectIsActive'];
$selectIsDeceased = $_POST['selectIsDeceased'];

    echo "
<link rel='stylesheet' type='text/css' href='css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.colReorder.css'>
<style>
div.DTTT_container {
	float: none;
}
#AddrVal_wrapper{background-color:red;  height:500px; width:100%; overflow:scroll; overflow-x: hidden;}
#example_wrapper{ height:1500px; width:100%; overflow:scroll;}
#hd{background:#FFFF66 !important;}
</style>
<script type='text/javascript' src='js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='js/dataTables.colVis.js'></script>";
if($_POST['patientStandardData'] != "" || $_POST['patientCustomData'] != ""){
    echo "<script type='text/javascript'>

            $('#AddrVal').DataTable( {
            iDisplayLength: 100,
            dom: 'T<\"clear\">lfrtip',
            \"tableTools\": {
                \"aButtons\": [
                    {
                        \"sExtends\": \"xls\",
                        \"sButtonText\": \"Save to Excel\"
                    }
                ]
            }
            } );

            $('#example').DataTable( {
            iDisplayLength: 100,
            dom: 'T<\"clear\">RC<\"clear\">lfrtip',
            \"tableTools\": {
                \"aButtons\": [
                    {
                        \"sExtends\": \"xls\",
                        \"sButtonText\": \"Save to Excel\"
                    }
                ]
            }
            } );

    </script>";
}
else{
    echo "<script type='text/javascript'>

            $('#AddrVal').DataTable( {
            iDisplayLength: 100,
            dom: 'T<\"clear\">lfrtip',
            \"tableTools\": {
                \"aButtons\": [
                    {
                        \"sExtends\": \"xls\",
                        \"sButtonText\": \"Save to Excel\"
                    }
                ]
            }
            } );

            $('#example').DataTable( {
            iDisplayLength: 100,
            dom: 'T<\"clear\">RC<\"clear\">lfrtip',
            \"tableTools\": {
                \"aButtons\": [
                    {
                        \"sExtends\": \"xls\",
                        \"sButtonText\": \"Save to Excel\"
                    }
                ]
            }
            } );
            
    </script>";
}
    echo "<table id='AddrVal' class='display' cellspacing='0' width='100%'><thead><tr><th>Patient Name</th><th>Wrong Address</th><th>Correct Address</th></tr></thead><tbody></tbody></table>";
    
    ?>
        <script>
        if($('#addrvalshow').val() == 0){
            $('#AddrVal_wrapper').hide();
            $('#lblHide').hide();
            $('#lblShow').show();
        }    
        if($('#addrvalshow').val() == 1 || $('#addrvalshow').val().length === 0){
            $('#AddrVal_wrapper').show();
            $('#lblHide').show();
            $('#lblShow').hide();
        }        
        </script>
   <div id="hd">
       
      <a onclick="javascript:jQuery('#example_wrapper').toggle(500);
                              jQuery('#lblHide1').hide();
                              jQuery('#lblShow1').show();jQuery('#patshow').val('0');"
       style="cursor:pointer;">
           <label id="lblHide1"><b>Hide Patient Data</b></label>
       </a>

       <a onclick="javascript:jQuery('#example_wrapper').toggle(500);
                              jQuery('#lblHide1').show();
                              jQuery('#lblShow1').hide();jQuery('#patshow').val('1');"
       style="cursor:pointer;">
           <label id="lblShow1" style="display:none;"><b>Show Patient Data</b></label>
       </a>
       <script>
        if(jQuery('#patshow').val() == 0){
            jQuery('#example_wrapper').hide();
            jQuery('#lblHide1').hide();
            jQuery('#lblShow1').show();
        }    
        if(jQuery('#patshow').val() == 1 || jQuery('#patshow').val().length === 0){
            jQuery('#example_wrapper').show();
            jQuery('#lblHide1').show();
            jQuery('#lblShow1').hide();
        }        
        </script>
    </div>
    <?php
    echo "<table id='example' class='display' cellspacing='0' width='100%'>
    <thead>
        <tr>
            <th>Name</th>
            <th>Age</th>";        

        $sqlTempCols="SELECT field_id
                      FROM `layout_options`
                      WHERE `form_id` = 'DEM' AND uor > 0 AND field_id != ''
                      ORDER BY group_name,seq";
        
        if(isset($_POST['patientStandardData']) && !empty($_POST['patientStandardData']))
        {
            $tempCols=implode($_POST['patientStandardData'],',');        
            $tempColsStr='';
            $patientDataFields=array();
            foreach ($_POST['patientStandardData'] as $col)
            {
                array_push($patientDataFields,"'".$col."'");
            }
            
            $tempColsStr=implode($patientDataFields,',');
            //$tempColsStr=rtrim($tempColsStr,",");   
            
            $sqlTempCols="SHOW COLUMNS FROM patient_data 
                          WHERE Field IN($tempColsStr)";                        
        }
        
        $getTempColumns=sqlStatement($sqlTempCols);
         
        if(sqlNumRows($getTempColumns)>0)
        {
            while($rowTempColumns=sqlFetchArray($getTempColumns))
            {
                if(isset($_POST['patientStandardData']) && !empty($_POST['patientStandardData'])):
                    echo "<th>".ucfirst(str_replace("_"," ",$rowTempColumns['Field']))."</th>";
                else:    
                    echo "<th>".ucfirst(str_replace("_"," ",$rowTempColumns['field_id']))."</th>";
                endif;
                
            }
        }
                
        echo "</tr></thead>";

        $getTemp="SELECT * FROM patient_data pd";
        
        if(isset($_POST['providerId']))
        {
        //$append_provider_id=($append_where=='') ? ' AND patient_data.providerID='.$providerId : ' patient_data.providerID='.$providerId;            
            $append_provider_id .=' (pd.providerID='.$_POST['providerId'][0];

            if(count($_POST['providerId'])>1)
            {
               foreach($_POST['providerId'] as $pro_id)
               {
                   $append_provider_id .= ' OR pd.providerID='.$pro_id; 
               }
            }
            if(in_array('-2', $_POST['providerId'], true))
            {
               $append_provider_id .= " OR pd.providerID IN(".$_POST['allProviders'].",'',0) OR pd.providerID IS NULL";  
            }

            $append_provider_id .=')';                            
        }
        /*        
        if(isset($_POST['payerId']))
        {
            //$append_payerId_Query=' patient_data.pid IN (SELECT DISTINCT pid FROM insurance_data WHERE provider='.$payerId.')';	
            $append_payerId_Query=' ((pd.insuranceID='.$_POST['payerId'][0].'';	

            if(count($_POST['payerId'])>1)
            {
               foreach($_POST['payerId'] as $pro_id)
               {
                   $append_payerId_Query .= ' OR pd.insuranceID='.$pro_id; 
               }
            }

            if(in_array('-2', $_POST['payerId'], true))
            {
               $append_payerId_Query .= " OR pd.insuranceID IN(".$_POST['allPayers'].",'',0) OR pd.insuranceID IS NULL";  
            }

            $append_payerId_Query .=')';    

            $append_payer_id=' '.$append_payerId_Query.')';    

        }
        */                             
        if(isset($_POST['providerId']))
        {
            $getTemp.=" WHERE ".$append_provider_id."";

            if(isset($_POST['payerId']))
            {
                $getTemp.=" AND ".$append_payer_id;                                             
            }
        }
        else if(!isset($_POST['providerId']) && isset($_POST['payerId']))
        {
            $getTemp.=" WHERE ".$append_payer_id;
//            if(isset($_POST['visitCategoryId']))
//            {
//                $getTemp.=" AND ".$append_visit_category_id;
//            }  
        }                
        
        $selectIsDeceased = '';
        $selectIsActive = '';
        $practiceIfActive = '';
        if(isset($_POST['selectIsDeceased'])):
            foreach( $_POST['selectIsDeceased'] as $data ){
                if($data == '-2'):
                    $selectIsDeceased .="'YES','NO',' ',";
                endif;
                if($data == 'YES'):
                    $selectIsDeceased .="'YES'";
                endif;
                if($data == 'NO'):
                    $selectIsDeceased .="'NO',' ',";
                endif;
            }
            $selectIsDeceased = rtrim($selectIsDeceased, ',');
        endif;
        if(isset($_POST['practiceIfActive'])):
            foreach( $_POST['practiceIfActive'] as $data ){
                if($data == '-2'):
                    $practiceIfActive .="'YES','NO','', 'PENDING'";
                endif;
                 if($data == 'YES'):
                    $practiceIfActive .="'YES',";
                endif;
                if($data == 'NO'):
                    $practiceIfActive .="'NO',";
                endif;
                if($data == 'PENDING'):
                    $practiceIfActive .="'PENDING',";
                endif;
            };
            $practiceIfActive = rtrim($practiceIfActive, ',');
            //$selectIsActive .= "''";
        endif;
        if(isset($_POST['selectIsActive'])):
            foreach( $_POST['selectIsActive'] as $data ){
                if($data == '-2'):
                    $selectIsActive .="'YES','NO','', 'PENDING'";
                endif;
                 if($data == 'YES'):
                    $selectIsActive .="'YES',";
                endif;
                if($data == 'NO'):
                    $selectIsActive .="'NO',";
                endif;
                if($data == 'PENDING'):
                    $selectIsActive .="'PENDING',";
                endif;
            };
            $selectIsActive = rtrim($selectIsActive, ',');
            //$selectIsActive .= "''";
        endif;
        if($patientVisitType == 1):
            $externalClause = " AND pd.deceased_stat IN(".$selectIsDeceased.")
                               AND pd.practice_status  IN(".$selectIsActive.")";
        elseif($patientVisitType == 2):    
            $externalClause = " AND pd.deceased_stat IN(".$selectIsDeceased.") 
                               AND pd.practice_status  IN(".$practiceIfActive.") 
                               AND pd.awv_required  IN(".$selectIsActive.")";
        elseif($patientVisitType == 3):    
            $externalClause = " AND pd.deceased_stat IN(".$selectIsDeceased.")  
                               AND pd.practice_status  IN(".$practiceIfActive.")  
                               AND pd.h_p  IN(".$selectIsActive.")";
        elseif($patientVisitType == 4):    
            $externalClause = " AND pd.deceased_stat IN(".$selectIsDeceased.") 
                               AND pd.practice_status  IN(".$practiceIfActive.")  
                               AND pd.cpo  IN(".$selectIsActive.")";
        elseif($patientVisitType == 5):    
            $externalClause = " AND pd.deceased_stat IN(".$selectIsDeceased.") 
                               AND pd.practice_status  IN(".$practiceIfActive.") 
                               AND pd.hh_certification  IN(".$selectIsActive.")";
        elseif($patientVisitType == 6):    
            $externalClause = " AND pd.deceased_stat IN(".$selectIsDeceased.") 
                               AND pd.practice_status  IN(".$practiceIfActive.") 
                               AND pd.ccm  IN(".$selectIsActive.")";
        elseif($patientVisitType == 7):    
            $externalClause = " AND pd.deceased_stat IN(".$selectIsDeceased.") 
                               AND pd.practice_status  IN(".$practiceIfActive.") 
                               AND pd.sudo_required  IN(".$selectIsActive.")";
        endif;
        
        
//        $externalClause = " AND pd.deceased_stat != 'YES' 
//                            AND pd.practice_status != 'NO' AND pd.practice_status != 'PENDING'";
        // Get appointmented patients in map
        $getTemp2 = $getTemp. $openSetAppIdStrInClause. $externalClause;
        // Get due for appointment patient in map
        $getTemp3 = $getTemp. $openAppIdStrInClause. $externalClause;
        
        if($_POST['patientL'] == 1){
            // get due for appointment patient in grid
            $getTemp = $getTemp. $openAppIdStrInClause. $externalClause;
        }
        else if($_POST['patientL'] == 2){
            // get appointmented patients in grid
            $getTemp = $getTemp. $openSetAppIdStrInClause. $externalClause;
        }
        else{
            // get all patients in grid
            $getTemp = $getTemp. $externalClause;
        }
        
        
        
         $showTemp=sqlStatement($getTemp);

        $i=0;
        while($rowTemp=sqlFetchArray($showTemp))
        {
            $fname=(isset($rowTemp['fname'])) ? $rowTemp['fname'] : '';
            $lname=(isset($rowTemp['lname'])) ? $rowTemp['lname'] : '';

            $age=(isset($rowTemp['DOB'])) ? (date("Y")-date('Y', strtotime($rowTemp['DOB']))) : '';

            $street=(isset($rowTemp['street']) && $rowTemp['street']!='') ? ($rowTemp['street'].', ') : '';
            $city=(isset($rowTemp['city']) && $rowTemp['city']!='') ? ($rowTemp['city'].', ') : '';
            $state=(isset($rowTemp['state']) && $rowTemp['state']!='') ? ($rowTemp['state']) : '';
            $postal_code=(isset($rowTemp['postal_code']) && $rowTemp['postal_code']!='') ? ($rowTemp['postal_code'].', ') :  '';			
            $country_code=(isset($rowTemp['country_code']) && $rowTemp['country_code']!='') ? ($rowTemp['country_code']) :  '';
            $phone_home=(isset($rowTemp['phone_home']) && $rowTemp['phone_home']!='') ? ($rowTemp['phone_home'].' / ') : '';
            $phone_biz=(isset($rowTemp['phone_biz']) && $rowTemp['phone_biz']!='') ? ($rowTemp['phone_biz'].' / ') : '';
            $phone_contact=(isset($rowTemp['phone_contact']) && $rowTemp['phone_contact']!='') ? ($rowTemp['phone_contact'].' / ') : '';
            $phone_cell=(isset($rowTemp['phone_cell']) && $rowTemp['phone_cell']!='') ? $rowTemp['phone_cell'] : '';			
            $pid = $rowTemp['pid']; 
            echo "<tr>";
            //$full_name=$fname." ".$lname;
            echo "<td title='".$fname." ".$lname."'>".$fname." ".$lname." <br /><a class=\"css_button_small\" href=\"javascript:;\" onclick=\"window.open('calendar/add_edit_event.php?patientid=".$rowTemp['pid']."','AppointmentWindow','width=650, height=320')\"><span>Add</span></a></td>";	           
            echo "<td title='".$age."'>".$age."</td>";
//            echo "<input type='hidden' id='hdnAddress_$i' name='hdnAddress' 
//                  value=\"".$street.", ".$city.", ".$state.", ".$country_code."\" />";
//            echo "<input type='hidden' id='hdnpid_$i' name='hdnpid' 
//                  value='$pid' />";
//            echo "<td title='".$street.$city.$state.$country_code."'>
//                  <input type='hidden' id='hdnDetails_$i' name='hdnDetails' 
//                  value=\"".$fname." ".$lname."-<br>".$street.", ".$city.", ".$state.", ".$country_code."<br>".$phone_home.$phone_biz.$phone_contact.$phone_cell."\" />
//
//                  <input type='hidden' id='hdnAddress_$i' name='hdnAddress' 
//                  value=\"".$street.", ".$city.", ".$state.", ".$country_code."\" />".
//                   ""; 
//                    $full_address=$street.$city.$state.$country_code;
//                    echo $full_address."</td>";
//
//            echo "<td title='".$phone_home.$phone_biz.$phone_contact.$phone_cell."'>
//                  ".$phone_home.$phone_biz.$phone_contact.$phone_cell."</td>";

            $sqlTempColumnsData="SELECT field_id
                                 FROM `layout_options`
                                 WHERE `form_id` = 'DEM' AND uor > 0 AND field_id != ''
                                 ORDER BY group_name,seq";
            
            
            if(isset($_POST['patientStandardData']) && !empty($_POST['patientStandardData']))
            {
                $tempCols=implode($_POST['patientStandardData'],',');           
                $tempColsStr='';
                $patientDataFields=array();
                foreach ($_POST['patientStandardData'] as $col)
                {
                    array_push($patientDataFields,"'".$col."'");
                }
            
                $tempColsStr=implode($patientDataFields,',');
                //$tempColsStr=rtrim($tempColsStr,",");     
                                
                $sqlTempColumnsData="SHOW COLUMNS FROM patient_data 
                         WHERE Field IN($tempColsStr)";
                
            }
            
            $getTempColumnsData=sqlStatement($sqlTempColumnsData);
            
            if(sqlNumRows($getTempColumnsData)>0)
            {
                while($rowTempColumnsData=sqlFetchArray($getTempColumnsData))
                {
                    if(isset($_POST['patientStandardData']) && !empty($_POST['patientStandardData'])):
                        $fieldValue=$rowTemp[$rowTempColumnsData['Field']];
                    else:   
                        $fieldValue=$rowTemp[$rowTempColumnsData['field_id']];
                    endif;
                    
                    
                    if(strlen($fieldValue)>100)
                    {
                        //$fieldValue=substr($fieldValue, 0, 100)." ...";
                    }
                    //echo "<td title='".$rowTemp[$rowTempColumnsData['Field']]."'>
                    //      ".$rowTemp[$rowTempColumnsData['Field']]."</td>";
                    if(isset($_POST['patientStandardData']) && !empty($_POST['patientStandardData'])):
                        echo "<td title='".$rowTemp[$rowTempColumnsData['Field']]."'>
                          ".$fieldValue."</td>";
                    else: 
                        echo "<td title='".$rowTemp[$rowTempColumnsData['field_id']]."'>
                          ".$fieldValue."</td>";
                    endif;
                    
                   
                }
            }
                                              
            echo "</tr>";

            $i++;

        }

        echo "</table>";
        
                       
        /* Set for Due APPOINTMENT YELLOW ICON */
        $j=0;
        
        $showTemp2=sqlStatement($getTemp3);
        while($rowTemp2=sqlFetchArray($showTemp2))
        {
            $fname=(isset($rowTemp2['fname'])) ? $rowTemp2['fname'] : '';
            $lname=(isset($rowTemp2['lname'])) ? $rowTemp2['lname'] : '';

            $age=(isset($rowTemp2['DOB'])) ? (date("Y")-date('Y', strtotime($rowTemp2['DOB']))) : '';
            
            $street=(isset($rowTemp2['street']) && $rowTemp2['street']!='') ? ($rowTemp2['street'].', ') : '';
            $streetAddr=(isset($rowTemp2['street_addr']) && $rowTemp2['street_addr']!='') ? ($rowTemp2['street_addr']) : '';
            $apt=(isset($rowTemp2['street']) && $rowTemp2['street']!='') ? ($rowTemp2['street'].', ') : '';
            $city=(isset($rowTemp2['city']) && $rowTemp2['city']!='') ? ($rowTemp2['city']) : '';
            $state=(isset($rowTemp2['state']) && $rowTemp2['state']!='') ? ($rowTemp2['state']) : '';
            $postal_code=(isset($rowTemp2['postal_code']) && $rowTemp2['postal_code']!='') ? ($rowTemp2['postal_code']) :  '';			
            $country_code=(isset($rowTemp2['country_code']) && $rowTemp2['country_code']!='') ? ($rowTemp2['country_code']) :  '';			
            $phone_home=(isset($rowTemp2['phone_home']) && $rowTemp2['phone_home']!='') ? ($rowTemp2['phone_home'].' / ') : '';
            $phone_biz=(isset($rowTemp2['phone_biz']) && $rowTemp2['phone_biz']!='') ? ($rowTemp2['phone_biz'].' / ') : '';
            $phone_contact=(isset($rowTemp2['phone_contact']) && $rowTemp2['phone_contact']!='') ? ($rowTemp2['phone_contact'].' / ') : '';
            $phone_cell=(isset($rowTemp2['phone_cell']) && $rowTemp2['phone_cell']!='') ? $rowTemp2['phone_cell'] : '';	
            $latitude=(isset($rowTemp2['latitude']) && $rowTemp2['latitude']!='') ? $rowTemp2['latitude'] : '';	
            $longitude=(isset($rowTemp2['longitude']) && $rowTemp2['longitude']!='') ? $rowTemp2['longitude'] : '';
            $pid=(isset($rowTemp2['pid']) && $rowTemp2['pid']!='') ? $rowTemp2['pid'] : '';
            
            $openAppdate=(isset($rowTemp2['openAppdate']) && $rowTemp2['openAppdate']!='') ? $rowTemp2['openAppdate'] : '';
            $awvopenAppDate=(isset($rowTemp2['awvopenAppDate']) && $rowTemp2['awvopenAppDate']!='') ? $rowTemp2['awvopenAppDate'] : '';
            $hpopenAppdate=(isset($rowTemp2['hpopenAppdate']) && $rowTemp2['hpopenAppdate']!='') ? $rowTemp2['hpopenAppdate'] : '';
            $spopenAppdate=(isset($rowTemp2['spopenAppdate']) && $rowTemp2['spopenAppdate']!='') ? $rowTemp2['spopenAppdate'] : '';
            $ctopenAppdate=(isset($rowTemp2['ctopenAppdate']) && $rowTemp2['ctopenAppdate']!='') ? $rowTemp2['ctopenAppdate'] : '';
            $ccmopenAppdate=(isset($rowTemp2['ccmopenAppdate']) && $rowTemp2['ccmopenAppdate']!='') ? $rowTemp2['ccmopenAppdate'] : '';
            $sudoopenAppdate=(isset($rowTemp2['sudoopenAppdate']) && $rowTemp2['sudoopenAppdate']!='') ? $rowTemp2['sudoopenAppdate'] : '';
            
            if($patientVisitType == 1):
                $appdate = "Appointment Date: ".$openAppdate;
            elseif($patientVisitType == 2):    
                $appdate = "AWV Appointment Date: ".$awvopenAppDate;
            elseif($patientVisitType == 3):    
                $appdate = "H&P Appointment Date: ".$hpopenAppdate;
            elseif($patientVisitType == 4):    
                $appdate = "CPO Appointment Date: ".$spopenAppdate;
            elseif($patientVisitType == 5):    
                $appdate = "Cert Appointment Date: ".$ctopenAppdate;
            elseif($patientVisitType == 6):    
                $appdate = "CCM Appointment Date: ".$ccmopenAppdate;
            elseif($patientVisitType == 7):    
                $appdate = "Sudo Appointment Date: ".$sudoopenAppdate;
            endif;
            echo "<input type='hidden' id='hdnDetailsBack_$j' name='hdnDetailsBack' 
                  value=\"".$fname." ".$lname."-<br>".$street.$city.$state." ".$postal_code.$country_code."<br>".$phone_home.$phone_biz.$phone_contact.$phone_cell."<br />".$appdate."\" />

                  <input type='hidden' id='hdnAddressBack_$j' name='hdnAddressBack' 
                  value=\"".$streetAddr."$*$".$city."$*$".$state."$*$".$postal_code."$*$".$country_code."$*$".$latitude."$*$".$longitude."$*$".$pid."\" />"
                    . "<input type='hidden' id='hdnStreet_$j' name='hdnStreet' 
                  value=\"".$streetAddr."\" />"
                    . "<input type='hidden' id='hdnCity_$j' name='hdnCity' 
                  value=\"".$city ."\" />"
                    . "<input type='hidden' id='hdnState_$j' name='hdnState' 
                  value=\"".$state."\" />".
                    "<input type='hidden' id='hdnZip_$j' name='hdnZip' 
                  value=\"".$postal_code."\" />";
            $j++;

        }
        
        $j = 0;
        /* Set for FUTURE APPOINTMENT GRAY ICON */
        $showTemp3=sqlStatement($getTemp2);
        while($rowTemp3=sqlFetchArray($showTemp3))
        {
            $fname=(isset($rowTemp3['fname'])) ? $rowTemp3['fname'] : '';
            $lname=(isset($rowTemp3['lname'])) ? $rowTemp3['lname'] : '';

            $age=(isset($rowTemp3['DOB'])) ? (date("Y")-date('Y', strtotime($rowTemp3['DOB']))) : '';
            
            $street=(isset($rowTemp3['street']) && $rowTemp3['street']!='') ? ($rowTemp3['street'].', ') : '';
            $streetAddr=(isset($rowTemp3['street_addr']) && $rowTemp3['street_addr']!='') ? ($rowTemp3['street_addr']) : '';
            $apt=(isset($rowTemp3['street']) && $rowTemp3['street']!='') ? ($rowTemp3['street'].', ') : '';
            $city=(isset($rowTemp3['city']) && $rowTemp3['city']!='') ? ($rowTemp3['city']) : '';
            $state=(isset($rowTemp3['state']) && $rowTemp3['state']!='') ? ($rowTemp3['state']) : '';
            $postal_code=(isset($rowTemp3['postal_code']) && $rowTemp3['postal_code']!='') ? ($rowTemp3['postal_code']) :  '';			
            $country_code=(isset($rowTemp3['country_code']) && $rowTemp3['country_code']!='') ? ($rowTemp3['country_code']) :  '';			
            $phone_home=(isset($rowTemp3['phone_home']) && $rowTemp3['phone_home']!='') ? ($rowTemp3['phone_home'].' / ') : '';
            $phone_biz=(isset($rowTemp3['phone_biz']) && $rowTemp3['phone_biz']!='') ? ($rowTemp3['phone_biz'].' / ') : '';
            $phone_contact=(isset($rowTemp3['phone_contact']) && $rowTemp3['phone_contact']!='') ? ($rowTemp3['phone_contact'].' / ') : '';
            $phone_cell=(isset($rowTemp3['phone_cell']) && $rowTemp3['phone_cell']!='') ? $rowTemp3['phone_cell'] : '';	
            $latitude=(isset($rowTemp3['latitude']) && $rowTemp3['latitude']!='') ? $rowTemp3['latitude'] : '';	
            $longitude=(isset($rowTemp3['longitude']) && $rowTemp3['longitude']!='') ? $rowTemp3['longitude'] : '';
            $pid=(isset($rowTemp3['pid']) && $rowTemp3['pid']!='') ? $rowTemp3['pid'] : '';
            
            $openAppdate=(isset($rowTemp3['openAppdate']) && $rowTemp3['openAppdate']!='') ? $rowTemp3['openAppdate'] : '';
            $awvopenAppDate=(isset($rowTemp3['awvopenAppDate']) && $rowTemp3['awvopenAppDate']!='') ? $rowTemp3['awvopenAppDate'] : '';
            $hpopenAppdate=(isset($rowTemp3['hpopenAppdate']) && $rowTemp3['hpopenAppdate']!='') ? $rowTemp3['hpopenAppdate'] : '';
            $spopenAppdate=(isset($rowTemp3['spopenAppdate']) && $rowTemp3['spopenAppdate']!='') ? $rowTemp3['spopenAppdate'] : '';
            $ctopenAppdate=(isset($rowTemp3['ctopenAppdate']) && $rowTemp3['ctopenAppdate']!='') ? $rowTemp3['ctopenAppdate'] : '';
            $ccmopenAppdate=(isset($rowTemp3['ccmopenAppdate']) && $rowTemp3['ccmopenAppdate']!='') ? $rowTemp3['ccmopenAppdate'] : '';
            $sudoopenAppdate=(isset($rowTemp3['sudoopenAppdate']) && $rowTemp3['sudoopenAppdate']!='') ? $rowTemp3['sudoopenAppdate'] : '';
            
            if($patientVisitType == 1):
                $appdate = "Appointment Date: ".$openAppdate;
            elseif($patientVisitType == 2):    
                $appdate = "AWV Appointment Date: ".$awvopenAppDate;
            elseif($patientVisitType == 3):    
                $appdate = "H&P Appointment Date: ".$hpopenAppdate;
            elseif($patientVisitType == 4):    
                $appdate = "CPO Appointment Date: ".$spopenAppdate;
            elseif($patientVisitType == 5):    
                $appdate = "Cert Appointment Date: ".$ctopenAppdate;
            elseif($patientVisitType == 6):    
                $appdate = "CCM Appointment Date: ".$ccmopenAppdate;
            elseif($patientVisitType == 7):    
                $appdate = "Sudo Appointment Date: ".$sudoopenAppdate;
            endif;
            
            echo "<input type='hidden' id='hdnDetailsBackk_$j' name='hdnDetailsBackk' 
                  value=\"".$fname." ".$lname."-<br>".$street.$city.$state." ".$postal_code.$country_code."<br>".$phone_home.$phone_biz.$phone_contact.$phone_cell."<br />".$appdate."\" />

                  <input type='hidden' id='hdnAddressBackk_$j' name='hdnAddressBackk' 
                  value=\"".$streetAddr."$*$".$city."$*$".$state."$*$".$postal_code."$*$".$country_code."$*$".$latitude."$*$".$longitude."$*$".$pid."\" />"
                    . "<input type='hidden' id='hdnStreetk_$j' name='hdnStreetk' 
                  value=\"".$streetAddr."\" />"
                    . "<input type='hidden' id='hdnCityk_$j' name='hdnCityk' 
                  value=\"".$city ."\" />"
                    . "<input type='hidden' id='hdnStatek_$j' name='hdnStatek' 
                  value=\"".$state."\" />".
                    "<input type='hidden' id='hdnZipk_$j' name='hdnZipk' 
                  value=\"".$postal_code."\" />";
            $j++;

        }



?>