<?php
/**
 * Copyright (C) 2010 OpenEMR Support LLC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * 2013/02/08 Minor tweaks by EMR Direct to allow integration with Direct messaging
 * 2013-03-27 by sunsetsystems: Fixed some weirdness with assigning a message recipient,
 *   and allowing a message to be closed with a new note appended and no recipient.
 */
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../interface/globals.php");
require_once("../library/formdata.inc.php");
require_once("../library/globals.inc.php");

$provider=$_REQUEST['provider'];
$pid=$_REQUEST['pid'];

$get_id= sqlStatement("SELECT id FROM users WHERE  `username`='$provider' and authorized=1");
            $stmt = sqlFetchArray($get_id);
            $provider_id=$stmt['id'];
            
function getIncompleteEncounterCount($providerid){
   
            $array = array();
            $count= '';
            $array_res = array();
            // get visit_categories list 
            $get_fuv = sqlStatement("SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$providerid."\"')");
            while($fuv_stmt = sqlFetchArray($get_fuv)) {
            $set_fuv []= $fuv_stmt; 
            }
            //print_r($set_fuv);
         
            for($i = 0; $i<count($set_fuv); $i++){
               $array[] =  unserialize( $set_fuv[$i]['visit_categories']);
            }
            $dataArray = array();
            for($j = 0; $j<count($array); $j++){
                foreach($array[$j] as $arraykey){
                     $dataArray[] = $arraykey;
                }
            }
            $enc_val = '';
            $dataarray = array_unique($dataArray);
            foreach($dataarray as $arrayval){
                $enc_val .= $arrayval.",";
            }
            $enc_value = rtrim($enc_val,",");
            if(!empty($enc_value)){
            //$sql = "SELECT count(id) as count FROM `form_encounter`  WHERE (`sensitivity` <> 'finalized' OR `sensitivity` IS NULL) AND provider_id=$providerid ";
                $sql = sqlStatement("SELECT count(f.id) as count FROM `form_encounter` f INNER JOIN patient_data p ON p.pid = f.pid WHERE (`elec_signed_on` = '' AND `elec_signedby` = '')AND p.deceased_stat !=  'YES'
                 AND p.practice_status =  'YES' AND p.providerID=$providerid AND pc_catid IN ($enc_value)");
               $stmt = sqlFetchArray($sql);
               $count = $stmt['count'];        
                }
     return $count;
}

            
            
function getPatientIncompleteEncounterCount($providerid){
   
	
         
            $count = '';
            // to get visit categories list
            $get_fuv = sqlStatement("SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$providerid."\"')");
            $fuv_stmt = sqlFetchArray($get_fuv) ;
             while($fuv_stmt = sqlFetchArray($get_fuv)) {
               $set_fuv []= $fuv_stmt; 
            }
            for($i = 0; $i<count($set_fuv); $i++){
               $array[] =  unserialize( $set_fuv[$i]['visit_categories']);
            }
            if(!empty($array)){
                $dataArray = array();
                for($j = 0; $j<count($array); $j++){
                    foreach($array[$j] as $arraykey){
                         $dataArray[] = $arraykey;
                    }
                }
                $enc_val = '';
                $dataarray = array_unique($dataArray);
                foreach($dataarray as $arrayval){
                    $enc_val .= $arrayval.",";
                }
                $enc_value = rtrim($enc_val,",");

                $sql =sqlStatement( "SELECT f.pid, p.lname, p.fname, GROUP_CONCAT( CASE WHEN (DATE_FORMAT( f.date, '%Y-%m-%d' ) <> '0000-00-00') THEN DATE_FORMAT( f.date, '%Y-%m-%d' ) END  ORDER BY f.date ASC) AS dos, COUNT( f.id ) AS encounter_count,  COUNT( f.id ) AS visit_count
                    FROM form_encounter f
                    INNER JOIN patient_data p ON p.pid = f.pid
                    WHERE (
                    `elec_signed_on` = '' AND `elec_signedby` = ''
                    )
                    AND p.providerID =$providerid
                    AND  p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND f.pc_catid IN ($enc_value)
                    GROUP BY p.lname ");
                while($stmt = sqlFetchArray($sql)){
                  $count[] = $stmt;  
                }
            }
            //print_r($count);
      return $count;
          
       }            
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Medical Website Template | News :: W3layouts</title>
		<link href="css/style.css" rel="stylesheet" type="text/css"  media="all" />
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/jquery.dataTables.css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.tableTools.css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colVis.css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colReorder.css'>
                <style>
                div.DTTT_container {
                        float: none;
                }
                </style>
                <script type='text/javascript' src='../interface/main/js/jquery-1.11.1.min.js'></script>
<!--                <script type='text/javascript' src='../interface/main/js/jquery.dataTables.min.js'></script>-->
                <script type='text/javascript' src='../interface/main/js/jquery.dataTables-1.10.7.min.js'></script>
                <script type='text/javascript' src='../interface/main/js/dataTables.tableTools.js'></script>
                <script type='text/javascript' src='../interface/main/js/dataTables.colReorder.js'></script>
                <script type='text/javascript' src='../interface/main/js/dataTables.colVis.js'></script>
	</head>
	<body>
		<!---start-wrap---->
		
			<!---start-header---->
			<div class="header">
				
					<div class="main-header">
						<div class="wrap">
							<div class="logo">
								<a href="index.html"><img src="images/logo.png" title="logo" /></a>
							</div>
							<div class="social-links">
								<ul>
									
                                                                      <li class="login"><a href="logout_page.php">Logout</a></li>
									<div class="clear"> </div>
								</ul>
							</div>
							<div class="clear"> </div>
						</div>
					</div>
					<div class="clear"> </div>
				             <div id='cssmenu1'>
                                            <?php $sql12=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' ORDER BY seq");?>
                                              <ul>   
                                                 <?php while($row11=sqlFetchArray($sql12)){ 
                                                        $mystring = $row11['option_id'];
                                                        $pos = strpos($mystring, '_');
                                                        if(false == $pos) {
                                                                $sql_lis=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$mystring' ORDER BY seq");
                                                                while($row_lis=sqlFetchArray($sql_lis)){
                                                                $opt_id=$row_lis['option_id']."_";
                                                                $sql_li=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id LIKE '%$opt_id%' ORDER BY seq");
                                                                if(sqlNumRows($sql_li) != 0 ){ ?>
                                                                     <li <?php if($row11['option_id']=='incomp'){ ?> class='active has-sub' <?php } else {  ?> class='has-sub' <?php } ?>><a href="<?php echo $row_lis['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row_lis['title']; ?></span></a>
                                                                     <ul>
                                                                 <?php while($row_li=sqlFetchArray($sql_li)){ 
                                                                             $ex=explode("_",$row_li['option_id']); 
                                                                             if(count($ex)==2){
                                                                                   $sub1=$ex[0]."_".$ex[1];
                                                                                   $sql_sub=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$sub1' ORDER BY seq");
                                                                                   $row_sub=sqlFetchArray($sql_sub);
                                                                                   ?>
                                                                                    <li class=last'><a href="<?php echo $row_sub['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                                   </li>   
                                                                            <?php   } ?>
                                                                             
                                                                    <?php } ?> </ul></li>
                                                                <?php }else { 
                                                                    ?>
                                                                     <li><a href="<?php echo $row11['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row11['title']; ?></span></a></li>
                                                               <?php 
                                                                
                                                                }
                                                               
                                                            }    
                                                         }
                                                         
                                                    } ?>
                                              </ul>      
                                        </div>
			</div>
			<!---End-header---->
			<!----start-content----->
			<div class="content">
				<div class="wrap">
					<div class="services">
						<div class="service-content">
						   
                                                        <h3>Incomplete Encounter Count : <?php echo getIncompleteEncounterCount($provider_id);             
                                                     ?></h3><?php $result1=getPatientIncompleteEncounterCount($provider_id); ?>
                                                        <table border='1' border-color='#007DAD !important'>
                                                           <thead style='background-color:#007DAD;'><tr>
                                                                
                                                                <th style="width:20%">Patient_id</th><th style="width:30%">Patient_name</th><th style="width:50%">Date_of_service</th><th style="width:30%">Encounter Count</th><th style="width:30%">Visit Count</th>  </tr>

                                                              <?php    for($i = 0; $i<count($result1); $i++){?>
                                                             </thead><tr>
                                                             <td style="width:20%"><?php echo $result1[$i]['pid']; ?></td>
                                                             <td style="width:30%"><a href="patient_incomplete_enc.php?pid=<?php echo $result1[$i]['pid']; ?>&provider_id=<?php echo $provider; ?>"><?php echo $result1[$i]['lname']." ".$result1[$i]['fname']; ?></a></td>
                                                             <td style="width:50%"><?php echo $result1[$i]['dos']; ?></td>
                                                             <td style="width:20%"><?php echo $result1[$i]['encounter_count']; ?></td>
                                                             <td style="width:20%"><?php echo $result1[$i]['visit_count']; ?></td>
                                                             </tr>
                                                              <?php } ?>
                                                        </table>
 
                                                    
						</div>

						<div class="clear"> </div>
					</div>
				<div class="clear"> </div>
				</div>
			<!----End-content----->
		</div>
		<!---End-wrap---->
                <script type='text/javascript'>
            
            $(document).ready( function () {
//                $('#patient_data').DataTable( {
//                    dom: 'T<"clear">lfrtip',
//                    "tableTools": {
//                        "sSwfPath": "../../swf/copy_csv_xls_pdf.swf",
//                        "aButtons": [
//                            {
//                                "sExtends": "xls",
//                                "sButtonText": "Save to Excel"
//                            }
//                        ]
//                    }
//                } );
              $('#patient_data tfoot th').each( function () {
        var title = $('#patient_data thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#patient_data').DataTable({ "iDisplayLength": 100});
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            that
                .search( this.value )
                .draw();
        } );
    } );
            } );
    </script>
	</body>
</html>

