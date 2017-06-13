<?php

require_once("../../globals.php");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/globals.inc.php");

$extension_name=$_GET['extension_name'];

?>

<html>    
    <head>         
        
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>
        <script type="text/javascript" src="../../library/topdialog.js"></script>
        <script type="text/javascript" src="../../library/dialog.js"></script>

        <script language="javascript">
// jQuery stuff to make the page a little easier to use

            $(document).ready(function(){
                
                $("#<?php echo $extension_name;?>_cancel").click(function() { 
                    window.close();
                    window.opener.unselectTable('<?php echo $_GET['selectedBox'];?>');
                });
                $("#<?php echo $extension_name;?>_select_fields_ok").click(function() {
                    /*var parentForm=window.opener.document.forms['theform'];
                    parentForm.elements['txtFieldName'].value=document.getElementById('hdnChkFieldName').value;
                    parentForm.elements['data_types'].value=document.getElementById('hdnChkFieldType').value;
                       */                 
                    //var inputs = document.form_tbl_fields.getElementsByTagName('input:');
                    var inputs = jQuery("input:checkbox");
                    var checked = [];
                    var checkedType=[];
                    
                    for (var i = 0; i < inputs.size(); i++) 
                    {
                        if (inputs[i].checked) 
                        {
                          checked.push(inputs[i].value);   
                          checkedType.push(jQuery('#<?php echo $extension_name;?>_hdnFieldType_'+i).val());
                        }
                    }
        
                    window.close();
                    //window.opener.receivedFromChild('<?php echo $_GET['table_name'];?>',checked,checkedType );                                                                               
                    window.opener.<?php echo $extension_name;?>_receivedFromChild('<?php echo $_GET['selectedBox'];?>','<?php echo $_GET['table_name'];?>',checked,checkedType );                                        
                });                                                
            });
                                                
        </script>
    </head>
    
    <body class="body_top">
        <form id='<?php echo $extension_name;?>_form_tbl_fields' name='<?php echo $extension_name;?>_form_tbl_fields'>
<?php

echo "Table - ".$_GET['table_name'];
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$res = sqlStatement("SHOW COLUMNS FROM ".$_GET['table_name']." WHERE Field!='id'");
      echo "<br><br>Select the Fields<br>"; 
      $i=0;
      while ($row = sqlFetchArray($res)) 
      {        
        echo "<input type='checkbox' id='".$extension_name."_chkField_".$i."' name='".$extension_name."_chkField[]' 
              value='".$row['Field']."' /><label for='".$extension_name."_chkField_".$i."'>".$row['Field']."</label><br>";         
        echo "<input type='hidden' id='".$extension_name."_hdnFieldName_".$i."' name='".$extension_name."_hdnFieldName[]' value='".$row['Field']."' />";        
        echo '<input type="hidden" id="'.$extension_name.'_hdnFieldType_'.$i.'" 
            name="'.$extension_name.'_hdnFieldType[]" value="'.$row['Type'].'" />'; 
        $i++;
      }

?>
        <br>
    <center>
        <input type='button' id='<?php echo $extension_name;?>_select_fields_ok' value='<?php echo xla('OK');?>'/>
        <input type='button' id='<?php echo $extension_name;?>_cancel' value='<?php echo xla('Cancel');?>' />        
    </center>
               
        </form>
        
    </body>
    
</html>

