<?php
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//
require_once("../../verify_session.php");

$pagename = "plist"; 
if(isset($_SESSION['portal_username']) !=''){
   $provider=$_SESSION['portal_username'];
}else {
   $provider=$_REQUEST['provider'];
   $refer=$_REQUEST['refer']; 
   $_SESSION['refer']=$_REQUEST['refer'];
   $_SESSION['portal_username']=$_REQUEST['provider'];
} 

$base_url="//".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/';

 $sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id=sqlFetchArray($sql);
$id1=$id['id'];

?>


<p><button onclick="javascript:sync_payer_plan_db_data();">Sync Payer Plan DB </button></p>
<div id="clicknexttab"></div>
<div id="metadatamapping" name="metadatamapping"> 
    <?php
    $emr_insurance_meta = array();
    $get_insurance_columns = sqlStatement("SHOW COLUMNS FROM insurance_companies ");
    while($set_insurance_columns = sqlFetchArray($get_insurance_columns)){
        $emr_insurance_meta[] = $set_insurance_columns['Field'];
    }
    $address_data = sqlStatement("SHOW COLUMNS FROM addresses");
    while($set_address_data = sqlFetchArray($address_data)){
        $emr_insurance_meta[] = $set_address_data['Field'];
    }

    ?>    
    <div id='saveinsurance_metakey_mapping' name='saveinsurance_metakey_mapping'></div>
    <table border='1' style="border-collapse: collapse" class="insu_payer_mapping" >
        <thead>
            <tr>
                <th>EMR Field Title</th>
                <th>EMR Database Meta Key</th>
                <th>Payer Plan Meta Key</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $k = 0;
            foreach($emr_insurance_meta as $ikey=>$ivalue){
                
                if($ivalue !== 'id'  && $ivalue !== 'foreign_id'  && $ivalue !== 'name' && $ivalue !== 'alt_cms_id' && $ivalue !== 'plus_four' && $ivalue !== 'x12_default_partner_id'){
                    ?>
                    <tr>
                        <?php if($ivalue == 'cms_id') { ?>
                            <td>CMS ID</td>
                        <?php } else if($ivalue == 'line1') { ?>
                            <td>Address 1</td>
                        <?php } else if($ivalue == 'line2') { ?>
                            <td>Address 2</td>
                        <?php } else if($ivalue == 'x12_receiver_id') { ?>
                            <td>Default X12 Partner</td>
                        <?php } else if($ivalue == 'freeb_type') { ?>
                            <td>Payer Type</td>    
                        <?php }else{ ?>
                            <td><?php echo ucwords(str_replace("_"," ",$ivalue )); ?></td>
                        <?php } ?>
                        <td><?php echo $ivalue; ?></td>
                        <td>
                            <?php
                            $get_meta_key_mapping = sqlStatement("SELECT DISTINCT meta_key FROM wp_postmeta pm INNER JOIN wp_posts p ON p.ID = pm.post_id WHERE p.post_type='payer' ORDER BY meta_key ASC")
                            ?>
                            <select id='insurance_metakey_mapping<?php echo $k;?>' name='insurance_metakey_mapping<?php echo $k;?>' class='insu_payer_mapping' onchange="save_insurance_metakey_mapping('<?php echo $k;?>','<?php echo $ivalue; ?>');">
                                <option value=''>Select</option>
                                <?php
                                while($set_meta_key_mapping = sqlFetchArray($get_meta_key_mapping)){
                                    ?>
                                    <option value='<?php echo str_replace(" ","$",$set_meta_key_mapping['meta_key']); ?>' 
                                    <?php       
                                        $get_meta_check = sqlStatement("SELECT payerplan_meta_key FROM tbl_insurance_payerplan_meta_mapping WHERE emr_meta_key = '$ivalue'");   
                                        $db_payer_meta_key = ''; 
                                        while($set_meta_check = sqlFetchArray($get_meta_check)){
                                            $db_payer_meta_key = $set_meta_check['payerplan_meta_key'];
                                        }
                                        if($db_payer_meta_key == $set_meta_key_mapping['meta_key'] )
                                            echo " selected ";
                                    ?>
                                    ><?php echo $set_meta_key_mapping['meta_key'];?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php 
                }
                $k++;
            } 
            ?>
        </tbody>
    </table>
    <!-- meta log -->

    <?php

    $get_log_data = sqlStatement("select updated_log FROM tbl_insurance_payerplan_meta_mapping ORDER BY id");
    echo "<div id='clicklog' >";
    echo "<b>Log Table</b>";
    echo "<table id='details' border='1' style='display:none;'>";
    echo "<tr>";
    echo "<th>EMR Meta Key</th>";
    echo "<th>Payer Plan Meta Key</th>";
    echo "<th>Date</th>";
    echo "<th>Action</th>";
    echo "<th>Username</th>";
    echo "<th>IP Address</th>";
    echo "</tr>";
    while($set_log_data = sqlFetchArray($get_log_data)){
        $log_data = unserialize($set_log_data['updated_log']);
        $count = count($log_data);
        $value = '';
        foreach ($log_data as $value) {
            ?>
                <tr>
                    <td style="width:20"><?php echo $value['meta_key'];     ?></td>
                    <td style="width:20"><?php echo $value['meta'];         ?></td>
                    <td style="width:20"><?php echo $value['action'];       ?></td>
                    <td style="width:20"><?php echo $value['date'];         ?></td>
                    <td style="width:20"><?php echo $value['authuser'];     ?></td>
                    <td style="width:20"><?php echo $value['ip_address'];   ?></td>
               </tr>
            <?php
        }
    }
    echo "</table>";
    echo "</div><br><br>";
    ?>
</div>