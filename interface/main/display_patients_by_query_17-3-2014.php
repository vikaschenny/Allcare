<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");


// Show today's appointments 

$getAppointments=sqlStatement("SELECT pd.id,pd.fname,pd.lname,pd.street,pd.city,pd.state,pd.phone_home,pd.phone_biz,pd.phone_contact,pd.phone_cell,	
                                      opc.pc_title,opc.pc_startTime,opc.pc_endTime
                               FROM patient_data pd INNER JOIN openemr_postcalendar_events opc
                               ON pd.id=opc.pc_pid
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

$providerId=0;
$payerId=0;

$allPOS='';
$posFields=(isset($_POST['posFields'])) ? $_POST['posFields'] : '';

$pos1to1='';
$pos1ton='';
$pos1to1_array='';
$pos1ton_array='';

$pos_1to1_fields=($_POST['pos_1to1']);
$pos_1ton_fields=($_POST['pos_1ton']);    

$pos1to1_string='';
if($pos_1to1_fields!='')
{
	
	$pos1to1_array=explode( ",",$pos_1to1_fields  );

	$i=0;
	
	while($pos1to1_array[$i])
	{
	    if($pos1to1_array[$i]!='')
	    {	
	    	//$pos1to1_string.=$pos1to1_array[$i];
		$pos1to1_array[$i] = str_replace("1to1_","",$pos1to1_array[$i]);
		$pos1to1_string.=$pos1to1_array[$i].",";
	    }
	    $i++;
	}  
	   
}

$pos1to1_string=substr($pos1to1_string,0,-1);

$pos1to1_headers='';

if($pos1to1_string!='')
{
    $pos1to1_headers=explode( ',',$pos1to1_string  );
}


$pos1ton_string='';
if($pos_1ton_fields!='')
{
	
	$pos1ton_array=explode( ",",$pos_1ton_fields  );

	$i=0;
	
	while($pos1ton_array[$i])
	{
	    if($pos1ton_array[$i]!='')
	    {	
	    	//$pos1ton_string.=$pos1ton_array[$i];
		$pos1ton_array[$i] = str_replace("1ton_","",$pos1ton_array[$i]);
		$pos1ton_string.=$pos1ton_array[$i].",";
	    }
	    $i++;
	}  
	   
}

$pos1ton_string=substr($pos1ton_string,0,-1);

$pos1ton_headers='';

if($pos1ton_string!='')
{
    $pos1ton_headers=explode( ',',$pos1ton_string  );
}


$patientDataFields='';
$patientDataFields_array='';
if(isset($_POST['patientDataFields']) && $_POST['patientDataFields']!='')
{
	$patientDataFields=$_POST['patientDataFields'].",";
	$patientDataFields_array=explode( ',', $patientDataFields);
	$i=0;
	$patientDataFields_new='';
	while($patientDataFields_array[$i])
	{	    	
	    $patientDataFields_new.=', patient_data.'.$patientDataFields_array[$i];
	    $i++;
	}    

	$patientDataFields=$patientDataFields_new;
	$patientDataFields=trim($patientDataFields,',');
	$patientDataFields=rtrim($patientDataFields,',');

}

$providerId=(isset($_POST['providerId'])) ? $_POST['providerId'] : 0;
$payerId=(isset($_POST['payerId'])) ? $_POST['payerId'] : 0;

//$showPatients=sqlStatement("SELECT id,fname,lname,Year(DOB) as dob_year,street,city,state,country_code,phone_cell
//		            FROM patient_data 
//			    WHERE providerID=$providerId 
//			    AND providerID IN (SELECT addedby FROM tbl_allcare_query WHERE name='$queryName')
//			    ");


$getQueryStatement=sqlStatement("SELECT querystring FROM tbl_allcare_query WHERE name='$queryName'");

if(sqlNumRows($getQueryStatement)==1)
{
	$rowQuery=sqlFetchArray($getQueryStatement);
	
	$queryString=str_replace(';',' ',$rowQuery['querystring']);
       //$queryString=str_replace('*','1 ',$queryString);
	$queryString=str_replace('*','patient_data.* ',$queryString);
        
        if($patientDataFields!='')
        {
$queryString=str_replace(' from patient_data',' '.','.$patientDataFields.' from patient_data ',$queryString);
            
        }
        
        if($posFields!='')
        {
		//if($patientDataFields!='')
                if($patientDataFields=='')
		{	
			//$queryString=str_replace(' patient_data.*',' patient_data.*, ',$queryString);
                        //$queryString=str_replace(' patient_data.*',' ',$queryString);
		}
		

            $posAll='';$pos1to1Join='';$pos1tonJoin='';
            if($pos1to1!='')
            {
                $posAll=$pos1to1;
                $pos1to1Join=' INNER JOIN tbl_allcare_patients1to1 ON patient_data.id=tbl_allcare_patients1to1.pid';
            }
            
            if($pos1ton!='')
            {
                $posAll=$pos1to1.','.$pos1ton;
                $pos1tonJoin=' LEFT JOIN tbl_allcare_patients1ton ON patient_data.id=tbl_allcare_patients1ton.pid';
            }
                        
$queryString=str_replace(' from patient_data',' '.$posAll.' from patient_data '.$pos1to1Join.' '.$pos1tonJoin.' ',$queryString);

        }
        
	$append_where=(strpos($queryString,'where')) ? '' : ' WHERE ';
	
	$append_provider_id='';
	if(isset($_POST['providerId']))
	{
		$append_provider_id=($append_where=='') ? ' AND patient_data.providerID='.$providerId : ' patient_data.providerID='.$providerId;
	}

	$append_payer_id='';
	if(isset($_POST['payerId']))
	{
 	    $append_payerId_Query=' patient_data.pid IN (SELECT DISTINCT pid FROM insurance_data WHERE provider='.$payerId.')';	

		if(($append_where=='' || isset($_POST['providerId'])) || ($append_where!='' && isset($_POST['providerId'])))
		{
			$append_payer_id=' AND '.$append_payerId_Query;
		}
		else if($append_where!='' && !isset($_POST['providerId']))
		{
			$append_payer_id=' '.$append_payerId_Query;
		}			
		
	}
	
	if(!isset($_POST['providerId']) && !isset($_POST['payerId']))
	{
		$append_where='';
	}	
	
	$showPatients=sqlStatement($queryString. "".$append_where." ".$append_provider_id." ".$append_payer_id."");
	//echo "<br><br>".$queryString. "".$append_where." ".$append_provider_id." ".$append_payer_id."";
	if(sqlNumRows($showPatients)>0)
	{

	        echo "<table border='1'>";
	        echo "<th>Name</th><th>Age</th><th>Address</th><th>Contact</th>";            

		if(isset($patientDataFields_array))
		{
			$i=0;
			while($patientDataFields_array[$i])
			{   
			    echo "<th>".trim($patientDataFields_array[$i],',')."</th>";    
			    $i++;
			}   
		} 

        /*
		if(isset($pos1to1_array))
		{
			$i=0;
			while($pos1to1_array[$i])
			{
			    echo "<th>".$pos1to1_array[$i]."</th>";    
			    $i++;
			}       
		}         
		  
		if(isset($pos1ton_array))   
		{
			$i=0;
			while($pos1ton_array[$i])
			{
			    echo "<th>".$pos1ton_array[$i]."</th>";
			    $i++;
			}    
		}
*/
        
            if($pos_1to1_fields!='')
		{
			$i=0;
			while($pos1to1_headers[$i])
			{
			    echo "<th>".$pos1to1_headers[$i]."</th>";    
			    $i++;
			}       
		}         
		  
		if($pos_1ton_fields!='')
		{
			$i=0;
			while($pos1ton_headers[$i])
			{
			    echo "<th>".$pos1ton_headers[$i]."</th>";
			    $i++;
			}    
		}
            
	    $i=0;
	    while($rowPatients=sqlFetchArray($showPatients))
	    {
		$fname=(isset($rowPatients['fname'])) ? $rowPatients['fname'] : '';
		$lname=(isset($rowPatients['lname'])) ? $rowPatients['lname'] : '';

		$age=(isset($rowPatients['DOB'])) ? (date("Y")-date('Y', strtotime($rowPatients['DOB']))) : '';

		$street=(isset($rowPatients['street']) && $rowPatients['street']!='') ? ($rowPatients['street'].', ') : '';
		$city=(isset($rowPatients['city']) && $rowPatients['city']!='') ? ($rowPatients['city'].', ') : '';
		$state=(isset($rowPatients['state']) && $rowPatients['state']!='') ? ($rowPatients['state'].', ') : '';
$country_code=(isset($rowPatients['country_code']) && $rowPatients['country_code']!='') ? ($rowPatients['country_code'].', ') :  '';			
		$phone_home=(isset($rowPatients['phone_home']) && $rowPatients['phone_home']!='') ? ($rowPatients['phone_home'].' / ') : '';
		$phone_biz=(isset($rowPatients['phone_biz']) && $rowPatients['phone_biz']!='') ? ($rowPatients['phone_biz'].' / ') : '';
		$phone_contact=(isset($rowPatients['phone_contact']) && $rowPatients['phone_contact']!='') ? ($rowPatients['phone_contact'].' / ') : '';
		$phone_cell=(isset($rowPatients['phone_cell']) && $rowPatients['phone_cell']!='') ? $rowPatients['phone_cell'] : '';			

		echo "<tr>";
		echo "<td>".$fname." ".$lname."</td>";	           
		echo "<td>".$age."</td>";	           
		echo "<td>

		<input type='hidden' id='hdnDetails_$i' name='hdnDetails' 
	              value='".$fname." ".$lname."-<br>".$street.", ".$city.", ".$state.", ".$country_code."<br>".$phone_home.$phone_biz.$phone_contact.$phone_cell."' />

		      <input type='hidden' id='hdnAddress_$i' name='hdnAddress' 
	              value='".$street.", ".$city.", ".$state.", ".$country_code."' />".$street.$city.$state.$country_code."</td>";
		

		echo "<td>".$phone_home.$phone_biz.$phone_contact.$phone_cell."</td>";
                   
		$k=0;
                while($patientDataFields_array[$k])
                {   
                    echo "<td>".$rowPatients[$patientDataFields_array[$k]]."</td>";
                    $k++;
                }    

/*
                $k=0;
                while($pos1to1_array[$k])
                {   
                    echo "<td>".$rowPatients[$pos1to1_array[$k]]."</td>";
                    $k++;
                }    

                $k=1;
                while($pos1ton_array[$k])
                {
                    echo "<td>".$rowPatients[$pos1ton_array[$k]]."</td>";
                    $k++;
                }                                    */


		$k=0;
                while($pos1to1_headers[$k])
                {   
                    echo "<td>".$rowPatients[$pos1to1_headers[$k]]."</td>";
                    $k++;
                }    

                $k=0;
                while($pos1ton_headers[$k])
                {
                    echo "<td>".$rowPatients[$pos1ton_headers[$k]]."</td>";
                    $k++;
                }             


                echo "</tr>";
                    

	        $i++;
	    }                

	    echo "</table>";
	}
	else
	{
	    echo "No data found for the given provider";
	}        
        
}

else
{
	echo 'No result found for above query';
}



// Show Provider's address starts

if(isset($_POST['providerId']))
{
	$getProviderAddress=sqlStatement("SELECT fname,lname,street,city,state FROM users WHERE id=".$_POST['providerId']);	

	if(sqlNumRows($getProviderAddress)==1)
	{
		$rowProviderAddress=sqlFetchArray($getProviderAddress);
		echo "<input type='hidden' id='hdnProviderDetails' name='hdnProviderDetails' 
	              value='".$rowProviderAddress['fname']." ".$rowProviderAddress['lname']."-<br>".$rowProviderAddress['street']." ".$rowProviderAddress['city'].", ".$rowProviderAddress['state']."' />				

		      <input type='hidden' id='hdnProviderAddress' name='hdnProviderAddress' 
	              value='".$rowProviderAddress['street']." ".$rowProviderAddress['city'].", ".$rowProviderAddress['state']."' />";
					
	}

}

/////////////////////// Show Provider's address ////////////////////////

?>
