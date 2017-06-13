<?php

require_once("../../globals.php");
require_once("../../../library/acl.inc");
require_once("../../../library/formdata.inc.php");
require_once("../../../library/globals.inc.php");

$description=$_POST['description'];
$getICD_desc=sqlStatement("SELECT dx_id,dx_code,formatted_dx_code,short_desc,long_desc,active,revision  
                           FROM icd9_dx_code WHERE dx_code='$description' OR long_desc like '%".$description."%'");

?>       
    <script type="text/javascript">
    function selectedCodeDesc(desc)
    {
       jQuery('#txtDiagnosisCodes').val(desc);
       jQuery('#tblSearchResult').hide();
    }
    </script>
    
<table id="tblSearchResult" style="display:block">
    
    <?php
    $i=0;
    while($resICD_desc=sqlFetchArray($getICD_desc))
    {?>
    <tr>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
        <td onclick="javascript:selectedCodeDesc('<?php echo $resICD_desc['dx_code'].":".$resICD_desc['long_desc']; ?>');"
            onmouseover="jQuery(this).css('background','YELLOW');" style="cursor:pointer"
            onmouseout="jQuery(this).css('background','');">

                <?php echo $resICD_desc['dx_code'].":".$resICD_desc['long_desc']; ?></td>
    </tr>

    <?php  
    $i++;
    }
    ?>

</table>



