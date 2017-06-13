<?php
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


 $payer=$_POST['payers'];
 $type=$_POST['type'];


$payers=sqlStatement("SELECT id,name FROM  `insurance_companies`");
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1">
        <link rel="stylesheet" href="../../../library/bootstrap/docs/css/bootstrap-3.2.0.min.css"/>
        <link rel="stylesheet" href="../../../library/datatables_responsive/css/dataTables.bootstrap.min.css"/>
        <link rel="stylesheet" href="../../../library/datatables_responsive/css/responsive.bootstrap.min.css"/>
        <link rel="stylesheet" href="../../../library/datatables_responsive/css/dataTables.tableTools.css"/>
        <link rel="stylesheet" href="../../../library/customselect/css/select2.css"/>
        <link rel="stylesheet" href="../../../library/customselect/css/select2-bootstrap.css"/>
        <link rel="stylesheet" href="css/patientlist.css"/>
        <script src="../../../library/bootstrap/docs/js/jquery-2.1.1.min.js"></script>
    </head>
    <body style="font-size: 13px;"> 
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h4>Patient List</h4>
                    </div>
                   <form name='payerpatients' method='post' action='' class="form-inline">
                       <div class="row">
                           <div class="form-group col-lg-5 col-sm-5 col-xs-12">
                               <label for="payers" class="control-label col-xs-5 col-sm-4 col-lg-3">Payer Name:</label>
                               <div class="col-xs-7 col-sm-8 col-lg-9">
                                    <select id="payers" name="payers" class="select2 input-default">
                                        <option value=''></option>    
                                    <?php 
                                    while($rows=sqlFetchArray($payers)) {
                                        echo "<option value=".$rows['id']; if($payer==$rows['id']){ echo "  selected"; } echo ">".$rows['name']."</option>";
                                    }
                                    ?>
                                    </select>
                               </div>
                           </div>
                           <div class="form-group col-lg-5 col-sm-5 col-xs-12">
                               <label for="type" class="control-label col-xs-5 col-sm-4 col-lg-3">Payer Type:</label>
                               <div class="col-xs-7 col-lg-9 col-sm-8">
                                    <select id="type" name='type' class="select2 input-default">
                                        <option value='All'>All</option>
                                        <option value='primary' <?php if($type=='primary'){ echo "selected"; }  ?> >Primary</option>
                                        <option value='secondary' <?php if($type=='secondary'){ echo "selected"; }  ?> >Secondary</option>
                                        <option value='tertiary' <?php if($type=='tertiary'){ echo "selected"; }  ?> >Tertiary</option>
                                    </select>
                               </div>
                           </div>
                           <div class="form-group col-xs-12 col-lg-2 col-sm-2 text-center">
                                <button class="btn btn-default btn-sm costomebtn"  name="search" id="search"><i class="glyphicon glyphicon-search"></i><span>&nbsp;Search</span></button>
                           </div>
                       </div>
                        
                    </form>
                    <table id="PatientList" class="table table-striped table-bordered dt-responsive nowrap" width="100%" style="font-size: 13px; border-radius: 5px;">
                        <thead>
                            <tr>
                                <th>Patient Name</th>  
                                <th>Patient Id</th>
                                <th>Date of Birth</th>
                                <th>Address</th>
                                <th>Provider</th>
                                <th>Plan Name</th>
                                <th>Policy Number</th>
                                <th>Group Number</th>
                                <th>Payer Type</th>
                            </tr>
                        </thead>
                    <?php //to get  patient  list  using filters
                            if($payer!='' && $type!='All'){

                            $list=sqlStatement("select * from insurance_data  i  inner join patient_data p on i.pid=p.pid where provider=$payer and type='$type' ");
                            }else if($payer!='' && $type=='All'){
                                 $list=sqlStatement("select * from insurance_data  i  inner join patient_data p on i.pid=p.pid where provider=$payer");
                            }
                            while($data=sqlFetchArray($list)){ 
                             $payers1=sqlStatement("SELECT name FROM  `insurance_companies`  where id=".$data['provider']);
                             $row1=sqlFetchArray($payers1);
                             echo"<tr>";
                             echo "<td>";
                             if($data['fname']!='') echo $data['fname']." ";
                             if($data['lname']!='') echo $data['lname']; echo "</td>";
                             echo "<td>".$data['pid']."</td>";
                             echo "<td>".$data['DOB']."</td>";

                             echo "<td>";
                             if($data['street']!='') echo $data['street'].", ";
                             if($data['city']!='') echo $data['city'].",";
                             if($data['state']!='') echo $data['state'].", ";
                             if($data['postal_code']!='')$data['postal_code'].", "; 
                             if($data['country_code']!='') echo $data['country_code']."</td>";
                             echo "<td>".$row1['name']."</td>";
                             echo "<td>".$data['plan_name']."</td>";
                             echo "<td>".$data['policy_number']."</td>";
                             echo "<td>".$data['group_number']."</td>";
                             echo "<td>".ucwords($data['type'])."</td>";
                             echo"</tr>";
                            }

                    ?>
                    </table>
                </div>
            </div>
        </div>    
        <script src="../../../library/bootstrap/docs/js/bootstrap-3.2.0.min.js"></script>
        <script src="../../../library/datatables_responsive/js/jquery.dataTables.min.js"></script>
        <script src="../../../library/datatables_responsive/js/dataTables.bootstrap.min.js"></script>
        <script src="../../../library/datatables_responsive/js/dataTables.responsive.min.js"></script>
        <script src="../../../library/datatables_responsive/js/dataTables.tableTools.js"></script>
        <script src="../../../library/customselect/js/select2.js"></script>
        <script src="js/patientsList.js"></script>
    </body>
</html>