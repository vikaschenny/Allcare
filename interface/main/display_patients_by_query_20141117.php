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

$getAppointments=sqlStatement("SELECT pd.id,pd.fname,pd.lname,pd.street,pd.city,pd.state,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,	
                                      opc.pc_title,opc.pc_startTime,opc.pc_endTime
                               FROM patient_data pd INNER JOIN openemr_postcalendar_events opc
                               ON pd.pid=opc.pc_pid
                               WHERE opc.pc_eventDate=CURDATE()");

if(sqlNumRows($getAppointments)>0)
{
    $i=0;                 
    while($rowAppointments=sqlFetchArray($getAppointments))
    {

        $fname=$rowAppointments['fname'];
        $lname=$rowAppointments['lname'];
        $street=$rowAppointments['street'];
        $city=$rowAppointments['city'];
        $state=$rowAppointments['state'];

        $phone_home=(isset($rowAppointments['phone_home']) && $rowAppointments['phone_home']!='') ? ($rowAppointments['phone_home'].' / ') : '';
        $phone_biz=(isset($rowAppointments['phone_biz']) && $rowAppointments['phone_biz']!='') ? ($rowAppointments['phone_biz'].' / ') : '';
        $phone_contact=(isset($rowAppointments['phone_contact']) && $rowAppointments['phone_contact']!='') ? ($rowAppointments['phone_contact'].' / ') : '';
        $phone_cell=(isset($rowAppointments['phone_cell']) && $rowAppointments['phone_cell']!='') ? $rowAppointments['phone_cell'] : '';			

        $title=$rowAppointments['pc_title'];
        $startTime=$rowAppointments['pc_startTime'];
        $endTime=$rowAppointments['pc_endTime'];                    

        echo "<input type='hidden' id='hdnAppointmentDetails_$i' name='hdnAppointmentDetails'
               value='".$fname." ".$lname."-<br>".$street.", ".$city.", ".$state."
                     <br>".$phone_home.$phone_biz.$phone_contact.$phone_cell."
                      <br>Appointment - ".$title."<br>From ".$startTime." to ".$endTime."' />

                  <input type='hidden' id='hdnAppointmentAddress_$i' name='hdnAppointmentAddress' 
                  value='".$street.", ".$city.", ".$state."' />";                                                

        $i++;
    }
}

/////////////////  Show today's appointments ends   ////////////////////////


$queryName=$_POST['queryName'];

$getQueryStatement=sqlStatement("SELECT querystring FROM tbl_allcare_query WHERE name='$queryName'");

if(sqlNumRows($getQueryStatement)==1)
{
    $rowQuery=sqlFetchArray($getQueryStatement);

    $queryString=str_replace(';',' ',$rowQuery['querystring']);
   //$queryString=str_replace('*','1 ',$queryString);
    //$queryString=str_replace('*','patient_data.* ',$queryString);
    
    $append_provider_id='';
    $append_payer_id='';
    $append_visit_category_id='';

    $showPatients=sqlStatement($queryString. "".//$append_where." ".
                               //$append_provider_id." ".$append_payer_id.
                               //" LIMIT 0,".sqlNumRows($showPatientsRows)."".
            "");        
    
        
//        echo "<br>field2=".mysql_field_name($showPatients,53);
//        echo "<br>field2 dt=".mysql_field_type($showPatients,53);
//        echo "<br>field2 dL=".mysql_field_len($showPatients,53);
//        echo "<br>field2 num=".  mysql_num_fields($showPatients);
                        
       // $showPatients=sqlStatement($queryString);
 
    $dropTemp=sqlStatement("DROP TABLE IF EXISTS temp_scheduling");

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

    $temp_table_query='CREATE TABLE temp_scheduling(';
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

<script type='text/javascript' src='js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='js/jquery.dataTables.min.js'></script>
<script type='text/javascript'>
    $('#example').dataTable();                    
</script>
";

    echo "<table id='example' class='display' cellspacing='0' width='100%'>
    <thead>
        <tr>
            <th>Name</th>
            <th>Age</th>
            <th>Address</th>
            <th>Contact</th>
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

            $insertInTempTable=sqlStatement("INSERT INTO temp_scheduling VALUES(".implode($temp_values,',').")");
        }
                
        $sqlTempCols="SHOW COLUMNS FROM temp_scheduling 
                      WHERE Field NOT IN('id','title','fname','mname','lname',
                      'street','city','postal_code','state')";
        
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
            
            $sqlTempCols="SHOW COLUMNS FROM temp_scheduling 
                          WHERE Field IN($tempColsStr)";                        
        }
        
        if(isset($_POST['patientCustomData']) && !empty($_POST['patientCustomData']))
        {
            //print_r($_POST['patientCustomData']);
            $_POST['patientCustomData']=explode(',',$_POST['patientCustomData']);
            $tempCols=implode($_POST['patientCustomData'],',');           
            $tempColsStr='';            
            $customDataFields=array();
            foreach ($_POST['patientCustomData'] as $col)
            {
                array_push($customDataFields,"'".$col."'");
            }
            //print_r($customDataFields);
            $tempColsStr=implode($customDataFields,',');
            //$tempColsStr=rtrim($tempColsStr,",");   
            if(isset($_POST['patientStandardData']) && !empty($_POST['patientStandardData']))
            {
                $sqlTempCols.=" OR Field IN($tempColsStr)";
            }
            else
            {
                $sqlTempCols="SHOW COLUMNS FROM temp_scheduling 
                              WHERE Field IN($tempColsStr)";
            }
        }

        $getTempColumns=sqlStatement($sqlTempCols);
         
        if(sqlNumRows($getTempColumns)>0)
        {
            while($rowTempColumns=sqlFetchArray($getTempColumns))
            {
                echo "<th>".ucfirst(str_replace("_"," ",$rowTempColumns['Field']))."</th>";
            }
        }
                
        echo "</tr></thead>";

        $getTemp="SELECT * FROM temp_scheduling";
        /*
        $getTempCols=  sqlStatement($getTemp);
        $getTempColsNum=sqlNumRows($getTempCols);        
        echo "<br>getColsNum==".$getTempColsNum;
        */
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
        
//echo '<br>append_provider_id='.$append_provider_id;//die;

//                     
//        if(isset($_POST['payerId']))
//        {
//            //$append_payerId_Query=' patient_data.pid IN (SELECT DISTINCT pid FROM insurance_data WHERE provider='.$payerId.')';	
//            $append_payerId_Query=' pid IN (SELECT DISTINCT pid FROM insurance_data WHERE (provider='.$_POST['payerId'][0].'';	
//
//            if(count($_POST['payerId'])>1)
//            {
//               foreach($_POST['payerId'] as $pro_id)
//               {
//                   $append_payerId_Query .= ' OR provider='.$pro_id; 
//               }    
//            }
//
//            if(in_array('-2', $_POST['payerId'], true))
//            {
//               $append_payerId_Query .= " OR provider='' OR provider IN(".$_POST['allPayers'].")";  
//            }
//
//            $append_payerId_Query .=')';    
//
//            $append_payer_id=' '.$append_payerId_Query.')';    
//
//        }
//        
         
                
        if(isset($_POST['payerId']))
        {
            //$append_payerId_Query=' patient_data.pid IN (SELECT DISTINCT pid FROM insurance_data WHERE provider='.$payerId.')';	
            $append_payerId_Query=' ((insuranceID='.$_POST['payerId'][0].'';	

            if(count($_POST['payerId'])>1)
            {
               foreach($_POST['payerId'] as $pro_id)
               {
                   $append_payerId_Query .= ' OR insuranceID='.$pro_id; 
               }
            }

            if(in_array('-2', $_POST['payerId'], true))
            {
               $append_payerId_Query .= " OR insuranceID IN(".$_POST['allPayers'].",NULL,'',0)";  
            }

            $append_payerId_Query .=')';    

            $append_payer_id=' '.$append_payerId_Query.')';    

        }
        
//                        
//        if(isset($_POST['visitCategoryId']))
//        {
//        //$append_provider_id=($append_where=='') ? ' AND patient_data.providerID='.$providerId : ' patient_data.providerID='.$providerId;            
//            $append_visit_category_id .=' (visitCategoryID='.$_POST['visitCategoryId'][0];
//
//            if(count($_POST['visitCategoryId'])>1)
//            {
//               foreach($_POST['visitCategoryId'] as $vc_id)
//               {
//                   $append_visit_category_id .= ' OR visitCategoryID='.$vc_id; 
//               }
//            }
//            if(in_array('-2', $_POST['visitCategoryId'], true))
//            {
//               $append_visit_category_id .= " OR visitCategoryID='0' OR visitCategoryID IN(".$_POST['allVisitCategories'].")";  
//            }
//
//            $append_visit_category_id .=')';                            
//        }
//                               
        if(isset($_POST['providerId']))
        {
            $getTemp.=" WHERE ".$append_provider_id."";

            if(isset($_POST['payerId']))
            {
                $getTemp.=" AND ".$append_payer_id;                                             
            }
            
//            if(isset($_POST['visitCategoryId']))
//            {
//                $getTemp.=" AND ".$append_visit_category_id;
//            } 
        }
        else if(!isset($_POST['providerId']) && isset($_POST['payerId']))
        {
            $getTemp.=" WHERE ".$append_payer_id;
//            if(isset($_POST['visitCategoryId']))
//            {
//                $getTemp.=" AND ".$append_visit_category_id;
//            }  
        }
        
//        else if(!isset($_POST['providerId']) && 
//                !isset($_POST['payerId']) && 
//                isset($_POST['visitCategoryId']))
//        {
//            $getTemp.=" WHERE ".$append_visit_category_id;
//        }
//        
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
            $state=(isset($rowTemp['state']) && $rowTemp['state']!='') ? ($rowTemp['state'].', ') : '';
            $country_code=(isset($rowTemp['country_code']) && $rowTemp['country_code']!='') ? ($rowTemp['country_code'].', ') :  '';			
            $phone_home=(isset($rowTemp['phone_home']) && $rowTemp['phone_home']!='') ? ($rowTemp['phone_home'].' / ') : '';
            $phone_biz=(isset($rowTemp['phone_biz']) && $rowTemp['phone_biz']!='') ? ($rowTemp['phone_biz'].' / ') : '';
            $phone_contact=(isset($rowTemp['phone_contact']) && $rowTemp['phone_contact']!='') ? ($rowTemp['phone_contact'].' / ') : '';
            $phone_cell=(isset($rowTemp['phone_cell']) && $rowTemp['phone_cell']!='') ? $rowTemp['phone_cell'] : '';			

            echo "<tr>";
            //$full_name=$fname." ".$lname;
            echo "<td title='".$fname." ".$lname."'>".$fname." ".$lname."</td>";	           
            echo "<td title='".$age."'>".$age."</td>";

            echo "<td title='".$street.$city.$state.$country_code."'>
                  <input type='hidden' id='hdnDetails_$i' name='hdnDetails' 
                  value=\"".$fname." ".$lname."-<br>".$street.", ".$city.", ".$state.", ".$country_code."<br>".$phone_home.$phone_biz.$phone_contact.$phone_cell."\" />

                  <input type='hidden' id='hdnAddress_$i' name='hdnAddress' 
                  value=\"".$street.", ".$city.", ".$state.", ".$country_code."\" />".
                   ""; 
                    $full_address=$street.$city.$state.$country_code;
                    if(strlen($full_address)>10)
                    {
                        $full_address=substr($full_address, 0, 10)." ...";
                    }
                    
                    //$street.$city.$state.$country_code."</td>";
                    echo $full_address."</td>";

            echo "<td title='".$phone_home.$phone_biz.$phone_contact.$phone_cell."'>
                  ".$phone_home.$phone_biz.$phone_contact.$phone_cell."</td>";

            $sqlTempColumnsData="SHOW COLUMNS FROM temp_scheduling 
                         WHERE Field NOT IN('id','title','fname','mname','lname',
                         'street','city','postal_code','state')";
            
            
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
                                
                $sqlTempColumnsData="SHOW COLUMNS FROM temp_scheduling 
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
                    $sqlTempColumnsData="SHOW COLUMNS FROM temp_scheduling 
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
                        $fieldValue=substr($fieldValue, 0, 10)." ...";
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
    }
    else
    {
        //echo "No data found for the given provider";
        echo "</tr></thead></table>";
    }

    $dropTemp=sqlStatement("DROP TABLE temp_scheduling");
}

else
{
    echo 'No result found for above query';
}


// Show Provider's address starts

if(isset($_POST['providerId']))
{
	//$getProviderAddress=sqlStatement("SELECT fname,lname,street,city,state FROM users WHERE id=".$_POST['providerId']);	
        $providers=array();
                
        if(in_array('-2', $_POST['providerId'], true))
        {
            $all_providers=explode(",",$_POST['allProviders']);                        
            
            foreach($all_providers as $pro_id)
            {
                array_push($providers,$pro_id);
            }
        }
        
        foreach($_POST['providerId'] as $pro_id)
        {
            array_push($providers,$pro_id);
        }
        
        $getProviderAddress=sqlStatement("SELECT id,fname,lname,street,city,state 
                                          FROM users 
                                          WHERE id IN (".implode(',',$providers).")");	

	if(sqlNumRows($getProviderAddress)>=1)
	{
                
            //$rowProviderAddress=sqlFetchArray($getProviderAddress);
            while($rowAddress=sqlFetchArray($getProviderAddress))
            {               
                
              echo "<input type='hidden' id='hdnProviderDetails".$rowAddress['id']."' name='hdnProviderDetails".$rowAddress['id']."' 
              value='".$rowAddress['fname']." ".$rowAddress['lname']."-<br>".$rowAddress['street']." ".$rowAddress['city'].", ".$rowAddress['state']."' />				

              <input type='hidden' id='hdnProviderAddress".$rowAddress['id']."' name='hdnProviderAddress".$rowAddress['id']."' 
              value='".$rowAddress['street']." ".$rowAddress['city'].", ".$rowAddress['state']."' />";
	
//              echo "<input type='text' value='hdnProviderAddress".$rowAddress['id']."' />";
//              echo "<label>hdnProviderDetails".$rowAddress['id']."</label>";
//              echo "<input type='text' value='hdnProviderDetails".$rowAddress['id']."' /><br>";
              
              
            }
	}
}


/////////////////////// Show Provider's address ////////////////////////

?>
