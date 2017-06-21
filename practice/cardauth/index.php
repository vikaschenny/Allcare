<!DOCTYPE html >
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
<meta name="viewport" content="initial-scale=1.0, width=device-height"><!--  mobile Safari, FireFox, Opera Mobile  -->
<script src="libs/modernizr.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" media="all" type="text/css" href="../drive_view/driveassets/css/uploadfile.css" />
<link rel="stylesheet" href="../drive_view/driveassets/css/lity.css"/>
<!--[if lt IE 9]>
<script type="text/javascript" src="../libs/flashcanvas.js"></script>
<![endif]-->
</head>

<body style="margin: 0;">
    <?php
        require_once("../verify_session.php");
        extract($_REQUEST);
        if($encId == ""):
            echo "<span style='color:red;'>Please mark appointment as arrived...</span>";
            exit;
            
        endif;
        //echo "<pre>"; print_r($_REQUEST); echo "</pre>";
        $excuselet = explode(":",$excuseid); 
        $excuseletter = $excuselet[0];
        
        $sql = sqlStatement("select notes from list_options where list_id='AllcareDriveSync' and option_id = 'email'");
        $sqlFetch = sqlFetchArray($sql);
        $syncEmail = $sqlFetch['notes'];

        $sql = sqlStatement("select * from patient_data where pid=".$pid);
        $sqlFetch = sqlFetchArray($sql);
        $pfolder = $sqlFetch['patient_folder'];
        
        $sql = sqlStatement("select gl_value from globals where gl_name='openemr_name'");
        $sqlFetch = sqlFetchArray($sql);
        $glvalue = $sqlFetch['gl_value'];
        
        $sql = sqlStatement("SELECT * FROM tbl_allcare_cardauth WHERE pid=".$pid." AND eid=".$encId);
    
        if(sqlNumRows($sql) > 0):
            if(!empty($_POST)):
                sqlStatement("UPDATE tbl_allcare_cardauth SET cardauthfile='".$excuseletter."', patient_name='$fullname', amount='$dollars', 
                              date='$date', total_amt='$usd', biiling_address='$billing', phone='$phone', address='$csz', email='$email', 
                              acc_options='".implode("|",$cs)."', account_name='$acct', bank_name='$bankname', account_num='$accountnumber', 
                              bank_routing='$bankrouting', bank_address='$bct', cc_options='".implode("|",$credit)."', cardholder_name='$holdername',
                              cc_account_num='$ac',expire_date='$expdate', cvv='$cvv',sign='$signimage', signed_date='$cdate'
                              WHERE pid=".$pid." AND eid=".$encId);
            endif;
        else:
            sqlStatement("INSERT INTO tbl_allcare_cardauth (pid,eid,cardauthfile,patient_name,amount,date,total_amt,biiling_address,
                          phone,address,email,acc_options,account_name,bank_name,account_num,bank_routing,bank_address,cc_options,
                          cardholder_name,cc_account_num,expire_date,cvv,sign,signed_date) VALUES
                          ($pid,$encId,'$excuseletter','$fullname','$dollars','$date','$usd','$billing','$phone','$csz','$email',"
                          . "'".implode("|",$cs)."','$acct','$bankname','$accountnumber','$bankrouting','$bct','".implode("|",$credit)."',"
                          . "'$holdername','$ac','$expdate','$cvv','$signimage','$cdate')");
        endif;
        
        $sql_data = sqlStatement("SELECT * FROM tbl_allcare_cardauth WHERE pid=".$pid." AND eid=".$encId);
        $sqlRows = sqlFetchArray($sql_data);
        
    ?>

<!--<div id="p1" style="overflow: hidden; position: relative; width: 935px; height: 1210px;">-->
<div id="p1">
<!-- Begin shared CSS values -->
<style class="shared-css" type="text/css" >
.t {
	-webkit-transform-origin: top left;
	-moz-transform-origin: top left;
	-o-transform-origin: top left;
	-ms-transform-origin: top left;
	-webkit-transform: scale(0.25);
	-moz-transform: scale(0.25);
	-o-transform: scale(0.25);
	-ms-transform: scale(0.25);
	z-index: 2;
	position: absolute;
	white-space: pre;
	overflow: visible;
}
</style>
<!-- End shared CSS values -->


<!-- Begin inline CSS -->
<style type="text/css" >

#t1_1{left:53px;top:57px;word-spacing:0.6px;}
#t2_1{left:49px;top:104px;letter-spacing:-0.1px;word-spacing:0.1px;}
#t3_1{left:630px;top:65px;letter-spacing:0.1px;word-spacing:-0.1px;}
#t4_1{left:630px;top:88px;}
#t5_1{left:126px;top:5px;}
#t6_1{left:182px;top:5px;word-spacing:0.6px;}
#t7_1{left:279px;top:5px;letter-spacing:0.2px;}
#t8_1{left:315px;top:5px;word-spacing:0.3px;}
#t9_1{left:409px;top:5px;letter-spacing:0.1px;}
#ta_1{left:459px;top:5px;letter-spacing:0.1px;}
#tb_1{left:647px;top:5px;letter-spacing:0.1px;}
#tc_1{left:310px;top:174px;letter-spacing:-0.1px;word-spacing:-0.4px;}
#td_1{left:110px;top:213px;word-spacing:-0.2px;}
#te_1{left:110px;top:230px;word-spacing:-0.3px;}
#tf_1{left:110px;top:263px;word-spacing:0.4px;}
#tg_1{left:138px;top:280px;}
#th_1{left:165px;top:280px;word-spacing:-0.3px;}
#ti_1{left:138px;top:296px;}
#tj_1{left:165px;top:296px;word-spacing:-0.3px;}
#tk_1{left:110px;top:330px;}
#tl_1{left:147px;top:330px;letter-spacing:-0.1px;word-spacing:0.3px;}
#tm_1{left:326px;top:330px;word-spacing:0.3px;}
#tn_1{left:110px;top:347px;word-spacing:-0.3px;}
#to_1{left:752px;top:347px;letter-spacing:0.1px;word-spacing:-0.3px;}
#tp_1{left:110px;top:363px;word-spacing:-0.2px;}
#tq_1{left:508px;top:363px;word-spacing:-0.3px;}
#tr_1{left:110px;top:380px;word-spacing:-0.2px;}
#ts_1{left:110px;top:397px;word-spacing:-0.3px;}
#tt_1{left:110px;top:413px;word-spacing:-0.3px;}
#tu_1{left:110px;top:449px;letter-spacing:0.2px;word-spacing:-0.3px;}
#tv_1{left:110px;top:484px;word-spacing:-0.2px;}
#tw_1{left:206px;top:500px;word-spacing:-1.1px;}
#tx_1{left:110px;top:524px;word-spacing:-0.2px;}
#ty_1{left:110px;top:541px;word-spacing:-0.2px;}
#tz_1{left:528px;top:541px;word-spacing:-0.3px;}
#t10_1{left:110px;top:576px;word-spacing:7px;}
#t11_1{left:550px;top:576px;word-spacing:-13.5px;}
#t12_1{left:110px;top:610px;word-spacing:-0.3px;}
#t13_1{left:560px;top:610px;letter-spacing:-0.1px;word-spacing:11.6px;}
#t14_1{left:147px;top:645px;letter-spacing:0.1px;word-spacing:0.1px;}
#t15_1{left:579px;top:645px;word-spacing:0.1px;}
#t16_1{left:91px;top:654px;}
#t17_1{left:271px;top:654px;letter-spacing:-0.1px;}
#t18_1{left:110px;top:700px;word-spacing:-0.2px;}
#t19_1{left:234px;top:700px;}
#t1a_1{left:110px;top:735px;word-spacing:-0.2px;}
#t1b_1{left:234px;top:735px;}
#t1c_1{left:110px;top:769px;word-spacing:8.2px;}
#t1d_1{left:110px;top:803px;word-spacing:13.7px;}
#t1e_1{left:110px;top:837px;word-spacing:19.2px;}
#t1f_1{left:462px;top:672px;letter-spacing:0.3px;}
#t1g_1{left:631px;top:672px;letter-spacing:0.1px;}
#t1h_1{left:462px;top:700px;letter-spacing:0.1px;}
#t1i_1{left:631px;top:700px;letter-spacing:0.1px;}
#t1j_1{left:462px;top:737px;word-spacing:7.4px;}
#t1k_1{left:462px;top:772px;word-spacing:19.2px;}
#t1l_1{left:462px;top:806px;word-spacing:-0.2px;}
#t1m_1{left:592px;top:806px;letter-spacing:0.1px;}
#t1n_1{left:462px;top:840px;word-spacing:-0.2px;}
#t1o_1{left:110px;top:961px;letter-spacing:0.1px;}
#t1p_1{left:545px;top:961px;letter-spacing:0.1px;}
#t1q_1{
    left:110px;top:1250px;letter-spacing:0.1px;word-spacing:3.1px;text-align: left;
    padding-left: 110px;
    width: 798px;
}

.s1_1{
	FONT-SIZE: 146.7px;
	FONT-FAMILY: Aparajita-Bold_5;
	color: rgb(0,112,192);
}

.s2_1{
	FONT-SIZE: 85.8px;
	FONT-FAMILY: Aparajita-BoldItalic_8;
	color: rgb(13,13,13);
}

.s3_1{
	FONT-SIZE: 79.2px;
	FONT-FAMILY: Arial-Bold_h;
	color: rgb(0,103,171);
}

.s4_1{
	FONT-SIZE: 79.3px;
	FONT-FAMILY: Arial-Bold_h;
	color: rgb(0,103,171);
}

.s5_1{
	FONT-SIZE: 60.9px;
	FONT-FAMILY: Arial-Bold_h;
	color: rgb(0,0,0);
}

.s6_1{
	FONT-SIZE: 60.9px;
	FONT-FAMILY: Arial_j;
	color: rgb(0,103,171);
}

.s7_1{
	FONT-SIZE: 60.9px;
	FONT-FAMILY: Calibri-Italic_l;
	color: rgb(0,112,192);
}

.s8_1{
	FONT-SIZE: 67.5px;
	FONT-FAMILY: Verdana-Bold_o;
	color: rgb(0,0,0);
}

.s9_1{
	FONT-SIZE: 55px;
	FONT-FAMILY: Verdana_r;
	color: rgb(0,0,0);
}

.s10_1{
	FONT-SIZE: 55px;
	FONT-FAMILY: Verdana-Bold_o;
	color: rgb(0,0,0);
}

.s11_1{
	FONT-SIZE: 55px;
	FONT-FAMILY: Symbol_w;
	color: rgb(0,0,0);
}

.s12_1{
	FONT-SIZE: 55px;
	FONT-FAMILY: Verdana__;
	color: rgb(0,0,0);
}

.s13_1{
	FONT-SIZE: 55px;
	FONT-FAMILY: Verdana-Bold_13;
	color: rgb(0,0,0);
}

.s14_1{
	FONT-SIZE: 60.9px;
	FONT-FAMILY: Verdana-Bold_o;
	color: rgb(0,0,0);
}

.s15_1{
	FONT-SIZE: 46.2px;
	FONT-FAMILY: Verdana_r;
	color: rgb(0,0,0);
}

.s16_1{
	FONT-SIZE: 60.9px;
	FONT-FAMILY: Verdana_r;
	color: rgb(0,0,0);
}

.s17_1{
	FONT-SIZE: 10.5px;
	FONT-FAMILY: Arial_j;
	color: rgb(0,0,0);
}

/* Signature */
#imageSig {
margin-left: 110px;
margin-top: 854px;
}
#signatureparent {
        color:darkblue;
        background-color:darkgrey;
        max-width:418px;
        padding:20px;
}

/*This is the div within which the signature canvas is fitted*/
#signature {
        border: 2px dotted black;
        background-color:lightgrey;
}

/* Drawing the 'gripper' for touch-enabled devices */ 
html.touch #content {
        float:left;
        width:92%;
}
html.touch #scrollgrabber {
        float:right;
        width:4%;
        margin-right:2%;
        background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAFCAAAAACh79lDAAAAAXNSR0IArs4c6QAAABJJREFUCB1jmMmQxjCT4T/DfwAPLgOXlrt3IwAAAABJRU5ErkJggg==)
}

</style>
<!-- End inline CSS -->

<!-- Begin embedded font definitions -->
<style id="fonts1" type="text/css" >

@font-face {
	font-family: Aparajita-BoldItalic_8;
	src: url("fonts/Aparajita-BoldItalic_8.woff") format("woff");
}

@font-face {
	font-family: Verdana-Bold_13;
	src: url("fonts/Verdana-Bold_13.woff") format("woff");
}

@font-face {
	font-family: Verdana-Bold_o;
	src: url("fonts/Verdana-Bold_o.woff") format("woff");
}

@font-face {
	font-family: Verdana__;
	src: url("fonts/Verdana__.woff") format("woff");
}

@font-face {
	font-family: Arial_j;
	src: url("fonts/Arial_j.woff") format("woff");
}

@font-face {
	font-family: Calibri-Italic_l;
	src: url("fonts/Calibri-Italic_l.woff") format("woff");
}

@font-face {
	font-family: Symbol_w;
	src: url("fonts/Symbol_w.woff") format("woff");
}

@font-face {
	font-family: Aparajita-Bold_5;
	src: url("fonts/Aparajita-Bold_5.woff") format("woff");
}

@font-face {
	font-family: Arial-Bold_h;
	src: url("fonts/Arial-Bold_h.woff") format("woff");
}

@font-face {
	font-family: Verdana_r;
	src: url("fonts/Verdana_r.woff") format("woff");
}
.textfield{
    height: 98px;
    border: 0;
    font-size: 1.1em;
    position: absolute;
    background: transparent;
    padding-left: 10px;
}
#fullname{
    width: 985px;
    top: -20px;
}
#dollars{
    width: 338px;
    top: -20px;
}
#date{
    width: 305px;
    top: -20px;
}
#usd{
    width: 800px;
    top: -20px;
}
#billing{
    width: 1000px;
    top: -21px;
}
#phone,#email{
    width: 950px;
    top: -21px;
}
#csz{
    width: 980px;
    top: -21px;
}

#acct,#bankname,#accountnumber,#bankrouting,#bct{
   top: -20px;
   width: 690px;
}
#holdername,#ac{
    width: 880px;
    top: -20px;
}
#expdate{
    top: -20px;
    width: 500px;
}

#cvv{
    width: 300px;
    top: -20px;
}
#cdate{
    width: 750px;
    top: -21px;
}
.control.control--checkbox{
    font-size: 54px;
    position: relative;
    top: -10px;
    
}
input[type=checkbox]{
    width: 72px;
    height:72px;
}
.headerborder{
    width: 920px;
    height: 142px;
    background: transparent;
    border-bottom: 2px solid #569ac7;
}

.box{
    width: 336px;
    height: 265px;
    background: transparent;
    position: absolute;
    border: 2px solid;
    
}
#lbox{
   top: 664px;
   left: 96px;
}
#rbox{
   top: 664px;
   left: 453px;
   width: 380px;
}
.footerborder{
    position: absolute;
    top: 1384px;
    height: 1px;
    width: 920px;
    border-top: 1px solid #569ac7;
}
.likeexucelatter{
    width: 868px;
    position: relative;
    border-bottom: 3px solid #569ac7;
    margin: 48px 48px 0px 48px;
    box-sizing: border-box;
    padding-bottom: 15px;
}
.orbtn{
    display: inline-block;
    background: #ccc;
    width: 35px;
    padding: 6px;
    border-radius: 50%;
    font-weight: bold;
    position: absolute;
    left: 396px;
    bottom: -17px;
}
input[type=file]{
    width: auto !important;
}
#eshowlatter{
    float: right;
    position: absolute;
    right: 0;
    bottom: 14px;
}

#loader{
    background: rgba(0,0,0,0.56);
    border-radius: 4px;
    display:table;
    height: 48px;
    width: 266px;
    color: #fff;
    position:fixed;
    left: 0px;
    top:0px;
    bottom: 0px;
    right: 0px;
    margin: auto;
    display: none;
}
.ajax-spinner-bars {
    height: 48px;
    left: 23px;
    position: relative;
    top: 20px;
    width: 35px;
    display: table-cell;
 }
 #loadertitle {
    display: table-cell;
    font-size: 17px;
    padding-left: 14px;
    vertical-align: middle;
 }

.ajax-spinner-bars > div {
    position: absolute;
    width: 2px;
    height: 8px;
    background-color: #fff;
    opacity: 0.05;
    animation: fadeit 0.8s linear infinite;
}
.ajax-spinner-bars > .bar-1 {
    transform: rotate(0deg) translate(0, -12px);
    animation-delay:0.05s;
}
.ajax-spinner-bars > .bar-2 {
    transform: rotate(22.5deg) translate(0, -12px);
    animation-delay:0.1s;
}
.ajax-spinner-bars > .bar-3 {
    transform: rotate(45deg) translate(0, -12px);
    animation-delay:0.15s;
}
.ajax-spinner-bars > .bar-4 {
    transform: rotate(67.5deg) translate(0, -12px);
    animation-delay:0.2s;
}
.ajax-spinner-bars > .bar-5 {
    transform: rotate(90deg) translate(0, -12px);
    animation-delay:0.25s;
}
.ajax-spinner-bars > .bar-6 {
    transform: rotate(112.5deg) translate(0, -12px);
    animation-delay:0.3s;
}
.ajax-spinner-bars > .bar-7 {
    transform: rotate(135deg) translate(0, -12px);
    animation-delay:0.35s;
}
.ajax-spinner-bars > .bar-8 {
    transform: rotate(157.5deg) translate(0, -12px);
    animation-delay:0.4s;
}
.ajax-spinner-bars > .bar-9 {
    transform: rotate(180deg) translate(0, -12px);
    animation-delay:0.45s;
}
.ajax-spinner-bars > .bar-10 {
    transform: rotate(202.5deg) translate(0, -12px);
    animation-delay:0.5s;
}
.ajax-spinner-bars > .bar-11 {
    transform: rotate(225deg) translate(0, -12px);
    animation-delay:0.55s;
}
.ajax-spinner-bars > .bar-12 {
    transform: rotate(247.5deg) translate(0, -12px);
    animation-delay:0.6s;
}
.ajax-spinner-bars> .bar-13 {
    transform: rotate(270deg) translate(0, -12px);
    animation-delay:0.65s;
}
.ajax-spinner-bars > .bar-14 {
    transform: rotate(292.5deg) translate(0, -12px);
    animation-delay:0.7s;
}
.ajax-spinner-bars > .bar-15 {
    transform: rotate(315deg) translate(0, -12px);
    animation-delay:0.75s;
}
.ajax-spinner-bars> .bar-16 {
    transform: rotate(337.5deg) translate(0, -12px);
    animation-delay:0.8s;
}
@keyframes fadeit{
      0%{ opacity:1; }
      100%{ opacity:0;}
}
@media print{
    .likeexucelatter,.subbtns,#imageSig{
        display: none;
    }
    .footerborder{
        top:1183px;
    }
    #t1q_1{
        margin-top: 840px;
    }
}
        
</style>
<form  action="" method="post" enctype=multipart/form-data" name="cardauthform" id="cardauthform">
<!-- End embedded font definitions -->
<div class="likeexucelatter">
    <div id="excuselatter"></div>
    <a href="#" class="btn btn-primary" id="eshowlatter"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Show Recurring Payment Authorization Form</a>
    <div class="orbtn">OR</div>
</div>
<div style="position: relative;top: 0;left: 0;">
<!-- Begin page background -->
<div id="pg1Overlay" style="width:100%; height:100%; z-index:1; background-color:rgba(0,0,0,0); -webkit-user-select: none;"></div>

<!-- Begin text definitions (Positioned/styled in CSS) -->
<div class="headerborder">
<div id="t1_1" class="t s1_1"><?php echo $glvalue; ?></div>
<div id="t2_1" class="t s2_1">Board Certified in Internal Medicine </div>
<div id="t3_1" class="t s3_1">2925 Skyway Circle North, </div>
<div id="t4_1" class="t s4_1">Irving, Texas, 75038.</div>
</div>
<div class="footerborder">
    <div id="t5_1" class="t s5_1">Phone: </div>
    <div id="t6_1" class="t s6_1">972 639 5838 </div>
    <div id="t7_1" class="t s5_1">Fax: </div>
    <div id="t8_1" class="t s6_1">972 791 8211</div>
    <div id="t9_1" class="t s5_1">Email: </div>
    <div id="ta_1" class="t s6_1">Office@dfwprimary.com</div>
    <div id="tb_1" class="t s7_1">WWW.DFWPRIMARY.COM</div>
</div>
<div id="recContent">
<div id="tc_1" class="t s8_1">Recurring Payment Authorization Form </div>
<div id="td_1" class="t s9_1">Schedule your payment to be automatically deducted from your bank account, or charged to your Visa, </div>
<div id="te_1" class="t s9_1">MasterCard, American Express or Discover Card.  Just complete and sign this form to get started! </div>
<div id="tf_1" class="t s10_1">Recurring Payments Will Make Your Life Easier: </div>
<div id="tg_1" class="t s11_1"></div>
<div id="th_1" class="t s12_1">It’s convenient (saving you time and postage)</div>
<div id="ti_1" class="t s11_1"></div>
<div id="tj_1" class="t s12_1">Your payment is always on time (even if you’re out of town), eliminating late charges</div>
<div id="tk_1" class="t s10_1">Here</div>
<div id="tl_1" class="t s13_1">’s How Recurring Paym</div>
<div id="tm_1" class="t s10_1">ents Work: </div>
<div id="tn_1" class="t s9_1">You authorize regularly scheduled charges to your checking/savings account or credit card. </div>
<div id="to_1" class="t s9_1">You will be </div>
<div id="tp_1" class="t s9_1">charged the amount indicated below each billing period. </div>
<div id="tq_1" class="t s9_1">A receipt for each payment can be emailed to you </div>
<div id="tr_1" class="t s9_1">and the charge will appear on your bank statement.  You agree that no prior-notification will be provided </div>
<div id="ts_1" class="t s9_1">unless the date or amount changes, in which case you will receive notice from us at least 10 days prior to the </div>
<div id="tt_1" class="t s9_1">payment being collected. </div>
<div id="tu_1" class="t s14_1">Please complete the information below: </div>
<?php
    $fulName = $sqlFetch['fname']. " " . $sqlFetch['mname'] . " " . $sqlFetch['lname'];
    
?>
<div id="tv_1" class="t s9_1">I <input type="text" name="fullname" disabled class="textfield" id="fullname" value="<?php echo $fulName; ?>"/>____________________________ authorize DFW Primary Care PLLC to charge my credit card </div>
<div id="tw_1" class="t s15_1">(full name)</div>
<div id="tx_1" class="t s9_1">indicated below for the amount of US Dollars <input type="number" min="1" name="dollars" class="textfield" id="dollars" value="<?php echo $sqlRows['amount']; ?>"/>__________on the <input type="date" name="date" class="textfield" id="date2" value="<?php echo $sqlRows['date']; ?>" style="top:-20px;"/>________________  (day or date) of each day of the </div>
<div id="ty_1" class="t s9_1">month for payment of my pending bill with total amount of </div>
<div id="tz_1" class="t s9_1"><input type="number" min="1" name="usd" class="textfield" id="usd" value="<?php echo $sqlRows['total_amt']; ?>"/>_______________________ USD. </div>
<div id="t10_1" class="t s9_1">Billing Address <input type="text" name="billing" disabled class="textfield" id="billing" value="<?php echo $sqlFetch['street']; ?>"/>____________________________ </div>
<div id="t11_1" class="t s9_1">Phone# <input type="text" name="phone" disabled class="textfield" id="phone" value="<?php echo $sqlFetch['phone_cell']; ?>"/>________________________</div>
<div id="t12_1" class="t s9_1">City, State, Zip <input type="text" name="csz" disabled class="textfield" id="csz" value="<?php echo $sqlFetch['city'].", ".$sqlFetch['state'].", ".$sqlFetch['postal_code']; ?>"/>____________________________</div>
<div id="t13_1" class="t s9_1">Email <input type="text" name="email" disabled class="textfield" id="email" value="<?php echo $sqlFetch['email']; ?>" />________________________</div>
<div id="t14_1" class="t s14_1">Checking/ Savings Account</div>
<div class="box" id="lbox"></div>
<div class="box" id="rbox"></div>
<div id="t15_1" class="t s14_1">Credit Card</div>
<div id="t16_1" class="t s9_1">
    <input type="checkbox" name="cs[]" id="cs1" <?php $acc=explode("|",$sqlRows['acc_options']); if(in_array('checking',$acc)) echo "checked"; ?> value='checking'/><label for="cs1" class="control control--checkbox">&nbsp;Checking</label></div>
<div id="t17_1" class="t s9_1">
        <input type="checkbox" name="cs[]" id="cs2" <?php if(in_array('savings',$acc)) echo "checked"; ?> value='savings'/><label for="cs2" class="control control--checkbox">&nbsp;Savings</label></div>
<div id="t18_1" class="t s9_1">Name on Acct </div>
<div id="t19_1" class="t s9_1"><input type="text" name="acct" class="textfield" id="acct" value="<?php echo $sqlRows['account_name']; ?>"/>____________________ </div>
<div id="t1a_1" class="t s9_1">Bank Name </div>
<div id="t1b_1" class="t s9_1"><input type="text" name="bankname" class="textfield" id="bankname" value="<?php echo $sqlRows['bank_name']; ?>"/>____________________ </div>
<div id="t1c_1" class="t s9_1">Account Number <input type="text" name="accountnumber" class="textfield" id="accountnumber" value="<?php echo $sqlRows['account_num']; ?>"/>____________________ </div>
<div id="t1d_1" class="t s9_1">Bank Routing # <input type="text" name="bankrouting" class="textfield" id="bankrouting" value="<?php echo $sqlRows['bank_routing']; ?>"/>____________________ </div>
<div id="t1e_1" class="t s9_1">Bank City/State <input type="text" name="bct" class="textfield" id="bct" value="<?php echo $sqlRows['bank_address']; ?>"/>____________________ <br/><br/><div style="text-align: center;"><img src="images/accnumber.png" alt="Account Number" width="750"/></div></div>
<div id="t1f_1" class="t s16_1"><input type="checkbox" name="credit[]" id="credit1" value="visa" <?php $cc=explode("|",$sqlRows['cc_options']); if(in_array('visa',$cc)) echo "checked"; ?>/><label for="credit1" class="control control--checkbox">&nbsp;Visa</label></div>
<div id="t1g_1" class="t s16_1"><input type="checkbox" name="credit[]" id="credit2" value="mastercard" <?php if(in_array('mastercard',$cc)) echo "checked"; ?> /><label for="credit2" class="control control--checkbox">&nbsp;MasterCard</label></div>
<div id="t1h_1" class="t s16_1"><input type="checkbox" name="credit[]" id="credit3" value="amex" <?php  if(in_array('amex',$cc)) echo "checked"; ?>/><label for="credit3" class="control control--checkbox">&nbsp;Amex</label> </div>
<div id="t1i_1" class="t s16_1"><input type="checkbox" name="credit[]" id="credit4" value="discover" <?php  if(in_array('discover',$cc)) echo "checked"; ?>/><label for="credit4" class="control control--checkbox">&nbsp;Discover</label></div>
<div id="t1j_1" class="t s9_1">Cardholder Name <input type="text" name="holdername" class="textfield" id="holdername" value="<?php echo $sqlRows['cardholder_name']; ?>"/>_________________________ </div>
<div id="t1k_1" class="t s9_1">Account Number <input type="text" name="ac" class="textfield" id="ac" value="<?php echo $sqlRows['cc_account_num']; ?>"/>_________________________ </div>
<div id="t1l_1" class="t s9_1">Exp. Date </div>
<div id="t1m_1" class="t s9_1"><input type="date" name="expdate" class="textfield" id="expdate" value="<?php echo $sqlRows['expire_date']; ?>"/>____________ </div>
<div id="t1n_1" class="t s9_1">CVV (3 digit number on back of card) <input type="text" name="cvv" class="textfield" id="cvv" value="<?php echo $sqlRows['cvv']; ?>"/>______ </div>
<div id="t1o_1" class="t s16_1">SIGNATURE <?php 
        if($sqlRows['sign'] == ""){
            echo '___________________________</div>';
        }else{
            echo '</div><img src="data:'.$sqlRows['sign'].'" style="position: absolute;left: 195px;top: 936px;width: 205px;" />';
        }
    ?>
    <div id="t1p_1" class="t s16_1">DATE <input type="date" name="cdate" class="textfield" id="cdate" value="<?php echo $sqlRows['signed_date']; ?>" />________________________ </div>
<div id="imageSig"><div id="displayarea"></div>
<div id="content">
	<div id="signatureparent">
		<div id="signature"></div></div>
<!--    <div><input type="button" id="signOK" value="Signature Done" /></div>
	<!--<div id="tools"></div>
	<div><p>Display Area:</p><div id="displayarea1"></div></div>-->
</div>

<div id="scrollgrabber"></div>
</div>
</div>
<input type="hidden" id="patientid" name="pid" value="<?php echo $pid; ?>" />
<input type="hidden" id="pc_eid" name="encId" value="<?php echo $encId; ?>" />
<input type="hidden" id="excuseid" name="excuseid" value="<?php echo $sqlRows['cardauthfile']; ?>" />
<input type="hidden" id="signimage" name="signimage" value="<?php echo $sqlRows['sign']; ?>" />
<div id="loader" style="display: none;">
    <div class="ajax-spinner-bars">
        <div class="bar-1"></div><div class="bar-2"></div><div class="bar-3"></div><div class="bar-4"></div><div class="bar-5"></div><div class="bar-6"></div><div class="bar-7"></div><div class="bar-8"></div><div class="bar-9"></div><div class="bar-10"></div><div class="bar-11"></div><div class="bar-12"></div><div class="bar-13"></div><div class="bar-14"></div><div class="bar-15"></div><div class="bar-16"></div></div>
    <div id="loadertitle">Recurring Payment Authorization Form Loading...</div>
</div>
<script src="libs/jquery.js"></script>
<script>
/*  @preserve
jQuery pub/sub plugin by Peter Higgins (dante@dojotoolkit.org)
Loosely based on Dojo publish/subscribe API, limited in scope. Rewritten blindly.
Original is (c) Dojo Foundation 2004-2010. Released under either AFL or new BSD, see:
http://dojofoundation.org/license for more information.
*/
var hostname = window.location.hostname;
var protocol = window.location.protocol;
var pfolderid = null;
(function($) {
    
	var topics = {};
	$.publish = function(topic, args) {
	    if (topics[topic]) {
	        var currentTopic = topics[topic],
	        args = args || {};
	
	        for (var i = 0, j = currentTopic.length; i < j; i++) {
	            currentTopic[i].call($, args);
	        }
	    }
	};
	$.subscribe = function(topic, callback) {
	    if (!topics[topic]) {
	        topics[topic] = [];
	    }
	    topics[topic].push(callback);
	    return {
	        "topic": topic,
	        "callback": callback
	    };
	};
	$.unsubscribe = function(handle) {
	    var topic = handle.topic;
	    if (topics[topic]) {
	        var currentTopic = topics[topic];
	
	        for (var i = 0, j = currentTopic.length; i < j; i++) {
	            if (currentTopic[i] === handle.callback) {
	                currentTopic.splice(i, 1);
	            }
	        }
	    }
	};
})(jQuery);

</script>
<script src="src/jSignature.js"></script>
<script src="src/plugins/jSignature.CompressorBase30.js"></script>
<script src="src/plugins/jSignature.CompressorSVG.js"></script>
<script src="src/plugins/jSignature.UndoButton.js"></script> 
<script src="src/plugins/signhere/jSignature.SignHere.js"></script> 
<script type="text/javascript" src="../drive_view/driveassets/js/jquery.uploadfile.js"></script>
<script src="../drive_view/driveassets/js/lity.js"></script>
<script>
    var nofiles = true;
    var pfolderid = null;
    var hostname = window.location.hostname;
    var protocol = window.location.protocol;
$(document).ready(function() {
	
    $.ajax({
            url:protocol+"//"+hostname+"/api/DriveSync/getsubfolderId/"+'<?php echo $syncEmail; ?>'+"/"+'<?php echo $pfolder; ?>'+"/payments/folder",
            type:"GET",
            async:false,
            success:function(data){
                pfolderid = $.parseJSON(data)[0];
            },
            error:function(err){console.log("folder info error")}
    });


    $("#submit_auth").click(function(evt){
            if( $sigdiv.jSignature('getData', 'native').length == 0) {
                alert('Please Sign the Form..');
                return false;
            }
            var data = $sigdiv.jSignature('getData', 'image');
            $("#signimage").val(data);
            $("#cardauthform").submit();
    });
    if(pfolderid== null)
        return;
    
    var filuploader = $("#excuselatter").uploadFile({
            url:protocol+"//"+hostname+"/api/DriveSync/uploadfile_web/"+'<?php echo $syncEmail; ?>'+"/practice/"+'<?php echo $_SESSION['authUser']; ?>'+"/"+pfolderid+"/patients",
            autoSubmit:true,
            fileName: "myfile",
            showCancel:false,
            showAbort:false,
            showDone:true,
            showStatusAfterSuccess:true,
            showError:false,
            showFileSize:true,
            dragDropStr:"",
            maxFileCount:1,
            statusBarWidth:"auto",
            uploadStr:"",
            showFileCounter:false,
            onLoad:function(obj)
            {
                $('.fileupload-buttonbar-text').html("Upload your Recurring Payment Authorization Form.");
                //setTimeout(function(){$(".ajax-file-upload").find('form span i').next().html(" Addfile...")},10)

            },
            onSelect:function(files)
            {
                nofiles = false;
                filuploader.reset();
                setTimeout(function(){ $('.ajax-file-upload-filename').find(".status").hide();$('.ajax-file-upload-filename').find(".filesize").hide();},10)
                return true; //to allow file submission.
            },
            onSuccess:function(files,data,xhr,pd)
            {        
                var excuseletterId = data.split(":");
                $("#excuseid").val(excuseletterId[0]);
                $("#cardauthform").submit();

            },onError: function(files,status,errMsg,pd)
            {        
                alert("file upload error")
            }

        });
        
        $("#eshowlatter").click(function(evt){
            evt.preventDefault();
            var lightbox = lity();
            lightbox(protocol+"//"+hostname+"/practice/drive_view/view_file.php?file_id="+$("#excuseid").val());
            $(document).on('click', '[data-lightbox]', lightbox);
            $('.lity').hide();
            $('.overlay').show();
            $("#loader").show();
        });
        
        $(document).on('lity:close', function(event, instance) {
                $(instance).remove();
        });

        $("#printcheckout").click(function(evt){
            evt.preventDefault();
            window.open("patient_check_out_print.php?patientid="+$("#patientid").val()+"&pc_eid="+$("#pc_eid").val(),'','width=800px,height=600px');
        });
        
	// This is the part where jSignature is initialized.
	var $sigdiv = $("#signature").jSignature({'UndoButton':true})
	
	// All the code below is just code driving the demo. 
	, $tools = $('#tools')
	, $extraarea = $('#displayarea')
	, pubsubprefix = 'jSignature.demo.'
        
        var export_plugins = $sigdiv.jSignature('listPlugins','export')
	, chops = ['<span><b>Extract signature data as: </b></span><select>','<option value="">(select export format)</option>']
	, name
	for(var i in export_plugins){
		if (export_plugins.hasOwnProperty(i)){
			name = export_plugins[i]
			chops.push('<option value="' + name + '">' + name + '</option>')
		}
	}
	chops.push('</select><span><b> or: </b></span>')
	
	$(chops.join('')).bind('change', function(e){
		if (e.target.value !== ''){
			var data = $sigdiv.jSignature('getData', e.target.value)
			$.publish(pubsubprefix + 'formatchanged')
			if (typeof data === 'string'){
				$('textarea', $tools).val(data)
			} else if($.isArray(data) && data.length === 2){
				$('textarea', $tools).val(data.join(','))
				$.publish(pubsubprefix + data[0], data);
			} else {
				try {
					$('textarea', $tools).val(JSON.stringify(data))
				} catch (ex) {
					$('textarea', $tools).val('Not sure how to stringify this, likely binary, format.')
				}
			}
		}
	}).appendTo($tools)
        
        $("#signOK").click(function(){
            var data = $sigdiv.jSignature('getData', 'image');
            $.publish(pubsubprefix + 'formatchanged')
            if (typeof data === 'string'){
                    $('textarea', $tools).val(data)
            } else if($.isArray(data) && data.length === 2){
                    $('textarea', $tools).val(data.join(','))
                    $.publish(pubsubprefix + data[0], data);
            } else {
                    try {
                            $('textarea', $tools).val(JSON.stringify(data))
                    } catch (ex) {
                            $('textarea', $tools).val('Not sure how to stringify this, likely binary, format.')
                    }
            }
        }).appendTo($tools)

	
	$('<input type="button" value="Reset">').bind('click', function(e){
		$sigdiv.jSignature('reset')
	}).appendTo($tools)
	
	$('<div><textarea style="width:100%;height:7em;"></textarea></div>').appendTo($tools)
	
	$.subscribe(pubsubprefix + 'formatchanged', function(){
		$extraarea.html('')
	})

	$.subscribe(pubsubprefix + 'image/svg+xml', function(data) {

		try{
			var i = new Image()
			i.src = 'data:' + data[0] + ';base64,' + btoa( data[1] )
			$(i).appendTo($extraarea)
		} catch (ex) {

		}
		
		var message = [
			"If you don't see an image immediately above, it means your browser is unable to display in-line (data-url-formatted) SVG."
			, "This is NOT an issue with jSignature, as we can export proper SVG document regardless of browser's ability to display it."
			, "Try this page in a modern browser to see the SVG on the page, or export data as plain SVG, save to disk as text file and view in any SVG-capabale viewer."
           ]
		$( "<div>" + message.join("<br/>") + "</div>" ).appendTo( $extraarea )
	});

	$.subscribe(pubsubprefix + 'image/svg+xml;base64', function(data) {
		var i = new Image()
		i.src = 'data:' + data[0] + ',' + data[1]
		$(i).appendTo($extraarea)
		
		var message = [
			"If you don't see an image immediately above, it means your browser is unable to display in-line (data-url-formatted) SVG."
			, "This is NOT an issue with jSignature, as we can export proper SVG document regardless of browser's ability to display it."
			, "Try this page in a modern browser to see the SVG on the page, or export data as plain SVG, save to disk as text file and view in any SVG-capabale viewer."
           ]
		$( "<div>" + message.join("<br/>") + "</div>" ).appendTo( $extraarea )
	});
	
	$.subscribe(pubsubprefix + 'image/png;base64', function(data) {
		var i = new Image()
		i.src = 'data:' + data[0] + ',' + data[1]
		//$('<span><b>As you can see, one of the problems of "image" extraction (besides not working on some old Androids, elsewhere) is that it extracts A LOT OF DATA and includes all the decoration that is not part of the signature.</b></span>').appendTo($extraarea)
		$(i).appendTo($extraarea)
	});
	
	$.subscribe(pubsubprefix + 'image/jsignature;base30', function(data) {
		$('<span><b>This is a vector format not natively render-able by browsers. Format is a compressed "movement coordinates arrays" structure tuned for use server-side. The bonus of this format is its tiny storage footprint and ease of deriving rendering instructions in programmatic, iterative manner.</b></span>').appendTo($extraarea)
	});

	if (Modernizr.touch){
		$('#scrollgrabber').height($('#content').height())		
	}
	
        $("#printpage").click(function(){
            window.print();
        })
});

function frameload(){
    $("#loader").hide();
    $('.overlay').hide();
    $('.lity').show();

}
function unShareFile(){
    $.ajax({url:protocol+'//'+hostname+'/api/DriveSync/delete_permission/'+'<?php echo $syncEmail; ?>'+'/'+$("#excuseid").val()+'/anyoneWithLink',type:"get",xhrFields: {withCredentials: true},data:null, crossDomain: true,error:function(e){console.log(e)},success:function(data){console.log(data);}});
}
</script>
<br/><div id="t1q_1" class="s17_1">I understand that this authorization will remain in effect until I cancel it in writing, and I agree to notify DFW Primary Care PLLC in writing of any changes in my
    account information or termination of this authorization at least 15 days prior to the next billing date. If the above noted payment dates fall on a weekend or holiday, I understand that the payments may be executed on the next business day. For ACH debits to my checking/savings account, 
    I understand that because these are electronic transactions, these funds may be withdrawn from my account as soon as the above noted periodic transaction dates. In the case of an ACH Transaction  
    being rejected for Non Sufficient Funds (NSF) I understand that DFW Primary Care PLLC  may at its discretion attempt to process the charge again within 30 days, 
    and agree to an additional USD 100 charge for each attempt returned NSF which will be initiated as a separate transaction from the authorized recurring 
    payment. I acknowledge that the origination of ACH transactions to my account must comply with the provisions of U.S. law. 
    I certify that I am an authorized user of this credit card/bank account and will not dispute these scheduled transactions with my bank or credit card company; so long as the transactions correspond to 
    the terms indicated in this authorization form. </div>
</div>
<br/>
<div class="subbtns" style="width: 920px;">
    <div class="text-center">
        <button id="submit_auth" class="btn btn-primary btn-sm" type="button" name="Submit" data-loading-text="Processing...">SUBMIT</button>
        <button id="printpage" class="btn btn-primary btn-sm" type="button" name="Submit"><span class="glyphicon glyphicon-print"></span> PRINT</button>
    </div>
</div>

</form>
<!-- End text definitions -->

<!--[if lt IE 9]><script type="text/javascript">
(function(divCount, pageNum) {
for (var i = 1; i < divCount; i++) {
    var div = document.getElementById('t' + i.toString(36) + '_' + pageNum);
    if (div !== null) {
        div.style.top = (div.offsetTop * 4) + 'px';
        div.style.left = (div.offsetLeft * 4) + 'px';
        div.style.zoom = '25%';
    }
}
})(72, 1);
</script><![endif]-->

</div>
</body>
</html>
