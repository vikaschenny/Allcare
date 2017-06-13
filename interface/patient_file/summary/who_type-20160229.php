<?php

require_once("../../globals.php");

$type=$_REQUEST['type'];  
$id=$_REQUEST['id'];
$fid=$_REQUEST['fid'];
if($id!=''){
    $sql12=sqlStatement("select * from tbl_form_chartoutput_transactions where id=$id");
    $res21=sqlFetchArray($sql12);
}


if($type=='provider') {
    
    $ures = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
    "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
    "AND authorized = 1 " .
    "ORDER BY lname, fname");
    echo "Provider: <select name='form_provider1' id='form_provider1' title='provider' onchange='provider_details();' >";
    echo "<option value=''>" . htmlspecialchars(xl('Select'), ENT_NOQUOTES) . "</option>";
    while ($urow = sqlFetchArray($ures)) {
        if($urow['fname']!='' && $urow['lname']!='') {
           $uname = htmlspecialchars( $urow['fname'] . ' ' . $urow['lname'], ENT_NOQUOTES);
           $optionId = htmlspecialchars( $urow['id'], ENT_QUOTES);
           echo "<option value='$optionId'";
           if ($urow['id'] == $res21['provider']) echo " selected";
          echo ">$uname</option>";
        }
        
    }
    echo "</select>";
    
}elseif($type=='facility'){
    
    $query = "SELECT id, name FROM facility ORDER BY name";
    $fres = sqlStatement($query);

    $name = htmlspecialchars($name, ENT_QUOTES);
    echo "Facility: <select name='form_facility' id='form_facility' onchange='facility_details();'>\n";
    echo "<option value=''>Select</option>";
    while ($frow = sqlFetchArray($fres)) {
    $facility_id = $frow['id'];
    $option_value = htmlspecialchars($facility_id, ENT_QUOTES);
    $option_selected_attr = '';
    if ($res21['facility'] == $facility_id) {
      $option_selected_attr = ' selected="selected"';
      $have_selected = true;
    }
    $option_content = htmlspecialchars($frow['name'], ENT_NOQUOTES);
    echo "    <option value=\"$option_value\" $option_selected_attr>$option_content</option>\n";
    }
    echo "   </select>\n";
}else if($type=="pharmacy"){
     echo "Pharmacies: <select name='form_pharmacy' id='form_pharmacy' title='pharmacy' onchange='pharmacy_details();'>";
    echo "<option value=''>Select</option>";
    $pres =sqlStatement("SELECT d.id, d.name, a.line1, a.city, " .
    "p.area_code, p.prefix, p.number FROM pharmacies AS d " .
    "LEFT OUTER JOIN addresses AS a ON a.foreign_id = d.id " .
    "LEFT OUTER JOIN phone_numbers AS p ON p.foreign_id = d.id " .
    "AND p.type = 2 " .
    "ORDER BY name, area_code, prefix, number");
    while ($prow = sqlFetchArray($pres)) {
      $key = $prow['id'];
      $optionValue = htmlspecialchars( $key, ENT_QUOTES);
      $optionLabel = htmlspecialchars( $prow['name'] . ' ' . $prow['area_code'] . '-' .
        $prow['prefix'] . '-' . $prow['number'] . ' / ' .
	$prow['line1'] . ' / ' . $prow['city'], ENT_NOQUOTES);
      echo "<option value='$optionValue'";
      if ($res21['pharmacy'] == $key) echo " selected";
      echo ">$optionLabel</option>";
    }
    echo "</select>";
}else if($type=='payer'){
    
  $query = "SELECT id, name FROM insurance_companies ORDER BY name";
  $fres = sqlStatement($query);

  $name = htmlspecialchars($name, ENT_QUOTES);
  echo "Payer: <select name='form_payer' id='form_payer' onchange='payer_details();'>\n";
  echo "<option value='' >Select</option>";
  while ($frow = sqlFetchArray($fres)) {
    $insurancecompany_id = $frow['id'];
    $option_value = htmlspecialchars($insurancecompany_id, ENT_QUOTES);
    $option_selected_attr = '';
    if ($res21['payer'] == $insurancecompany_id) {
      $option_selected_attr = ' selected="selected"';
      $have_selected = true;
    }
    $option_content = htmlspecialchars($frow['name'], ENT_NOQUOTES);
    echo "    <option value=\"$option_value\" $option_selected_attr>$option_content</option>\n";
  }

  echo "   </select>\n";
} else{
    
    if($fid=='incomplete') {
        $encounter=$_REQUEST['enc'];
        $patient=$_REQUEST['pid'];
        
        $sql34=sqlStatement("select rendering_provider from form_encounter where pid=$patient and encounter=$encounter");
        $res34=sqlFetchArray($sql34);
        
        $ures = sqlStatement("SELECT id, fname, lname, organization, username FROM users " .
            "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
            " AND ( username = '' OR authorized = 1 ) AND abook_type='$type' ORDER BY organization, lname, fname"); ?>
            <label>who:</label>  
       <select name="refer_to" id="refer_to" title="" onchange="addr_bk();" >-->
            <?php echo "<option value=''>" . htmlspecialchars( xl('Select'), ENT_NOQUOTES) . "</option>";
      while ($urow = sqlFetchArray($ures)) { 
              $uname = $urow['organization'];
              if (empty($uname) || substr($uname, 0, 1) == '(') {
                $uname = $urow['lname'];
                if ($urow['fname']) $uname .= ", " . $urow['fname'];
              }
              $optionValue = htmlspecialchars( $urow['id'], ENT_QUOTES);
              $optionLabel = htmlspecialchars( $uname, ENT_NOQUOTES);
              echo "<option value='$optionValue'";
              $title = $urow['username'] ? xl('Local') : xl('External');
              $optionTitle = htmlspecialchars( $title, ENT_QUOTES);
              echo " title='$optionTitle'";
              if ($urow['id'] == $res34['rendering_provider']) echo " selected";
              echo ">$optionLabel</option>";
            }
         echo "</select>";
        
  }else {
        
        $ures = sqlStatement("SELECT id, fname, lname, organization, username FROM users " .
            "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
            " AND ( username = '' OR authorized = 1 ) AND abook_type='$type' ORDER BY organization, lname, fname"); ?>
            <label>who:</label>  
            <select name="refer_to" id="refer_to" title="" onchange="addr_bk();" >
            <?php echo "<option value=''>" . htmlspecialchars( xl('Select'), ENT_NOQUOTES) . "</option>";
            while ($urow = sqlFetchArray($ures)) {
              $uname = $urow['organization'];
              if (empty($uname) || substr($uname, 0, 1) == '(') {
                $uname = $urow['lname'];
                if ($urow['fname']) $uname .= ", " . $urow['fname'];
              }
              $optionValue = htmlspecialchars( $urow['id'], ENT_QUOTES);
              $optionLabel = htmlspecialchars( $uname, ENT_NOQUOTES);
              echo "<option value='$optionValue'";
              $title = $urow['username'] ? xl('Local') : xl('External');
              $optionTitle = htmlspecialchars( $title, ENT_QUOTES);
              echo " title='$optionTitle'";
              if ($urow['id'] == $res21['refer_to']) echo " selected";
              echo ">$optionLabel</option>";
            }
            echo "</select>";
    }
 }

            
?>
 <br><br><div id="addr_bk"></div>
<script type="text/javascript">
 var inc='<?php echo $fid; ?>';
        if(inc=='incomplete'){
           var url="../patient_file/summary/addr_bk_details.php";
        }else {
           var url="addr_bk_details.php";
        }
        //$(document).ready(function(){
         var nid=0; var pro=0; var fac=0;  var pharmacy=0; var payer=0;
         if(typeof(jQuery('#refer_to').val())!='undefined'){
            nid =jQuery('#refer_to').val();
          
         }
        if(typeof(jQuery('#form_provider1').val())!='undefined'){
            pro=jQuery('#form_provider1').val();
           
        }
        
        if(typeof(jQuery('#form_facility').val())!='undefined'){
            fac=jQuery('#form_facility').val();
           
        }
        
        if(typeof(jQuery('#form_pharmacy').val())!='undefined'){
           pharmacy=jQuery('#form_pharmacy').val();
         
        }
        if(typeof(jQuery('#form_payer').val())!='undefined'){
            payer=jQuery('#form_payer').val();
            
        }
        
       
        $.ajax({
        type: 'POST',
        url: url,	
        data:{org:nid,provider:pro,facility:fac,pharmacy:pharmacy,payer:payer},
        success: function(response)
        {
          
         $('#addr_bk').html(response);

        },
        failure: function(response)
        {
            alert("error"); 
        }		
    //});	
 });
function addr_bk(){
    var value = $("#refer_to").val();
   
    $.ajax({
        type: 'POST',
        url: url,	
        data:{org:value},
        success: function(response)
        {
           
         $('#addr_bk').html(response);

        },
        failure: function(response)
        {
            alert("error"); 
        }		
    });	
}

//for provider details
function provider_details(){
    var value1 = $("#form_provider1").val();
    $.ajax({
        type: 'POST',
        url: url,	
        data:{provider:value1},
        success: function(response)
        {
           
         $('#addr_bk').html(response);

        },
        failure: function(response)
        {
            alert("error"); 
        }		
    });	
}
function facility_details(){
    var value2 = $("#form_facility").val();
    $.ajax({
        type: 'POST',
        url: url,	
        data:{facility:value2},
        success: function(response)
        {
           
         $('#addr_bk').html(response);

        },
        failure: function(response)
        {
            alert("error"); 
        }		
    });	
}
function pharmacy_details(){
    var value3 = $("#form_pharmacy").val();
    $.ajax({
        type: 'POST',
        url: url,	
        data:{pharmacy:value3},
        success: function(response)
        {
           
         $('#addr_bk').html(response);

        },
        failure: function(response)
        {
            alert("error"); 
        }		
    });	
}
function payer_details(){
    var value4 = $("#form_payer").val();
    $.ajax({
        type: 'POST',
        url: url,	
        data:{payer:value4},
        success: function(response)
        {
           
         $('#addr_bk').html(response);

        },
        failure: function(response)
        {
            alert("error"); 
        }		
    });	
}
</script>