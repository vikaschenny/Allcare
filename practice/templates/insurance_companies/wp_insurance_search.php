<?php 
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;

require_once("../../verify_session.php");


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

require_once("../../../interface/globals.php");

$list=sqlStatement("select * from list_options where list_id='AllcareWPDB'");
        while ($list_val=sqlFetchArray($list)) {
            //wordpress db connection
            if($list_val['option_id']=='host'){
                $dbhost=$list_val['notes'];
            }
            if($list_val['option_id']=='user'){
                 $dbuser=$list_val['notes'];
            }
            if($list_val['option_id']=='pwd') {
                $dbpass=$list_val['notes'];
            }
            if($list_val['option_id']=='dbname'){
                $dbname=$list_val['notes']; 
            }
        }

        global $wpdb;


        $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
       // echo $_POST['ins_name'];
       if($_POST['ins_name']!=''){
            $insname    = explode(":",$_POST['ins_name']);
            $name       = $insname[0];
            $payer_id   = $insname[1];
            
            $emr_sql=sqlStatement("select id from insurance_companies where name='$name' and payer_id='$payer_id'");
            $ins_row=sqlFetchArray($emr_sql);
            $post_title = $name.":".$payer_id;
            
            
            $sql_post=$conn->query("select ID from wp_posts where post_type='payer' and post_title='$post_title'");
            $post_id = $sql_post->fetch_object();
            if(!empty($post_id)){
                $sql_postmeta=$conn->query("select * from wp_postmeta where post_id=$post_id->ID ");
                $params='';
                if ($sql_postmeta->num_rows != 0) {
                   
                    while ($postmeta1 = $sql_postmeta->fetch_object()) {
                        //$array[$postmeta1->meta_key]=$postmeta1->meta_value;
                        $params.=$postmeta1->meta_key."=".$postmeta1->meta_value."&";
                    }
                }else {
                   $sql_post=$conn->query("select post_title from wp_posts where post_type='payer' and post_title='$post_title'");
                   $postmeta1 = $sql_post->fetch_object();
                   $tite=explode(":",$postmeta1->post_title);
                   $params.="name=".$tite[0]."&payer_id=".$tite[1]."&";
                }
                
                $params.="id=".$ins_row['id'];
                $param1=trim($params,"&");
                echo $param1;
                exit();
            }else {
                 $params.='name'."=".$name."&".'payer_id'.'='.$payer_id;
                 echo $params;
                 exit();
            }
            
            
        }
        if($_POST['payer_name']!='' && $_POST['validation']==1){
            //api validation
            
            // Initialize cURL
                    $curl = curl_init();
            // Define URL where the form resides
                    $form_url = 'https://www.zirmed.com/Shared/Payers/ViewPayersCSV.aspx';

            // This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
                    $data_to_post = array();
                    $data_to_post['PayerName']  = $_POST['payer_name'];
                    $data_to_post['GroupBy']    = 'N';
                    $data_to_post['Application']= 'All';
                    $data_to_post['AllStates' ] = 'on';
                    $data_to_post['StateCodes'] = ''; //'ISA*00*0000000   00*0000000000*ZZ*101246         ZZ*ZIRMED         151209*0752*U*00401*000000001*1*P*~^GS*HS*101246*ZIRMED*20151209*075240*000000002*X*004010X092A1^ST*270*000000003^BHT*0022*13*PROVTest600*20151209*075240  ^HL*1**20*1^NM1*PR*2*Medicare B Texas (SMTX0)*****46*ZIRMED^HL*2*1*21*1^NM1*IP*1*Texas Physician House Calls (H)*Perkins*Darolyn***XX*1609272905^REF*4A*^HL*3*2*22*0^TRN*1*1234501*9000000000*5432101^NM1*IL*1*Brown Billy****MI*455040140A^REF*EJ*5979^DMG*D8*19560804^DTP*472*D8*20151209^EQ*30^HL*4*2*22*0^TRN*1*1234502*9000000000*5432102^NM1*IL*1*Wilson*Connie****MI*462561011C5^REF*EJ*1234^DMG*D8*19650919^DTP*472*D8*20151209^EQ*30^HL*5*2*22*0^TRN*1*1234503*9000000000*5432103^NM1*IL*1*Chitwood*Virginia****MI*466204078D^REF*EJ*6000^DMG*D8*19290703^DTP*472*D8*20151209^EQ*30^HL*6*2*22*0^TRN*1 1234504*9000000000*5432104^NM1*IL*1*Rose*Refugia****MI*449667780A^REF*EJ*1389^DMG*D8*19420913^DTP*472*D8*20151209^EQ*30^HL*7*2*22*0^TRN*1*1234505*9000000000*5432105^NM1*IL*1*Finley*Governor****MI*437501408A^REF*EJ*6020^DMG*D8*19380314^DTP*472*D8*20151209^EQ*30^HL*8*2*22*0^TRN*1*1234506*9000000000*5432106^NM1*IL*1*Hanks*Roylene****MI*459845711A^REF*EJ*5932^DMG*D8*19490803^DTP*472*D8*20151209^EQ*30^ HL*9*2*22*0^TRN*1*1234507*9000000000*5432107^NM1*IL*1*Mullen*Jettie****MI*465522005A^REF*EJ*6039^DMG*D8*19340911^DTP*472*D8*20151209^EQ*30^HL*10*2*22*0^TRN*1*1234508*9000000000*5432108^NM1*IL*1*Draper*Charlesetta****MI*464279993A^REF*EJ*2924^DMG*D8*19610413^DTP*472*D8*20151209^EQ*30^HL*11*2*22*0^TRN*1*1234509*9000000000*5432109^NM1*IL*1*Portley*Meladie****MI*462157775A^REF*EJ*5663^DMG*D8 19760403^DTP*472*D8*20151209^EQ*30^HL*12*2*22*0^TRN*1*1234510*9000000000*5432110^NM1*IL*1*Curlin*Franklin****MI*456689901A^REF*EJ*1999^DMG*D8*19421226^DTP*472*D8*20151209^EQ*30^HL*13*2*22*0^TRN*1*1234511*9000000000*5432111^NM1*IL*1*Oliver*Ora*F***MI*464628748A^REF*EJ*5747^DMG*D8*19461126^DTP*472*D8*20151209^EQ*30^HL*14*2*22*0^TRN*1*1234512*9000000000*5432112^NM1*IL*1*Rufus Bessie****MI*450480485A^REF*EJ*5789^DMG*D8*19280502^DTP*472*D8*20151209^EQ*30^HL*15*2*22*0^TRN*1*1234513*9000000000*5432113^NM1*IL*1*Hoffmann*Jerry****MI*452528349A^REF*EJ*5855^DMG*D8*19370409^DTP*472*D8*20151209^EQ*30^HL*16*2*22*0^TRN*1*1234514*9000000000*5432114^NM1*IL*1*West*Rebertha****MI*450983344A^REF*EJ*1119^DMG*D8*19370215^DTP*472*D8*20151209^EQ*30^HL*17*2*22*0^TRN*1 1234515*9000000000*5432115^NM1*IL*1*Wilmore*Elizabeth****MI*450665969A^REF*EJ*3186^DMG*D8*19441120^DTP*472*D8*20151209^EQ*30^HL*18*2*22*0^TRN*1*1234516*9000000000*5432116^NM1*IL*1*Anderson*Dorothy****MI*459748446A^REF*EJ*6046^DMG*D8*19440925^DTP*472*D8*20151209^EQ*30^HL*19*2*22*0^TRN*1*1234517*9000000000*5432117^NM1*IL*1*Shoulder*Joann****MI*436196361A^REF*EJ*6013^DMG*D8*19581011^DTP*472*D8*20151209^EQ*30^ HL*20*2*22*0^TRN*1*1234518*9000000000*5432118^NM1*IL*1*Durant*Tyree*P***MI*562745698A^REF*EJ*5828^DMG*D8*19481206^DTP*472*D8*20151209^EQ*30^HL*21*2*22*0^TRN*1*1234519*9000000000*5432119^NM1*IL*1*Luna*Guadalupe****MI*457042557A^REF*EJ*1245^DMG*D8*19310930^DTP*472*D8*20151209^EQ*30^SE*141*000000003^GE*1*000000002^IEA*1*000000001^';
                    $data_to_post['BCBS']       = True;
                    $data_to_post['Medicare']   = True;
                    $data_to_post['Medicaid']   = True;
                    $data_to_post['Commercial'] = True;
                    $data_to_post['DMERC']      = True;
                    $data_to_post['TRICARE']    = True;

            //// Set the options
                    curl_setopt($curl,CURLOPT_URL, $form_url);
                    //
                    //  This sets the number of fields to post
                    curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));

            //  This is the fields to post in the form of an array.
                    curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
                    //
                    ////execute the post
                    $result = curl_exec($curl);
                    $resultant = $result;
                    //
                    ////close the connection
                    curl_close($curl);

                    $exploded_string = explode("\n", $resultant);
                     //echo "<pre>"; print_r($exploded_string); echo "</pre>";
                                
                    $exploded_csv_result = array();
                                
                    if(count($exploded_string)>6){
                        $k=0;
                        for($i=7; $i<count($exploded_string); $i++){
                            $exploded_csv = explode(",", $exploded_string[$i]);
                            for($j=0; $j< count($exploded_csv); $j++){
                                $exploded_csv_result[$k][]= $exploded_csv[$j];
                            }
                            $k++;
                        }
                    }
                    $array_size = $k;
                    foreach($exploded_csv_result as $key => $value){
                        if($value[0] == '"'.$_POST['payer_name'] .'"') {
                           echo  $check_payer = 1;
                            exit();
                        }
                    }
        }
?>
<html>
    <head>
        
    </head>
    <body>
        <?php
        $display='true';
        if($_POST['ins_id']=='' && $_POST['payer_name']==''){
            $sql_vis     = sqlStatement("SELECT insurance_company from tbl_user_custom_attr_1to1 where userid='".$_SESSION['authId']."'");
            $row1_vis    = sqlFetchArray($sql_vis);   
            $avail4 = explode("|",$row1_vis['insurance_company']);
            if(!in_array("update",$avail4)) {
                $display='false';
            }
        }
        if($_POST['ins_name']=='' && $display=='true' && $_POST['payer_name']==''){
            $result = $conn->query("SELECT * from wp_eligibility_posttypes where post_type='payer'");
            if ($result->num_rows != 0) {
                echo "<datalist id='ins_name'>";
                $sql1=$conn->query("select ID from wp_posts where post_type='payer'");
                while ($obj = $sql1->fetch_object()) {
                    $title='';
                   $sel2=$conn->query("select * from wp_postmeta where post_id=$obj->ID and meta_key='name' ");
                   $obj1 = $sel2->fetch_object();
                   if($obj1->meta_value!='')
                   $title.=$obj1->meta_value.":";
                   $sel3=$conn->query("select * from wp_postmeta where post_id=$obj->ID and meta_key='payer_id' ");
                   $obj2 = $sel3->fetch_object();
                   if($obj2->meta_value!='')
                   $title.=$obj2->meta_value;
                   if($title!='')
                    echo "<option value='$title'>";
                }
                echo "</datalist>";

            }
        }
        
            
        ?>
    </body>
</html>
