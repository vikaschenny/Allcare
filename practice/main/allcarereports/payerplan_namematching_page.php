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

<div style="height:20px;"><p id = 'savespan' name = 'savespan' style="text-align:center;font-weight: bold;"></p></div>
    <table width='100%' border='1' style="border-collapse: collapse" class="payerplanclass">
        <thead width='100%'>
            <tr>
                <th>EMR Insurance Company Name</th>
                <th>Payer Plan DB: Claim Payer Name</th>
                <th>Payer Plan DB: Eligibility Payer Name</th>
            </tr>
        </thead>
        <tbody width='100%'>
        <?php
        $payer_plan_array = array();
        // to get payer plan db practice payers
        $get_payer_plan_payers = sqlStatement("SELECT * FROM wp_posts p INNER JOIN wp_postmeta pm WHERE pm.post_id = p.ID  AND post_id = (SELECT post_id FROM wp_postmeta WHERE meta_key = 'practice_id' AND ID = post_id) AND meta_key='Claim Payer ID' AND post_type='payer' ORDER BY id ");
        $i = 1;
        while($set_payer_plan_payers = sqlFetchArray($get_payer_plan_payers)){
            $payer_plan_array[$i]['payer_id'] = $set_payer_plan_payers['meta_value'];
            $payer_plan_array[$i]['title']    = $set_payer_plan_payers['post_title'];
            $i++;
        }
        $j = 0; 

        // to get emr payers
        $get_emr_payers = sqlStatement("SELECT id, name, payer_id FROM insurance_companies ORDER BY id");
        while($set_emr_payers = sqlFetchArray($get_emr_payers)){
            ?>
                <tr>
                    <td>
                        <?php echo $set_emr_payers['name']; ?>
                    </td>
                    <td>
                        <?php 
                        $mapped_id              = 0;
                        $payerplan_payer_id     = '';
                        $payerplan_payer_name   = '';

                        // get payer plan mapping data
                        $get_mapping_payer = sqlStatement("SELECT id, payerplan_payer_id, payerplan_payer_name FROM tbl_payerplan_emrpayerplan_mapping WHERE emr_payer_id = '".$set_emr_payers['id']."'");
                        while($set_mapping_payer = sqlFetchArray($get_mapping_payer)){
                            $mapped_id              = $set_mapping_payer['id'];
                            $payerplan_payer_id     = $set_mapping_payer['payerplan_payer_id'];
                            $payerplan_payer_name   = $set_mapping_payer['payerplan_payer_name'];
                        }
                        ?>
                        <input type = 'hidden' name = 'hiddenpayerplanTypesid<?php echo $j; ?>' id = 'hiddenpayerplanTypesid<?php echo $j; ?>' value = '<?php echo $mapped_id; ?>'>
                        <select class='payerplanTypes' id="payerplanTypes<?php echo $j; ?>" onchange="save_payer_plan('<?php echo $j; ?>','<?php echo $set_emr_payers['id']; ?>','<?php echo $set_emr_payers['name']; ?>');">
                            <option value="">Select</option>
                            <?php 
                            for($i=1; $i<=count($payer_plan_array); $i++){ ?>
                                <option value='<?php echo $payer_plan_array[$i]['payer_id'];?>' 
                                <?php 
                                if(trim($payerplan_payer_id) == trim($payer_plan_array[$i]['payer_id']) && trim($payerplan_payer_name) == trim($payer_plan_array[$i]['title']) )
                                    echo " selected ";
                                ?>
                                ><?php echo $payer_plan_array[$i]['title']; ?></option>
                                <?php 
                            } ?>     
                        </select>
                    </td>

                    <?php

                    // to get elig payers
                    $elig_payer_plan_array = array();
                    // to get payer plan db practice payers
                    $get_elig_payer_plan_payers = sqlStatement("SELECT * FROM wp_posts p INNER JOIN wp_postmeta pm WHERE pm.post_id = p.ID  AND post_id = (SELECT post_id FROM wp_postmeta WHERE meta_key = 'practice_id' AND ID = post_id) AND meta_key='Elig Payer ID' AND post_type='payer' ORDER BY id ");
                    $i = 1;
                    while($set_elig_payer_plan_payers = sqlFetchArray($get_elig_payer_plan_payers)){
                        $elig_payer_plan_array[$i]['payer_id'] = $set_elig_payer_plan_payers['meta_value'];
                        $elig_payer_plan_array[$i]['title']    = $set_elig_payer_plan_payers['post_title'];
                        $i++;
                    }
                    ?>

                    <td>
                        <?php 
                        $elig_mapped_id              = 0;
                        $elig_payerplan_payer_id     = '';
                        $elig_payerplan_payer_name   = '';

                        // get payer plan mapping data
                        $get_elig_mapping_payer = sqlStatement("SELECT id, elig_payer_id, elig_payer_name FROM tbl_payerplan_emrpayerplan_mapping WHERE emr_payer_id = '".$set_emr_payers['id']."'");
                        while($set_elig_mapping_payer = sqlFetchArray($get_elig_mapping_payer)){
                            $elig_mapped_id              = $set_elig_mapping_payer['id'];
                            $elig_payerplan_payer_id     = $set_elig_mapping_payer['elig_payer_id'];
                            $elig_payerplan_payer_name   = $set_elig_mapping_payer['elig_payer_name'];
                        }
                        ?>
                        <input type = 'hidden' name = 'hiddeneligpayerplanTypesid<?php echo $j; ?>' id = 'hiddeneligpayerplanTypesid<?php echo $j; ?>' value = '<?php echo $elig_mapped_id; ?>'>
                        <select class='eligpayerplanTypes' id="eligpayerplanTypes<?php echo $j; ?>" onchange="save_elig_payer_plan('<?php echo $j; ?>','<?php echo $set_emr_payers['id']; ?>','<?php echo $set_emr_payers['name']; ?>');">
                            <option value="">Select</option>
                            <?php 
                            for($i=1; $i<=count($elig_payer_plan_array); $i++){ ?>
                                <option value='<?php echo $elig_payer_plan_array[$i]['payer_id'];?>' 
                                <?php 
                                if(trim($elig_payerplan_payer_id) == trim($elig_payer_plan_array[$i]['payer_id']) && trim($elig_payerplan_payer_name) == trim($elig_payer_plan_array[$i]['title']) )
                                    echo " selected ";
                                ?>
                                ><?php echo $elig_payer_plan_array[$i]['title']; ?></option>
                                <?php 
                            } ?>     
                        </select>
                    </td>
                </tr>
            <?php 
            $j++;
        } 
        ?>
        </tbody>
    </table>
    <br>
    <br>