<?php
require_once("../../verify_session.php");
$mainPlans = [];
$mainPlans['insplans'] = [];
$insPlans = [];
$insidArr = array();
$planArr = array();
$query = sqlStatement("SELECT provider,plan_name FROM insurance_data WHERE pid=".$pid);
while($row   = sqlFetchArray($query)):
    if($row['provider'] != ""):
        array_push($insidArr, $row['provider']);
        array_push($planArr, $row['plan_name']);
    endif;
endwhile;

if(count($insidArr) > 0):
    $insidStr = implode(",",$insidArr);
    $sql2   = sqlStatement("SELECT * from `tbl_patientinsurancecompany` WHERE `insuranceid` IN (".$insidStr.")");
    while($row2  = sqlFetchArray($sql2)){
        if(in_array($row2['planname'], $planArr)):
            $insType = $row2['insurance_type'];

            $instype = sqlStatement("SELECT `title` from `list_options` WHERE `list_id` = 'Payer_Types' AND `option_id` = '".$insType."'");
            $instypeRes = sqlFetchArray($instype);

            $row2['ins_type'] = $insType;

            $row2['insurance_type'] = $instypeRes['title'];

            $insComQuery = sqlStatement("SELECT ins.name,insdt.type FROM insurance_companies ins
                                         INNER JOIN insurance_data insdt ON ins.id = insdt.provider WHERE insdt.provider=".$row2['insuranceid']. " AND 
                                         insdt.pid = ". $pid);
            $rowins      = sqlFetchArray($insComQuery);
            $row2['insuringType'] = $rowins['type'];
            $row2['insuranceName'] = $rowins['name'];
            // Get benefit fields related to this insurance type
            $bfields = array();
            if($insType != ""):
                $query = sqlStatement("SELECT fields FROM tbl_benefit_fields_map WHERE ins_type=".$insType);
                $bfieldArr = sqlFetchArray($query);
                $bfieldsStr = str_replace('"','',$bfieldArr['fields']);
                $bfieldsStr = str_replace('[','',$bfieldsStr);
                $bfieldsStr = str_replace(']','',$bfieldsStr);
                $bfields = explode(",",$bfieldsStr);
            endif;

            $resultantArr = array();
            $sql3 = sqlStatement("SELECT * FROM tbl_inscomp_benefits WHERE planid=".$row2['id']." and deleted=0");
            $i = 0;
            while($row3 = sqlFetchArray($sql3)):
                foreach($row3 as $key => $value):
                    $groupName = "";
                    if(in_array($key,$bfields)):
                        $groupQuery = sqlStatement("SELECT group_name,title FROM `layout_options` WHERE form_id='BENEFITS' AND field_id='".$key."' AND uor > 0 AND field_id != '' ORDER BY group_name, seq");
                        $groupRow = sqlFetchArray($groupQuery);
                        $groupName = substr($groupRow['group_name'], 1);
                        if($key=='plan_type'){
                            $list=sqlStatement("select * from list_options where list_id='Allcare_Plan_Type' and option_id='".$value."'");
                            $ldata=sqlFetchArray($list);
                            $resultantArr[$i][$groupName][$groupRow['title']] = nl2br($ldata['title'],false);
                        }else {
                            $resultantArr[$i][$groupName][$groupRow['title']] = nl2br($value,false);
                        }
                    endif;
                endforeach;
                $i++;
            endwhile;

            $row2['benefits'][$row2['id']] = $resultantArr;
            array_push($insPlans,$row2);
        endif;   
    }
endif;
 
$mainPlans['insplans'] = $insPlans;

/*
echo "<pre>";
print_r($mainPlans);
echo "<pre>";
echo json_encode($mainPlans);*/

?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Benefits</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../css/tabulous.css"/>
        <style>
            .accordion {
                background-color: #eee;
                color: #444;
                cursor: pointer;
                padding: 10px;
                text-align: left;
                border: none;
                outline: none;
                transition: 0.4s;
                position: relative;
                border-radius: 2px;
                display: table;
                width: 98%;
            }
            div.panel > .accordion {
                background-color: #e9e2f8;
            }
            .accordion.active,.accordion:hover {
                background-color: #ddd;
            }
            div.panel > .accordion.active,div.panel > .accordion:hover {
                background-color: #d5c7f3;
            }
            div.panel {
                padding: 0 18px;
                background-color: white;
                height: auto;
                background: #fff;
                position: relative;
                display: none;
            }
            .accordion:after {
                content: '\002B';
                color: #777;
                font-weight: bold;
                float: right;
                margin-left: 5px;
            }

            .accordion.active:after {
                content: "\2212";
            }
            .tabs_container > div{
                width: auto;
            }
        </style>
    </head>
    <body>
        <div id="plans"></div>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="../../js/tabulous.js"></script>
        <script>
            $(document).ready(function(){
                var plans = JSON.parse('<?php echo addslashes(json_encode($mainPlans)); ?>');
                var planshtml = "";
                var counter = 0;
                $.each(plans['insplans'],function(index,value){
                    counter++;
                     planshtml += '<div class="accordion" style="margin-top:2px;"><div style="display:table-cell; width: 60%;"><b>Insurance Name : </b>'+value['insuranceName']+' ('+value['insuringType']+')</div><div style="display:table-cell;"><b>Plan Name :</b>'+ value['planname']+'</div></div>';
                     planshtml += '<div class="panel">';
                     var benfit = 0;
                     //console.log(value['benefits'][value['id']])
                     $.each(value['benefits'][value['id']],function(i,v){
                          benfit++;
                          var tbname = value['id']+""+benfit;
                          planshtml += '<div class="accordion" style="margin-top:5px;">Benefit '+benfit+ '</div>';
                          planshtml +=  '<div class="panel">\n\
                                <div class="tabs">\n\
                                        <ul>';
                          var tabcount = 0;              
                                        $.each(v,function(j,b){
                                            tabcount++;
                                            planshtml += '<li><a href="#'+tbname+'tabs-'+tabcount+'" title="">'+j.replace(/_/g,' ')+'</a></li>';
                                        });  
                          planshtml +=  '</ul>';
                          planshtml +=  '<div class="tabs_container">';
                          tabcount = 0;  
                                        $.each(v,function(j,b){
                                            tabcount++;
                                            planshtml += '<div id="'+tbname+'tabs-'+tabcount+'">'
                                            planshtml += '<table width="100%">'
                                                            $.each(b,function(k,l){
                                                                var benifitval = l != ""?l:"--"
                                                                 planshtml += '<tr>\n\
                                                                                <td width="50%"><b>'+k.replace(/_/g,' ')+'</b></td>\n\
                                                                                <td width="50%">'+benifitval+'</td>\n\
                                                                               </tr>';

                                                            });
                                             planshtml += '</table>\n\
                                                       </div>';
                                        });
                           planshtml +=  '</div>\n\
                                </div>\n\
                            </div>';
                     });
                     if(!value['benefits'][value['id']].length)
                        planshtml += "No Benefits this Plan.<br/>"; 
                     planshtml += '</div>';           
                });
                if(!plans['insplans'].length)
                   planshtml ="<h4 style='text-align:center;'>No Insurance Plans to this patient.</h4>";
                $("#plans").html(planshtml);
                $('.tabs').tabulous({
                    effect: 'scale'
                });
                
                $("#plans").on("click",".accordion",function(evt){
                    evt.preventDefault();
                    $("#planbox #planselected").prop("disabled",false);
                    $(this).find('input[type=radio]').prop("checked",true);
                    $(this).parent().next(".panel").slideToggle();
                    $(this).next(".panel").slideToggle();
                    $(this).toggleClass("active");
                });
            });
        </script>
    </body>
</html>