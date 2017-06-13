<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="HandheldFriendly" content="true">
<title>Patient Center Batch</title>
<link rel='stylesheet' type='text/css' href='css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='css/dataTables.colReorder.css'>
<script type='text/javascript' src='js/jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='js/dataTables.tableTools.js'></script>
<script type='text/javascript' src='js/dataTables.colReorder.js'></script>
<script type='text/javascript' src='js/dataTables.colVis.js'></script>
<script type='text/javascript'>
    $(document).ready(function() {
        $('#dupliadjustment').dataTable( {
        iDisplayLength: 100,
        dom: 'T<\"clear\">lfrtip',
        tableTools: {
            aButtons: [
                {
                    sExtends: "xls",
                    sButtonText: "Save to Excel",
                    sFileName: $('#openemrTitle').val() + " duplicate adjustments "+ $('#currTime').val() +".csv"
                }
            ]
        }
        } ); 
    });     
</script>
<style>a{text-decoration: none;}</style>
</head>
<body style="background-color:#FFFFCC;">
    <div style="width:1350px; height:590px; overflow: scroll;">
    <table id='dupliadjustment' class='display' cellspacing='0' width='100%'>
    <thead>
        <tr>
<!--            <th>Zirmed Id</th>-->
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Suffix</th>
            <th>Gender</th>
            <th>DOB</th>
            <th>Invoice</th>
            <th>Code</th>
            <th>Adjustment Amount</th>
            <th>Document Reference</th>
            <th>Check Date</th>
            <th>Deposit Date</th>
            <th>Payer</th>
         </tr>
    </thead>
    
        <?php
            $res = sqlStatement("SELECT COUNT( * ) AS c, f.id, p.fname, p.mname, p.lname, p.title,p.sex,p.DOB,a.pid, a.encounter, a.code_type, a.code, a.adj_amount, a.reason_code, a.post_time, s.reference, s.check_date, s.deposit_date, i.name
                                FROM ar_activity AS a
                                LEFT OUTER JOIN ar_session AS s ON s.session_id = a.session_id
                                LEFT OUTER JOIN insurance_companies AS i ON i.id = s.payer_id
                                INNER JOIN form_encounter f ON f.encounter = a.encounter
                                INNER JOIN patient_data p ON p.pid = a.pid
                                WHERE s.reference <>  ''
                                AND a.adj_amount <>  '0.00'
                                GROUP BY s.reference, a.adj_amount, a.pid, a.encounter
                                HAVING c >1
                                ORDER BY s.check_date, a.sequence_no");
            while($rowTemp=sqlFetchArray($res))
            {
                $fname=(isset($rowTemp['fname'])) ? $rowTemp['fname'] : '';
                $mname=(isset($rowTemp['mname'])) ? $rowTemp['mname'] : '';
                $lname=(isset($rowTemp['lname'])) ? $rowTemp['lname'] : '';
                $title=(isset($rowTemp['title'])) ? $rowTemp['title'] : '';
                $sex=(isset($rowTemp['sex'])) ? $rowTemp['sex'] : '';
                $dob=(isset($rowTemp['DOB'])) ? $rowTemp['DOB'] : '';
                
                $code=(isset($rowTemp['code'])) ? $rowTemp['code'] : '';
                $adjamt=(isset($rowTemp['adj_amount'])) ? $rowTemp['adj_amount'] : '';
                $reference=(isset($rowTemp['reference'])) ? $rowTemp['reference'] : '';
                $chkdate=(isset($rowTemp['check_date'])) ? $rowTemp['check_date'] : '';
                $depdate=(isset($rowTemp['deposit_date'])) ? $rowTemp['deposit_date'] : '';
                $payername=(isset($rowTemp['name'])) ? $rowTemp['name'] : '';
                
                if($sex == 'Male') $sex = 1;
                if($sex == 'Female') $sex = 2;
                if($sex == '') $sex = 0;
                
                $formatedDate = date('m-d-Y',strtotime($dob));
                
                if(strtolower($status) == 'single') $status = 1;
                if(strtolower($status) == 'married') $status = 2;
                if(strtolower($status) == 'separated') $status = 3;
                if(strtolower($status) == 'divorced') $status = 4;
                if(strtolower($status) == 'widowed') $status = 5;
                                
                echo "<tr>";
                //echo "<td>$domain_identifier</td><td>Patient</td><td>$fname</td><td>$mname</td><td>$lname</td><td>$title</td>";
                echo "<td>$fname</td><td>$mname</td><td>$lname</td><td>$title</td>";
                echo "<td>$sex</td><td>$formatedDate</td>";
                ?>
                <td>
                <a href="../billing/sl_eob_invoice.php?id=<?php echo $rowTemp['id'] ?>"
                 target="_blank"><?php echo $rowTemp['pid'] . '.' . $rowTemp['encounter']; ?></a>
               </td>
                <?php
                echo "<td>$code</td><td>$adjamt</td><td>$reference</td><td>$chkdate</td><td>$depdate</td><td>$payername</td>";
                echo "</tr>";
            }    
        ?>
    
    </table>
    <input type='hidden' id='openemrTitle' value='<?php echo text($openemr_name); ?>' />
    <input type='hidden' id='currTime' value='<?php echo time(); ?>' />
    </div>
</body>
</html>