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

require_once("../../verify_session.php");
require_once("../../../library/sqlCentralDB.inc");
global $sqlconfCentralDB;
$mainPlans = [];
$mainPlans['insplans'] = [];
$insPlans = [];
$insid = $_POST['insid'];

$insQuery = $sqlconfCentralDB->prepare("SELECT * FROM insurance_companies WHERE id=".$insid);
$insQuery->execute();
$row1 = $insQuery->fetch(PDO::FETCH_ASSOC);

// check if same name insurance company is already there or not
$query = sqlStatement("SELECT * FROM insurance_companies WHERE name = '".$row1['name']."' LIMIT 1");
$insRow = sqlFetchArray($query);
$insuranceId = $insRow['id']; // Insurance id for this Insurance in practice
$count = sqlNumRows($query);

if($count > 0):
    $message = 'Could not add this Insurance since same name Already exists';
    // Get only contract rates for plans in this insurance and update insurance plan in practice
    $sql4   = $sqlconfCentralDB->prepare("SELECT contractRates,planname from `tbl_patientinsurancecompany` WHERE `insuranceid` = '".$insid."'");
    $sql4->execute();
    while($row4 = $sql4->fetch(PDO::FETCH_ASSOC)):
        sqlStatement("UPDATE tbl_patientinsurancecompany SET contractRates = '".$row4['contractRates']."' WHERE planname='".$row4['planname']."' AND insuranceid='".$insRow['id']."'");
    endwhile;
    
    // Central Insurance Id = $insid
    // Practice Insurance Id = $insRow['id']
    $sql4   = $sqlconfCentralDB->prepare("SELECT * from `tbl_patientinsurancecompany` WHERE `insuranceid` = '".$insid."'");
    $sql4->execute();
    while($row4  = $sql4->fetch(PDO::FETCH_ASSOC)):
        $centralPlan = $row4['planname']; // Insurance Company plan name from central
        $centralInsurance = $row1['name'];
        $sqlQ = sqlStatement("SELECT pl.id as plId,ins.id as insId FROM tbl_patientinsurancecompany pl INNER JOIN  
                              insurance_companies ins ON pl.insuranceid = ins.id 
                              WHERE pl.planname = ? AND ins.name=?",array($centralPlan,$centralInsurance));
        $rows = sqlFetchArray($sqlQ);
        $existingInsuranceId = $rows['insId'];
        $existingPlanId = $rows['plId'];
        $count2 = sqlNumRows($sqlQ);
        if($count2 > 0):
            // This means there is same planname in Practice for the same insurance name. So, you have to update here
            $insComUpdateQry = sprintf("UPDATE tbl_patientinsurancecompany SET created_date='%s',updated_date='%s',insuranceid=%d,
                                     planname='%s',specialhealth='%s',insurance_type='%s',plan_summary='%s' WHERE id=%d",
                             $row4['created_date'],$row4['updated_date'],$existingInsuranceId,
                                     $row4['planname'],$row4['specialhealth'],$row4['insurance_type'],$row4['plan_summary'],$existingPlanId);
            sqlStatement($insComUpdateQry);

            $planId = $row4['id'];
            $sql5   = $sqlconfCentralDB->prepare("SELECT * from `tbl_inscomp_benefits` WHERE `planid` = '".$planId."' and deleted=0");
            $sql5->execute();
            while($row5  = $sql5->fetch(PDO::FETCH_ASSOC)):
            // Since benefits have no unique name like we have to planname, we can consider old benefits to be deleted and add new ones.    
            sqlStatement("DELETE FROM tbl_inscomp_benefits WHERE planid = ?",array($existingPlanId));   
            $insComInsertQry = sprintf("INSERT INTO tbl_inscomp_benefits (planid,created_date,updated_date,plan_type,sum_benfits_link,
                                        ded_lim___exce,innet_oop_individual,innet_oop_family,outnet_oop_individua,out_oop_family,out_of_poc_lim_exc,
                                        innet_primary_visit,innet_spe_visit,out_of_net_pri_visit,out_of_net_spe_visit,other_pract_office,
                                        innet_preve_care,out_of_net_preve_car,provider_network_sta,need_referral_spe,lim_exce,pre_auth_req,
                                        innet_hh_care,out_of_net_hh_care,hh__lim_exc,innet_snf,out_of_net_snf,snf_lim_exc,innet_rehab_services,
                                        out_of_net_rehab_ser,rehab_lim_exc,innet_facility_fee,innet_physician,innet_lim_exc,out_of_net_facility,
                                        hh_pcp_req,off_pcp_req,hh_pre_auth,out_of_net_physician,out_lim_exc,Inpatient_pcp_req,inpatient_pre_auth,
                                        innet_ded_individual,innet_ded_family,outnet_ded_individua,outnet_ded_family,out_other_prac,deleted) 
                             VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
                             '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
                             '%s','%s','%s')",
                            $existingPlanId,$row5['created_date'],$row5['updated_date'],mysql_escape_string($row5['plan_type']),mysql_escape_string($row5['sum_benfits_link']),
                            mysql_escape_string($row5['ded_lim___exce']), mysql_escape_string($row5['innet_oop_individual']),mysql_escape_string($row5['innet_oop_family']),
                            mysql_escape_string($row5['outnet_oop_individua']),mysql_escape_string($row5['out_oop_family']),mysql_escape_string($row5['out_of_poc_lim_exc']),
                            mysql_escape_string($row5['innet_primary_visit']),mysql_escape_string($row5['innet_spe_visit']),mysql_escape_string($row5['out_of_net_pri_visit']),
                            mysql_escape_string($row5['out_of_net_spe_visit']),mysql_escape_string($row5['other_pract_office']),mysql_escape_string($row5['innet_preve_care']),
                            mysql_escape_string($row5['out_of_net_preve_car']),mysql_escape_string($row5['provider_network_sta']),mysql_escape_string($row5['need_referral_spe']),
                            mysql_escape_string($row5['lim_exce']),mysql_escape_string($row5['pre_auth_req']),mysql_escape_string($row5['innet_hh_care']),
                            mysql_escape_string($row5['out_of_net_hh_care']),mysql_escape_string($row5['hh__lim_exc']),mysql_escape_string($row5['innet_snf']),
                            mysql_escape_string($row5['out_of_net_snf']),mysql_escape_string($row5['snf_lim_exc']),mysql_escape_string($row5['innet_rehab_services']),
                            mysql_escape_string($row5['out_of_net_rehab_ser']),mysql_escape_string($row5['rehab_lim_exc']),mysql_escape_string($row5['innet_facility_fee']),
                            mysql_escape_string($row5['innet_physician']),mysql_escape_string($row5['innet_lim_exc']),mysql_escape_string($row5['out_of_net_facility']),
                            mysql_escape_string($row5['hh_pcp_req']),mysql_escape_string($row5['off_pcp_req']),mysql_escape_string($row5['hh_pre_auth']),
                            mysql_escape_string($row5['out_of_net_physician']),mysql_escape_string($row5['out_lim_exc']),mysql_escape_string($row5['Inpatient_pcp_req']),
                            mysql_escape_string($row5['inpatient_pre_auth']),mysql_escape_string($row5['innet_ded_individual']),mysql_escape_string($row5['innet_ded_family']),
                            mysql_escape_string($row5['outnet_ded_individua']),mysql_escape_string($row5['outnet_ded_family']),mysql_escape_string($row5['out_other_prac']),$row5['deleted']);
            sqlInsert($insComInsertQry);
            endwhile;
        else:    
            // This means there is no planname in Practice for the same insurance name. So, you have to add here
            $insComInsertQry = sprintf("INSERT INTO tbl_patientinsurancecompany (created_date,updated_date,insuranceid,
                                     planname,specialhealth,insurance_type,plan_summary) 
                             VALUES('%s','%s',%d,'%s','%s','%s','%s')",
                             $row4['created_date'],$row4['updated_date'],$insuranceId,
                                     $row4['planname'],$row4['specialhealth'],$row4['insurance_type'],$row4['plan_summary']);
            $newPlanId = sqlInsert($insComInsertQry);

            $planId = $row4['id'];
            $sql5   = $sqlconfCentralDB->prepare("SELECT * from `tbl_inscomp_benefits` WHERE `planid` = '".$planId."' and deleted=0");
            $sql5->execute();
            while($row5  = $sql5->fetch(PDO::FETCH_ASSOC)):
            $insComInsertQry = sprintf("INSERT INTO tbl_inscomp_benefits (planid,created_date,updated_date,plan_type,sum_benfits_link,
                                        ded_lim___exce,innet_oop_individual,innet_oop_family,outnet_oop_individua,out_oop_family,out_of_poc_lim_exc,
                                        innet_primary_visit,innet_spe_visit,out_of_net_pri_visit,out_of_net_spe_visit,other_pract_office,
                                        innet_preve_care,out_of_net_preve_car,provider_network_sta,need_referral_spe,lim_exce,pre_auth_req,
                                        innet_hh_care,out_of_net_hh_care,hh__lim_exc,innet_snf,out_of_net_snf,snf_lim_exc,innet_rehab_services,
                                        out_of_net_rehab_ser,rehab_lim_exc,innet_facility_fee,innet_physician,innet_lim_exc,out_of_net_facility,
                                        hh_pcp_req,off_pcp_req,hh_pre_auth,out_of_net_physician,out_lim_exc,Inpatient_pcp_req,inpatient_pre_auth,
                                        innet_ded_individual,innet_ded_family,outnet_ded_individua,outnet_ded_family,out_other_prac,deleted) 
                             VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
                             '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
                             '%s','%s','%s')",
                            $planId,$row5['created_date'],$row5['updated_date'],mysql_escape_string($row5['plan_type']),mysql_escape_string($row5['sum_benfits_link']),mysql_escape_string($row5['ded_lim___exce']),
                            mysql_escape_string($row5['innet_oop_individual']),mysql_escape_string($row5['innet_oop_family']),mysql_escape_string($row5['outnet_oop_individua']),mysql_escape_string($row5['out_oop_family']),
                            mysql_escape_string($row5['out_of_poc_lim_exc']), mysql_escape_string($row5['innet_primary_visit']),mysql_escape_string($row5['innet_spe_visit']),mysql_escape_string($row5['out_of_net_pri_visit']),
                            mysql_escape_string($row5['out_of_net_spe_visit']),mysql_escape_string($row5['other_pract_office']),mysql_escape_string($row5['innet_preve_care']),mysql_escape_string($row5['out_of_net_preve_car']),
                            mysql_escape_string($row5['provider_network_sta']),mysql_escape_string($row5['need_referral_spe']),mysql_escape_string($row5['lim_exce']), mysql_escape_string($row5['pre_auth_req']),
                            mysql_escape_string($row5['innet_hh_care']),mysql_escape_string($row5['out_of_net_hh_care']),mysql_escape_string($row5['hh__lim_exc']),mysql_escape_string($row5['innet_snf']),
                            mysql_escape_string($row5['out_of_net_snf']),mysql_escape_string($row5['snf_lim_exc']),mysql_escape_string($row5['innet_rehab_services']),mysql_escape_string($row5['out_of_net_rehab_ser']),
                            mysql_escape_string($row5['rehab_lim_exc']),mysql_escape_string($row5['innet_facility_fee']),mysql_escape_string($row5['innet_physician']),mysql_escape_string($row5['innet_lim_exc']),
                            mysql_escape_string($row5['out_of_net_facility']),mysql_escape_string($row5['hh_pcp_req']),mysql_escape_string($row5['off_pcp_req']),mysql_escape_string($row5['hh_pre_auth']),
                            mysql_escape_string($row5['out_of_net_physician']),mysql_escape_string($row5['out_lim_exc']),mysql_escape_string($row5['Inpatient_pcp_req']),mysql_escape_string($row5['inpatient_pre_auth']),
                            mysql_escape_string($row5['innet_ded_individual']),mysql_escape_string($row5['innet_ded_family']),mysql_escape_string($row5['outnet_ded_individua']),mysql_escape_string($row5['outnet_ded_family']),
                            mysql_escape_string($row5['out_other_prac']),$row5['deleted']);
            sqlInsert($insComInsertQry);
            endwhile;
        endif;
        
    endwhile;
else:    
    $message = 1;
    $query = sqlStatement("SELECT MAX(id) as id FROM insurance_companies");
    $maxIdArr = sqlFetchArray($query);
    $maxId = $maxIdArr['id']+1;
    $insuranceId = $maxId;
    $insComInsertQry = sprintf("INSERT INTO insurance_companies (id,name,attn,cms_id,freeb_type,x12_receiver_id,x12_default_partner_id,alt_cms_id) 
                             VALUES(%d,'%s','%s','%s','%s','%s','%s','%s')",
                             $maxId,$row1['name'],$row1['attn'],$row1['cms_id'],$row1['freeb_type'],$row1['x12_receiver_id'],
                                 $row1['x12_default_partner_id'],$row1['alt_cms_id']);
    $newInsId = sqlInsert($insComInsertQry);
    
    $sql3   = $sqlconfCentralDB->prepare("SELECT * from `tbl_inscomp_custom_attr_1to1` WHERE `insuranceid` = '".$insid."'");
    $sql3->execute();
    $row3  = $sql3->fetch(PDO::FETCH_ASSOC);
    
    // Add insurance companies custom attributes
    $insComInsertQry = sprintf("INSERT INTO tbl_inscomp_custom_attr_1to1 (insuranceid,created_date,updated_date,payerid,primaryclaim,
                                 secclaim,eligmethod,payerexplain,payersite,payerappeal,payerpay,payercontact,payer_folder,isExternalPayer,
                                 elig_verify_type,uniqueid,aliases,parent_company,login_url,user_name,password,appeals_contact,relatedpractice) 
                             VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
                             $maxId,$row3['created_date'],$row3['updated_date'],$row3['payerid'],$row3['primaryclaim'],
                                 $row3['secclaim'],$row3['eligmethod'],$row3['payerexplain'],$row3['payersite'],$row3['payerappeal'],$row3['payerpay'],$row3['payercontact'],$row3['payer_folder'],$row3['isExternalPayer'],
                                 $row3['elig_verify_type'],$row3['uniqueid'],$row3['aliases'],$row3['parent_company'],$row3['login_url'],$row3['user_name'],$row3['password'],$row3['appeals_contact'],$row3['relatedpractice']);
    sqlInsert($insComInsertQry);
    
    // Add insurance companies Plans
    $sql4   = $sqlconfCentralDB->prepare("SELECT * from `tbl_patientinsurancecompany` WHERE `insuranceid` = '".$insid."'");
    $sql4->execute();
    while($row4  = $sql4->fetch(PDO::FETCH_ASSOC)):
        $insComInsertQry = sprintf("INSERT INTO tbl_patientinsurancecompany (created_date,updated_date,insuranceid,
                                     planname,specialhealth,insurance_type,plan_summary) 
                             VALUES('%s','%s',%d,'%s','%s','%s','%s')",
                             $row4['created_date'],$row4['updated_date'],$maxId,
                                     $row4['planname'],$row4['specialhealth'],$row4['insurance_type'],$row4['plan_summary']);
        $newPlanId = sqlInsert($insComInsertQry);
        
        $planId = $row4['id'];
        $sql5   = $sqlconfCentralDB->prepare("SELECT * from `tbl_inscomp_benefits` WHERE `planid` = '".$planId."'  and deleted=0");
        $sql5->execute();
        while($row5  = $sql5->fetch(PDO::FETCH_ASSOC)):
            $insComInsertQry = sprintf("INSERT INTO tbl_inscomp_benefits (planid,created_date,updated_date,plan_type,sum_benfits_link,
                                        ded_lim___exce,innet_oop_individual,innet_oop_family,outnet_oop_individua,out_oop_family,out_of_poc_lim_exc,
                                        innet_primary_visit,innet_spe_visit,out_of_net_pri_visit,out_of_net_spe_visit,other_pract_office,
                                        innet_preve_care,out_of_net_preve_car,provider_network_sta,need_referral_spe,lim_exce,pre_auth_req,
                                        innet_hh_care,out_of_net_hh_care,hh__lim_exc,innet_snf,out_of_net_snf,snf_lim_exc,innet_rehab_services,
                                        out_of_net_rehab_ser,rehab_lim_exc,innet_facility_fee,innet_physician,innet_lim_exc,out_of_net_facility,
                                        hh_pcp_req,off_pcp_req,hh_pre_auth,out_of_net_physician,out_lim_exc,Inpatient_pcp_req,inpatient_pre_auth,
                                        innet_ded_individual,innet_ded_family,outnet_ded_individua,outnet_ded_family,out_other_prac,deleted) 
                             VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
                             '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
                             '%s','%s','%s')",
                            $planId,$row5['created_date'],$row5['updated_date'],mysql_escape_string($row5['plan_type']),mysql_escape_string($row5['sum_benfits_link']),mysql_escape_string($row5['ded_lim___exce']),
                            mysql_escape_string($row5['innet_oop_individual']),mysql_escape_string($row5['innet_oop_family']),mysql_escape_string($row5['outnet_oop_individua']),mysql_escape_string($row5['out_oop_family']),mysql_escape_string($row5['out_of_poc_lim_exc']),
                            mysql_escape_string($row5['innet_primary_visit']),mysql_escape_string($row5['innet_spe_visit']),mysql_escape_string($row5['out_of_net_pri_visit']),mysql_escape_string($row5['out_of_net_spe_visit']),mysql_escape_string($row5['other_pract_office']),
                            mysql_escape_string($row5['innet_preve_care']),mysql_escape_string($row5['out_of_net_preve_car']),mysql_escape_string($row5['provider_network_sta']),mysql_escape_string($row5['need_referral_spe']),mysql_escape_string($row5['lim_exce']),
                            mysql_escape_string($row5['pre_auth_req']),mysql_escape_string($row5['innet_hh_care']),mysql_escape_string($row5['out_of_net_hh_care']),mysql_escape_string($row5['hh__lim_exc']),mysql_escape_string($row5['innet_snf']),mysql_escape_string($row5['out_of_net_snf']),
                            mysql_escape_string($row5['snf_lim_exc']),mysql_escape_string($row5['innet_rehab_services']),mysql_escape_string($row5['out_of_net_rehab_ser']),mysql_escape_string($row5['rehab_lim_exc']),mysql_escape_string($row5['innet_facility_fee']),
                            mysql_escape_string($row5['innet_physician']),mysql_escape_string($row5['innet_lim_exc']),mysql_escape_string($row5['out_of_net_facility']),mysql_escape_string($row5['hh_pcp_req']),mysql_escape_string($row5['off_pcp_req']),mysql_escape_string($row5['hh_pre_auth']),
                            mysql_escape_string($row5['out_of_net_physician']),mysql_escape_string($row5['out_lim_exc']),mysql_escape_string($row5['Inpatient_pcp_req']),mysql_escape_string($row5['inpatient_pre_auth']),mysql_escape_string($row5['innet_ded_individual']),
                            mysql_escape_string($row5['innet_ded_family']),mysql_escape_string($row5['outnet_ded_individua']),mysql_escape_string($row5['outnet_ded_family']),mysql_escape_string($row5['out_other_prac']),$row5['deleted']);
            sqlInsert($insComInsertQry);
        endwhile;
        
    endwhile;
    
    
    
endif;

echo json_encode(array('insId'=>$insuranceId,'message'=>$message));


?>
