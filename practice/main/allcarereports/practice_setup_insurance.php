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

<html>
    <head>
        <script type='text/javascript' src='../js/jquery-1.11.1.min.js'></script>
        <link rel="stylesheet" href="js/jquery-steps-master/css/jquery.steps.css">
        <link rel="stylesheet" href="js/jquery-steps-master/css/main.css">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="js/jquery-steps-master/jquery.steps.js"></script>
        <script>
            $(function ()
            {
                
                $("#payer-setup-insurance").steps({
                    headerTag: "h3",
                    bodyTag: "section",
                    transitionEffect: "slideLeft",
                    autoFocus: true,
                    onStepChanging: function (event, currentIndex, newIndex){
                        return true;
                    },
                    onStepChanged: function (event, currentIndex, priorIndex){
                        $('#loader3').show();
                        if(currentIndex == 2){
                            $.ajax({
                                type: "GET",
                                url: "remaining_payerplanpayers_page.php",
                                success: function(data) {
                                    $("#remianingpayermetadata").html(data);
                                },
                                error: function(jqXHR, exception){
                                    alert("failed" + jqXHR.responseText);
                                }    
                            });
                            $('#loader3').hide();
                        }
                        
                    },
                    onFinishing: function (event, currentIndex){
                        return true;
                    },
                    onFinished: function (event, currentIndex){
                        
                    }
                });
                
                
                /*
                $("select.payerplanTypes").each( function () {
                    $("select.payerplanTypes option[value='" + $(this).data('index') + "']").prop('disabled', false);
                    $(this).data('index', this.value);
                    $("select.payerplanTypes option[value='" + this.value + "']:not([value=''])").prop('disabled', true);
                    $(this).find("option[value='" + this.value + "']:not([value=''])").prop('disabled', false);
                    
                });*/
                $("select.payerplanTypes").each( function () {
                    $("select.payerplanTypes option[value='" + $(this).data('index') + "']").prop('disabled', false);
                    $(this).data('index', this.value);
                    $("select.payerplanTypes option[value='" + this.value + "']:not([value=''])").prop('disabled', true);
                    $(this).find("option[value='" + this.value + "']:not([value=''])").prop('disabled', false);
                    
                });
                
                $("select.insu_payer_mapping").each( function () {
                    $("select.insu_payer_mapping option[value='" + $(this).data('index') + "']").prop('disabled', false);
                    $(this).data('index', this.value);
                    $("select.insu_payer_mapping option[value='" + this.value + "']:not([value=''])").prop('disabled', true);
                    $(this).find("option[value='" + this.value + "']:not([value=''])").prop('disabled', false);
                    
                });
                $("#clicklog").click(function(){
                    $("#details").toggle();
                });
            });
            function sync_payer_plan_db_data(){
                $.ajax({
                    type: "POST",
                    url: "sync_payerplan_db_to_emr.php",
                    success: function() {
                        $("#clicknexttab").html('Click Next Button to Check the Synced Data.');
                        alert("Synced Successfully..!!");
                    },
                    error: function(jqXHR, exception){
                        alert("failed" + jqXHR.responseText);
                    }    
                });
            }
            
            function save_payer_plan(id,emr_payer_id,emr_payer_name){
                var mapping_id              = $("#hiddenpayerplanTypesid"+id).val();
                var payerplan_payer_id      = $("#payerplanTypes"+id +" option:selected").val();
                var payer_plan_payer_name   = $("#payerplanTypes"+id +" option:selected").text();
                $.ajax({
                    type: "POST",
                    url: "save_payer_plan_mapping.php",
                    data : {'mapping_id' : mapping_id, 'emr_payer_id':emr_payer_id, 'payerplan_payer_id': payerplan_payer_id, 'emr_payer_name': emr_payer_name, 'payer_plan_payer_name':payer_plan_payer_name },
                    success: function(data) {
                        $("#savespan").html('Claim Payer Saved');
                        
                        $("select.payerplanTypes").each( function () {
                            $("select.payerplanTypes option[value='" + $(this).data('index') + "']").prop('disabled', false);
                            $(this).data('index', this.value);
                            $("select.payerplanTypes option[value='" + this.value + "']:not([value=''])").prop('disabled', true);
                            $(this).find("option[value='" + this.value + "']:not([value=''])").prop('disabled', false);
                        });
                        
                        $("#savespan").css("opacity",'1');
                        $("#hiddenpayerplanTypesid"+id).val(data.trim());
                        $("#savespan").fadeOut(2000,function(){ $(this).css({"display":"block","opacity":0})});
                        
                        
                    },
                    error: function(jqXHR, exception){
                        alert("failed" + jqXHR.responseText);
                    }    
                });
            }
            function save_elig_payer_plan(id,emr_payer_id,emr_payer_name){
                var mapping_id                  = $("#hiddeneligpayerplanTypesid"+id).val();
                var elig_payerplan_payer_id     = $("#eligpayerplanTypes"+id +" option:selected").val();
                var elig_payer_plan_payer_name  = $("#eligpayerplanTypes"+id +" option:selected").text();
                alert(elig_payer_plan_payer_name);
                $.ajax({
                    type: "POST",
                    url: "save_payer_plan_mapping.php",
                    data : {'mapping_id' : mapping_id, 'emr_payer_id':emr_payer_id, 'elig_payerplan_payer_id': elig_payerplan_payer_id, 'emr_payer_name': emr_payer_name, 'elig_payer_plan_payer_name':elig_payer_plan_payer_name },
                    success: function(data) {
                        $("#savespan").html('Eligibility Payer Saved');
       
                        $("#savespan").css("opacity",'1');
                        $("#savespan").fadeOut(2000,function(){ $(this).css({"display":"block","opacity":0})});
                        
                        
                    },
                    error: function(jqXHR, exception){
                        alert("failed" + jqXHR.responseText);
                    }    
                });
            }
            function changeAddPayer(){
                if($(".reminingpayers").is(':checked')){
                    $("#savenewpayer").prop('disabled', false); // checked
                }else{
                    $("#savenewpayer").prop('disabled', true);
                }
            }
            
            // to add new payers
            function addNewPayers(){
                
                var payerlist = [];
                $('.reminingpayers').each( function (){
                    if($(this).prop('checked') == true){
                        payerlist.push($(this).val());
                    }
                    
                });
                $.ajax({
                    type: "POST",
                    url: "add_new_payer.php",
                    data : {payerlist:payerlist},
                    success: function(data) {
                           alert("Created new payers Successfully.!")        ;
                           
                    },
                    error: function(jqXHR, exception){
                        alert("failed" + jqXHR.responseText);
                    }    
                });
            }
            function get_payer_plan_info(){
                var emr_payer_id = $("#payerslist option:selected").val();
                if(emr_payer_id != ''){
                    $.ajax({
                        type: "POST",
                        url: "get_payer_plan_info.php",
                        data : {emr_payer_id:emr_payer_id},
                        success: function(data) {
                             $("#payerplaninfo").html(data);                     
                        },
                        error: function(jqXHR, exception){
                            alert("failed" + jqXHR.responseText);
                        }    
                    });
                }else{
                    $("#payerplaninfo").html('');       
                }
                
            }
            function save_insurance_metakey_mapping(id,meta_key){
                var meta_id = $("#insurance_metakey_mapping"+id+" option:selected").val();
                if(meta_key != ''){
                    $.ajax({
                        type: "POST",
                        url: "save_insurance_meta_mapping.php",
                        data:{meta_id:meta_id,meta_key:meta_key},
                        success: function(data) {
                            $("#saveinsurance_metakey_mapping").html('Saved.!');
                            
                            $("select.insu_payer_mapping").each( function () {
                                $("select.insu_payer_mapping option[value='" + $(this).data('index') + "']").prop('disabled', false);
                                $(this).data('index', this.value);
                                $("select.insu_payer_mapping option[value='" + this.value + "']:not([value=''])").prop('disabled', true);
                                $(this).find("option[value='" + this.value + "']:not([value=''])").prop('disabled', false);

                            });
                            
                            $("#saveinsurance_metakey_mapping").css("opacity",'1');
                            $("#saveinsurance_metakey_mapping").fadeOut(2000,function(){ $(this).css({"display":"block","opacity":0})});
                        },
                        error: function(jqXHR, exception){
                            alert("failed" + jqXHR.responseText);
                        }    
                    });
                }
            }
        </script>
        <style>
            .scrollable {
                overflow-y: auto;
            }
            #sortable1, #sortable2 {
                border: 1px solid #eee;
                width: 142px;
                min-height: 20px;
                list-style-type: none;
                margin: 0;
                padding: 5px 0 0 0;
                float: left;
                margin-right: 10px;
            }
            #sortable1 li, #sortable2 li {
                margin: 0 5px 5px 5px;
                padding: 5px;
                font-size: 1.2em;
                width: 120px;
            }
            #details {
                font-family: verdana,arial,sans-serif;
                font-size:11px;
                color:#333333;
                border-width: 1px;
                border-color: #999999;
                border-collapse: collapse;
            }
            #details th {
                background-color:#c3dde0;
                border-width: 1px;
                padding: 8px;
                border-style: solid;
                border-color: #a9c6c9;
            }
            #details tr {
                background-color:#ffffcc;
            }
            #details td{
                border-width: 1px; 
                padding: 8px;
                border-style: solid;
                border-color: #a9c6c9;
            }
            .wizard > .steps a, .wizard > .steps a:hover, .wizard > .steps a:active{
                padding: 0.4em 1em;
            }
            .loader {
                    height: 100px;
                    text-align: center;
                    background: url('images/loading.gif') 50% 50% no-repeat rgb(249,249,249);
                    background-color: #F0F0F0  ;
            }
/*            .loader2 {
                    position: fixed;
                    left: 0px;
                    top: 0px;
                    width: 100%;
                    height: 100%;
                    z-index: 9999;
                    background: url('images/loading.gif') 50% 50% no-repeat rgb(249,249,249);
            }
            .loader3 {
                    position: fixed;
                    left: 0px;
                    top: 0px;
                    width: 100%;
                    height: 100%;
                    z-index: 9999;
                    background: url('images/loading.gif') 50% 50% no-repeat rgb(249,249,249);
            }
            .loader4 {
                    position: fixed;
                    left: 0px;
                    top: 0px;
                    width: 100%;
                    height: 100%;
                    z-index: 9999;
                    background: url('images/loading.gif') 50% 50% no-repeat rgb(249,249,249);
            }*/
        </style>
    </head>
    <body>
        <div id="payer-setup-insurance">
            <h3>Sync Payer Plan DB Data</h3>
            <section class="scrollable">
                <div id='loader1' class="loader"></div>
                
                <script>
                    $('#loader1').show();
                    $.ajax({
                        type: "GET",
                        url: "display_payerplan_sync_page.php",
                        success: function(data) {
                            $('#loader1').hide();
                            $("#syncmetadata").html(data);
                        },
                        error: function(jqXHR, exception){
                            alert("failed" + jqXHR.responseText);
                        }    
                    });
                </script>  
                <div id='syncmetadata'></div>
            </section>
            <h3>Name Matching</h3>
            <section class="scrollable">
                <div id='loader2' class="loader"></div>
                <script>
                    $('#loader2').show();
                    $.ajax({
                        type: "GET",
                        url: "payerplan_namematching_page.php",
                        success: function(data) {
                            $('#loader2').hide();
                            $("#namematchingdata").html(data);
                        },
                        error: function(jqXHR, exception){
                            alert("failed" + jqXHR.responseText);
                        }    
                    });
                </script>  
                <div id='namematchingdata'></div>
            </section>
            <h3>Remaining Payers of Payer Plan DB</h3>
            <section class="scrollable">
                <div id='loader3' class="loader"></div>
                <div id='remianingpayermetadata'></div>
            </section>
            <h3>Payers and their Plans Information</h3>
            <section class="scrollable">
                <div id='loader4' class="loader"></div>
                <script>
                    $('#loader4').show();
                    $.ajax({
                        type: "GET",
                        url: "get_payer_plans_page.php",
                        success: function(data) {
                            $('#loader4').hide();
                            $("#payerplansmetadata").html(data);
                        },
                        error: function(jqXHR, exception){
                            alert("failed" + jqXHR.responseText);
                        }    
                    });
                </script>  
                <div id='payerplansmetadata'></div>
            </section>
        </div>
    </body>
</html>