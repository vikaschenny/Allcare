CREATE TABLE IF NOT EXISTS `form_Chirpractic_physical_therapy_form` (
id bigint(20) NOT NULL auto_increment,
date datetime default NULL,
pid bigint(20) default NULL,
user varchar(255) default NULL,
groupname varchar(255) default NULL,
authorized tinyint(4) default NULL,
activity tinyint(4) default NULL,
_date TEXT,
_social_security_number TEXT,
_drivers_license_number TEXT,
_name TEXT,
_address TEXT,
_city TEXT,
_state TEXT,
_zip TEXT,
_home_phone TEXT,
_cell_phone TEXT,
_birth_date TEXT,
_age TEXT,
_sex TEXT,
_business_or_employer TEXT,
_type_of_work TEXT,
_business_address_and_phone_number TEXT,
_check_one TEXT,
_number_of_children TEXT,
_name_and_number_of_emergency_contact TEXT,
_spouse_name TEXT,
_occupation TEXT,
_employer TEXT,
_who_is_responsible_for_your_bill TEXT,
_other TEXT,
_purpose_of_this_appointment TEXT,
_other_doctors_seen_for_this_condition TEXT,
_when_did_this_condition_begin TEXT,
_check TEXT,
_medication_you_now_take TEXT,
_others TEXT,
_major_surgery_or_operations TEXT,
_otherone TEXT,
_major_accidents_or_falls TEXT,
_hospitalization_if_other_than_above TEXT,
_previous_chiropractic_care TEXT,
_doctors_name TEXT,
_appox_date_of_last_visit TEXT,
_coughing_or_sneezing TEXT,
_climbing TEXT,
_getting_in_and_out_of_a_car TEXT,
_kneeling TEXT,
_bending_forward_to_brush_teeth TEXT,
_balancing TEXT,
_turing_over_in_bed TEXT,
_dressing_self TEXT,
_walking_short_distance TEXT,
_sleeping TEXT,
_standing_more_than_one_hour TEXT,
_stooping TEXT,
_sitting_at_table TEXT,
_gripping TEXT,
_lying_on_back TEXT,
_pushing TEXT,
_lying_flat_on_stomach TEXT,
_pulling TEXT,
_lying_on_side_with_knees_bent TEXT,
_reaching TEXT,
_bending_over_forward TEXT,
_sexual_activity TEXT,
_checking_symptoms_of_nervous_systems TEXT,
_how_often_do_you_have_headaches TEXT,
_symptoms_are_better_in TEXT,
_symptoms_are_worse_in TEXT,
_symptoms_do_not_change_with_time_of_day TEXT,
_are_you_pregnant TEXT,
_date_of_onset_of_last_menstrual_cycle TEXT,
_give_date_of_last_xray TEXT,
_what_body_part_were_they_taken_of TEXT,
_cancer TEXT,
_diabetes TEXT,
_heart_problems TEXT,
_back_or_neck_problems TEXT,
_have_you_retained_an_attorney TEXT,
_attorney_name TEXT,
_attorney_address TEXT,
_attorney_phone TEXT,
_number_of_people_in_vechicle_and_their_name TEXT,
_were_the_policy_notified TEXT,
_what_direction_were_you_headed TEXT,
_what_direction_was_other_vechicle TEXT,
_name_of_street_or_town TEXT,
_were_you_struck_from TEXT,
_in_your_own_words_please_describe_accident TEXT,
_please_complaints_and_symptoms TEXT,
_did_you_lose_any_time_from_work TEXT,
_date_when_you_lose_from_work TEXT,
_type_of_employment TEXT,
_where_were_you_taken_immediately_following_accident TEXT,
_if_taken_to_the_hospital_did_you TEXT,
_have_you_ever_been_involved_in_an_accident_before TEXT,

PRIMARY KEY (id)
) ENGINE=MyISAM;