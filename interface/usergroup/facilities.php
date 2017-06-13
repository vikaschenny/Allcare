<?php
require_once("../globals.php");
require_once("../../library/acl.inc");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formdata.inc.php");

require_once("{$GLOBALS['srcdir']}/sqlCentralDB.inc"); // This is to connect central db to insert/update patient data in central db
global $sqlconfCentralDB; // This is declared in central db connection

$alertmsg = '';

// Get practice ID
$practiceId = '';
$query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practiceID'");
while($row = sqlFetchArray($query)){
    $practiceId = $row['title'];
}
//Data sync flag
$datasync = 1; // 1 = practice could be in sync with central db; 0 = This is a standalone practice which should not be in sync with central db
$query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practicetocentral'");
while($row = sqlFetchArray($query)){
    $datasync = $row['title'];
}
if(trim(formData('fid')) != ''):
    $sql2 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".trim(formData('fid'))." AND objecttype = 'Facility'";
    $r = sqlStatement($sql2);
    $objref = "";
    while($row2 = sqlFetchArray($r)):
       $objref = $row2['objectref'];
    endwhile;
    $count = sqlNumRows($r);
endif;    

/*		Inserting New facility					*/
if (isset($_POST["mode"]) && $_POST["mode"] == "facility" && $_POST["newmode"] != "admin_facility") {
  $insert_id=sqlInsert("INSERT INTO facility SET " .
  "name = '"         . trim(formData('facility'    )) . "', " .
  "phone = '"        . trim(formData('phone'       )) . "', " .
  "fax = '"          . trim(formData('fax'         )) . "', " .
  "street = '"       . trim(formData('street'      )) . "', " .
  "city = '"         . trim(formData('city'        )) . "', " .
  "state = '"        . trim(formData('state'       )) . "', " .
  "postal_code = '"  . trim(formData('postal_code' )) . "', " .
  "country_code = '" . trim(formData('country_code')) . "', " .
  "federal_ein = '"  . trim(formData('federal_ein' )) . "', " .
  "website = '"      . trim(formData('website'     )) . "', " .
  "email = '"      	 . trim(formData('email'       )) . "', " .
  "color = '"  . trim(formData('ncolor' )) . "', " .
  "service_location = '"  . trim(formData('service_location' )) . "', " . 
  "billing_location = '"  . trim(formData('billing_location' )) . "', " .
  "accepts_assignment = '"  . trim(formData('accepts_assignment' )) . "', " .
  "pos_code = '"  . trim(formData('pos_code' )) . "', " .
  "domain_identifier = '"  . trim(formData('domain_identifier' )) . "', " .
  "attn = '"  . trim(formData('attn' )) . "', " .
  "tax_id_type = '"  . trim(formData('tax_id_type' )) . "', " .
  "primary_business_entity = '"  . trim(formData('primary_business_entity' )) . "', ".
  "facility_npi = '" . trim(formData('facility_npi')) . "'");
  
  
   $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://'; 
  //get user id 
$uid=sqlStatement("select max(id)as id from facility");
$uidrow=sqlFetchArray($uid);
$sql = sqlStatement("select * from list_options where list_id='AllcareDriveSync' and option_id='email'");
$row = sqlFetchArray($sql);
$selection = sqlStatement("select * from tbl_drivesync_authentication where email='" . $row['notes'] . "' order by id desc");
$sel_rows = sqlFetchArray($selection);
if($sel_rows['facility_folder_trigger']=='yes'){
    
       // to get configured email
                 if($sel_rows['facility_parent_folder']!='')
                 $parentid = str_replace('https://drive.google.com/drive/folders/', '', $sel_rows['facility_parent_folder']);
                else
                 $parentid='root';   

                if($sel_rows['facility_folder_query']!='')
                {
                    $query = $sel_rows['facility_folder_query'] . " where id=" . $uidrow['id'] ;
                    $fsql = sqlStatement("$query");
                    $frow = sqlFetchArray($fsql);
                    $folder_name = str_replace(" ", "_", $frow['fac_folder']);  
                }
                $curl = curl_init();
                $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes'].'/'.$parentid.'/'.$folder_name;
                curl_setopt($curl,CURLOPT_URL, $form_url2);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                $result = curl_exec($curl);
                $resultant = $result;
                curl_close($curl);
                $val= explode(':',$resultant);
                if($val[0]!=''){
                         $link='https://drive.google.com/drive/folders/'.$val[0];
                         $today = date("Y-m-d"); 
                         $ins=sqlStatement("insert into tbl_facility_custom_attr_1to1 (facilityid,facilityfolder,created_date)values('".$uidrow['id']."','".$val[0]."','$today')");
                         $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$uidrow['id']."','$link','','','folder_created(during Facility Creation in EMR)','','facility')");
                    //subfolders
                    if($sel_rows['facility_sub_folder']=='yes'){
                        $subsql = sqlStatement("select * from list_options where list_id='AllcareFacilitySubfolders'");
                        $link1='';
                        $row=mysql_num_rows($subsql);
                        if($row!=0){
                        while($subrow = sqlFetchArray($subsql)){
                            $curl = curl_init();
                            $form_url2 = $protocol.$_SERVER['HTTP_HOST'].'/api/DriveSync/insert_folder_web/'.$row['notes'].'/'.$val[0].'/'.str_replace(" ","_",$subrow['title']);
                            curl_setopt($curl,CURLOPT_URL, $form_url2);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                            $result = curl_exec($curl);
                            $subresultant = $result;
                            curl_close($curl);
                            $subval= explode(':',$subresultant);
                            $link1.='https://drive.google.com/drive/folders/'.$subval[0]."||";
                        }
                         $ins_log=sqlStatement("insert into DriveSync_log (date,user,email,encounter,patient_id,google_folder,file_name,file_id,status,watsID,category)values(now(),'".$_SESSION['authUser']."','".$row['notes']."','','".$uidrow['id']."','$link1','','','subfolder_created(during facility Creation in EMR)','','facility')");
                    }
                    }
                         
                }
    
        }
        if($datasync == 1):
            $sql = "INSERT INTO allcareobjects (practiceId,facility_name,phone,fax,street,city,state,zip,country,website,uemail,service_location,billing_location,
                                                    pos_code,attn,domain_identifier,facility_npi,primary_business_entity,objecttype) 
                    VALUES('".$practiceId."','".trim(formData('facility'    ))."','".trim(formData('phone'    ))."','".trim(formData('fax'    ))."','".trim(formData('street'    ))."','".trim(formData('city'    ))."','".trim(formData('state'    ))."','".trim(formData('postal_code'    ))."','".trim(formData('country_code'    ))."',
                        '".trim(formData('website'    ))."', '".trim(formData('email'    ))."', '".trim(formData('service_location'    ))."', '".trim(formData('billing_location'    ))."',
                        '".trim(formData('pos_code'    ))."','".trim(formData('attn'    ))."','".trim(formData('domain_identifier'    ))."','".trim(formData('facility_npi'    ))."','".trim(formData('primary_business_entity'    ))."','Facility')";
            $stmt = $sqlconfCentralDB->prepare($sql) ;
            $stmt->execute();
            $id = $sqlconfCentralDB->lastInsertId();

            if($count == 0):
                sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".$insert_id.",".$id.",'Facility')");
            endif;
        endif;    
}

/*		Editing existing facility					*/
if ($_POST["mode"] == "facility" && $_POST["newmode"] == "admin_facility")
{
	sqlStatement("update facility set
		name='" . trim(formData('facility')) . "',
		phone='" . trim(formData('phone')) . "',
		fax='" . trim(formData('fax')) . "',
		street='" . trim(formData('street')) . "',
		city='" . trim(formData('city')) . "',
		state='" . trim(formData('state')) . "',
		postal_code='" . trim(formData('postal_code')) . "',
		country_code='" . trim(formData('country_code')) . "',
		federal_ein='" . trim(formData('federal_ein')) . "',
		website='" . trim(formData('website')) . "',
		email='" . trim(formData('email')) . "',
		color='" . trim(formData('ncolor')) . "',
		service_location='" . trim(formData('service_location')) . "',
		billing_location='" . trim(formData('billing_location')) . "',
		accepts_assignment='" . trim(formData('accepts_assignment')) . "',
		pos_code='" . trim(formData('pos_code')) . "',
		domain_identifier='" . trim(formData('domain_identifier')) . "',
		facility_npi='" . trim(formData('facility_npi')) . "',
		attn='" . trim(formData('attn')) . "' ,
		primary_business_entity='" . trim(formData('primary_business_entity')) . "' ,
		tax_id_type='" . trim(formData('tax_id_type')) . "' 
	where id='" . trim(formData('fid')) . "'" );
        
        if($datasync == 1):
            $sql = "UPDATE allcareobjects SET facility_id='".trim(formData('fid'))."',
                                                  facility_name='".trim(formData('facility'))."',
                                                  phone='".trim(formData('phone'))."',
                                                  fax='".trim(formData('fax'))."',    
                                                  street='".trim(formData('street'))."',
                                                  city='".trim(formData('city'))."',
                                                  state='".trim(formData('state'))."',
                                                  zip='".trim(formData('postal_code'))."',
                                                  country='".trim(formData('country_code'))."',
                                                  website='".trim(formData('website'))."',
                                                  uemail='".trim(formData('email'))."',
                                                  service_location='".trim(formData('service_location'))."',
                                                  billing_location='".trim(formData('billing_location'))."',
                                                  pos_code='".trim(formData('pos_code'))."',
                                                  attn='".trim(formData('attn'))."',
                                                  domain_identifier='".trim(formData('domain_identifier'))."',
                                                  facility_npi='".trim(formData('facility_npi'))."',
                                                  primary_business_entity='".trim(formData('primary_business_entity'))."',
                                                  practiceId='".$practiceId."'      
                                                  WHERE id=".$objref;
            $stmt = $sqlconfCentralDB->prepare($sql) ;
            $stmt->execute(); 
        endif;   
}

?>
<html>
<head>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui.js"></script>

<script type="text/javascript">


$(document).ready(function(){

    // fancy box
    enable_modals();

    // special size for
	$(".addfac_modal").fancybox( {
		'overlayOpacity' : 0.0,
		'showCloseButton' : true,
		'frameHeight' : 460,
		'frameWidth' : 650
	});

    // special size for
	$(".medium_modal").fancybox( {
		'overlayOpacity' : 0.0,
		'showCloseButton' : true,
		'frameHeight' : 460,
		'frameWidth' : 650
	});

});

</script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
</head>

<body class="body_top">

<div>
    <div>
	<table><tr><td>
        <b><?php xl('Facilities','e'); ?></b>&nbsp;</td><td>
		 <a href="facilities_add.php" class="iframe addfac_modal css_button"><span><?php xl('Add','e');?></span></a>
		 </td>
                 <td>
		 <a href="facilities_dropdown_1to1.php" class="css_button"><span><?php xl('Facility Custom Attributes','e');?></span></a>
		 </td>
                 <td>
		 <a href="facUserVisitcat_mapping.php" class="css_button"><span><?php xl('Facilty based mapping with Users and Visit Category','e');?></span></a>
		 </td>
                 <td>
		 <a href="visitcat_codegrp_mapping.php" class="css_button"><span><?php xl('Visit Category Code Group option mapping','e');?></span></a>
		 </td>
                 
            </tr>
	</table>
    </div>
    <div class="tabContainer" style="width:550px;">
        <div>
<table cellpadding="1" cellspacing="0" class="showborder">
	<tr class="showborder_head" height="22">
		<th style="border-style:1px solid #000" width="140px"><?php xl('Name','e'); ?></th>
		<th style="border-style:1px solid #000" width="320px"><?php xl('Address','e'); ?></th>
		<th style="border-style:1px solid #000"><?php xl('Phone','e'); ?></th>
    </tr>
     <?php
        $fres = 0;
        $fres = sqlStatement("select * from facility order by name");
        if ($fres) {
          $result2 = array();
          for ($iter3 = 0;$frow = sqlFetchArray($fres);$iter3++)
            $result2[$iter3] = $frow;
          foreach($result2 as $iter3) {
			$varstreet="";//these are assigned conditionally below,blank assignment is done so that old values doesn't get propagated to next level.
			$varcity="";
			$varstate="";
          $varstreet=$iter3{street };
          if ($iter3{street }!="")$varstreet=$iter3{street }.",";
          if ($iter3{city}!="")$varcity=$iter3{city}.",";
          if ($iter3{state}!="")$varstate=$iter3{state}.",";
    ?>
    <tr height="22">
       <td valign="top" class="text"><b><a href="facility_admin.php?fid=<?php echo $iter3{id};?>" class="iframe medium_modal"><span><?php echo htmlspecialchars($iter3{name});?></span></a></b>&nbsp;</td>
       <td valign="top" class="text"><?php echo htmlspecialchars($varstreet.$varcity.$varstate.$iter3{country_code}." ".$iter3{postal_code}); ?>&nbsp;</td>
       <td><?php echo htmlspecialchars($iter3{phone});?>&nbsp;</td>
    </tr>
<?php
  }
}
 if (count($result2)<=0)
  {?>
  <tr height="25">
		<td colspan="3"  style="text-align:center;font-weight:bold;"> <?php echo xl( "Currently there are no facilities." ); ?></td>
	</tr>
  <?php }
?>
	</table>
        </div>
    </div>
</div>
<script language="JavaScript">
<?php
  if ($alertmsg = trim($alertmsg)) {
    echo "alert('$alertmsg');\n";
  }
?>
</script>

</body>
</html>
