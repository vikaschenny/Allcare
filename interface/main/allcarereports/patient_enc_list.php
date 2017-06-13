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
require_once("../../../library/formdata.inc.php");
require_once("../../../library/globals.inc.php");

?>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="HandheldFriendly" content="true">
           <style>
        .css_button_small {
        -moz-font-feature-settings: normal;
        -moz-font-language-override: normal;
        -moz-text-decoration-color: -moz-use-text-color;
        -moz-text-decoration-line: none;
        -moz-text-decoration-style: solid;
        -x-system-font: none;
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("../../images/bg_button_a_small.gif");
        background-origin: padding-box;
        background-position: right top;
        background-repeat: no-repeat;
        background-size: auto auto;
        color: #444;
        display: block;
        float: left;
        font-family: arial,sans-serif;
        font-size: 9px;
        font-size-adjust: none;
        font-stretch: normal;
        font-style: normal;
        font-variant: normal;
        font-weight: bold;
        height: 19px;
        line-height: normal;
        margin-right: 3px;
        padding-right: 10px;
        }

        .css_button_small span {
        background-attachment: scroll;
        background-clip: border-box;
        background-color: transparent;
        background-image: url("../../images/bg_button_span_small.gif");
        background-origin: padding-box;
        background-position: 0 0;
        background-repeat: no-repeat;
        background-size: auto auto;
        display: block;
        line-height: 20px;
        padding-bottom: 0;
        padding-left: 10px;
        padding-right: 0;
        padding-top: 0;
        }
    </style>
<link rel='stylesheet' type='text/css' href='../css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='../css/dataTables.tableTools.css'>
<link rel='stylesheet' type='text/css' href='../css/dataTables.colVis.css'>
<link rel='stylesheet' type='text/css' href='../css/dataTables.colReorder.css'>
<style>
div.DTTT_container {
	float: none;
}
</style>
<script src="../jquery-latest.min.js" type="text/javascript"></script>

<style type="text/css">@import url(../../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../../library/dynarch_calendar_setup.js"></script>

    
</head>
<body style="background-color:#FFFFCC;" >
    <script type='text/javascript'>
         
           function fromSelected(){
                $('#date_to').val($('#date_from').val());
            }
         function showPatientsByEncounters()
            {
	var fromDate=$('#date_from').val(); 
        var toDate=$('#date_to').val();
        $.ajax({
		type: 'POST',
		url: "patient_enc.php",	
		data: { fromDate:fromDate,toDate:toDate
                        },	

		success: function(response)
		{
                    //alert(response);
                    $('#encounter').html(response);
                    //showProviderLocationEncounter();
                    //showTodaysAppointments();
                    //showLocationsEncounter();
		     		     
		},
		failure: function(response)
		{
			alert("error");
		}		
	});	       
}
    </script>
<div id="divDateRange">
            <div class=""><b>From:</b></div>
            <input type='text' size='10' name="date_from" id="date_from" 
        onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' 
        title='yyyy-mm-dd from date of this event' readonly="readonly" 
        value="<?php echo date('Y-m-d'); ?>" />
        <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
        id='img_calendar_from' border='0' alt='[?]' style='cursor:pointer;'
        title='Click here to choose a date'>
        <script>
        Calendar.setup({inputField:"date_from", ifFormat:'%Y-%m-%d', button:"img_calendar_from"});
        </script>

             <div class=""><b>To:</b></div>
                <input type='text' size='10' name="date_to" id="date_to" 
            onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' 
            title='yyyy-mm-dd last date of this event' readonly="readonly" 
            value="<?php echo date('Y-m-d'); ?>" onchange='javascript:showPatientsByEncounters()' />
            <img src='../../pic/show_calendar.gif' align='absbottom' width='24' height='22'
            id='img_calendar_to' border='0' alt='[?]' style='cursor:pointer;'
            title='Click here to choose a date'>
            <script>
            Calendar.setup({inputField:"date_to", ifFormat:'%Y-%m-%d', button:"img_calendar_to"});
            </script>

</div>
    <div id="encounter"></div>
  <!-- include support for the list-add selectbox feature -->
<?php //include $GLOBALS['fileroot']."/library/options_listadd.inc"; ?>   
</body>
