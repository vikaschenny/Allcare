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


$get_plan_id = $_REQUEST['plan_id'];
$pid         = $_REQUEST['pid'] ?  $_REQUEST['pid'] : $_SESSION['pid'];
$emr_payer_id = $_REQUEST['payer_id'];
if($get_plan_id != ''){
    ?>
    <html>
        <head>
           <script type='text/javascript' src='../../js/jquery-1.11.1.min.js'></script>
            <style>

                button.benifitpayerplanaccordion {
                    background-color: #82CAFF;
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
                    background-color: #FFB682;
                    color: black;
                }

                div.benifitpanel{
                    padding: 1px 0px;
                    display: none;
                    width: 75%;
                    background-color: #B4FCFF;
                    margin-left: 80px;
                }

                .innteraco {
                    display: table;
                }

                .bnf {
                    display: table-cell;
                    word-wrap: break-word;
                    border-radius: 7px 0 0 7px;
                    background-color:  #c0e4ff;
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
                function togglediv(planid,i){
                    $("#benifitpaneldetails"+planid+"i"+i).toggle();
                    if ($("#benifitpaneldetails"+planid+"i"+i).is(":visible")) {
                       $("#spanimage"+planid+"i"+i).html('<img id="collapse" width="25px" height="20px" align="right" src="images/collapse.png" />')
                     } else {
                       $("#spanimage"+planid+"i"+i).html('<img id="collapse" width="25px" height="20px" align="right" src="images/expand.png" />')
                    }
                }
                //Patient Estimation  verification
    function validate_est(pid,form_id,month_value,payer,provider_id,dos) 
    { 
        
        if(document.getElementById('form_x12').value=='')
        {
            alert(stringverify);
            return false;
        }
        else
        {
            from        = '2016-09-14'; // document.getElementById('form_from_date').value;
            to          = '2016-09-14'; // document.getElementById('form_to_date').value;
            facility    = ''; //document.getElementById('form_facility').value;
            providerid  = ''; //document.getElementById('form_users').value;
            removedrows = ''; //document.getElementById('removedrows').value;
            form_x12    = document.getElementById('form_x12').value;

            provider    = '<?php echo $provider; ?>';
            refer       = '<?php echo $refer; ?>';

            if(form_id == '' || form_id == '0' || form_id == 0){
                form_id     = 0;
            }

            var eligibility = [
                {from:from,to:to,facility:facility,provider:provider,providerid:providerid,refer:refer,removedrows:removedrows,form_x12:form_x12,pid:pid,frame:"show"},
                {pid:pid,form_id:form_id,month_value:month_value,verify_type:"patient_estimation",payer_id:payer,provider:provider,provider_id:provider_id,refer:refer,dos:dos,frame:"show"}
            ];

            if(pid != ''){
                // checkbox condition
                var class_name_checkbox = "checkmonth";

                var viewportwidth   = document.documentElement.clientWidth;
                var viewportheight  = document.documentElement.clientHeight;
                window.resizeBy(-300,0);
                window.moveTo(0,0);

                if ($("."+class_name_checkbox).is(':checked') == false) {

                    eligibility[0].page = "patient_estimation_verify";
//                                eligibility[0].page = "save_eligibility_response_data";
                    eligibility[1].page = "save_eligibility_response_data";
                    eligibility[0].pagename = "Estimation Response";
                    eligibility[1].pagename = "Eligibility Data Screen";

                    window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility))
                    var url = "../../verify_eligibility.php";
                    window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");
                }else{
                    //window.open("save_eligibility_response_data.php?pid="+pid+"&form_id="+form_id+"&month_value="+month_value+"&verify_type=patient_estimation&payer_id="+payer+"&provider_id="+provider_id+"&dos="+dos, "", "width=1000, height=600,scrollbars=1,resizable=1");
                    eligibility[0].frame="hide";
                    eligibility[1].page = "save_eligibility_response_data";
                    eligibility[1].pagename = "Eligibility Data Screen";

                    window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility))
                    var url = "../../verify_eligibility.php";
                    window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");
                }
            }else{
                //window.open("patient_estimation_verify.php?from="+from+"&to="+to+"&facility="+facility+"&provider="+provider+"&removedrows="+removedrows+"&form_x12="+form_x12+"&pid="+pid, "", "width=880, height=600,scrollbars=1,resizable=1");
                eligibility[1].frame="hide";
                eligibility[0].page = "patient_estimation_verify";
//                            eligibility[0].page = "save_eligibility_response_data";
                eligibility[0].pagename = "Estimation Response";
                window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility));
                var url = "../../verify_eligibility.php";
                window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");
            }
        }
    }
    
    //Eligibility verification
    function validate_elig(pid,form_id,month_value,payer,provider_id,dos)
    {
        
        if(document.getElementById('form_x12').value=='')
        {
            alert(stringverify);
            return false;
        }
        else
        {
             from        = '2016-09-14'; // document.getElementById('form_from_date').value;
            to          = '2016-09-14'; // document.getElementById('form_to_date').value;
            facility    = ''; //document.getElementById('form_facility').value;
            providerid  = ''; //document.getElementById('form_users').value;
            removedrows = ''; //document.getElementById('removedrows').value;
            form_x12    = document.getElementById('form_x12').value;

            provider    = '<?php echo $provider; ?>';
            refer       = '<?php echo $refer; ?>';

            if(form_id === '' || form_id === '0'){
                form_id     = 0;
            }
            var eligibility = [
                {from:from,to:to,facility:facility,provider:provider,providerid:providerid,refer:refer,removedrows:removedrows,form_x12:form_x12,pid:pid,frame:"show"},
                {pid:pid,form_id:form_id,month_value:month_value,verify_type:"patient_eligibility",payer_id:payer,provider:provider,provider_id:provider_id,refer:refer,dos:dos,frame:"show"}
            ];
            if(pid != ''){
                // checkbox condition
                var class_name_checkbox = "checkmonth";

                var viewportwidth   = document.documentElement.clientWidth;
                var viewportheight  = document.documentElement.clientHeight;
                window.resizeBy(-300,0);
                window.moveTo(0,0);


                if ($("."+class_name_checkbox).is(':checked') == false) {
                    //window.open("elig-verify.php?from="+from+"&to="+to+"&facility="+facility+"&provider="+provider+"&removedrows="+removedrows+"&form_x12="+form_x12+"&pid="+pid, "", "width=700, height=600,scrollbars=1,resizable=1");
                    //window.open("save_eligibility_response_data.php?pid="+pid+"&form_id="+form_id+"&month_value="+month_value+"&verify_type=patient_eligibility&payer_id="+payer+"&provider_id="+provider_id+"&dos="+dos, "", "width=600,left="+(viewportwidth-100)+",height=600,top=0,scrollbars=1,resizable=1");

                    eligibility[0].page = "elig-verify";
//                                eligibility[0].page = "save_eligibility_response_data";
                    eligibility[1].page = "save_eligibility_response_data";
                    eligibility[0].pagename = "Eligibility Response";
                    eligibility[1].pagename = "Eligibility Data Screen";

                    window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility))
                    var url = "../../verify_eligibility.php";
                    window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");

                }else{
                    eligibility[0].frame="hide";
                    eligibility[1].page = "save_eligibility_response_data";
                    eligibility[1].pagename = "Eligibility Data Screen";
                    window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility))
                    var url = "../../verify_eligibility.php";
                    window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");
                  //window.open("save_eligibility_response_data.php?pid="+pid+"&form_id="+form_id+"&month_value="+month_value+"&verify_type=patient_eligibility&payer_id="+payer+"&provider_id="+provider_id+"&dos="+dos, "","width=1000, height=600,scrollbars=1,resizable=1");  
                }
            }else{
                eligibility[1].frame="hide";
                eligibility[0].page = "elig-verify";
//                            eligibility[0].page = "save_eligibility_response_data";
                eligibility[0].pagename = "Eligibility Response";
                window.localStorage.setItem("provider_eligibility",JSON.stringify(eligibility));
                var url = "../../verify_eligibility.php";
                window.open(url,"","width=1000, height=600,scrollbars=1,resizable=1");
               //window.open("elig-verify.php?from="+from+"&to="+to+"&facility="+facility+"&provider="+provider+"&removedrows="+removedrows+"&form_x12="+form_x12+"&pid="+pid, "", "width=880, height=600,scrollbars=1,resizable=1");
            }
        }
    }
    function popupdropdown(element,pid){
        if($("#popupdropdown").val() == 'eligibility'){
           $(element).next('a').show();
//                       var funcal = $("#hiddenpverify"+id).val();
            var funcal = $("#hiddenpverify").val(); //$("#hiddenpverify_"+month_value+"-p"+pid).val();
           var funcaltrimed = funcal.replace(/['"]+/g, '');
           var functionvalue = funcaltrimed.split(",");

           validate_elig(functionvalue[0],functionvalue[1].trim('"'),functionvalue[2].trim('"'),functionvalue[3],functionvalue[4],functionvalue[5]);
        }else if($("#popupdropdown").val()== 'estimation'){
             $(element).next('a').show();
//                        var funcal = $("#hiddenpverify"+id).val();
            var funcal = $("#hiddenpverify").val(); //"5988','12','09-2016','5257','18','2016-09-14"; //$("#hiddenpverify_"+month_value+"-p"+pid).val();
            var funcaltrimed = funcal.replace(/['"]+/g, '');
            var functionvalue = funcaltrimed.split(",");

            validate_est(functionvalue[0],functionvalue[1].trim('"'),functionvalue[2].trim('"'),functionvalue[3],functionvalue[4],functionvalue[5]);
        }else if($("#popupdropdown").val()== 'review_patient'){
            $(element).next('a').show();
            review_patient(pid);
        }else if($("#popupdropdown").val()== 'patient_insurance'){
            $(element).next('a').show();
            patient_insurance(pid);
        }else{
            $(element).next('a').hide();
        }
    }
    function get_htmldata(postedid){
         window.open('get_elig_data.php?pid=<?php echo $pid;  ?>&posted_id='+postedid,"","width=1000, height=600,scrollbars=1,resizable=1");
    }
            </script>

        </head>
        <body>
            <?php
            $benifits_columns_array  = array();
            if($get_plan_id !== ''){
                $get_plan_benefits = sqlStatement("SELECT * FROM `wp_benefits` WHERE plan_id='$get_plan_id'");
                while($set_benifits_screen = sqlFetchArray($get_plan_benefits)){
                    $benifits_columns_array[] = $set_benifits_screen;

                }
                echo "<div class='innteraco'>";
                echo "<div class='bnf'><div>benefits</div></div>";
                echo "<div class='bnftab'>";
                for($i=0; $i< count($benifits_columns_array); $i++){
                    $planid = trim($get_plan_id);
                    echo "<div id='benifitpanel".$get_plan_id."i$i' onclick='togglediv($planid,$i);'>";
                    echo "<b><button class='benifitpayerplanaccordion'>".$benifits_columns_array[$i]['benefit_title']."<span align='right' id='spanimage$planid"."i".$i."'><img id='collapse' width='25px' height='20px' align='right' src='images/expand.png' /></span></button></b>";
                    echo "<table id='benifitpaneldetails".$get_plan_id."i$i' style='display:none; margin-left: 80px;'>";
                    foreach($benifits_columns_array[$i] as $bkey => $bvalue){
                        if($bkey != 'bt_title' && $bkey != 'id' && $bkey != 'plan_id' && $bkey != 'practice_id'){
                            $get_label = sqlStatement("SELECT `name` FROM `wp_terms` WHERE `slug` = '$bkey'");
                            while($get_label = sqlFetchArray($get_label)){
                                $label_title = $label_title['name'];
                            }
                            if($label_title)
                                $title = $label_title; 
                            else
                                $title = ucwords(str_replace("_"," ",$bkey));
                            echo "<tr><td><b>".$title.":</b></td><td>".$bvalue."</td></tr>";
                        }
                          
                    }

                    echo "</table>";
                    echo "</div>";
                }
                echo "</div>";
                echo "</div>";
                echo "</div><br><br>";
            }
            
            
            
            // get check data
            $get_elig_est_check = sqlStatement("SELECT meta_value FROM wp_postmeta  WHERE meta_key = 'elig_estimation_check' AND post_id= '$get_plan_id'");
            while($set_elig_est_check = sqlFetchArray($get_elig_est_check)){
                $check_value = $set_elig_est_check['meta_value'];
            }
            if($check_value != ''){
                $setcheckbox = 0;
                $get_data = sqlStatement("SELECT elig_est_data,html_data, date,id FROM tbl_eligibility_html_data WHERE pid = '$pid' ORDER BY id");
                while($set_data = sqlFetchArray($get_data)){
                    $setcheckbox = 1;
                }
                ?>
                <a href="#" onclick='popupdropdown(this,<?php echo $pid; ?>)'>Check <?php echo ucwords($check_value);?></a>
                
                <input type="checkbox" name="checkmonth" id='checkmonth' class ='checkmonth' 
                      <?php 
                      if($setcheckbox == 1)
                          echo " checked ";
                      ?>
                >
                <br>
                <?php
                $get_elig_data = sqlStatement("SELECT elig_est_data,html_data, date,id FROM tbl_eligibility_html_data WHERE pid = '$pid' ORDER BY id");
                while($set_elig_data = sqlFetchArray($get_elig_data)){
                    ?> <a href="#" onclick='get_htmldata(<?php echo $set_elig_data['id']; ?>);'>Verify Already Checked <?php echo ucwords($check_value);?> on <?php echo $set_elig_data['date']; ?></a> <br> <?php
                }
                ?>
                
                <input type="hidden" value="<?php echo $check_value ;?>" name="popupdropdown" id="popupdropdown">
                <?php
            }
            $dos = date("Y-m-d");
            $month_check = date("m-Y");
            echo "<input type='hidden' id='hiddenpverify' class='hiddenpverify' value='".$pid.",$month_check,\"$month_check\",".$emr_payer_id.",".$provider.",\"$dos\"' >";
            // retieving zirmed data statically
            $rez = sqlStatement("select * from x12_partners WHERE id='5'");
            while($clearinghouse = sqlFetchArray($rez)){
                ?>
                <input type="hidden" value="<?php echo htmlspecialchars( $clearinghouse['id']."|".$clearinghouse['id_number']."|".$clearinghouse['x12_sender_id']."|".$clearinghouse['x12_receiver_id']."|".$clearinghouse['x12_version']."|".$clearinghouse['processing_format'], ENT_QUOTES); ?>" name ='form_x12' id='form_x12'>
               <!--<a href="elig-verify.php?from=<?php echo date("Y-m-d"); ?>&to=<?php echo date("Y-m-d"); ?>&facility=&provider=&removedrows=&form_x12=<?php echo htmlspecialchars( $clearinghouse['id']."|".$clearinghouse['id_number']."|".$clearinghouse['x12_sender_id']."|".$clearinghouse['x12_receiver_id']."|".$clearinghouse['x12_version']."|".$clearinghouse['processing_format'], ENT_QUOTES); ?>&pid=<?php echo $pid; ?>">Check Eligibility</a>-->
            <? } ?>
                <input type='hidden' name='form_from_date' id="form_from_date" value='<?php echo date("Y-m-d"); ?>'>
                <input type='hidden' name='form_to_date' id="form_from_date" value='<?php echo date("Y-m-d"); ?>'>
        </body>
    </html>
    <?php
}else{
    echo "<p style='font-style:normal; font-size:18px; font-weight: bold; color:red;'>Please enter Proper Plan to get benefits.</p>";
   
}