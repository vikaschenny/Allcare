<?php
/**
 *
 * Patient summary screen.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Brady Miller <brady@sparmy.com>
 * @link    https://www.open-emr.org
 */

//
require_once("verify_session.php");
 
if(isset($_SESSION['portal_username']) !=''){
    $provider    = $_SESSION['portal_username'];
    $refer       = $_REQUEST['refer'];
    
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}else {
    $provider                    = $_REQUEST['provider'];
    $_SESSION['portal_username'] = $_REQUEST['provider'];
    //for logout
    $refer                       = $_REQUEST['refer'];
    if($_REQUEST['refer']!='')
        $_SESSION['refer'] = $_REQUEST['refer'];
}

//

 require_once("../interface/globals.php");
//

 
$pid=$_REQUEST['pid'];

 ?>
<html>
    <head>
        <link rel='stylesheet' type='text/css' href='../../main/css/jquery.dataTables.css'>
        <link rel='stylesheet' type='text/css' href='../../main/css/dataTables.tableTools.css'>
        <link rel='stylesheet' type='text/css' href='../../main/css/dataTables.colVis.css'>
        <link rel='stylesheet' type='text/css' href='../../main/css/dataTables.colReorder.css'>
        <style>
        div.DTTT_container {
                float: none;
        }
        </style>
        <script type='text/javascript' src='../../main/js/jquery-1.11.1.min.js'></script>
        <script type='text/javascript' src='../../main/js/jquery.dataTables.min.js'></script>
        <script type='text/javascript' src='../../main/js/dataTables.tableTools.js'></script>
        <script type='text/javascript' src='../../main/js/dataTables.colReorder.js'></script>
        <script type='text/javascript' src='../../main/js/dataTables.colVis.js'></script>
        
        <!--<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>-->
         <script type='text/javascript'>
            $(document).ready( function () {
                $('#eligibility').DataTable( {
                    dom: 'T<"clear">lfrtip',
                    "tableTools": {
                        "sSwfPath": "../../swf/copy_csv_xls_pdf.swf",
                        "aButtons": [
                            {
                                "sExtends": "xls",
                                "sButtonText": "Save to Excel"
                            }
                        ]
                    }
                } );
            } );
    </script>
    <script type="text/javascript" src="../library/topdialog.js"></script>
    <script type="text/javascript" src="../library/dialog.js"></script>
    <script type="text/javascript" src="../library/textformat.js"></script>
    <script type="text/javascript" src="../library/dynarch_calendar.js"></script>
        <script type="text/javascript">
            function datafromchildwindow(id,pid) {
//                alert("call");
                location.reload();
//                alert("come");
                history.go(0);

//                window.document.getElementById('fancy_frame').contentWindow.location.reload(true);
//                var table = $('#eligibility').DataTable();
//                table.ajax.reload();
//                $.ajax({
//                    type: "GET",
//                    url: "get_eligibility_data.php",
//                    data: {id:id,pid:pid},
//                    dataType : "json",
//                    success: function(data) {alert(data);
////                        var dataresult = data +' ';
////                        var res = dataresult.split(',');
////                        $("#lbf_form_id").val(res[0]);
////                        $('#savealert').html("<div>Saved.</div>").fadeIn(500,function(){$(this).fadeOut()});
//                    },
//                    error: function(jqXHR, exception){
//                        alert("failed" + jqXHR.responseText);
//                    }    
//                });
                
//                var class_name_check = "check"+month+"-p"+pid;
//                var getvrclass = document.getElementsByClassName(class_name_check);
//                for(var i=0; i<getvrclass.length; i++){
//                    var oldattr = getvrclass[i].getAttribute('onclick');
//                    getvrclass[i].setAttribute( "onClick", " return validate_elig("+pid+","+id.trim()+","+month+")" );
//                }
//                var class_name_checkbox = "checkbox"+month+"-p"+pid;
//                $("."+class_name_checkbox).prop('checked', true);
            }
            function editScreen(pid,form_id){
                window.open("save_eligibility_response_data.php?pid="+pid+"&form_id="+form_id, "", "width=600,height=600,top=0,scrollbars=1,resizable=1");
            }
        </script>
    </head>
    <body style="background-color:#FFFFCC;">
       <table border='1' id='eligibility' class='display'>
            <thead>
                <tr>
                    <?php 
                    $get_fields = '';
                    $getFields = sqlStatement("SELECT field_id,title FROM layout_options WHERE form_id='ELIGIBILITY' AND uor <> 0 ORDER BY group_name, seq"); 
                    echo "<th></th>";
                    while($rowfields = sqlFetchArray($getFields)){
                        echo "<th>".$rowfields['title']."</th>";
                        $get_fields .= "`".$rowfields['field_id']."`,";
                    }
                     
                     ?>
                </tr>
            </thead>
            <tbody>
             <?php 
                $get_fields_names = rtrim($get_fields,",");
                $sql=sqlStatement("select `id`,$get_fields_names from tbl_eligibility_response_data where pid=$pid ORDER BY updated_date DESC"); 
                while($row=sqlFetchArray($sql)){
                    echo "<tr>";
                    foreach($row as $key => $value){
                        if($key == 'id')
                            echo "<td><a href='#' onclick='return editScreen($pid,".$row['id'].");'> Edit </a></td>";
                        else
                            echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>
       
    </body>
</html>