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

require_once("../../globals.php");
?>
<html>
<head>

<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.js"></script>
</head>
<body class="body_top">
<?php 
$page=$_GET['page'];
$page_val=strpos($page, ','); ?>

    
 <?php if($page_val!=''){
     $list_menu = sqlStatement("SELECT * FROM layout_options " .
        "WHERE form_id = 'ALLCARE' AND uor > 0 AND field_id != ''  AND field_id = 'ext' " .
        "ORDER BY group_name, seq" ); 
        $lres1= sqlFetchArray($list_menu); ?>
    <span class="title"><?php echo xlt($lres1['title']); ?></span><br><br>
    <?php  $arr=explode(",",$page);
//     echo "<pre>"; print_r($arr); echo "</pre>";
      foreach($arr as $val1){
         $list1 = sqlStatement("SELECT * FROM layout_options " .
        "WHERE form_id = 'UCA' AND uor > 0 AND field_id != ''  AND field_id = '$val1' " .
        "ORDER BY group_name, seq" ); 
        while($lres1= sqlFetchArray($list1)){
         $list_value1=$lres1['list_id'];
         $title1=$lres1['title'];
         $description1=$lres1['description'];
        }
       $fres1 = sqlStatement("SELECT * FROM tbl_user_custom_attr_1to1 uc INNER JOIN users u ON u.id = uc.userid " .
                "WHERE username  = '".$_SESSION['authUser']."'" 
       ); 
while($frow1= sqlFetchArray($fres1)){
$reports1=$frow1[$val1];
if($reports1=='YES'){ ?>
      <span class="title"><a href="<?php echo $description1; ?>" target="_blank"><?php echo xlt($title1); ?></a></span><br><br>
<?php }
}
}

 }else{
$list = sqlStatement("SELECT * FROM layout_options " .
"WHERE form_id = 'UCA' AND uor > 0 AND field_id != ''  AND field_id = '$page' " .
"ORDER BY group_name, seq" ); 
while($lres= sqlFetchArray($list)){
 $list_value=$lres['list_id'];
 $title=$lres['title'];
 $description=$lres['description'];
}
$fres = sqlStatement("SELECT * FROM tbl_user_custom_attr_1to1 uc INNER JOIN users u ON u.id = uc.userid " .
"WHERE username  = '".$_SESSION['authUser']."'" 
); 
while($frow= sqlFetchArray($fres)){
$reports=$frow[$page];
}
$report_links=explode("|",$reports);
$i=0;
        foreach($report_links as $key => $value){
                $i++; 
                if($i==1 && !empty($value)) {  ?>
                   <span class="title"><?php echo xlt($title); ?></span>
                 <?php }
                
               
                     $fres1 = sqlStatement("SELECT * FROM list_options " .
                    "WHERE list_id  = '$list_value' AND option_id='$value' " .
                    "ORDER BY  seq");
                    while($frow1= sqlFetchArray($fres1)){?>   
                      <br /><br />
                      <span class="title"><a href="<?php echo $frow1['notes']; ?>" target="_blank"><?php echo xlt($frow1['title']); ?></a></span>
                    <?php }
        } 

 }
 
 ?>
                
</body>
</html>
