<?php
require_once('../interface/globals.php');
require_once("$srcdir/formdata.inc.php");

$provider=$_REQUEST['provider_id'];
$pid=$_REQUEST['pid'];
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
$get_id= sqlStatement("SELECT id FROM users WHERE  `username`='$provider' and authorized=1");
            $stmt = sqlFetchArray($get_id);
            $provider_id=$stmt['id'];
            
            

//patient_list
if($pid!=''){       
function getIncompleteEncounterList($pid,$uid){
    
	 // to get vist category list
            $get_fuv = sqlStatement("SELECT visit_categories FROM tbl_allcare_facuservisit WHERE  `users`  REGEXP ('".":\"".$uid."\"')");
            $fuv_stmt = sqlFetchArray($get_fuv) ;
          while($fuv_stmt = sqlFetchArray($get_fuv)) {
            $set_fuv []= $fuv_stmt; 
            } 
            for($i = 0; $i<count($set_fuv); $i++){
               $array[] =  unserialize($set_fuv[$i]['visit_categories']);
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
            $sql = sqlStatement("SELECT form_encounter.facility,form_encounter.facility_id, form_encounter.encounter,form_encounter.pc_catid AS visitcategory_id,DATE_FORMAT( form_encounter.date,  '%Y-%m-%d' ) AS dos, patient_data.providerID as provider_id
                        FROM form_encounter
                        INNER JOIN patient_data ON patient_data.pid = form_encounter.pid
                        WHERE form_encounter.pid =$pid AND pc_catid IN ($enc_value)
                            AND (
                             `elec_signed_on` = '' AND `elec_signedby` = ''
                            ) ORDER BY form_encounter.date");
            while($stmt = sqlFetchArray($sql)){
            
            $formlabels[] = $stmt; 
            }
            //echo "<pre>"; print_r($formlabels); echo "</pre>";
            $formfields = array();
            $formvalues = array();
            $datacheck8= array();
            $datacheck7 = array();
            $dataArr = array();
            foreach($formlabels as $element): 
                $sql6 = sqlStatement("SELECT DISTINCT(fe.encounter),CONCAT(pd.title,pd.fname,' ',pd.lname) as pname,DATE_FORMAT( fe.date,  '%Y-%m-%d' ) AS dos, fe.facility AS facility, fe.pid AS pid, fe.pc_catid AS visitcategory_id,oe.pc_catname as visitcategory,audited_status
                    FROM form_encounter fe
                    INNER JOIN patient_data pd on pd.pid = fe.pid
                    INNER JOIN openemr_postcalendar_categories oe ON oe.pc_catid = fe.pc_catid
                    WHERE fe.pid = $pid and fe.encounter = '".$element['encounter']."'
                    AND (
                     `elec_signed_on` = '' AND `elec_signedby` = ''
                    )  ");
                    while($stmt6 = sqlFetchArray($sql6)){
                       $datacheck6[] = $stmt6;
                    }
                    if(!empty($datacheck6)):
                        $datacheck6[0]->form_status = 'Incomplete';
                        $datacheck7['finalizetype'] = 'checkbox';
                        if($datacheck6[0]->audited_status == 'Completed'){
                            $datacheck7['isfinalize'] = 'Enable';
                            $datacheck7['title'] = 'Finalize';
                            $datacheck6[0]->finalize = $datacheck7;
                        }else{
                            $datacheck6[0]->audited_status = 'Incomplete';
                            $datacheck7['isfinalize'] = 'Disable';
                            $datacheck7['title'] = 'Finalize';
                            $datacheck6[0]->finalize = $datacheck7;
                        }
                        //$datacheck6[0]->finalize_field_type = $datacheck7;
                        
                    endif;
                $formfields = $datacheck6;

            endforeach;
            
          //  echo "<pre>"; print_r($formfields); echo "</pre>";
  return $formfields;
}
}            
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Medical Website Template | News :: W3layouts</title>
		<link href="css/style.css" rel="stylesheet" type="text/css"  media="all" />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
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

                                                    <h3>Incomplete Encounter Count : <?php  echo getIncompleteEncounterCount($provider_id);    ?></h3>
                                                 <?php $result= getIncompleteEncounterList($pid,$provider_id); ?>
                                                <table border="1">
                                                    <thead style='background-color:#007DAD; height:50px;'><tr>
                                                        <th style="padding: 10px 18px; ">Patient_id</th><th style=" padding: 10px 18px; ">Patient_name</th><th style="padding: 10px 18px;">Date_of_service</th><th style=" padding: 10px 18px;">Encounter</th><th style=" padding: 10px 18px;">Visit_category</th><th style=" padding: 10px 18px;">Facility</th><th style=" padding: 10px 18px;">Audit_status</th>
                                                        </tr></thead>

                                                      <?php    for($i = 0; $i<count($result); $i++){?>
                                                     <tr>
                                                     <td style=" padding: 5px; "><?php echo $result[$i]['pid']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['pname']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['dos']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['encounter']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['visitcategory']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['facility']; ?></td>
                                                     <td style="padding: 5px ; "><?php echo $result[$i]['audited_status']; ?></td></tr>
                                                <?php }?>
                                                  </table>
                                               </div>

						<div class="clear"> </div>
					</div>
				<div class="clear"> </div>
				</div>
			<!----End-content----->
		</div>
		<!---End-wrap---->
               
	</body>
</html>

