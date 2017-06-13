<?php
/* Copyright (C) 2005-2007 Rod Roark <rod@sunsetsystems.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 */

/*
 *
 * This popup is called when adding/editing a calendar event
 *
 */

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once('../../../globals.php');
include_once("custom_search_objects.php");
include_once("$srcdir/patient.inc");
include_once("$srcdir/formdata.inc.php");

$info_msg = "";

 // If we are searching, search.
 //
$obj_type=$_REQUEST['obj_type'];

if ($_REQUEST['searchby'] && $_REQUEST['searchparm']) {
    $searchby = $_REQUEST['searchby'];
    $searchparm = trim($_REQUEST['searchparm']);
  
    if($obj_type=='patient'){
      if ($searchby == "Last") {
        $result = getPatientLnames("$searchparm","concat(lname,',',fname)as Name,pubpid as ID ,phone_home as Phone,DOB,SS");
      } elseif ($searchby == "Phone") {                  //(CHEMED) Search by phone number
        $result = getPatientPhone("$searchparm","concat(lname,',',fname)as Name,pubpid as ID ,phone_home as Phone,DOB,SS");
      } elseif ($searchby == "ID") {
        $result = getPatientId("$searchparm","concat(lname,',',fname)as Name ,pubpid as ID ,phone_home as Phone,DOB,SS");
      } elseif ($searchby == "DOB") {
        $result = getPatientDOB("$searchparm","concat(lname,',',fname)as Name ,pubpid as ID ,phone_home as Phone,DOB,SS");
      } elseif ($searchby == "SSN") {
        $result = getPatientSSN("$searchparm","concat(lname,',',fname)as Name ,pubpid as ID,phone_home as Phone,DOB,SS");
      }
    }elseif($obj_type=='facility'){
      if ($searchby == "Name") {
        $result = getFacilityName("$searchparm","*");
      } 
    }elseif($obj_type=='insurance'){
      if ($searchby == "Name") {
        $result = getInsuranceName("$searchparm","name,id,attn");
      } 
    }elseif($obj_type=='pharmacy'){
        if ($searchby == "Name") {
         $result = getPharmacyName("$searchparm","name,id");
        } 
    }elseif($obj_type=='users'){
        if ($searchby == "Name") {
            $result = getUserName("$searchby","$searchparm","concat(lname,',',fname)as Name,id,username");
        }elseif($searchby == "UserName"){
            $result = getUserName("$searchby","$searchparm","concat(lname,',',fname)as Name,id,username");
        }elseif($searchby == "Email"){
            $result = getUserName("$searchby","$searchparm","concat(lname,',',fname)as Name,u1.id,username");
        } 
    }elseif($obj_type=='address_Book'){
        if ($searchby == "Name") {
            $result = getAgencyName("$searchby","$searchparm","concat(lname,',',fname)as Name,id,organization");
          
        }elseif($searchby == "Email"){
            $result = getAgencyName("$searchby","$searchparm","concat(lname,',',fname)as Name,id,organization");
        }elseif($searchby == "Organization"){
            $result = getAgencyName("$searchby","$searchparm","concat(lname,',',fname)as Name,id,organization");
        }elseif($searchby == "Address Book Type"){
            $result = getAgencyName("$searchby","$searchparm","concat(lname,',',fname)as Name,id,organization");
        }    
    }
}

function search_obj($field,$param,$obj_type){
    $img_url=$GLOBALS['webroot'].'/interface/pic/ajax-loader.gif';
    $form="<div id='searchCriteria'><form name='search_criteria' id='search_criteria' method='post' action='find_facility_popup.php'>"
    . "<label>Search by:<label><select name='searchby'>";
    foreach($field as $val){
         $form.= "<option value='$val'>$val</option>";
    }
    $form.="</select>&nbsp;<label>For:</label><input type='text' id='searchparm' name='searchparm' size='12' value='$param' />"
            . "<input type='hidden' name='obj_type' id='obj_type' value='$obj_type' /> <input type='submit' id='submitbtn' value='Search'><div id='searchspinner'><img src='$img_url'></div></form></div>";
    
    return  $form;
}

function result_fun($res){
    $table_data="<table><tr>";
    foreach(array_keys($res[0]) as $val){
        $table_data.="<th>$val</th>";
    }
    $table_data.="</tr>";
    for($i=0;$i<count($res);$i++){
        $val1='';
        $table_data.="<tr class='oneresult' id='textshow'>";
        foreach($res[$i] as $fval){
            $val1.=$fval."~";
            $table_data.="<td>$fval</td>";
        }
        $table_data.="</tr>";
        $table_data=str_replace('textshow',trim($val1,"~"),$table_data);
    }
    $table_data.="</table>";
    
    return   $table_data;
    
}
?>

<html>
<head>
<title><?php echo htmlspecialchars( xl('Facility Finder'), ENT_NOQUOTES); ?></title>
<style>
form {
    padding: 0px;
    margin: 0px;
}
#searchCriteria {
    text-align: center;
    width: 100%;
    font-size: 0.8em;
    background-color: #ddddff;
    font-weight: bold;
    padding: 3px;
}
#searchResultsHeader { 
    width: 100%;
    background-color: lightgrey;
}
#searchResultsHeader table { 
    width: 96%;  /* not 100% because the 'searchResults' table has a scrollbar */
    border-collapse: collapse;
}
#searchResultsHeader th {
    font-size: 0.7em;
}
#searchResults {
    width: 100%;
    height: 80%;
    overflow: auto;
}

/* search results column widths */
.srName { width: 30%; }
.srPhone { width: 21%; }
.srSS { width: 17%; }
.srDOB { width: 17%; }
.srID { width: 15%; }

#searchResults table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
}
#searchResults tr {
    cursor: hand;
    cursor: pointer;
}
#searchResults td {
    font-size: 0.7em;
    border-bottom: 1px solid #eee;
}
.oneResult { }
.billing { color: red; font-weight: bold; }

/* for search results or 'searching' notification */
#searchstatus {
    font-size: 0.8em;
    font-weight: bold;
    padding: 1px 1px 10px 1px;
    font-style: italic;
    color: black;
    text-align: center;
}
.noResults { background-color: #ccc; }
.tooManyResults { background-color: #fc0; }
.howManyResults { background-color: #9f6; }
#searchspinner { 
    display: inline;
    visibility: hidden;
}

/* highlight for the mouse-over */
.highlight {
    background-color: #336699;
    color: white;
}
</style>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.2.2.min.js"></script>
<!-- ViSolve: Verify the noresult parameter -->
<?php
if(isset($_GET["res"])){
echo '
<script language="Javascript">
			// Pass the variable to parent hidden type and submit
			opener.document.theform.resname.value = "noresult";
			opener.document.theform.submit();
			// Close the window
			window.self.close();
</script>';
}
?>
<!-- ViSolve: Verify the noresult parameter -->

<script language="JavaScript">

 function selpid(field,type) {
  opener.setvalue(field,type);
  window.close();
  return false;
 }

</script>

</head>

<body>
    <?php
        if($obj_type=='patient'){
             $search_field=array("Last","Phone","ID","SSN","DOB");
        }elseif($obj_type=='facility'){
             $search_field=array("Name");
        }elseif($obj_type=='insurance'){
             $search_field=array("Name");
        }elseif($obj_type=='pharmacy'){
             $search_field=array("Name");
        }elseif($obj_type=='users'){
             $search_field=array("Name","UserName","Email");
        }elseif($obj_type=='users'){
             $search_field=array("Name","Email");
        }elseif($obj_type=='address_Book'){
             $search_field=array("Name","Email","Organization","Address Book Type");
        }
       
        echo search_obj($search_field,$_REQUEST['param'],$obj_type); 
 
        if (! isset($_REQUEST['searchparm'])): ?>
            <div id="searchstatus">
                <?php echo htmlspecialchars( xl('Enter your search criteria above'), ENT_NOQUOTES); ?>
            </div>
        <?php elseif (count($result) == 0): ?>
            <div id="searchstatus" class="noResults">
                <?php echo htmlspecialchars( xl('No records found. Please expand your search criteria.'), ENT_NOQUOTES); ?>
             </div>
        <?php elseif (count($result)>=100): ?>
            <div id="searchstatus" class="tooManyResults">
                <?php echo htmlspecialchars( xl('More than 100 records found. Please narrow your search criteria.'), ENT_NOQUOTES); ?>
            </div>
        <?php elseif (count($result)<100): ?>
            <div id="searchstatus" class="howManyResults">
                <?php echo htmlspecialchars( count($result), ENT_NOQUOTES); ?> 
                <?php echo htmlspecialchars( xl('records found.'), ENT_NOQUOTES); ?>
            </div>    
        <?php endif; 
        echo result_fun($result); ?>

<script language="javascript">

// jQuery stuff to make the page a little easier to use

$(document).ready(function(){
    $("#searchparm").focus();
    $(".oneresult").mouseover(function() { $(this).toggleClass("highlight"); });
    $(".oneresult").mouseout(function() { $(this).toggleClass("highlight"); });
    $(".oneresult").click(function() { SelectRow(this); });
    //ViSolve 
    $(".noresult").click(function () { SubmitForm(this);});

    //$(".event").dblclick(function() { EditEvent(this); });
    $("#theform").submit(function() { SubmitForm(this); });

});

// show the 'searching...' status and submit the form
var SubmitForm = function(eObj) {
    $("#submitbtn").css("disabled", "true");
    $("#searchspinner").css("visibility", "visible");
    return true;
}

// another way to select a patient from the list of results
// parts[] ==>  0=PID, 1=LName, 2=FName, 3=DOB
var SelectRow = function (eObj) {
    objID = eObj.id;
    
    return selpid(objID,'<?php echo $obj_type; ?>');
}

</script>

</center>
</body>
</html>
