<?php
 // Copyright (C) 2011 Cassian LUP <cassi.lup@gmail.com>
 // 
 // Moved out of individual get_* portal functions for re-use by
 // Kevin Yeh (kevin.y@integralemr.com) May 2013
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.
 // 
 // 
 
    // All of the common intialization steps for the get_* patient portal functions are now in this single include.

    //SANITIZE ALL ESCAPES
    $sanitize_all_escapes=true;

    //STOP FAKE REGISTER GLOBALS
    $fake_register_globals=false;

    //continue session
    session_start();

    //landing page definition -- where to go if something goes wrong
    $landingpage    = "index.php?site=".$_SESSION['site_id'];	
    $ignoreAuth     = true; // ignore the standard authentication for a regular OpenEMR user
    /*
    $_SESSION['portal_username'] = $_GET['provider'];
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
    */
require_once("verify_session.php");
require_once("$srcdir/forms.inc");
require_once("$srcdir/billing.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/formatting.inc.php");
require_once "$srcdir/options.inc.php";
require_once "$srcdir/formdata.inc.php";
include_once("$srcdir/calendar.inc");
include_once("$srcdir/edi.inc");

# create and load the HTML
include('simple_html_dom.php');
$html = new simple_html_dom();

//for logout
$refer                      = $_REQUEST['refer'];
/*
$_SESSION['refer']          = $_REQUEST['refer'];
$_SESSION['portal_username']= $_REQUEST['provider'];
*/

$sql = sqlStatement("SELECT id, fname, lname, specialty FROM users " .
      "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
      "AND authorized = 1 AND username='".$provider."'" .
      "ORDER BY lname, fname");
$id         = sqlFetchArray($sql);

// Element data seperator		
$eleDataSep		= "*";

// Segment Terminator	
$segTer			= "~"; 	

// Component Element seperator
$compEleSep		= "^"; 	
$from_date      = fixDate($_REQUEST['from'], date('Y-m-d'));
$to_date        = fixDate($_REQUEST['to'], date('Y-m-d'));
$form_facility  = $_REQUEST['facility'];
$form_provider  = $_REQUEST['providerid'];
$exclude_policy = $_REQUEST['removedrows'];
$X12info        = $_REQUEST['form_x12'];
$pid            = $_REQUEST['pid'];

//Set up the sql variable binding array (this prevents sql-injection attacks)
$sqlBindArray = array();

$where  = "e.pc_pid IS NOT NULL AND e.pc_eventDate >= ?";
array_push($sqlBindArray, $from_date);

//$where .="and e.pc_eventDate = (select max(pc_eventDate) from openemr_postcalendar_events where pc_aid = d.id)";

if ($to_date) {
        $where .= " AND e.pc_eventDate <= ?";
        array_push($sqlBindArray, $to_date);
}

if($form_facility != "") {
        $where .= " AND f.id = ? ";
        array_push($sqlBindArray, $form_facility);
}

if($form_provider != "") {
        $where .= " AND d.id = ? ";
        array_push($sqlBindArray, $form_provider);
}

if($exclude_policy != ""){	$arrayExplode	=	explode(",", $exclude_policy);
                                                        array_walk($arrayExplode, 'arrFormated');
                                                        $exclude_policy = implode(",",$arrayExplode);
                                                        $where .= " AND i.policy_number not in (".stripslashes($exclude_policy).")";
                                                }

$where .= " AND (i.policy_number is not null and i.policy_number != '')";  


// Subhan: This query is used to DISPLAY records with patient name in ASC order
$pidClause = "";
if($pid !=""):
    $pidClause  = " AND p.pid = ". $pid." ORDER BY p.lname,p.fname,p.mname ASC"; 
endif;
$where = $where.$pidClause;
$query = sprintf("SELECT DISTINCT p.pid
        FROM openemr_postcalendar_events AS e
        LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id)
        LEFT JOIN facility AS f on (f.id = e.pc_facility)
        LEFT JOIN patient_data AS p ON p.pid = e.pc_pid
        LEFT JOIN insurance_data AS i ON (i.id =(
                                                SELECT id
                                                FROM insurance_data AS i
                                                WHERE pid = p.pid AND type = 'primary'
                                                ORDER BY date DESC
                                                LIMIT 1
                                                )
                                        )
        LEFT JOIN insurance_companies as c ON (c.id = i.provider)
        WHERE %s", $where, " ORDER BY p.lname,p.fname,p.mname ASC");

$res = sqlStatement($query, $sqlBindArray);
// Initialize cURL
$curl = curl_init();

// to retrive the zirmed credentials from openemr 
$getcred = sqlStatement("SELECT title,option_id FROM list_options WHERE list_id='AllCareConfig' and option_id IN('zirmedUsername','zirmedPassword','zirmedCustID','zirmedResponseType')");
while($setcred = sqlFetchArray($getcred)){
    ${$setcred['option_id']} = $setcred['title'];
}
if(isset($zirmedUsername) || isset($zirmedPassword) || isset($zirmedCustID) || isset($zirmedResponseType)){
    while($rows = sqlFetchArray($res)){
        $clause = "";
        if($pid == "" ):
            $clause  = " AND p.pid = ". $rows['pid']." ORDER BY p.lname,p.fname,p.mname ASC"; 
        endif;

        $query2 = sprintf("SELECT DATE_FORMAT(e.pc_eventDate, '%%Y%%m%%d') as pc_eventDate,
                           e.pc_facility,
                           p.lname,
                           p.fname,
                           p.mname, 
                           DATE_FORMAT(p.dob, '%%Y%%m%%d') as dob,
                           p.ss,
                           p.sex,
                           p.pid,
                           p.pubpid,
                           i.policy_number,
                           i.provider as payer_id,
                           i.subscriber_relationship,
                           i.subscriber_lname,
                           i.subscriber_fname,
                           i.subscriber_mname,
                           DATE_FORMAT(i.subscriber_dob, '%%m/%%d/%%Y') as subscriber_dob,
                           i.subscriber_ss,
                           i.subscriber_sex,
                           DATE_FORMAT(i.date,'%%Y%%m%%d') as date,
                           d.lname as provider_lname,
                           d.fname as provider_fname,
                           d.npi as provider_npi,
                           d.upin as provider_pin,
                           f.federal_ein,
                           f.facility_npi,
                           f.name as facility_name,
                           c.name as payer_name
                FROM openemr_postcalendar_events AS e
                LEFT JOIN users AS d on (e.pc_aid is not null and e.pc_aid = d.id)
                LEFT JOIN facility AS f on (f.id = e.pc_facility)
                LEFT JOIN patient_data AS p ON p.pid = e.pc_pid
                LEFT JOIN insurance_data AS i ON (i.id =(
                                                        SELECT id
                                                        FROM insurance_data AS i
                                                        WHERE pid = p.pid AND type = 'primary'
                                                        ORDER BY date DESC
                                                        LIMIT 1
                                                        )
                                                )
                LEFT JOIN insurance_companies as c ON (c.id = i.provider)
                WHERE %s %s",	$where,$clause );
        $res2 = sqlStatement($query2, $sqlBindArray);
        $printData = allCare_print_elig($res2,$X12info,$segTer,$compEleSep);
        //$resp = file_get_contents('https://qa2allcare.texashousecalls.com/api/elig-270-Draper-Charlesetta.elg');
        $resp = $printData;
  
    
        // Define URL where the form resides
        $form_url = 'https://eligibilityapi.zirmed.com/1.0/Rest/Gateway/GatewayAsync.ashx';

        // This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
        $data_to_post = array();
        $data_to_post['UserID']         = $zirmedUsername;
        $data_to_post['Password']       = $zirmedPassword;
        $data_to_post['CustID']         = $zirmedCustID;
        $data_to_post['DataFormat' ]    = 'X12';

        $data_to_post['Data'] = $resp; //'ISA*00*0000000   *00*0000000000*ZZ*101246         *ZZ*ZIRMED         *151209*0752*U*00401*000000001*1*P*~^GS*HS*101246*ZIRMED*20151209*075240*000000002*X*004010X092A1^ST*270*000000003^BHT*0022*13*PROVTest600*20151209*075240  ^HL*1**20*1^NM1*PR*2*Medicare B Texas (SMTX0)*****46*ZIRMED^HL*2*1*21*1^NM1*IP*1*Texas Physician House Calls (H)*Perkins*Darolyn***XX*1609272905^REF*4A*^HL*3*2*22*0^TRN*1*1234501*9000000000*5432101^NM1*IL*1*Brown*Billy****MI*455040140A^REF*EJ*5979^DMG*D8*19560804^DTP*472*D8*20151209^EQ*30^HL*4*2*22*0^TRN*1*1234502*9000000000*5432102^NM1*IL*1*Wilson*Connie****MI*462561011C5^REF*EJ*1234^DMG*D8*19650919^DTP*472*D8*20151209^EQ*30^HL*5*2*22*0^TRN*1*1234503*9000000000*5432103^NM1*IL*1*Chitwood*Virginia****MI*466204078D^REF*EJ*6000^DMG*D8*19290703^DTP*472*D8*20151209^EQ*30^HL*6*2*22*0^TRN*1*1234504*9000000000*5432104^NM1*IL*1*Rose*Refugia****MI*449667780A^REF*EJ*1389^DMG*D8*19420913^DTP*472*D8*20151209^EQ*30^HL*7*2*22*0^TRN*1*1234505*9000000000*5432105^NM1*IL*1*Finley*Governor****MI*437501408A^REF*EJ*6020^DMG*D8*19380314^DTP*472*D8*20151209^EQ*30^HL*8*2*22*0^TRN*1*1234506*9000000000*5432106^NM1*IL*1*Hanks*Roylene****MI*459845711A^REF*EJ*5932^DMG*D8*19490803^DTP*472*D8*20151209^EQ*30^HL*9*2*22*0^TRN*1*1234507*9000000000*5432107^NM1*IL*1*Mullen*Jettie****MI*465522005A^REF*EJ*6039^DMG*D8*19340911^DTP*472*D8*20151209^EQ*30^HL*10*2*22*0^TRN*1*1234508*9000000000*5432108^NM1*IL*1*Draper*Charlesetta****MI*464279993A^REF*EJ*2924^DMG*D8*19610413^DTP*472*D8*20151209^EQ*30^HL*11*2*22*0^TRN*1*1234509*9000000000*5432109^NM1*IL*1*Portley*Meladie****MI*462157775A^REF*EJ*5663^DMG*D8*19760403^DTP*472*D8*20151209^EQ*30^HL*12*2*22*0^TRN*1*1234510*9000000000*5432110^NM1*IL*1*Curlin*Franklin****MI*456689901A^REF*EJ*1999^DMG*D8*19421226^DTP*472*D8*20151209^EQ*30^HL*13*2*22*0^TRN*1*1234511*9000000000*5432111^NM1*IL*1*Oliver*Ora*F***MI*464628748A^REF*EJ*5747^DMG*D8*19461126^DTP*472*D8*20151209^EQ*30^HL*14*2*22*0^TRN*1*1234512*9000000000*5432112^NM1*IL*1*Rufus*Bessie****MI*450480485A^REF*EJ*5789^DMG*D8*19280502^DTP*472*D8*20151209^EQ*30^HL*15*2*22*0^TRN*1*1234513*9000000000*5432113^NM1*IL*1*Hoffmann*Jerry****MI*452528349A^REF*EJ*5855^DMG*D8*19370409^DTP*472*D8*20151209^EQ*30^HL*16*2*22*0^TRN*1*1234514*9000000000*5432114^NM1*IL*1*West*Rebertha****MI*450983344A^REF*EJ*1119^DMG*D8*19370215^DTP*472*D8*20151209^EQ*30^HL*17*2*22*0^TRN*1*1234515*9000000000*5432115^NM1*IL*1*Wilmore*Elizabeth****MI*450665969A^REF*EJ*3186^DMG*D8*19441120^DTP*472*D8*20151209^EQ*30^HL*18*2*22*0^TRN*1*1234516*9000000000*5432116^NM1*IL*1*Anderson*Dorothy****MI*459748446A^REF*EJ*6046^DMG*D8*19440925^DTP*472*D8*20151209^EQ*30^HL*19*2*22*0^TRN*1*1234517*9000000000*5432117^NM1*IL*1*Shoulder*Joann****MI*436196361A^REF*EJ*6013^DMG*D8*19581011^DTP*472*D8*20151209^EQ*30^HL*20*2*22*0^TRN*1*1234518*9000000000*5432118^NM1*IL*1*Durant*Tyree*P***MI*562745698A^REF*EJ*5828^DMG*D8*19481206^DTP*472*D8*20151209^EQ*30^HL*21*2*22*0^TRN*1*1234519*9000000000*5432119^NM1*IL*1*Luna*Guadalupe****MI*457042557A^REF*EJ*1245^DMG*D8*19310930^DTP*472*D8*20151209^EQ*30^SE*141*000000003^GE*1*000000002^IEA*1*000000001^';
        $data_to_post['ResponseType']   = $zirmedResponseType;


        //
        //// Set the options
        curl_setopt($curl,CURLOPT_URL, $form_url);
        //
        //  This sets the number of fields to post
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));
        
        //echo "<p style='color:blue;'><strong>Requested Data Sent to Zirmed.</strong></p>";
        //echo "<p style='color:blue;'>Parameters passed are: <ol style='color:blue;'><li>Userid</li><li>Password</li><li>CustId</li><li>X12 DATA</li><li>Input File format (X12)</li><li>Response format (HTML)</li></ol></p>";
        //print_r($data_to_post);
        //
        //  This is the fields to post in the form of an array.
        curl_setopt($curl,CURLOPT_POSTFIELDS, $data_to_post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        //
        ////execute the post
        //echo "<br /><br /><br />";
        //echo "<p style='color:blue;'><strong>Response Recieved From Zirmed</strong></p>";
//        $result = curl_exec($curl);
//        echo $result;
//
//        $save_data = file_get_contents($result);
//        if(!empty($result)){
//            $insert_blob = sqlStatement("INSERT INTO tbl_eligibility_html_data(`date`,`elig_est_data`,`user`,`pid`,`html_data`) VALUES (NOW(),'".addslashes(base64_encode ($save_data))."','$provider','$pid','".addslashes(base64_encode($result))."')");
//        }

    }

    //
    ////close the connection
    curl_close($curl);
}

?>