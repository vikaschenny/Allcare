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
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../interface/globals.php");
require_once("../library/formdata.inc.php");
require_once("../library/globals.inc.php");

$provider=$_REQUEST['provider'];
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Medical Website Template | News :: W3layouts</title>
		<link href="css/style.css" rel="stylesheet" type="text/css"  media="all" />
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/jquery.dataTables.css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.tableTools.css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colVis.css'>
                <link rel='stylesheet' type='text/css' href='../interface/main/css/dataTables.colReorder.css'>
                <style>
                div.DTTT_container {
                        float: none;
                }
                </style>
                <script type='text/javascript' src='../interface/main/js/jquery-1.11.1.min.js'></script>
<!--                <script type='text/javascript' src='../interface/main/js/jquery.dataTables.min.js'></script>-->
                <script type='text/javascript' src='../interface/main/js/jquery.dataTables-1.10.7.min.js'></script>
                <script type='text/javascript' src='../interface/main/js/dataTables.tableTools.js'></script>
                <script type='text/javascript' src='../interface/main/js/dataTables.colReorder.js'></script>
                <script type='text/javascript' src='../interface/main/js/dataTables.colVis.js'></script>
	</head>
	<body>
		<!---start-wrap---->
		
			<!---start-header---->
			<div class="header">
				
					<div class="main-header">
						<div class="wrap">
							<div class="logo">
								<a href="index.html"><img src="images/logo.png" title="logo" /></a>
							</div>
							<div class="social-links">
								<ul>
									
                                                                      <li class="login"><a href="logout_page.php">Logout</a></li>
									<div class="clear"> </div>
								</ul>
							</div>
							<div class="clear"> </div>
						</div>
					</div>
					<div class="clear"> </div>
				             <div id='cssmenu1'>
                                            <?php $sql12=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' ORDER BY seq");?>
                                              <ul>   
                                                 <?php while($row11=sqlFetchArray($sql12)){ 
                                                        $mystring = $row11['option_id'];
                                                        $pos = strpos($mystring, '_');
                                                        if(false == $pos) {
                                                                $sql_lis=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$mystring' ORDER BY seq");
                                                                while($row_lis=sqlFetchArray($sql_lis)){
                                                                $opt_id=$row_lis['option_id']."_";
                                                                $sql_li=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id LIKE '%$opt_id%' ORDER BY seq");
                                                                if(sqlNumRows($sql_li) != 0 ){ ?>
                                                                     <li class='has-sub'><a href="<?php echo $row_lis['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row_lis['title']; ?></span></a>
                                                                     <ul>
                                                                 <?php while($row_li=sqlFetchArray($sql_li)){ 
                                                                             $ex=explode("_",$row_li['option_id']); 
                                                                             if(count($ex)==2){
                                                                                   $sub1=$ex[0]."_".$ex[1];
                                                                                   $sql_sub=sqlStatement("SELECT * FROM  `list_options` WHERE list_id ='AllCareProviderPortal' AND option_id = '$sub1' ORDER BY seq");
                                                                                   $row_sub=sqlFetchArray($sql_sub);
                                                                                   ?>
                                                                                    <li class=last'><a href="<?php echo $row_sub['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php  echo $row_sub['title']; ?></span></a> 
                                                                                   </li>   
                                                                            <?php   } ?>
                                                                             
                                                                    <?php } ?> </ul></li>
                                                                <?php }else { 
                                                                    if($row11['option_id']=='plist'){?>
                                                                             <li class='active'><a href="<?php echo $row11['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row11['title']; ?></span></a></li>
                                                                <?php  }else { ?>
                                                                     <li><a href="<?php echo $row11['notes']; ?>?provider=<?php echo $provider;  ?>"><span><?php echo $row11['title']; ?></span></a></li>
                                                               <?php }
                                                                
                                                                }
                                                               
                                                            }    
                                                         }
                                                         
                                                    } ?>
                                              </ul>      
                                        </div>
			</div>
			<!---End-header---->
			<!----start-content----->
			<div class="content">
				<div class="wrap">
					<div class="services">
						<div class="service-content">
						<?php
                                                      function display_db_query($query_string) {
                                                            // perform the database query
                                                            $result_id = mysql_query($query_string)
                                                            or die("display_db_query:" . mysql_error());
                                                            // find out the number of columns in result
                                                            $column_count = mysql_num_fields($result_id)
                                                            or die("display_db_query:" . mysql_error());
                                                            // Here the table attributes from the $table_params variable are added
                                                            echo "<br/>";
                                                            print("<TABLE border='1' id='patient_data' class='display'>\n");
                                                            // optionally print a bold header at top of table

                                                                    print("<thead style='background-color:#007DAD;'><tr>");
                                                                    for($column_num = 0; $column_num < $column_count; $column_num++) {
                                                                            $field_name = mysql_field_name($result_id, $column_num);
                                                                            print("<th>$field_name</th>");
                                                                    }
                                                                    print("</tr></thead>\n");

                                                            // print the body of the table
                                                            while($row = mysql_fetch_row($result_id)) {
                                                                    print("<tr align=left valign=top>");
                                                                    for($column_num = 0; $column_num < $column_count; $column_num++) {
                                                                            print("<td>$row[$column_num]</td>\n");
                                                                    }
                                                                    print("</tr>\n");
                                                            }
                                                             print("<tfoot style='background-color:#007DAD;'><tr>");
                                                                    for($column_num = 0; $column_num < $column_count; $column_num++) {
                                                                            $field_name = mysql_field_name($result_id, $column_num);
                                                                            print("<th>$field_name</th>");
                                                                    }
                                                                    print("</tr></tfoot>\n");
                                                            print("</table>\n"); 
                                                    }
                                                    function display_db_table($provider) {
                                                        //echo "select id from users where username='".$provider."'";
                                                          $sql=sqlStatement("select id from users where username='".$provider."'");
                                                          $row=sqlFetchArray($sql);
                                                          $id=$row['id'];
                                                          $sql1=sqlStatement("SELECT DISTINCT(fe.pid),p.* from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid where provider_id=$id");
                                                          $row1=sqlFetchArray($sql1);
                                                          //echo "<pre>"; print_r($row1); echo "</pre>";
                                                          $sql12="SELECT DISTINCT(fe.pid),p.* from form_encounter fe  INNER JOIN patient_data p ON p.pid=fe.pid where provider_id=$id";
                                                            display_db_query($sql12);
                                                    }
                                                display_db_table($provider); ?>
						</div>

						<div class="clear"> </div>
					</div>
				<div class="clear"> </div>
				</div>
			<!----End-content----->
		</div>
		<!---End-wrap---->
                <script type='text/javascript'>
            
            $(document).ready( function () {
//                $('#patient_data').DataTable( {
//                    dom: 'T<"clear">lfrtip',
//                    "tableTools": {
//                        "sSwfPath": "../../swf/copy_csv_xls_pdf.swf",
//                        "aButtons": [
//                            {
//                                "sExtends": "xls",
//                                "sButtonText": "Save to Excel"
//                            }
//                        ]
//                    }
//                } );
              $('#patient_data tfoot th').each( function () {
        var title = $('#patient_data thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#patient_data').DataTable({ "iDisplayLength": 100});
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            that
                .search( this.value )
                .draw();
        } );
    } );
            } );
    </script>
	</body>
</html>

