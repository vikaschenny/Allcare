<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");

// Show today's appointments 

//print_r($_POST);

$getOpenAppQuery = sqlStatement("SELECT pc_pid, MAX( pc_eid ) 
                                FROM openemr_postcalendar_events
                                WHERE pc_catid
                                IN (15, 16, 17, 18, 19, 20, 24, 25, 29, 44 ) 
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
//      if($openAppIdStr != ""){
//        $openAppIdStrInClause = " AND pd.pid NOT IN (".$openAppIdStr.")";
//    } 
}

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
#AddrVal_wrapper{background-color:red;}
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
    echo "<table id='example' class='display' cellspacing='0' width='100%'>
    <thead>
        <tr>
            <th>Name</th>
            <th>Age</th>
            <th>Contact</th>";        

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

        } */
        
//        if(isset($_POST['selectIsActive']))
//        {
//            //$append_payerId_Query=' patient_data.pid IN (SELECT DISTINCT pid FROM insurance_data WHERE provider='.$payerId.')';	
//           $selectisactive =' ((pd.deceased_stat='.$_POST['selectIsActive'][0].'';	
//
//            if(count($_POST['selectIsActive'])>1)
//            {
//               foreach($_POST['selectIsActive'] as $pro_id)
//               {
//                   $selectisactive .= ' OR pd.deceased_stat='.$pro_id; 
//               }
//            }
//
//            if(in_array('-2', $_POST['selectIsActive'], true))
//            {
//               $selectisactive .= " OR pd.deceased_stat IN(".$_POST['allPayers'].",'',0) OR pd.deceased_stat IS NULL";  
//            }
//
//            $selectisactive .=')';    
//
//            $selectisactivedata=' '.$selectisactive.')';    
//
//        }
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
            $getTemp.=" WHERE ".$append_payer_id. $selectisactivedata;
//            if(isset($_POST['visitCategoryId']))
//            {
//                $getTemp.=" AND ".$append_visit_category_id;
//            }  
        }                
        $selectIsDeceased = '';
        $selectIsActive = '';
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
        if(isset($_POST['selectIsActive'])):
            foreach( $_POST['selectIsActive'] as $data ){
                if($data == '-2'):
                    $selectIsActive .="'YES','NO','', 'PENDING'";
                endif;
                 if($data == 'YES'):
                    $selectIsActive .="'YES','',";
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
        $externalClause = " AND pd.deceased_stat IN(".$selectIsDeceased.")
                        AND pd.practice_status  IN(".$selectIsActive.")";
        
        $getTemp = $getTemp. $externalClause;
                        
        
        
        
        $getTemp = $getTemp;
        $showTemp=sqlStatement($getTemp);

        $i=0;
        while($rowTemp=sqlFetchArray($showTemp))
        {
            $fname=(isset($rowTemp['fname'])) ? $rowTemp['fname'] : '';
            $lname=(isset($rowTemp['lname'])) ? $rowTemp['lname'] : '';

            $age=(isset($rowTemp['DOB'])) ? (date("Y")-date('Y', strtotime($rowTemp['DOB']))) : '';

            $street=(isset($rowTemp['street']) && $rowTemp['street']!='') ? ($rowTemp['street'].', ') : '';
            $streetAddr=(isset($rowTemp2['street_addr']) && $rowTemp2['street_addr']!='') ? ($rowTemp2['street_addr'].', ') : '';
            $apt=(isset($rowTemp2['street']) && $rowTemp2['street']!='') ? ($rowTemp2['street'].', ') : '';
            $city=(isset($rowTemp['city']) && $rowTemp['city']!='') ? ($rowTemp['city'].', ') : '';
            $state=(isset($rowTemp['state']) && $rowTemp['state']!='') ? ($rowTemp['state']) : '';
            $postal_code=(isset($rowTemp['postal_code']) && $rowTemp['postal_code']!='') ? ($rowTemp['postal_code'].', ') :  '';			
            $country_code=(isset($rowTemp['country_code']) && $rowTemp['country_code']!='') ? ($rowTemp['country_code']) :  '';
            $phone_home=(isset($rowTemp['phone_home']) && $rowTemp['phone_home']!='') ? ($rowTemp['phone_home'].' / ') : '';
            $phone_biz=(isset($rowTemp['phone_biz']) && $rowTemp['phone_biz']!='') ? ($rowTemp['phone_biz'].' / ') : '';
            $phone_contact=(isset($rowTemp['phone_contact']) && $rowTemp['phone_contact']!='') ? ($rowTemp['phone_contact'].' / ') : '';
            $phone_cell=(isset($rowTemp['phone_cell']) && $rowTemp['phone_cell']!='') ? $rowTemp['phone_cell'] : '';			

            echo "<tr>";
            //$full_name=$fname." ".$lname;
            echo "<td title='".$fname." ".$lname."'>".$fname." ".$lname." <br /><a class=\"css_button_small\" href=\"javascript:;\" onclick=\"window.open('calendar/add_edit_event.php?patientid=".$rowTemp['pid']."','AppointmentWindow','width=650, height=320')\"><span>Add</span></a></td>";	           
            echo "<td title='".$age."'>".$age."</td>";

//            echo "<td title='".$street.$city.$state.$country_code."'>
//                  <input type='hidden' id='hdnDetails_$i' name='hdnDetails' 
//                  value=\"".$fname." ".$lname."-<br>".$street.", ".$city.", ".$state.", ".$country_code."<br>".$phone_home.$phone_biz.$phone_contact.$phone_cell."\" />
//
//                  <input type='hidden' id='hdnAddress_$i' name='hdnAddress' 
//                  value=\"".$streetAddr.", ".$city.", ".$state.", ".$country_code."\" />".
//                   ""; 
//                    $full_address=$streetAddr.$city.$state.$country_code;
//                    if(strlen($full_address)>10)
//                    {
//                        //$full_address=substr($full_address, 0, 10)." ...";
//                    }
//                    
//                    //$street.$city.$state.$country_code."</td>";
//                    echo $full_address."</td>";

            echo "<td title='".$phone_home.$phone_biz.$phone_contact.$phone_cell."'>
                  ".$phone_home.$phone_biz.$phone_contact.$phone_cell."</td>";

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
        
                       
        
        $j=0;
        
        $showTemp2=sqlStatement($getTemp);
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

            echo "<input type='hidden' id='hdnDetailsBack_$j' name='hdnDetailsBack' 
                  value=\"".$fname." ".$lname."-<br>".$street.$city.$state." ".$postal_code.$country_code."<br>".$phone_home.$phone_biz.$phone_contact.$phone_cell."\" />

                  <input type='hidden' id='hdnAddressBack_$j' name='hdnAddressBack' 
                  value=\"".$streetAddr."$*$".$city."$*$".$state."$*$".$postal_code."$*$".$country_code."$*$".$latitude."$*$".$longitude."$*$".$pid."\" />";
            $j++;

        }  



?>