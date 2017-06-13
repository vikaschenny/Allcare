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
require_once("../verify_session.php");

$subpage = "Map Benefit Fields";
$pagename = "insurance"; 
if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer']; 
}else {
   $provider                     = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}

$sql=sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id = sqlFetchArray($sql);
$id1    = $id['id'];

$headerName = 'Clearing House Payers List';
include '../section_header.php';
?>

<!DOCTYPE html>

<html>

	<head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Benefit Fields Mapping</title>
            <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
            <link rel="stylesheet" type="text/css" href="assets/skins/all.css">
            <script src="assets/js/responsive_datatable/version1.0/jquery-1.11.3.min.js"></script>
            <style>
                .js-example-basic-single, .select2{
                    width: 100% !important;
                }
                .select2-container ul > li{
                    display: block;
                }
                h1, h2, h3, h4, h5, h6{
                    font-weight: 500;
                }
                h4, .h4 {
                    font-size: 17px;
                }
                .form-group {
                    margin-bottom: 7px;
                }
                .benefitsmapfiels div[class^='icheckbox_square'] + label{
                    display: inline;
                    font-weight: normal;
                    position: absolute;
                    cursor: pointer;
                }
            </style>
        <script language="javascript">
            var linkurl= "../helplinks.php";
            var prsetting = "../practiceload.php";
            var userprofile = "../userprofile.php"; 
//           function DoPost(page_name, provider,refer) {
//                method = "post"; // Set method to post by default if not specified.
//
//                var form = document.createElement("form");
//                form.setAttribute("method", method);
//                form.setAttribute("action", page_name);
//                var key='provider';
//                var hiddenField = document.createElement("input");
//                hiddenField.setAttribute("type", "hidden");
//                hiddenField.setAttribute("name", key);
//                hiddenField.setAttribute("value", provider);
//                form.appendChild(hiddenField);
//                
//                var key1='refer';
//                var hiddenField1 = document.createElement("input");
//                hiddenField1.setAttribute("type", "hidden");
//                hiddenField1.setAttribute("name", key1);
//                hiddenField1.setAttribute("value", refer);
//                form.appendChild(hiddenField1);
//
//               document.body.appendChild(form);
//                form.submit();
//        } 
        </script>       

	</head>

	<body><?php //include 'header_nav.php'; ?>
             <section id= "services">
                <div class= "container-fluid">
                    <form class="form-horizontal">
                        <div class= "row">
                            <div class="col-sm-6 col-sm-offset-3">
                                <select name="insurance_type" id="insurance_type" class="js-example-basic-single"> 
                                    <option></option>
                                    <?php $sqlInsTypes = sqlStatement("SELECT * FROM `list_options` WHERE list_id = 'Payer_Types' ORDER BY `seq`");
                                    while($resInsTypes = sqlFetchArray($sqlInsTypes)){ ?>
                                    <option value="<?php echo $resInsTypes['option_id']; ?>"><?php echo $resInsTypes['title']; ?></option>
                                   <?php } ?>
                                </select>
                            </div>
                        </div>    
                        <div class="row benefitsmapfiels" style="margin-top: 20px;">
                            <?php 

                                $groupAry = [];
                                $count = 0;
                                $sqlFields = sqlStatement("SELECT * FROM `layout_options` WHERE form_id = 'BENEFITS'");
                                while($resFields = sqlFetchArray($sqlFields)){
    //                                echo "<pre>"; print_r($resFields);
                                    if(!in_array($resFields['group_name'], $groupAry)){
                                        array_push($groupAry, $resFields['group_name']);
                                    }
                                }
                                //echo "<pre>"; print_r($groupAry);

                                foreach($groupAry as $each){
                                    $count++;
                                    echo "<div class='col-sm-4 col-md-3'>";
                                    echo "<h4>".substr($each, 1)."<a href=\"#\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Select All\">&nbsp;&nbsp;<input type='checkbox' data-checkall='true' class='selectallchek' name='selectall' id='checkbox$count' value=''></a></h4>";
                                    $sqlgroupFields = sqlStatement("SELECT * FROM `layout_options` WHERE form_id = 'BENEFITS' AND `group_name` = '".$each."'");
                                    while($resgroupFields = sqlFetchArray($sqlgroupFields)){
                                        if($resgroupFields['uor'] != 0){
                                            echo "<div class='form-group'> &nbsp;&nbsp;&nbsp;&nbsp; <input type='checkbox' name='fieldid[]' id='".$resgroupFields['field_id']."' value='".$resgroupFields['field_id']."'>&nbsp;&nbsp;<label for='".$resgroupFields['field_id']."'>".$resgroupFields['title']; echo "</label></div>";
                                        }
                                    }
                                    echo "</div>";
                                    if($count % 2 == 0)
                                        echo "<div class='visible-sm clearfix'></div>";
                                    if($count % 3 == 0)
                                        echo "<div class='visible-md clearfix'></div>";
                                    if($count % 4 == 0)
                                        echo "<div class='visible-lg clearfix'></div>";
                                }

                            ?>
                        </div>
                        <div class="row" style="margin: 20px 0;">
                            <div class="col-xs-12 text-center">
                                <button class="btn btn-primary" id="assinbenefits" data-loading-text="Processing...." disabled="">ASSIGN</button>
                            </div>
                        </div>
                    </form>    
                </div>
		</section>
          
                <?php include '../section_footer.php'; ?> 
                <script src="assets/js/select2.min.js"></script>
                <script src="assets/js/icheck.min.js"></script>
                <script>
                    $(document).ready(function(){
                        //$('[data-toggle="tooltip"]').tooltip(); 
                         $(".js-example-basic-single").select2({placeholder: "Select Insurance Type",}).on("change",function(evt){
                             $.ajax({url:"benefit_fields_assign.php",type: 'POST',data:{action:"change",ins_type:$(this).val()},success: function (data, textStatus, jqXHR) {
                                   $("#assinbenefits").prop("disabled",false);
                                   $(".benefitsmapfiels input[type=checkbox]").iCheck('uncheck');
                                   //console.log(data);
                                    if(data != "null" ){
                                        var fielddata = JSON.parse(data)
                                       $.each(fielddata,function(index,value){
                                           $("#"+value).iCheck('check');
                                       });
                                    }
                                    
                                    /*$.each($('.selectallchek').parents("h4").nextAll().find("input[type=checkbox]"),function(index,value){
                                    });*/
                                    
                                },error: function (jqXHR, textStatus, errorThrown) {
                                    alert("Error to sending data.");
                                }
                            })
                         })
                         
                         $(".benefitsmapfiels input[type=checkbox]").iCheck({
                            checkboxClass: 'icheckbox_square-blue',
                            radioClass: 'iradio_square-blue',
                            cursor: true,
                            increaseArea: '20%'
                          });
                           $(".selectallchek").iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green',
                            cursor: true,
                            increaseArea: '20%'
                          });
                          
                          $(".benefitsmapfiels .form-group input[type=checkbox]").on('ifChanged', function(){
                              //console.log("callselectchekstart1 : " +  $(this).parents('.form-group').parent().find(".selectallchek").data("checkall"))
                                if($(this).parents('.form-group').parent().find(".form-group input[type=checkbox]").length == $(this).parents('.form-group').parent().find(".form-group input:checked").length){                                   
                                   $(this).parents('.form-group').parent().find("[data-toggle='tooltip']").attr("data-original-title","Deselect All");
                                   $(this).parents('.form-group').parent().find(".selectallchek").iCheck('check');
                                   $(this).parents('.form-group').parent().find(".selectallchek").data("checkall",false);
                                   //console.log("callselectchekstartin1 : " +  $(this).parents('.form-group').parent().find(".selectallchek").data("checkall"))
                                }else{     
                                   $(this).parents('.form-group').parent().find("[data-toggle='tooltip']").attr("data-original-title","Select All");
                                   $(this).parents('.form-group').parent().find(".selectallchek").iCheck('uncheck');
                                   $(this).parents('.form-group').parent().find(".selectallchek").data("checkall",true);
                                }
                                 //console.log("callselectchek1 : " +  $(this).parents('.form-group').parent().find(".selectallchek").data("checkall"))
                             //console.log($(this).parents('.form-group').parent().find(".form-group input[type=checkbox]").length + " : " + $(this).parents('.form-group').parent().find(".form-group input:checked").length)
                          });
                           
                        $('.selectallchek').on('ifClicked', function(){
                            //console.log("callselectchek : " + $(this).data("checkall"))
                            if($(this).data("checkall")){
                               $(this).parents("[data-toggle='tooltip']").attr("data-original-title","Deselect All");
                               $(this).parents("[data-toggle='tooltip']").tooltip('show');
                               $(this).parents("h4").nextAll().find("input[type=checkbox]").iCheck('check');
                               $(this).data("checkall",false);
                           }else{
                               $(this).parents("[data-toggle='tooltip']").attr("data-original-title","Select All");
                               $(this).parents("[data-toggle='tooltip']").tooltip('show');
                               $(this).parents("h4").nextAll().find("input[type=checkbox]").iCheck('uncheck');
                               $(this).data("checkall",true)
                           }
                           
                        });

                        $("#assinbenefits").click(function(evt){
                            evt.preventDefault();
                            var $self = $(this);
                            $self.button('loading');
                            var data = $('form').serializeArray();
                            data.push({name: 'action', value: "assign"})
                            $.ajax({url:"benefit_fields_assign.php",type: 'POST',data:data,success: function (data, textStatus, jqXHR) {
                                    $self.button('reset');
                                },error: function (jqXHR, textStatus, errorThrown) {
                                    alert("Error to sending data.")
                                }
                            })
                            //console.log("form.data: " + JSON.stringify($('form').serializeArray()))
                        })
                        $('.selectallchek + .iCheck-helper').hover(function(){
                            $(this).parents("[data-toggle='tooltip']").tooltip('show');
                        },function(){
                            $("[data-toggle='tooltip']").tooltip('hide');
                        });
                        
                        $.each($(".benefitsmapfiels .form-group input[type=checkbox]"),function(){
                            var label_height = $(this).parent().next('label').height()+2;
                            $(this).parent().height(label_height);
                        })
                        //$("[data-toggle='tooltip']").tooltip('show');
                    })
                </script>
	</body>

</html>