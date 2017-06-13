<?php
function editWhoDetails()
{
   
    try
    {
        $db = getConnection();
        $flag=0; 
        $apikey = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $apikey);
        $contactArray = json_decode($result,TRUE);
          
        $pid=$contactArray['pid'];
        $title=$contactArray['title'];
        $fname=$contactArray['fname'];
        $mname=$contactArray['mname'];
        $lname=$contactArray['lname'];
        $dob=$contactArray['DOB'];
        $sex=$contactArray['sex'];
        $ss=$contactArray['ss'];
        $drivers_license=$contactArray['drivers_license'];
        $status=$contactArray['status'];
        
       
        $update_issues_Sql="UPDATE patient_data
            SET
            title=:title,
            fname=:fname,
            mname=:mname,
            lname=:lname,
            DOB=:DOB,
            sex=:sex,
            ss=:ss,
            drivers_license=:drivers_license,
            status=:status,
            date=NOW()
            WHERE pid=$pid";
            
            $q = $db->prepare($update_issues_Sql);

            if($q->execute(array( ':title'=>$title,
                      ':fname'=>$fname,':lname'=>$lname,':mname'=>$mname,
                      ':DOB'=>$dob,':sex'=>$sex,':ss'=>$ss,':drivers_license'=>$drivers_license,':status'=>$status
            )))
            {  
                $data = '[{"id":"1"}]';
                echo $errorresult = GibberishAES::enc($data, $apikey);
            }
            else
            {
                $data = '[{"id":"0"}]';
                echo $errorresult = GibberishAES::enc($data, $apikey);
            }
    }catch(PDOException $e)
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $errorresult = GibberishAES::enc($error, $apikey);
    }    
}
function editContactDetails()
{
	try
    {
        $db = getConnection();
        $key = 'rotcoderaclla';
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $flag=0;
        $request = Slim::getInstance()->request();
        $result =  GibberishAES::dec($request->getBody(), $key);
        
        $contactArray = json_decode($result,TRUE);
			
        $pid=$contactArray['pid'];
        $street=$contactArray['street'];
        $state=$contactArray['state'];
        $city=$contactArray['city'];
        $country_code=$contactArray['country_code'];
        $postal_code=$contactArray['postal_code'];
        $mothersname=$contactArray['mothersname'];
        $guardiansname=$contactArray['guardiansname'];
        $contact_relationship=$contactArray['contact_relationship'];
        $phone_contact=$contactArray['phone_contact'];
        $phone_home=$contactArray['phone_home'];
        $phone_biz=$contactArray['phone_biz'];
        $phone_cell=$contactArray['phone_cell'];
        $email=$contactArray['email'];
        
        $update_issues_Sql="UPDATE patient_data
			SET
			street=:street,
                        city=:city,
			state=:state,
			country_code=:country_code,
			postal_code=:postal_code,
			mothersname=:mothersname,
			guardiansname=:guardiansname,
			contact_relationship=:contact_relationship,
			phone_contact=:phone_contact,
			phone_home=:phone_home,
			phone_biz=:phone_biz,
			phone_cell=:phone_cell,
			email=:email,
			date=NOW()
			WHERE pid=:pid";
			
        $q = $db->prepare($update_issues_Sql);

        if($q->execute(array( ':pid'=>$pid,':street'=>$street,':city'=>$city,
                          ':state'=>$state,':country_code'=>$country_code,
                          ':postal_code'=>$postal_code,':mothersname'=>$mothersname,':guardiansname'=>$guardiansname,':contact_relationship'=>$contact_relationship,':phone_contact'=>$phone_contact,':phone_home'=>$phone_home,':phone_biz'=>$phone_biz,':phone_cell'=>$phone_cell,':email'=>$email
        )))
        {  
                $data = '[{"id":"1"}]';
                echo  $titlelistresult = GibberishAES::enc($data, $key);
        }
        else
        {
                $data = '[{"id":"0"}]';
                echo  $titlelistresult = GibberishAES::enc($data, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
    }	
}

function editChoicesDetails()
{
    try
    {
        $db = getConnection();
        $flag=0;
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $key);
        $choicesArray = json_decode($result,TRUE);
			
        $pid=$choicesArray['pid'];
        $providerID=$choicesArray['providerID'];
        $ref_providerID=$choicesArray['ref_providerID'];
        $pharmacy_id=$choicesArray['pharmacy_id'];
        $hipaa_notice=$choicesArray['hipaa_notice'];
        $hipaa_mail=$choicesArray['hipaa_mail'];
        $hipaa_voice=$choicesArray['hipaa_voice'];
        $hipaa_message=$choicesArray['hipaa_message'];
        $hipaa_allowsms=$choicesArray['hipaa_allowsms'];
        $hipaa_allowemail=$choicesArray['hipaa_allowemail'];
        $allow_patient_portal=$choicesArray['allow_patient_portal'];
        $allow_imm_info_share=$choicesArray['allow_imm_info_share'];
        $allow_health_info_ex=$choicesArray['allow_health_info_ex'];
        $allow_imm_reg_use=$choicesArray['allow_imm_reg_use'];
		        
        $update_issues_Sql="UPDATE patient_data
			SET
			providerID=:providerID,
			ref_providerID=:ref_providerID,
			pharmacy_id=:pharmacy_id,
			hipaa_notice=:hipaa_notice,
                        hipaa_mail =:hipaa_mail,
                        hipaa_voice =:hipaa_voice,
                        hipaa_message =:hipaa_message,
                        hipaa_allowsms =:hipaa_allowsms,
                        hipaa_allowemail=:hipaa_allowemail,
                        allow_patient_portal=:allow_patient_portal,
                        allow_imm_info_share=:allow_imm_info_share,
                        allow_health_info_ex=:allow_health_info_ex,
                        allow_imm_reg_use=:allow_imm_reg_use,
			date=NOW()
			WHERE pid=$pid";

        $q = $db->prepare($update_issues_Sql);

        if($q->execute(array( ':providerID'=>$providerID, ':ref_providerID'=>$ref_providerID, ':pharmacy_id'=>$pharmacy_id,
                              ':hipaa_allowsms'=>$hipaa_allowsms,':hipaa_message'=>$hipaa_message,':hipaa_mail'=>$hipaa_mail,
                              ':hipaa_notice'=>$hipaa_notice,':hipaa_voice'=>$hipaa_voice,':hipaa_allowemail'=>$hipaa_allowemail,
                              ':allow_imm_info_share'=>$allow_imm_info_share,':allow_health_info_ex'=>$allow_health_info_ex,
                              ':allow_patient_portal'=>$allow_patient_portal,':allow_imm_reg_use' =>$allow_imm_reg_use
        )))
        {  
                $data = '[{"id":"1"}]';
                echo  $titlelistresult = GibberishAES::enc($data, $key);
        }
        else
        {
                $data = '[{"id":"0"}]';
                echo  $titlelistresult = GibberishAES::enc($data, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
    }
}

function editEmployerDetails()
{
    try
    {
        $db = getConnection();
	$flag=0;
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $key);
        $employerArray = json_decode($result,TRUE);
			
        $pid=$employerArray['pid'];
        $occupation=$employerArray['occupation'];
        $name=$employerArray['name'];
        $street=$employerArray['street'];
        $city=$employerArray['city'];
        $state=$employerArray['state'];
        $country=$employerArray['country'];
        $postal_code=$employerArray['postal_code'];

        $update_issues_Sql="UPDATE patient_data
			SET
			occupation=:occupation,
			date=NOW()
			WHERE pid=:pid";
			
        $q = $db->prepare($update_issues_Sql);

        if($q->execute(array( ':pid'=>$pid,':occupation'=>$occupation)))
        {  
            $data = '[{"id":"1"}]';
            echo  $titlelistresult = GibberishAES::enc($data, $key);
        } 
        else
        {
                $data = '[{"id":"0"}]';
                echo  $titlelistresult = GibberishAES::enc($data, $key);
        }
         $insert_emp_Sql=" UPDATE employer_data SET 
                    name=:name,
                    street=:street,
                    city=:city,
                    state=:state,
                    country=:country,
                    postal_code=:postal_code,
                    date=NOW()
                    WHERE pid=:pid
                    ";

            $sql_emp = $db->prepare($insert_emp_Sql);
            if($sql_emp->execute(array( ':name'=>$name,':street'=>$street,':postal_code'=>$postal_code,':city'=>$city,':state'=>$state,':country'=>$country,':pid'=>$pid)))
            {
                    $data = '[{"id":"1"}]';
                    echo  $titlelistresult = GibberishAES::enc($data, $key);
            }
            else
            {
                    $data = '[{"id":"0"}]';
                    echo  $titlelistresult = GibberishAES::enc($data, $key);
            }
       
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
    }
}

function editStatsDetails()
{
	try
    {
        $db = getConnection();
        $flag=0;
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $key);
        $statsArray = json_decode($result,TRUE);
			
        $pid=$statsArray['pid'];
        $language=$statsArray['language'];
        $ethnicity=$statsArray['ethnicity'];
        $race=$statsArray['race'];
        $financial_review=$statsArray['financial_review'];
        $family_size=$statsArray['family_size'];
        $monthly_income=$statsArray['monthly_income'];
        $homeless=$statsArray['homeless'];
        $interpretter=$statsArray['interpretter'];
        $migrantseasonal=$statsArray['migrantseasonal'];
        $referral_source=$statsArray['referral_source'];
        $vfc=$statsArray['vfc'];
		        
        $update_stats_Sql="UPDATE patient_data
			SET
			language=:language,
			ethnicity=:ethnicity,
			race=:race,
			financial_review=:financial_review,
			family_size=:family_size,
			monthly_income=:monthly_income,
			homeless=:homeless,
			interpretter=:interpretter,
			migrantseasonal=:migrantseasonal,
			referral_source=:referral_source,
                        vfc=:vfc,
			date=NOW()
			WHERE pid=:pid";
			
        $q = $db->prepare($update_stats_Sql);

        if($q->execute(array( ':pid'=>$pid,':language'=>$language,':ethnicity'=>$ethnicity,':race'=>$race,
                            ':financial_review'=>$financial_review,':family_size'=>$family_size,':monthly_income'=>$monthly_income,
                            ':homeless'=>$homeless,':interpretter'=>$interpretter,':migrantseasonal'=>$migrantseasonal,
                            ':referral_source'=>$referral_source,':vfc'=>$vfc
        )))
        {  
                echo '[{"id":"1"}]';
                echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
        else
        {
                $titlelistres = '[{"id":"0"}]';
                echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
    }
}

function editMiscDetails()
{
    try
    {
        $db = getConnection();
        $flag=0;
        $key = 'rotcoderaclla';
        $request = Slim::getInstance()->request();
        // The default key size is 256 bits.
        $old_key_size = GibberishAES::size();
        GibberishAES::size(256);
        $result =  GibberishAES::dec($request->getBody(), $key);
        $miscArray = json_decode($result,TRUE);

        $pid=$miscArray['pid'];
        $deceased_date=$miscArray['deceased_date'];
        $deceased_reason=$miscArray['deceased_reason'];
       				        
        $update_misc_Sql="UPDATE patient_data
			SET
			deceased_date=:deceased_date,
			deceased_reason=:deceased_reason,
                        date=NOW()
			WHERE pid=:pid";
			
        $q = $db->prepare($update_misc_Sql);

        if($q->execute(array( ':pid'=>$pid,':deceased_date'=>$deceased_date,':deceased_reason'=>$deceased_reason,
        )))
        {  
                $titlelistres = '[{"id":"1"}]';
                echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
        else
        {
                $titlelistres = '[{"id":"0"}]';
                echo  $titlelistresult = GibberishAES::enc($titlelistres, $key);
        }
    }catch(PDOException $e)
    {
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo  $titlelistresult = GibberishAES::enc($error, $key);
    }
}

//method to edit patient contact details
$app->post('/editcontactdetails','editContactDetails');

//method to edit patient choices details
$app->post('/editchoicesdetails','editChoicesDetails');

//method to edit patient employer details
$app->post('/editemployerdetails','editEmployerDetails');

//method to edit patient stats details
$app->post('/editstatsdetails','editStatsDetails');

//method to edit patient misc details
$app->post('/editmiscdetails','editMiscDetails');

//method to edit patient who details  
$app->post('/editwhodetails','editWhoDetails');

?>