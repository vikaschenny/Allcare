<?php
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
<!DOCTYPE html>
<html>
    <head>

        <style>
            button.payerplanaccordion {
                background-color: #3eb0f7;
                color: black;
                cursor: pointer;
                padding: 5px;
                width: 100%;
                border: none;
                text-align: left;
                outline: none;
                font-size: 17px;
                transition: 0.4s;
            }

            button.payerplanaccordion.active, button.payerplanaccordion:hover {
                background-color: #FF8C00;
                color: white;
            }
            
            button.benifitpayerplanaccordion {
                background-color: #78866B;
                color: black;
                cursor: pointer;
                padding: 5px;
                width: 75%;
                border: none;
                text-align: left;
                outline: none;
                font-size: 17px;
                transition: 0.4s;
                margin-left: 80px;
            }
            
            button.benifitpayerplanaccordion.active, button.benifitpayerplanaccordion:hover {
                background-color: #7A6C87;
                color: #C2DFFF;
            }
            
            div.panel {
                padding: 5px 5px;
                display: none;
                background-color: white;
            }
            div.benifitpanel{
                padding: 1px 0px;
                display: none;
                width: 75%;
                background-color: #C2DFFF;
                margin-left: 80px;
            }
            
            div.panel.show {
                display: block;
            }
/*            .benifitpanel.show {
                display: block;
            }*/
            .innteraco {
                display: table;
            }
            
            .bnf {
                display: table-cell;
                word-wrap: break-word;
                border-radius: 7px 0 0 7px;
                background-color: #f7f7eb;
                text-align: center;
            }
            
            .bnftab {
                display: table-cell;
                width: 100%;
            }
            button.benifitpayerplanaccordion{
                margin-left: 0px;
            }
            .bnf > div {
                box-sizing: border-box;
                font-size: 14px;
                padding: 0 15px;
                text-transform: uppercase;
               
            }
            .bnftab > div:not(:last-child){
                margin-bottom: 15px;
            }
            
            
        </style>
        <script>
            var acc = document.getElementsByClassName("payerplanaccordion");
            var i;

            for (i = 0; i < acc.length; i++) {
                acc[i].onclick = function(){
                    this.classList.toggle("active");
                    this.nextElementSibling.classList.toggle("show");
              }
            }
            function togglediv(planid,i){
                $("#benifitpaneldetails"+planid+"i"+i).toggle();
                if ($("#benifitpaneldetails"+planid+"i"+i).is(":visible")) {
                   $("#spanimage"+planid+"i"+i).html('<img id="collapse" width="25px" height="20px" align="right" src="images/collapse.png" />')
                 } else {
                   $("#spanimage"+planid+"i"+i).html('<img id="collapse" width="25px" height="20px" align="right" src="images/expand.png" />')
                }
                
//                $("#benifitpanel"+i).click(function(){
//                        $("#benifitpaneldetails"+i).toggle();
//                });
            }
        </script>

    </head>
    <body>


    <?php
    $emr_payer_id               = $_REQUEST['emr_payer_id'];

    $check_table = sqlStatement("SELECT p.ID, p.post_title
                                        FROM wp_posts p
                                        INNER JOIN tbl_payerplan_emrpayerplan_mapping t ON p.post_parent = ( 
                                        SELECT p1.id
                                        FROM wp_posts p1
                                        INNER JOIN wp_postmeta pm ON p1.id = pm.post_id
                                        WHERE t.payerplan_payer_id = pm.meta_value
                                        AND p1.post_type =  'payer'
                                        AND pm.meta_key =  'Claim Payer ID' ) 
                                        WHERE p.post_type =  'plan'
                                        AND t.emr_payer_id = '$emr_payer_id'
                                    "); 
    $check_table_data = 0; 
    while($get_check_table = sqlFetchArray($check_table)){
        $check_table_data = 1;
        $set_table = $get_check_table['ID']." - ".$get_check_table['post_title'];
        
        $set_plan_data = sqlStatement(" SELECT  * FROM wp_postmeta WHERE post_id='".$get_check_table['ID']."'");
        
        echo "<div>";
        echo "<button class='payerplanaccordion'>$set_table</button>";
        echo "<div class='panel'>";
        while($get_plan_data = sqlFetchArray($set_plan_data)){
            echo "<p><b>".ucwords(str_replace("_"," ",$get_plan_data['meta_key'])). "</b> : ".$get_plan_data['meta_value']."</p>";
        }
        
        $benifits_columns = '';
        $get_benifits_columns = sqlStatement("SHOW COLUMNS FROM wp_benefits ");
        while($set_benifits_columns = sqlFetchArray($get_benifits_columns)){
            $benifits_columns .= "`".$set_benifits_columns['Field']."`,";
        }
        
        $final_benifits_columns = rtrim($benifits_columns,",");
        $benifits_columns_array  = array();
        $get_benifits_screen = sqlStatement("SELECT benefit_title as bt_title, $final_benifits_columns FROM wp_benefits WHERE plan_id='".trim($get_check_table['ID'])."'");
        while($set_benifits_screen = sqlFetchArray($get_benifits_screen)){
            $benifits_columns_array[] = $set_benifits_screen;
            
        }
        echo "<div class='innteraco'>";
        echo "<div class='bnf'><div>benefits</div></div>";
        echo "<div class='bnftab'>";
        for($i=0; $i< count($benifits_columns_array); $i++){
            $planid = trim($get_check_table['ID']);
            echo "<div id='benifitpanel".$get_check_table['ID']."i$i' onclick='togglediv($planid,$i);'>";
            echo "<b><button class='benifitpayerplanaccordion'>".$benifits_columns_array[$i]['bt_title']."<span align='right' id='spanimage$planid"."i".$i."'><img id='collapse' width='25px' height='20px' align='right' src='images/expand.png' /></span></button></b>";
            echo "<table id='benifitpaneldetails".$get_check_table['ID']."i$i' style='display:none; margin-left: 80px;'>";
            foreach($benifits_columns_array[$i] as $bkey => $bvalue){
                if($bkey != 'bt_title' && $bkey != 'id' && $bkey != 'plan_id' && $bkey != 'practice_id')
                    echo "<tr><td><b>".ucwords(str_replace("_"," ",$bkey)).":</b></td><td>".$bvalue."</td></tr>";
               
            }
           
            echo "</table>";
            echo "</div>";
        }
        echo "</div>";
        echo "</div>";
        echo "</div><br><br>";
        echo "<div>";
        
    }
    if($check_table_data == 0){
        echo " No plans related to this Payer.";
    }
    ?>
    </body>
</html>