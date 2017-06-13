<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// Show today's appointments 

//print_r($_POST);

/////////////////  Show today's appointments ends   ////////////////////////
error_reporting(E_ALL);
$host	= 'mysql51-023.wc2.dfw1.stabletransit.com';
$port	= '3306';
$login	= '551948_alcrdfw';
$pass	= 'Alcrdfw@123';
$dbase	= '551948_alcrdfw';

$con = mysql_connect($host, $login, $pass);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
$isQueryChanged='Y';
$queryName='appointmentdue_f2f';

$db_selected = mysql_select_db($dbase, $con);
if (!$db_selected) {
    die ('Can\'t use foo : ' . mysql_error());
}

$getQueryStatement=mysql_query("SELECT querystring FROM tbl_allcare_query WHERE name LIKE '%$queryName%'",$con) or die (mysql_error());
if(mysql_num_rows($getQueryStatement))
{
    $rowQuery=mysql_fetch_assoc($getQueryStatement);

    $queryString=str_replace(';',' ',$rowQuery['querystring']);
    //$queryString=str_replace('*','1 ',$queryString);
    //$queryString=str_replace('*','patient_data.* ',$queryString);
    
    $append_provider_id='';
    $append_payer_id='';
    $append_visit_category_id='';
        
    //$queryString="SELECT * FROM patient_data";

    $showPatients=mysql_query($queryString,$con);
    
    
    if($isQueryChanged=='Y')
    { 
        $dropTemp=mysql_query("DROP TABLE IF EXISTS temp_scheduling",$con);
    }
    
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
     
    
    if($isQueryChanged=='Y')
    { 
        $createTemp=mysql_query($temp_table_query,$con); 
    }
    if(mysql_num_rows($showPatients)>0)
    {
        
        if($isQueryChanged=='Y')
        {
            $temp_values_string='';
            while($rowPatients=mysql_fetch_assoc($showPatients))
            {
                $temp_values=array();
                foreach($rowPatients as $val)
                {
                    $val=str_replace("'","\'",$val);
                    $val="'".$val."'";
                    array_push($temp_values,$val);
                }

                //$insertInTempTable=mysql_query ("INSERT INTO temp_scheduling VALUES(".implode($temp_values,',').")");
                $temp_values_string.='('.implode(',',$temp_values).'),';

            }

            $temp_values_string=rtrim($temp_values_string,',');
        //echo "<br>TVS=".$temp_values_string;
           echo "INSERT INTO temp_scheduling(".implode(',',$temporary_table_column_name).") VALUES ".$temp_values_string."";      
$insertInTempTable=mysql_query("INSERT INTO temp_scheduling(".implode(',',$temporary_table_column_name).")
                                 VALUES ".$temp_values_string."",$con);
        }
        
    }
    else
    {
        echo "No data found for the given provider";
    }

    /*
    if($isQueryChanged=='Y')
    {    
        $dropTemp=mysql_query ("DROP TABLE temp_scheduling");
    }*/
}

else
{
    echo 'No result found for above query';
}

/////////////////////// Show Provider's address ////////////////////////

?>
