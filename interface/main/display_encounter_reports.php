<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");
$schedulingProviderId=array();
$append_provider_id='';

if(isset($_POST['schedulingProviderId']))
{    
    $append_provider_id = '='.$_POST['schedulingProviderId'][0];  
}
if(count($_POST['schedulingProviderId'])>1)
{
    foreach($_POST['schedulingProviderId'] as $pro_id)
    {
        array_push($schedulingProviderId,$pro_id);
    }   
   $append_provider_id = ' IN('.implode($schedulingProviderId,',').')';    
}
if(in_array('-2', $_POST['schedulingProviderId'], true))
{
   $append_provider_id = ' IN('.$_POST['allSchedulingProviders'].')';  
}


$fromDate=$_POST['fromDate'];
$toDate=$_POST['toDate'];

if(isset($_POST['visitCategoryId']))
{
//$append_provider_id=($append_where=='') ? ' AND patient_data.providerID='.$providerId : ' patient_data.providerID='.$providerId;            
    $append_visit_category_id2 .=' (opc.pc_catid='.$_POST['visitCategoryId'][0];

    if(count($_POST['visitCategoryId'])>1)
    {
       foreach($_POST['visitCategoryId'] as $vc_id)
       {
           $append_visit_category_id2 .= ' OR opc.pc_catid='.$vc_id; 
       }
    }
    if(in_array('-2', $_POST['visitCategoryId'], true))
    {
       $append_visit_category_id2 .= " OR opc.pc_catid IN(".$_POST['allVisitCategories'].",NULL,'',0)";  
    }

    $append_visit_category_id2 .=') AND ';   
    
}

//$sqlGetPatients="SELECT DISTINCT pd.title,pd.fname,pd.lname,pd.mname,opc.pc_eventDate AS Appointment_Date,CONCAT(u.fname,' ',u.lname) AS Provider,opc.pc_title AS Visit_Category,opc.pc_comments AS Comments,pd.last_visit AS Last_Visit,pd.cert_end AS Cert_End_Date,
//     pd.hhs_days AS Next_Visit,pd.calc_next_visit AS Calc_Next_Visit,pd.street,pd.street_addr,pd.suite,pd.postal_code,pd.city,pd.state,pd.country_code,pd.deceased_date,pd.deceased_reason,pd.deceased_stat,pd.practice_status,pd.patient_facility,pd.cpo,pd.ccm,pd.refill_due,pd.insuranceID,pd.language,pd.ethnicity,pd.race,pd.financial_review,pd.family_size,pd.monthly_income,pd.Statement_cat,pd.homeless,
//     pd.living_facility,pd.living_facility_org,pd.interpretter,pd.migrantseasonal,pd.contrastart,pd.referral_source,pd.referlink,pd.referral_note,pd.referred_date,pd.hhagency,
//     pd.vfc,pd.Insured_Type,pd.annual_visit,pd.authorization,pd.Copay,pd.Deduct_Fam_Ann,pd.Deduct_Indiv_Ann,pd.indoutofpocket,pd.familyoutofpocket,
//     pd.PCP,opc.pc_endDate,opc.pc_duration,opc.pc_time,
//    
//    /*opc.pc_recurrtype,opc.pc_recurrspec,*/
//    opc.pc_recurrfreq,opc.pc_startTime,opc.pc_endTime,
//    opc.pc_alldayevent,
//    /*opc.pc_location,opc.pc_conttel,opc.pc_contname,*/
//    /*opc.pc_contemail,opc.pc_website,*/
//    opc.pc_fee,opc.pc_eventstatus,
//    opc.pc_sharing,opc.pc_language,
//    /*opc.pc_apptstatus,*/
//    lo.title AS App_status,opc.pc_prefcatid,
//    /*opc.pc_facility,*/
//    fc.name AS Facility,opc.pc_sendalertsms,opc.pc_sendalertemail,
//    /*opc.pc_billing_location,*/
//    bfc.name AS Billing_facility,
//
//    pd.DOB,
//
//    /*pd.drivers_license,pd.ss,pd.occupation,*/
//    pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,
//    /*pd.pharmacy_id,pd.status,pd.contact_relationship,pd.date,*/
//    pd.sex,/*pd.referrer,*/
//    /*pd.referrerID,pd.providerID,pd.ref_providerID,*/
//    CONCAT(u1.fname,' ',u1.lname) AS Actual_Provider,
//    CONCAT(u2.fname,' ',u2.lname) AS Referrer_Provider,
//
//    pd.email,
//    /*pd.interpretter,pd.migrantseasonal,pd.family_size,*/
//    /*pd.genericname1,pd.genericval1,pd.genericname2,pd.genericval2,*/
//    /*pd.fitness,pd.referral_source,pd.pricelevel,pd.regdate,pd.mothersname,*/
//    /*pd.guardiansname,pd.deceased_date,pd.deceased_reason,*/
//    /*opc.pc_catid,*/
//    op_cat.pc_catname,/*opc.pc_multiple,*/
//    /*opc.pc_aid,*/
//    opc.pc_hometext,
//    opc.pc_counter,opc.pc_topic,opc.pc_informant,opc.pc_eid,pd.pid,pd.latitude,pd.longitude
    
if($_POST['allSelectedFields'][0] == '-1' || empty($_POST['allSelectedFields']) ){
     $fieldparamters = "pd.title,pd.fname,pd.lname,pd.mname,opc.pc_eventDate AS Appointment_Date,CONCAT(u.fname,' ',u.lname) AS Provider,opc.pc_title AS Visit_Category,opc.pc_comments AS Comments,pd.last_visit AS Last_Visit,pd.cert_end AS Cert_End_Date,
     pd.hhs_days AS Next_Visit,pd.calc_next_visit AS Calc_Next_Visit,pd.street,pd.street_addr,pd.suite,pd.postal_code,pd.city,pd.state,pd.country_code,pd.deceased_date,pd.deceased_reason,pd.deceased_stat,pd.practice_status,pd.patient_facility,pd.cpo,pd.ccm,pd.refill_due,pd.language,pd.ethnicity,pd.race,pd.financial_review,pd.family_size,pd.monthly_income,pd.Statement_cat,pd.homeless,
     pd.living_facility,pd.living_facility_org,pd.interpretter,pd.migrantseasonal,pd.contrastart,pd.referral_source,pd.referlink,pd.referral_note,pd.referred_date,pd.hhagency,
     pd.vfc,pd.Insured_Type,pd.annual_visit,pd.authorization,pd.Copay,pd.Deduct_Fam_Ann,pd.Deduct_Indiv_Ann,pd.indoutofpocket,pd.familyoutofpocket,
     pd.PCP,opc.pc_endDate,opc.pc_duration,opc.pc_time,opc.pc_recurrfreq,opc.pc_startTime,opc.pc_endTime,opc.pc_alldayevent,opc.pc_fee,opc.pc_eventstatus,opc.pc_sharing,opc.pc_language,lo.title AS App_status,opc.pc_prefcatid,fc.name AS Facility,opc.pc_sendalertsms,opc.pc_sendalertemail,bfc.name AS Billing_facility,pd.DOB,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,
    pd.sex,CONCAT(u1.fname,' ',u1.lname) AS Actual_Provider,CONCAT(u2.fname,' ',u2.lname) AS Referrer_Provider,pd.email,op_cat.pc_catname,opc.pc_hometext,opc.pc_counter,opc.pc_topic,opc.pc_informant,opc.pc_eid,pd.pid,pd.latitude,pd.longitude
    ";
    
 }else{ 
    $fieldsarray = $_POST['allSelectedFields'];
   
    foreach ($fieldsarray as $key => $value ) {
        if($value == 'provider')
            $fieldparamters2 .= 'CONCAT(u.fname ," " ,u.lname) AS Provider,';
        elseif($value == 'actualprovider')
            $fieldparamters2 .= 'CONCAT(u1.fname ," " ,u1.lname) AS Actual_Provider,';
        elseif($value == 'referrerprovider')
             $fieldparamters2 .= 'CONCAT(u2.fname ," " ,u2.lname) AS Referrer_Provider,';
        else
            $fieldparamters2 .= str_replace('-', '.', str_replace('--',' ',$value)).",";
    }
    $fieldparamters2 .= "pd.latitude,pd.longitude";
    $fieldparamters = rtrim($fieldparamters2, ',');
 }
$sqlGetPatients="SELECT DISTINCT $fieldparamters
FROM patient_data pd INNER JOIN openemr_postcalendar_events opc
ON pd.pid=opc.pc_pid
INNER JOIN facility fc ON opc.pc_facility=fc.id
INNER JOIN facility bfc ON opc.pc_billing_location=bfc.id
INNER JOIN openemr_postcalendar_categories op_cat ON opc.pc_catid=op_cat.pc_catid
INNER JOIN list_options lo ON opc.pc_apptstatus=lo.option_id
INNER JOIN users u ON opc.pc_aid=u.id

LEFT JOIN users u1 ON pd.providerID=u1.id
LEFT JOIN users u2 ON pd.ref_providerID=u2.id

/*WHERE pd.providerID $append_provider_id AND*/
WHERE opc.pc_aid $append_provider_id AND
(opc.pc_eventDate BETWEEN '$fromDate' AND '$toDate') AND
pd.pid NOT IN 
(SELECT fe.pid FROM form_encounter fe
 WHERE $append_visit_category_id2 fe.date BETWEEN '$fromDate' AND '$toDate')";

if(isset($_POST['visitCategoryId']))
{
//$append_provider_id=($append_where=='') ? ' AND patient_data.providerID='.$providerId : ' patient_data.providerID='.$providerId;            
    $append_visit_category_id .=' AND (opc.pc_catid='.$_POST['visitCategoryId'][0];

    if(count($_POST['visitCategoryId'])>1)
    {
       foreach($_POST['visitCategoryId'] as $vc_id)
       {
           $append_visit_category_id .= ' OR opc.pc_catid='.$vc_id; 
       }
    }
    if(in_array('-2', $_POST['visitCategoryId'], true))
    {
       $append_visit_category_id .= " OR opc.pc_catid IN(".$_POST['allVisitCategories'].",NULL,'',0)";  
    }

    $append_visit_category_id .=')';   
    
    $sqlGetPatients.=$append_visit_category_id;
}
//echo $sqlGetPatients;//die;

$showPatients=sqlStatement($sqlGetPatients);

if(sqlNumRows($showPatients)>0)
{

    $dropTemp=sqlStatement("DROP TABLE IF EXISTS temp_scheduling_Enc");

    $temporary_table_column_name=array();
    $temporary_table_column_type=array();
    $temporary_table_column_length=array();

    $num=mysql_num_fields($showPatients);

    for($m=0;$m<$num;$m++)
    {
        array_push($temporary_table_column_name,mysql_field_name($showPatients,$m));
        array_push($temporary_table_column_type,mysql_field_type($showPatients,$m));
        if(mysql_field_type($showPatients,$m)=='VARCHAR' || mysql_field_type($showPatients,$m)=='varchar' ||
           mysql_field_type($showPatients,$m)=='STRING' || mysql_field_type($showPatients,$m)=='string')
        {
            array_push($temporary_table_column_length,(mysql_field_len($showPatients,$m))/3);
        }
        else if(mysql_field_type($showPatients,$m)=='DATE' || mysql_field_type($showPatients,$m)=='date' ||
                mysql_field_type($showPatients,$m)=='TIME' || mysql_field_type($showPatients,$m)=='time' ||
                mysql_field_type($showPatients,$m)=='DATETIME' || mysql_field_type($showPatients,$m)=='datetime' ||
                mysql_field_type($showPatients,$m)=='YEAR' || mysql_field_type($showPatients,$m)=='year' ||
                mysql_field_type($showPatients,$m)=='TIMESTAMP' || mysql_field_type($showPatients,$m)=='timestamp')
        {
            array_push($temporary_table_column_length,'');
        }
        else
        {
            array_push($temporary_table_column_length,mysql_field_len($showPatients,$m));
        }
    }
       
    $temp_table_query='CREATE TABLE temp_scheduling_Enc(';
    for($m=0;$m<$num;$m++)
    {
        $temp_table_query.="".$temporary_table_column_name[$m]." ".$temporary_table_column_type[$m]."";

        if($temporary_table_column_type[$m]=='DATE' || $temporary_table_column_type[$m]=='date' ||
           $temporary_table_column_type[$m]=='TIME' || $temporary_table_column_type[$m]=='time' ||
           $temporary_table_column_type[$m]=='DATETIME' || $temporary_table_column_type[$m]=='datetime' ||
           $temporary_table_column_type[$m]=='YEAR' || $temporary_table_column_type[$m]=='year' ||
           $temporary_table_column_type[$m]=='TIMESTAMP' || $temporary_table_column_type[$m]=='timestamp')
        {
           $temp_table_query.=' ';
        }
        else
        {
            $temp_table_query.='('.$temporary_table_column_length[$m].')';
        }
        $temp_table_query.=' NOT NULL ';
        if($m<($num)-1)
        {
            $temp_table_query.=',';
        }
    }
    $temp_table_query.=')';

    $temp_table_query=  str_replace('STRING','VARCHAR',$temp_table_query);
    $temp_table_query=  str_replace('string','varchar',$temp_table_query);

        //echo "<br>temp_table_query=".$temp_table_query;die;                    
     
    echo "
<link rel='stylesheet' type='text/css' href='css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.colReorder.css'>
<style>
div.DTTT_container {
	float: none;
}
#encAddrVal_wrapper{background-color:red;}
</style>
<script type='text/javascript' src='js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='js/dataTables.colVis.js'></script>
<script type='text/javascript'>
    $('#encAddrVal').DataTable( {
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
    
    $('#example2').dataTable({
        iDisplayLength: 100,
        dom: 'T<\"clear\">RC<\"clear\">lfrtip',
        \"tableTools\": {
            \"aButtons\": [
                {
                    \"sExtends\": \"xls\",
                    \"sButtonText\": \"Save to Excel\"
                }
            ]
        },
        columnDefs: [
            { visible: false, targets: [4,7,8,11,12,13,14,15,17,19,20] }
        ]
        });                    
</script>
";
    echo "<table id='encAddrVal' class='display' cellspacing='0' width='100%'><thead><tr><th>Patient Name</th><th>Wrong Address</th><th>Correct Address</th></tr></thead><tbody></tbody></table>";
    echo "<table id='example2' class='display' cellspacing='0' width='100%'>
    <thead>
        <tr>
            <th>Name</th>
            <th>Age</th>
        ";
    
    $createTemp=sqlStatement($temp_table_query); 

    if(sqlNumRows($showPatients)>0)
    {
        while($rowPatients=sqlFetchArray($showPatients))
        {
            $temp_values=array();
            foreach($rowPatients as $val)
            {
                $val=str_replace("'","\'",$val);
                $val="'".$val."'";
                array_push($temp_values,$val);
            }

            $insertInTempTable=sqlStatement("INSERT INTO temp_scheduling_Enc VALUES(".implode($temp_values,',').")");
        }
                
        $sqlTempCols="SHOW COLUMNS FROM temp_scheduling_Enc 
                      WHERE Field NOT IN('id','title','fname','mname','lname')";
              
        $getTempColumns=sqlStatement($sqlTempCols);
         
        if(sqlNumRows($getTempColumns)>0)
        {
            while($rowTempColumns=sqlFetchArray($getTempColumns))
            {
                echo "<th>".ucfirst(str_replace("_"," ",$rowTempColumns['Field']))."</th>";
            }
        }
                
        echo "</tr></thead>";            	   

        $getTemp="SELECT * FROM temp_scheduling_Enc";
        if(isset($_POST['providerId']))
        {
        //$append_provider_id=($append_where=='') ? ' AND patient_data.providerID='.$providerId : ' patient_data.providerID='.$providerId;            
            $append_provider_id .=' (providerID='.$_POST['providerId'][0];

            if(count($_POST['providerId'])>1)
            {
               foreach($_POST['providerId'] as $pro_id)
               {
                   $append_provider_id .= ' OR providerID='.$pro_id; 
               }
            }
            if(in_array('-2', $_POST['providerId'], true))
            {
               $append_provider_id .= " OR providerID IN(".$_POST['allProviders'].",NULL,'',0)";  
            }

            $append_provider_id .=')';                            
        }
                              
        if(isset($_POST['providerId']))
        {
            $getTemp.=" WHERE ".$append_provider_id."";
            
            if(isset($_POST['visitCategoryId']))
            {
                $getTemp.=" AND ".$append_visit_category_id;
            }  
        }
                       
              
        //echo "getTemp==".$getTemp;
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

            echo "<tr>";
            //$full_name=$fname." ".$lname;
            echo "<td title='".$fname." ".$lname."'>".$fname." ".$lname."<br /><a class=\"css_button_small\" href=\"javascript:;\" onclick=\"window.open('calendar/add_edit_event.php?eid=".$rowTemp['pc_eid']."','EditAppointmentWindow','width=650, height=320')\"><span>Edit</span></a></td>";	           
            echo "<td title='".$age."'>".$age."</td>";

//            echo "<td title='".$street.$city.$state.$country_code."'>
//                  <input type='hidden' id='hdnDetailsEnc_$i' name='hdnDetailsEnc' 
//                  value=\"".$fname." ".$lname."-<br>".$street.", ".$city.", ".$state.", ".$country_code."<br>".$phone_home.$phone_biz.$phone_contact.$phone_cell."\" />
//
//                  <input type='hidden' id='hdnAddressEnc_$i' name='hdnAddressEnc' 
//                  value=\"".$street.", ".$city.", ".$state.", ".$country_code."\" />".
//                   ""; 
//                    $full_address=$street.$city.$state.$country_code;
//                    if(strlen($full_address)>10)
//                    {
//                       // $full_address=substr($full_address, 0, 10)." ...";
//                    }
//                    
//                    //$street.$city.$state.$country_code."</td>";
//                    echo $full_address."</td>";

//            echo "<td title='".$phone_home.$phone_biz.$phone_contact.$phone_cell."'>
//                  ".$phone_home.$phone_biz.$phone_contact.$phone_cell."</td>";

            $sqlTempColumnsData="SHOW COLUMNS FROM temp_scheduling_Enc 
                         WHERE Field NOT IN('id','title','fname','mname','lname')";
                        
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
                                
                $sqlTempColumnsData="SHOW COLUMNS FROM temp_scheduling_Enc 
                         WHERE Field IN($tempColsStr)";
                
            }
            
            if(isset($_POST['patientCustomData']) && !empty($_POST['patientCustomData']))
            {
                //$_POST['patientCustomData']=explode(',',$_POST['patientCustomData']);
                $tempCols=implode($_POST['patientCustomData'],',');           
                $tempColsStr='';            
                $customDataFields=array();
                foreach ($_POST['patientCustomData'] as $col)
                {
                    array_push($customDataFields,"'".$col."'");
                }

                $tempColsStr=implode($customDataFields,',');
                //$tempColsStr=rtrim($tempColsStr,",");   
                if(isset($_POST['patientStandardData']) && !empty($_POST['patientStandardData']))
                {
                    $sqlTempColumnsData.=" OR Field IN($tempColsStr)";
                }
                else
                {
                    $sqlTempColumnsData="SHOW COLUMNS FROM temp_scheduling_Enc 
                                  WHERE Field IN($tempColsStr)";
                }
            }
                                             
            $getTempColumnsData=sqlStatement($sqlTempColumnsData);
            
            if(sqlNumRows($getTempColumnsData)>0)
            {
                while($rowTempColumnsData=sqlFetchArray($getTempColumnsData))
                {
                    $fieldValue=$rowTemp[$rowTempColumnsData['Field']];
                    
                    if(strlen($fieldValue)>10)
                    {
                        //$fieldValue=substr($fieldValue, 0, 10)." ...";
                    }
                    //echo "<td title='".$rowTemp[$rowTempColumnsData['Field']]."'>
                    //      ".$rowTemp[$rowTempColumnsData['Field']]."</td>";
                    echo "<td title='".$rowTemp[$rowTempColumnsData['Field']]."'>
                          ".$fieldValue."</td>";
                   
                }
            }
                                              
            echo "</tr>";

            $i++;

        }                                        

        echo "</table>";
        
        $j=0;
        $showTemp2=sqlStatement($getTemp);
        while($rowTemp=sqlFetchArray($showTemp2))
        {
            $fname=(isset($rowTemp['fname'])) ? $rowTemp['fname'] : '';
            $lname=(isset($rowTemp['lname'])) ? $rowTemp['lname'] : '';

            $age=(isset($rowTemp['DOB'])) ? (date("Y")-date('Y', strtotime($rowTemp['DOB']))) : '';
            
            $streetAddr=(isset($rowTemp['street_addr']) && $rowTemp['street_addr']!='') ? ($rowTemp['street_addr']) : '';
            $street=(isset($rowTemp['street']) && $rowTemp['street']!='') ? ($rowTemp['street'].', ') : '';
            $city=(isset($rowTemp['city']) && $rowTemp['city']!='') ? ($rowTemp['city']) : '';
            $state=(isset($rowTemp['state']) && $rowTemp['state']!='') ? ($rowTemp['state']) : '';
            $postal_code=(isset($rowTemp['postal_code']) && $rowTemp['postal_code']!='') ? ($rowTemp['postal_code']) :  '';			
            $country_code=(isset($rowTemp['country_code']) && $rowTemp['country_code']!='') ? ($rowTemp['country_code']) :  '';
            $phone_home=(isset($rowTemp['phone_home']) && $rowTemp['phone_home']!='') ? ($rowTemp['phone_home'].' / ') : '';
            $phone_biz=(isset($rowTemp['phone_biz']) && $rowTemp['phone_biz']!='') ? ($rowTemp['phone_biz'].' / ') : '';
            $phone_contact=(isset($rowTemp['phone_contact']) && $rowTemp['phone_contact']!='') ? ($rowTemp['phone_contact'].' / ') : '';
            $phone_cell=(isset($rowTemp['phone_cell']) && $rowTemp['phone_cell']!='') ? $rowTemp['phone_cell'] : '';
            $latitude=(isset($rowTemp['latitude']) && $rowTemp['latitude']!='') ? $rowTemp['latitude'] : '';	
            $longitude=(isset($rowTemp['longitude']) && $rowTemp['longitude']!='') ? $rowTemp['longitude'] : '';
            $pid=(isset($rowTemp['pid']) && $rowTemp['pid']!='') ? $rowTemp['pid'] : '';		

            echo "<input type='hidden' id='hdnDetailsEncBack_$j' name='hdnDetailsEncBack' 
                  value=\"".$fname." ".$lname."-<br>".$street.", ".$city.", ".$state.", ".$country_code."<br>".$phone_home.$phone_biz.$phone_contact.$phone_cell."\" />

                  <input type='hidden' id='hdnAddressEncBack_$j' name='hdnAddressEncBack' 
                  value=\"".$streetAddr."$*$".$city."$*$".$state."$*$".$postal_code."$*$".$country_code."$*$".$latitude."$*$".$longitude."$*$".$pid."\" />";
            $j++;
        }           
    }
    else
    {
        //echo "No data found for the given provider";
        echo "</tr></thead></table>";
    }

    $dropTemp=sqlStatement("DROP TABLE temp_scheduling_Enc");
}

else
{
    echo 'No result found';
}


// Show Provider's address starts

if(isset($_POST['schedulingProviderId']))
{
	//$getProviderAddress=sqlStatement("SELECT fname,lname,street,city,state FROM users WHERE id=".$_POST['schedulingProviderId']);	
        $providers=array();
        foreach($_POST['schedulingProviderId'] as $pro_id)
        {
            array_push($providers,$pro_id);
        }
        $getProviderAddress=sqlStatement("SELECT id,fname,lname,street,city,state 
                                          FROM users 
                                          WHERE id IN (".implode(',',$providers).")");	
//echo "SELECT id,fname,lname,street,city,state 
//      FROM users 
//      WHERE id IN (".implode(',',$providers).")";
//die;
	if(sqlNumRows($getProviderAddress)>=1)
	{
                
            //$rowProviderAddress=sqlFetchArray($getProviderAddress);
            while($rowAddress=sqlFetchArray($getProviderAddress))
            {
              echo "<input type='hidden' id='hdnSchedulingProviderDetails".$rowAddress['id']."' name='hdnSchedulingProviderDetails".$rowAddress['id']."' 
              value='".$rowAddress['fname']." ".$rowAddress['lname']."-<br>".$rowAddress['street']." ".$rowAddress['city'].", ".$rowAddress['state']."' />				

              <input type='hidden' id='hdnSchedulingProviderAddress".$rowAddress['id']."' name='hdnSchedulingProviderAddress".$rowAddress['id']."' 
              value='".$rowAddress['street']." ".$rowAddress['city'].", ".$rowAddress['state']."' />";
	
//              echo "<input type='text' value='hdnProviderAddress".$rowAddress['id']."' />";
//              echo "<label>hdnProviderDetails".$rowAddress['id']."</label>";
//              echo "<input type='text' value='hdnProviderDetails".$rowAddress['id']."' /><br>";
              
              
            }
                
					
	}

}

/////////////////////// Show Scheduling Provider's address ////////////////////////

?>
