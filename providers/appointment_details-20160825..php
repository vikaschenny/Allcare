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



require_once("verify_session.php");
$date = $_REQUEST['evtdate'];
$id = $_REQUEST['patientid'];
$enc_value = $_REQUEST['encounterid'];
?>

    <div class="col-xs-12">
        <a href="#" class="close fontawesome-remove"></a>
        <h2>Appointment Details</h2>
    </div>
    <?php $sql_pdata=sqlStatement("SELECT  ope.*,pc_startTime,p.fname,p.lname ,lo.title as stat,p.*
              FROM patient_data p INNER JOIN openemr_postcalendar_events ope ON p.pid=ope.pc_pid
              inner join openemr_postcalendar_categories pc on pc.pc_catid=ope.pc_catid 
              inner join list_options lo on lo.option_id=ope.pc_apptstatus
              WHERE ope.pc_aid=$id  AND ope.pc_eventdate='$date' and p.practice_status = 'YES' AND p.deceased_stat != 'YES' AND (p.deceased_date = '' OR p.deceased_date = '0000-00-00 00:00:00' ) and ope.pc_catid IN ($enc_value)  ");    
     $d1=explode("-",$date);
     $d2=$d1[0].$d1[1].$d1[2];
     $count = 0;
     while($row=sqlFetchArray($sql_pdata)){
         $eid=$row['pc_eid'];
         $count++;
          echo "<div  class='col-lg-4 col-xs-12 col-sm-6 col-md-6 group'><div class='appointment' style='border: 2px solid #cbd1d2;  font-size:12px !important;'>";
          echo "<b>Patient Name: </b>". $row['fname'].' '.$row['lname']; echo "<br>";
          echo "<b>Event Date: </b>".$date; echo "<br>";
          echo "<b>Time: </b>".$row['pc_startTime']; echo "<br>";
          echo "<b>Category: </b>".$row['pc_title'];  echo "<br>";
           $sql_hh=sqlStatement("SELECT id, fname, lname, organization, username FROM users " .
                           "WHERE id='".$row['hhagency']."' AND active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                           "ORDER BY organization, lname, fname");
          $hh=sqlFetchArray($sql_hh);
           echo "<b>HH Agency: </b>".$hh['organization'];  echo "<br>";
          $sql=sqlStatement("SELECT id, fname, lname, organization, username FROM users " .
                           "WHERE id='".$row['living_facility_org']."' AND active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) " .
                           "ORDER BY organization, lname, fname");
          $fac=sqlFetchArray($sql);
          echo "<b>Living Facility: </b>".$fac['organization'];  echo "<br>";
          echo "<b>Address: </b>".$row['street'];  echo "<br>";
          echo "<b>City: </b>".$row['city'];  echo "<br>";
          $sql_st=sqlStatement("SELECT * from list_options where list_id='state' AND option_id='".$row['state']."'");
          $st=sqlFetchArray($sql_st);
          echo "<b>State: </b>".$st['title'];  echo "<br>";
          echo "<b>Postal Code: </b>".$row['postal_code'];  echo "<br>";
          $sql_cc=sqlStatement("SELECT * from list_options where list_id='country' AND option_id='".$row['country_code']."'");
          $cc=sqlFetchArray($sql_cc);
          echo "<b>Country: </b>".$cc['title'];  echo "<br>";
          echo "<b>Home Phone: </b>".$row['phone_home'];  echo "<br>";
          echo "<b>Mobile Phone: </b>".$row['phone_cell'];  echo "<br>";
          echo "<b>Status: </b>".$row['stat'];  echo "<br>";
             if(strlen($row['pc_hometext']) > 15):
                 $comm = substr ($row['pc_hometext'], 0, 15);
                 $comments="$comm"."...";
             else:
                 $comments=$row['pc_hometext'];
             endif;
          echo "<b>Comments: </b>".$comments;  echo "<br>";
          echo "<a class='various hidden-print' data-fancybox-type='iframe' href='edit_appointment.php?date=$d2&eid=$eid&provider=$id'>More Details <i class='fa fa-caret-right'></i></a>";
          echo "</div></div>";
          if(($count%3==0))
            echo "<div class = 'clearfix visible-lg'></div>";
          if(($count%2==0))
            echo "<div class = 'clearfix visible-md visible-sm'></div>";

       } 
       ?>



