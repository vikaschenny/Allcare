<?php $base_url="https://".$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/'; ?>
<div id="sidenave" class="col-sm-3">                                           
        <ul class="nav nav-list bs-docs-sidenav affix">
            <input type="hidden" id="sidenavep" value="<?php echo $page_id; ?>"/>
             <li class=""><a  style="border-radius: 6px 6px 0 0;" href='javascript:;' onclick=DoPost_patient('<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=my_patients')>My Patients</a></li>
             <li class=""><a  href='javascript:;' onclick=DoPost_patient('<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=all_patients')>All Patients</a></li>
             <li class=""><a  href='javascript:;' onclick=DoPost_patient('<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=by_facility')>Patients By Facility</a></li>
             <li class=""><a  href='javascript:;' onclick=DoPost_patient('<?php echo $base_url ?>patient_data.php?order=<?php echo $order; ?>&provider=<?php echo $provider; ?>&id=by_appointment')>Patients By Appointments</a></li>
             <?php  $sql_vis=sqlStatement("SELECT provider_plist_links from tbl_user_custom_attr_1to1 where userid='".$id['id']."'");
                    $row1_vis=sqlFetchArray($sql_vis);  
                    if(!empty($row1_vis)){
                        $links=explode("|",$row1_vis['provider_plist_links']);
                         if(in_array('patient_center',$links)){ ?>
                              <li class=""><a href='javascript:;' onclick=DoPost_patient('<?php echo $base_url ?>patient-center-batch.php?provider=<?php echo $provider; ?>')>Patient Center Batch</a></li>
                        <?php  } if(in_array('patient_stat',$links)){  ?>
                              <li class=""><a href='javascript:;' onclick=DoPost_patient('<?php echo $base_url ?>patient-statement.php?provider=<?php echo $provider; ?>')>Patient Statement Batch</a></li>
                        <?php }if(in_array('create_patient',$links)){ ?>
                               <li class=""><a href='javascript:;' onclick=DoPost_patient_ajax()>Create Patient</a></li>
                       <?php  }  
                              if(in_array('create_app',$links)){ ?>
                               <li class=""><a href='javascript:;' onclick=DoPost_patient('<?php echo $base_url ?>/scheduling/calendar/add_edit_event.php')>Create Appointment</a></li>
                        <?php }
                              if(in_array('create_enc',$links)){ ?>
                               <li class=""><a href='javascript:;' onclick=DoPost_patient('<?php echo $base_url ?>create_encounter/new.php?provider=<?php echo $provider; ?>')>Create Encounter</a></li>
                        <?php }
                    }
            ?>
        </ul>                  
</div>

                                              
                                        
                                                