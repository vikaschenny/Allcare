<?php

require_once(dirname(__FILE__) . "/../../../library/classes/ORDataObject.class.php");


/**
 * class 
 *
 */
class allcare_FormROS extends ORDataObject {

	/**
	 *
	 * @access public
	 */


	/**
	 *
	 * static
	 */

	/**
	 *
	 * @access private
	 */

	var $id;
	var $date;
	var $pid;
	var $weight_change = "N/A";
	var $weakness = "N/A";
	var $fatigue = "N/A";
	var $anorexia = "N/A";
	var $fever = "N/A";
	var $chills = "N/A";
	var $night_sweats = "N/A";
	var $insomnia = "N/A";
	var $irritability = "N/A";
	var $heat_or_cold = "N/A";
	var $intolerance = "N/A";
        var $change_in_appetite = "N/A";
	var $change_in_vision = "N/A";
	var $glaucoma_history = "N/A";
	var $eye_pain = "N/A";
	var $irritation = "N/A";
	var $redness = "N/A";
	var $excessive_tearing = "N/A";
	var $double_vision = "N/A";
	var $blind_spots = "N/A";
	var $photophobia = "N/A";
        var $glaucoma = "N/A";
	var $cataract = "N/A";
        var $injury = "N/A";
	var $hearing_loss = "N/A";
        var $ha = "N/A";
        var $coryza = "N/A";
	var $obstruction = "N/A";
    var $discharge = "N/A";
    var $pain = "N/A";
    var $vertigo = "N/A";
    var $tinnitus = "N/A";
    var $frequent_colds = "N/A";
    var $sore_throat = "N/A";
    var $sinus_problems = "N/A";
    var $post_nasal_drip = "N/A";
    var $nosebleed = "N/A";
    var $snoring = "N/A";
    var $apnea = "N/A";
    var $breast_mass = "N/A";
    var $bleeding_gums = "N/A";
    var $hoarseness = "N/A";
    var $dental_difficulties = "N/A";
	var $use_of_dentures = "N/A";
	var $breast_discharge = "N/A";
	var $biopsy = "N/A";
	var $abnormal_mammogram = "N/A";
	var $cough = "N/A";
	var $sputum = "N/A";
	var $shortness_of_breath = "N/A";
	var $wheezing = "N/A";
	var $hemoptsyis = "N/A";
	var $asthma = "N/A";
	var $copd = "N/A";
	var $chest_pain = "N/A";
    var $palpitation = "N/A";
    var $syncope = "N/A";
    var $pnd = "N/A";
    var $doe = "N/A";
    var $orthopnea = "N/A";
    var $peripheal = "N/A";
    var $edema = "N/A";
    var $legpain_cramping = "N/A";
    var $history_murmur = "N/A";
    var $arryhmia = "N/A";
    var $heart_problem = "N/A";
    var $dysphagia = "N/A";
	var $heartburn = "N/A";
	var $bloating = "N/A";
	var $belching = "N/A";
	var $flatulence = "N/A";
	var $nausea = "N/A";
	var $vomiting = "N/A";
	var $hematemesis = "N/A";
	var $gastro_pain = "N/A";
	var $food_intolerance = "N/A";
	var $hepatitis = "N/A";
	var $jaundice = "N/A";
	var $hematochezia = "N/A";
	var $changed_bowel = "N/A";
	var $diarrhea = "N/A";
	var $constipation = "N/A";
        var $blood_in_stool = "N/A";
	var $polyuria = "N/A";
	var $polydypsia = "N/A";
	var $dysuria = "N/A";
	var $hematuria = "N/A";
	var $frequency = "N/A";
	var $urgency = "N/A";
	var $incontinence = "N/A";
	var $renal_stones = "N/A";
	var $utis = "N/A";
        var $change_in_nature_of_urine = "N/A";
	var $hesitancy = "N/A";
        var $blood_in_urine = "N/A";
	var $urinary_retention = "N/A";
	var $dribbling = "N/A";
	var $stream = "N/A";
	var $nocturia = "N/A";
	var $erections = "N/A";
	var $ejaculations = "N/A";
	var $g = "N/A";
	var $p = "N/A";
	var $ap = "N/A";
	var $lc = "N/A";
	var $mearche = "N/A";
	var $menopause = "N/A";
	var $lmp = "N/A";
	var $f_frequency = "N/A";
	var $f_flow = "N/A";
	var $f_symptoms = "N/A";
	var $abnormal_hair_growth = "N/A";
	var $f_hirsutism = "N/A";
	var $joint_pain = "N/A";
	var $swelling = "N/A";
	var $m_redness = "N/A";
	var $m_warm = "N/A";
	var $m_stiffness = "N/A";
	var $m_aches = "N/A";
	var $fms = "N/A";
	var $arthritis = "N/A";
        var $gout = "N/A";
        var $paresthesia = "N/A";
        var $limitation_in_range_of_motion = "N/A";
        var $muscle_pain = "N/A";
        var $spasms = "N/A";
        var $extreme_tremors = "N/A";
	var $back_pain = "N/A";
	var $loc = "N/A";
	var $seizures = "N/A";
	var $stroke = "N/A";
	var $tia = "N/A";
	var $n_numbness = "N/A";
	var $n_weakness = "N/A";
	var $paralysis = "N/A";
	var $intellectual_decline = "N/A";
	var $memory_problems = "N/A";
	var $dementia = "N/A";
	var $n_headache = "N/A";
        var $dizziness_vertigo = "N/A";
	var $slurred_speech = "N/A";
	var $tremors = "N/A";
	var $migraines = "N/A";
        var $changes_in_mentation = "N/A";
	var $s_cancer = "N/A";
	var $psoriasis = "N/A";
	var $s_acne = "N/A";
	var $s_other = "N/A";
	var $s_disease = "N/A";
        var $rashes = "N/A";
	var $dryness = "N/A";
	var $itching = "N/A";
	var $p_diagnosis = "N/A";
	var $p_medication = "N/A";
	var $depression = "N/A";
	var $anxiety = "N/A";
	var $social_difficulties = "N/A";
        var $alcohol_drug_dependence = "N/A";
	var $suicide_thoughts = "N/A";
	var $use_of_antideprassants = "N/A";
        var $thought_content = "N/A";
        var $thyroid_problems = "N/A";
	var $diabetes = "N/A";
	var $abnormal_blood = "N/A";
	var $goiter = "N/A";
	var $heat_intolerence = "N/A";
	var $cold_intolerence = "N/A";
	var $increased_thirst = "N/A";
	var $excessive_sweating = "N/A";
	var $excessive_hunger = "N/A";
	var $anemia = "N/A";
	var $fh_blood_problems = "N/A";
	var $bleeding_problems = "N/A";
	var $allergies = "N/A";
	var $frequent_illness = "N/A";
	var $hiv = "N/A";
	var $hai_status = "N/A";
        var $hay_fever = "N/A";
	var $positive_ppd = "N/A";
        var $arrythmia = "N/A";
        var $stiffness = "N/A";
	var $neck_pain = "N/A";
        var $masses = "N/A";
	var $tenderness = "N/A";
	
        var $constitutional = 'Not Examined';
        var $eyes  = 'Not Examined';
        var $ent = 'Not Examined';
        var $breast  = 'Not Examined';
        var $respiratory = 'Not Examined';
        var $cardiovascular  = 'Not Examined';
        var $gastrointestinal = 'Not Examined';
        var $genitourinary  = 'Not Examined';
        var $genitourinarymale  = 'Not Examined';
        var $genitourinaryfemale = 'Not Examined';
        var $musculoskeletal  = 'Not Examined';
        var $extremities  = 'Not Examined';
        var $neurologic = 'Not Examined';
        var $skin  = 'Not Examined';
        var $psychiatric = 'Not Examined';
        var $endocrine  = 'Not Examined';
        var $hai = 'Not Examined';
        var $neck  = 'Not Examined';
        
	/**
	 * Constructor sets all Form attributes to their default value
	 */

	function allcare_FormROS($id= "", $_prefix = ""){
                //print_r($_POST);
          
		if (is_numeric($id)) {
			$this->id = $id;
		}
		else {
			$id = "";	
		}
		$this->date = date("Y-m-d H:i:s");
		$this->date_of_onset = date("Y-m-d");
		$this->_table = "tbl_form_allcare_ros";
		//$this->constitutional 
		$this->pid = $GLOBALS['pid'];
		if ($id != "") {
                       
			$this->populate();
                        
		}
	}
	function populate() {
		parent::populate();
	}

	function set_id($id) {
		if (!empty($id) && is_numeric($id)) {
			$this->id = $id;
		}
	}
	function get_id() {
		return $this->id;
	}
	function set_pid($pid) {
		if (!empty($pid) && is_numeric($pid)) {
			$this->pid = $pid;
		}
	}
	function get_pid() {
		return $this->pid;
	}

	function get_date() {
		return $this->date;
	}
	
	function set_date($date) {
		if(!empty($date)){
			$this->date = $date;
		}	
	}
        function get_constitutional(){
		return $this->constitutional;
	}
	function set_constitutional($data){
		if(!empty($data)){
			$this->constitutional = $data;
		}
	}
	function get_constitutional_text(){
                return $this->constitutional_text;
	}
	function set_constitutional_text($data){
		if(!empty($data)){
			return $this->constitutional_text = $data;
		}
	}
	function get_weight_change(){
		return $this->weight_change;
	}
	function set_weight_change($data){
		if(!empty($data)){
			$this->weight_change = $data;
		}
	}
	
	function get_weakness(){
		return $this->weakness;
	}
	function set_weakness($data){
		if(!empty($data)){
			$this->weakness = $data;
		}
	}
	
	function get_fatigue(){
		return $this->fatigue;
	}
	function set_fatigue($data){
		if(!empty($data)){
			$this->fatigue = $data;
		}
	}
	
	function get_anorexia(){
		return $this->anorexia;
	}
	function set_anorexia($data){
		if(!empty($data)){
			$this->anorexia = $data;
		}
	}
	
	function get_fever(){
		return $this->fever;
	}
	function set_fever($data){
		if(!empty($data)){
			$this->fever = $data;
		}
	}
	
	function get_chills(){
		return $this->chills;
	}
	function set_chills($data){
		if(!empty($data)){
			$this->chills = $data;
		}
	}
	
	function get_night_sweats(){
		return $this->night_sweats;
	}
	function set_night_sweats($data){
		if(!empty($data)){
			$this->night_sweats = $data;
		}
	}
	
	function get_insomnia(){
		return $this->insomnia;
	}
	function set_insomnia($data){
		if(!empty($data)){
			$this->insomnia = $data;
		}
	}
	
	function get_irritability(){
		return $this->irritability;
	}
	function set_irritability($data){
		if(!empty($data)){
			$this->irritability = $data;
		}
	}
	
	function get_heat_or_cold(){
		return $this->heat_or_cold;
	}
	function set_heat_or_cold($data){
		if(!empty($data)){
			$this->heat_or_cold = $data;
		}
	}
	
	function get_intolerance(){
		return $this->intolerance;
	}
	function set_intolerance($data){
		if(!empty($data)){
			$this->intolerance = $data;
		}
	}
	
        function get_change_in_appetite(){
		return $this->change_in_appetite;
	}
	function set_change_in_appetite($data){
		if(!empty($data)){
			$this->change_in_appetite = $data;
		}
	}
        function get_eyes(){
		return $this->eyes;
	}
	function set_eyes($data){
		if(!empty($data)){
			$this->eyes = $data;
		}
	}
        function get_eyes_text(){
		return $this->eyes_text;
	}
	function set_eyes_text($data){
		if(!empty($data)){
			$this->eyes_text = $data;
		}
	}
	function get_change_in_vision(){
		return $this->change_in_vision;
	}
	function set_change_in_vision($data){
		if(!empty($data)){
			$this->change_in_vision = $data;
		}
	}
	function get_glaucoma_history(){
		return $this->glaucoma_history;
	}
	function set_glaucoma_history($data){
		if(!empty($data)){
			$this->glaucoma_history = $data;
		}
	}
	function get_eye_pain(){
		return $this->eye_pain;
	}
	function set_eye_pain($data){
		if(!empty($data)){
			$this->eye_pain = $data;
		}
	}
	function get_irritation(){
		return $this->irritation;
	}
	function set_irritation($data){
		if(!empty($data)){
			$this->irritation = $data;
		}
	}
	function get_redness(){
		return $this->redness;
	}
	function set_redness($data){
		if(!empty($data)){
			$this->redness = $data;
		}
	}
	function get_excessive_tearing(){
		return $this->excessive_tearing;
	}
	function set_excessive_tearing($data){
		if(!empty($data)){
			$this->excessive_tearing = $data;
		}
	}
	function get_double_vision(){
		return $this->double_vision;
	}
	function set_double_vision($data){
		if(!empty($data)){
			$this->double_vision = $data;
		}
	}
	function get_blind_spots(){
		return $this->blind_spots;
	}
	function set_blind_spots($data){
		if(!empty($data)){
			$this->blind_spots = $data;
		}
	}
	function get_photophobia(){
		return $this->photophobia;
	}
	function set_photophobia($data){
		if(!empty($data)){
			$this->photophobia = $data;
		}
	}
	function get_glaucoma(){
		return $this->glaucoma;
	}
	function set_glaucoma($data){
		if(!empty($data)){
			$this->glaucoma = $data;
		}
	}
	function get_cataract(){
		return $this->cataract;
	}
	function set_cataract($data){
		if(!empty($data)){
			$this->cataract = $data;
		}
	}
        function get_injury(){
		return $this->injury;
	}
	function set_injury($data){
		if(!empty($data)){
			$this->injury = $data;
		}
	}
        function get_ha(){
		return $this->ha;
	}
	function set_ha($data){
		if(!empty($data)){
			$this->ha = $data;
		}
	}
        function get_coryza(){
		return $this->coryza;
	}
	function set_coryza($data){
		if(!empty($data)){
			$this->coryza = $data;
		}
	}
        function get_obstruction(){
		return $this->obstruction;
	}
	function set_obstruction($data){
		if(!empty($data)){
			$this->obstruction = $data;
		}
	}
        function get_ent(){
		return $this->ent;
	}
	function set_ent($data){
		if(!empty($data)){
			$this->ent = $data;
		}
	}
        function get_ent_text(){
		return $this->ent_text;
	}
	function set_ent_text($data){
		if(!empty($data)){
			$this->ent_text = $data;
		}
	}
	function get_hearing_loss(){
		return $this->hearing_loss;
	}
	function set_hearing_loss($data){
		if(!empty($data)){
			$this->hearing_loss = $data;
		}
	}
	function get_discharge(){
		return $this->discharge;
	}
	function set_discharge($data){
		if(!empty($data)){
			$this->discharge = $data;
		}
	}
	function get_pain(){
		return $this->pain;
	}
	function set_pain($data){
		if(!empty($data)){
			$this->pain = $data;
		}
	}
	function get_vertigo(){
		return $this->vertigo;
	}
	function set_vertigo($data){
		if(!empty($data)){
			$this->vertigo = $data;
		}
	}
	function get_tinnitus(){
		return $this->tinnitus;
	}
	function set_tinnitus($data){
		if(!empty($data)){
			$this->tinnitus = $data;
		}
	}
	function get_frequent_colds(){
		return $this->frequent_colds;
	}
	function set_frequent_colds($data){
		if(!empty($data)){
			$this->frequent_colds = $data;
		}
	}
	function get_sore_throat(){
		return $this->sore_throat;
	}
	function set_sore_throat($data){
		if(!empty($data)){
			$this->sore_throat = $data;
		}
	}
	function get_sinus_problems(){
		return $this->sinus_problems;
	}
	function set_sinus_problems($data){
		if(!empty($data)){
			$this->sinus_problems = $data;
		}
	}
	function get_post_nasal_drip(){
		return $this->post_nasal_drip;
	}
	function set_post_nasal_drip($data){
		if(!empty($data)){
			$this->post_nasal_drip = $data;
		}
	}
	function get_nosebleed(){
		return $this->nosebleed;
	}
	function set_nosebleed($data){
		if(!empty($data)){
			$this->nosebleed = $data;
		}
	}
	function get_snoring(){
		return $this->snoring;
	}
	function set_snoring($data){
		if(!empty($data)){
			$this->snoring = $data;
		}
	}
	function get_apnea(){
		return $this->apnea;
	}
	function set_apnea($data){
		if(!empty($data)){
			$this->apnea = $data;
		}
	}
        function get_bleeding_gums(){
		return $this->bleeding_gums;
	}
	function set_bleeding_gums($data){
		if(!empty($data)){
			$this->bleeding_gums = $data;
		}
	}
        function get_hoarseness(){
		return $this->hoarseness;
	}
	function set_hoarseness($data){
		if(!empty($data)){
			$this->hoarseness = $data;
		}
	}
        function get_dental_difficulties(){
		return $this->dental_difficulties;
	}
	function set_dental_difficulties($data){
		if(!empty($data)){
			$this->dental_difficulties = $data;
		}
	}
        function get_use_of_dentures(){
		return $this->use_of_dentures;
	}
	function set_use_of_dentures($data){
		if(!empty($data)){
			$this->use_of_dentures = $data;
		}
	}
        function get_breast(){
		return $this->breast;
	}
	function set_breast($data){
		if(!empty($data)){
			$this->breast = $data;
		}
	}
        function get_breast_text(){
		return $this->breast_text;
	}
	function set_breast_text($data){
		if(!empty($data)){
			$this->breast_text = $data;
		}
	}
        
	function get_breast_mass(){
		return $this->breast_mass;
	}
	function set_breast_mass($data){
		if(!empty($data)){
			$this->breast_mass = $data;
		}
	}
	function get_breast_discharge(){
		return $this->breast_discharge;
	}
	function set_breast_discharge($data){
		if(!empty($data)){
			$this->breast_discharge = $data;
		}
	}
	function get_biopsy(){
		return $this->breast_discharge;
	}
	function set_biopsy($data){
		if(!empty($data)){
			$this->biopsy = $data;
		}
	}
	function get_abnormal_mammogram(){
		return $this->abnormal_mammogram;
	}
	function set_abnormal_mammogram($data){
		if(!empty($data)){
			$this->abnormal_mammogram = $data;
		}
	}
        function get_respiratory(){
		return $this->respiratory;
	}
	function set_respiratory($data){
		if(!empty($data)){
			$this->respiratory = $data;
		}
	}
        function get_respiratory_text(){
		return $this->respiratory_text;
	}
	function set_respiratory_text($data){
		if(!empty($data)){
			$this->respiratory_text = $data;
		}
	}
	function get_cough(){
		return $this->cough;
	}
	function set_cough($data){
		if(!empty($data)){
			$this->cough = $data;
		}
	}
	function set_sputum($data){
		if(!empty($data)){
			$this->sputum = $data;
		}
	}
	function get_sputum(){
		return $this->sputum;
	}
	function get_shortness_of_breath(){
		return $this->shortness_of_breath;
	}
	function set_shortness_of_breath($data){
		if(!empty($data)){
			$this->shortness_of_breath = $data;
		}
	}
	function get_wheezing(){
		return $this->wheezing;
	}
	function set_wheezing($data){
		if(!empty($data)){
			$this->wheezing = $data;
		}
	}
	function get_hemoptsyis(){
		return $this->hemoptsyis;
	}
	function set_hemoptsyis($data){
		if(!empty($data)){
			$this->hemoptsyis = $data;
		}
	}
	function get_asthma(){
		return $this->asthma;
	}
	function set_asthma($data){
		if(!empty($data)){
			$this->asthma = $data;
		}
	}
	function get_copd(){
		return $this->copd;
	}
	function set_copd($data){
		if(!empty($data)){
			$this->copd = $data;
		}
	}
	function get_cardiovascular(){
		return $this->cardiovascular;
	}
	function set_cardiovascular($data){
		if(!empty($data)){
			$this->cardiovascular = $data;
		}
	}
        function get_cardiovascular_text(){
		return $this->cardiovascular_text;
	}
	function set_cardiovascular_text($data){
		if(!empty($data)){
			$this->cardiovascular_text = $data;
		}
	}	  
    function get_chest_pain(){
		return $this->chest_pain;
	}
	function set_chest_pain($data){
		if(!empty($data)){
			$this->chest_pain = $data;
		}
	}
	function get_palpitation(){
		return $this->palpitation;
	}
	function set_palpitation($data){
		if(!empty($data)){
			$this->palpitation = $data;
		}
	}
	function get_syncope(){
		return $this->syncope;
	}
	function set_syncope($data){
		if(!empty($data)){
			$this->syncope = $data;
		}
	}
	function get_pnd(){
		return $this->pnd;
	}
	function set_pnd($data){
		if(!empty($data)){
			$this->pnd = $data;
		}
	}
	function get_doe(){
		return $this->doe;
	}
	function set_doe($data){
		if(!empty($data)){
			$this->doe = $data;
		}
	}
	function get_orthopnea(){
		return $this->orthopnea;
	}
	function set_orthopnea($data){
		if(!empty($data)){
			$this->orthopnea = $data;
		}
	}
	function get_peripheal(){
		return $this->peripheal;
	}
	function set_peripheal($data){
		if(!empty($data)){
			$this->peripheal = $data;
		}
	}
	function get_edema(){
		return $this->edema;
	}
	function set_edema($data){
		if(!empty($data)){
			$this->edema = $data;
		}
	}
	function get_legpain_cramping(){
		return $this->legpain_cramping;
	}
	function set_legpain_cramping($data){
		if(!empty($data)){
			$this->legpain_cramping = $data;
		}
	}
	function get_history_murmur(){
		return $this->history_murmur;
	}
	function set_history_murmur($data){
		if(!empty($data)){
			$this->history_murmur = $data;
		}
	}
	function get_arrythmia(){
		return $this->arrythmia;
	}
	function set_arrythmia($data){
		if(!empty($data)){
			$this->arrythmia = $data;
		}
	}
	function get_heart_problem(){
		return $this->heart_problem;
	}
	function set_heart_problem($data){
		if(!empty($data)){
			$this->heart_problem = $data;
		}
	}
	function get_gastrointestinal(){
		return $this->gastrointestinal;
	}
	function set_gastrointestinal($data){
		if(!empty($data)){
			$this->gastrointestinal = $data;
		}
	}
        function get_gastrointestinal_text(){
		return $this->gastrointestinal_text;
	}
	function set_gastrointestinal_text($data){
		if(!empty($data)){
			$this->gastrointestinal_text = $data;
		}
	}
	function get_polyuria(){
		return $this->polyuria;
	}
	function set_polyuria($data){
		if(!empty($data)){
			$this->polyuria = $data;
		}
	}
	function get_polydypsia(){
		return $this->polydypsia;
	}
	function set_polydypsia($data){
		if(!empty($data)){
			$this->polydypsia = $data;
		}
	}
	function get_dysuria(){
		return $this->dysuria;
	}
	function set_dysuria($data){
		if(!empty($data)){
			$this->dysuria = $data;
		}
	}
	function get_hematuria(){
		return $this->hematuria;
	}
	function set_hematuria($data){
		if(!empty($data)){
			$this->hematuria = $data;
		}
	}
	function get_frequency(){
		return $this->frequency;
	}
	function set_frequency($data){
		if(!empty($data)){
			$this->frequency = $data;
		}
	}
	function get_urgency(){
		return $this->urgency;
	}
	function set_urgency($data){
		if(!empty($data)){
			$this->urgency = $data;
		}
	}
	function get_incontinence(){
		return $this->incontinence;
	}
	function set_incontinence($data){
		if(!empty($data)){
			$this->incontinence = $data;
		}
	}
	function get_renal_stones(){
		return $this->renal_stones;
	}
	function set_renal_stones($data){
		if(!empty($data)){
			$this->renal_stones = $data;
		}
	}
	function get_utis(){
		return $this->utis;
	}
	function set_utis($data){
		if(!empty($data)){
			$this->utis = $data;
		}
	}
        function get_blood_in_urine(){
		return $this->blood_in_urine;
	}
	function set_blood_in_urine($data){
		if(!empty($data)){
			$this->blood_in_urine = $data;
		}
	}
        function get_urinary_retention(){
		return $this->urinary_retention;
	}
	function set_urinary_retention($data){
		if(!empty($data)){
			$this->urinary_retention = $data;
		}
	}
	function get_change_in_nature_of_urine(){
		return $this->change_in_nature_of_urine;
	}
	function set_change_in_nature_of_urine($data){
		if(!empty($data)){
			$this->change_in_nature_of_urine = $data;
		}
	}
	function get_hesitancy(){
		return $this->hesitancy;
	}
	function set_hesitancy($data){
		if(!empty($data)){
			$this->hesitancy = $data;
		}
	}
	function get_dribbling(){
		return $this->dribbling;
	}
	function set_dribbling($data){
		if(!empty($data)){
			$this->dribbling = $data;
		}
	}
	function get_stream(){
		return $this->stream;
	}
	function set_stream($data){
		if(!empty($data)){
			$this->stream = $data;
		}
	}
	function get_nocturia(){
		return $this->nocturia;
	}
	function set_nocturia($data){
		if(!empty($data)){
			$this->nocturia = $data;
		}
	}
	function get_erections(){
		return $this->erections;
	}
	function set_erections($data){
		if(!empty($data)){
			$this->erections = $data;
		}
	}
	function get_ejaculations(){
		return $this->ejaculations;
	}
	function set_ejaculations($data){
		if(!empty($data)){
			$this->ejaculations = $data;
		}
	}
		
	function get_g(){
		return $this->g;
	}
	function set_g($data){
		if(!empty($data)){
			$this->g = $data;
		}
	}
	function get_p(){
		return $this->p;
	}
	function set_p($data){
		if(!empty($data)){
			$this->p = $data;
		}
	}
	function get_ap(){
		return $this->ap;
	}
	function set_ap($data){
		if(!empty($data)){
			$this->ap = $data;
		}
	}
	function get_lc(){
		return $this->lc;
	}
	function set_lc($data){
		if(!empty($data)){
			$this->lc = $data;
		}
	}
	function get_mearche(){
		return $this->mearche;
	}
	function set_mearche($data){
		if(!empty($data)){
			$this->mearche = $data;
		}
	}
	function get_menopause(){
		return $this->menopause;
	}
	function set_menopause($data){
		if(!empty($data)){
			$this->menopause = $data;
		}
	}
	function get_lmp(){
		return $this->lmp;
	}
	function set_lmp($data){
		if(!empty($data)){
			$this->lmp = $data;
		}
	}
	function get_f_frequency(){
		return $this->f_frequency;
	}
	function set_f_frequency($data){
		if(!empty($data)){
			$this->f_frequency = $data;
		}
	}
	function get_f_flow(){
		return $this->f_flow;
	}
	function set_f_flow($data){
		if(!empty($data)){
			$this->f_flow = $data;
		}
	}
	function get_f_symptoms(){
		return $this->f_symptoms;
	}
	function set_f_symptoms($data){
		if(!empty($data)){
			$this->f_symptoms = $data;
		}
	}
	function get_abnormal_hair_growth(){
		return $this->abnormal_hair_growth;
	}
	function set_abnormal_hair_growth($data){
		if(!empty($data)){
			$this->abnormal_hair_growth = $data;
		}
	}
	function get_f_hirsutism(){
		return $this->f_hirsutism;
	}
	function set_f_hirsutism($data){
		if(!empty($data)){
			$this->f_hirsutism = $data;
		}
	}
	
	function get_joint_pain(){
		return $this->joint_pain;
	}
	function set_joint_pain($data){
		if(!empty($data)){
			$this->joint_pain = $data;
		}
	}
	function get_swelling(){
		return $this->swelling;
	}
	function set_swelling($data){
		if(!empty($data)){
			$this->swelling = $data;
		}
	}
	function get_m_redness(){
		return $this->m_redness;
	}
	function set_m_redness($data){
		if(!empty($data)){
			$this->m_redness = $data;
		}
	}
	function get_m_warm(){
		return $this->m_warm;
	}
	function set_m_warm($data){
		if(!empty($data)){
			$this->m_warm = $data;
		}
	}
	function get_m_stiffness(){
		return $this->m_stiffness;
	}
	function set_m_stiffness($data){
		if(!empty($data)){
			$this->m_stiffness = $data;
		}
	}
	
	function get_m_aches(){
		return $this->m_aches;
	}
	function set_m_aches($data){
		if(!empty($data)){
			$this->m_aches = $data;
		}
	}
	function get_fms(){
		return $this->fms;
	}
	function set_fms($data){
		if(!empty($data)){
			$this->fms = $data;
		}
	}
	function get_arthritis(){
		return $this->arthritis;
	}
	function set_arthritis($data){
		if(!empty($data)){
			$this->arthritis = $data;
		}
	}
	function get_gout(){
		return $this->gout;
	}
	function set_gout($data){
		if(!empty($data)){
			$this->gout = $data;
		}
	}
        function get_back_pain(){
		return $this->back_pain;
	}
	function set_back_pain($data){
		if(!empty($data)){
			$this->back_pain = $data;
		}
	}
        
        function get_paresthesia(){
		return $this->paresthesia;
	}
	function set_paresthesia($data){
		if(!empty($data)){
			$this->paresthesia = $data;
		}
	}
        function get_muscle_pain(){
		return $this->muscle_pain;
	}
	function set_muscle_pain($data){
		if(!empty($data)){
			$this->muscle_pain = $data;
		}
	}
        function get_limitation_in_range_of_motion(){
		return $this->limitation_in_range_of_motion;
	}
	function set_limitation_in_range_of_motion($data){
		if(!empty($data)){
			$this->limitation_in_range_of_motion = $data;
		}
	}
	function get_loc(){
		return $this->loc;
	}
	function set_loc($data){
		if(!empty($data)){
			$this->loc = $data;
		}
	}
	function get_seizures(){
		return $this->seizures;
	}
	function set_seizures($data){
		if(!empty($data)){
			$this->seizures = $data;
		}
	}
	function get_stroke(){
		return $this->stroke;
	}
	function set_stroke($data){
		if(!empty($data)){
			$this->stroke = $data;
		}
	}
	function get_tia(){
		return $this->tia;
	}
	function set_tia($data){
		if(!empty($data)){
			$this->tia = $data;
		}
	}
	function get_n_numbness(){
		return $this->n_numbness;
	}
	function set_n_numbness($data){
		if(!empty($data)){
			$this->n_numbness = $data;
		}
	}
	function get_n_weakness(){
		return $this->n_weakness;
	}
	function set_n_weakness($data){
		if(!empty($data)){
			$this->n_weakness = $data;
		}
	}
	function get_paralysis(){
		return $this->paralysis;
	}
	function set_paralysis($data){
		if(!empty($data)){
			$this->paralysis = $data;
		}
	}
	function get_intellectual_decline(){
		return $this->intellectual_decline;
	}
	function set_intellectual_decline($data){
		if(!empty($data)){
			$this->intellectual_decline = $data;
		}
	}
	function get_memory_problems(){
		return $this->memory_problems;
	}
	function set_memory_problems($data){
		if(!empty($data)){
			$this->memory_problems = $data;
		}
	}
	function get_dementia(){
		return $this->dementia;
	}
	function set_dementia($data){
		if(!empty($data)){
			$this->dementia = $data;
		}
	}
	function get_n_headache(){
		return $this->n_headache;
	}
	function set_n_headache($data){
		if(!empty($data)){
			$this->n_headache = $data;
		}
	}
	function get_dizziness_vertigo(){
		return $this->dizziness_vertigo;
	}
	function set_dizziness_vertigo($data){
		if(!empty($data)){
			$this->dizziness_vertigo = $data;
		}
	}
        function get_slurred_speech(){
		return $this->slurred_speech;
	}
	function set_slurred_speech($data){
		if(!empty($data)){
			$this->slurred_speech = $data;
		}
	}
        function get_tremors(){
		return $this->tremors;
	}
	function set_tremors($data){
		if(!empty($data)){
			$this->tremors = $data;
		}
	}
        function get_migraines(){
		return $this->migraines;
	}
	function set_migraines($data){
		if(!empty($data)){
			$this->migraines = $data;
		}
	}
        function get_changes_in_mentation(){
		return $this->changes_in_mentation;
	}
	function set_changes_in_mentation($data){
		if(!empty($data)){
			$this->changes_in_mentation = $data;
		}
	}
	function get_s_cancer(){
		return $this->s_cancer;
	}
	function set_s_cancer($data){
		if(!empty($data)){
			$this->s_cancer = $data;
		}
	}
	function get_psoriasis(){
		return $this->psoriasis;
	}
	function set_psoriasis($data){
		if(!empty($data)){
			$this->psoriasis = $data;
		}
	}
	function get_s_acne(){
		return $this->s_acne;
	}
	function set_s_acne($data){
		if(!empty($data)){
			$this->s_acne = $data;
		}
	}
	function get_s_other(){
		return $this->s_other;
	}
	function set_s_other($data){
		if(!empty($data)){
			$this->s_other = $data;
		}
	}
	function get_s_disease(){
		return $this->s_disease;
	}
	function set_s_disease($data){
		if(!empty($data)){
			$this->s_disease = $data;
		}
	}
	function get_rashes(){
		return $this->rashes;
	}
	function set_rashes($data){
		if(!empty($data)){
			$this->rashes = $data;
		}
	}
        function get_dryness(){
		return $this->dryness;
	}
	function set_dryness($data){
		if(!empty($data)){
			$this->dryness = $data;
		}
	}
        function get_itching(){
		return $this->itching;
	}
	function set_itching($data){
		if(!empty($data)){
			$this->itching = $data;
		}
	}
	function get_p_diagnosis(){
		return $this->p_diagnosis;
	}
	function set_p_diagnosis($data){
		if(!empty($data)){
			$this->p_diagnosis = $data;
		}
	}
	function get_p_medication(){
		return $this->p_medication;
	}
	function set_p_medication($data){
		if(!empty($data)){
			$this->p_medication = $data;
		}
	}
	function get_depression(){
		return $this->depression;
	}
	function set_depression($data){
		if(!empty($data)){
			$this->depression = $data;
		}
	}
	function get_anxiety(){
		return $this->anxiety;
	}
	function set_anxiety($data){
		if(!empty($data)){
			$this->anxiety = $data;
		}
	}
	function get_social_difficulties(){
		return $this->social_difficulties;
	}
	function set_social_difficulties($data){
		if(!empty($data)){
			$this->social_difficulties = $data;
		}
	}
	function get_alcohol_drug_dependence(){
		return $this->alcohol_drug_dependence;
	}
	function set_alcohol_drug_dependence($data){
		if(!empty($data)){
			$this->alcohol_drug_dependence = $data;
		}
	}
        function get_suicide_thoughts(){
		return $this->suicide_thoughts;
	}
	function set_suicide_thoughts($data){
		if(!empty($data)){
			$this->suicide_thoughts = $data;
		}
	}
        function get_use_of_antideprassants(){
		return $this->use_of_antideprassants;
	}
	function set_use_of_antideprassants($data){
		if(!empty($data)){
			$this->use_of_antideprassants = $data;
		}
	}
        function get_thought_content(){
		return $this->thought_content;
	}
	function set_thought_content($data){
		if(!empty($data)){
			$this->thought_content = $data;
		}
	}
	function get_thyroid_problems(){
		return $this->thyroid_problems;
	}
	function set_thyroid_problems($data){
		if(!empty($data)){
			$this->thyroid_problems = $data;
		}
	}
	function get_diabetes(){
		return $this->diabetes;
	}
	function set_diabetes($data){
		if(!empty($data)){
			$this->diabetes = $data;
		}
	}
	function get_abnormal_blood(){
		return $this->abnormal_blood;
	}
	function set_abnormal_blood($data){
		if(!empty($data)){
			$this->abnormal_blood = $data;
		}
	}
	function get_goiter(){
		return $this->goiter;
	}
	function set_goiter($data){
		if(!empty($data)){
			$this->goiter = $data;
		}
	}
        function get_heat_intolerence(){
		return $this->heat_intolerence;
	}
	function set_heat_intolerence($data){
		if(!empty($data)){
			$this->heat_intolerence = $data;
		}
	}
        function get_cold_intolerence(){
		return $this->cold_intolerence;
	}
	function set_cold_intolerence($data){
		if(!empty($data)){
			$this->cold_intolerence = $data;
		}
	}
        function get_increased_thirst(){
		return $this->increased_thirst;
	}
	function set_increased_thirst($data){
		if(!empty($data)){
			$this->increased_thirst = $data;
		}
	}
        function get_excessive_sweating(){
		return $this->excessive_sweating;
	}
	function set_excessive_sweating($data){
		if(!empty($data)){
			$this->excessive_sweating = $data;
		}
	}
        function get_excessive_hunger(){
		return $this->excessive_hunger;
	}
	function set_excessive_hunger($data){
		if(!empty($data)){
			$this->excessive_hunger = $data;
		}
	}
        
	function get_anemia(){
		return $this->anemia;
	}
	function set_anemia($data){
		if(!empty($data)){
			$this->anemia = $data;
		}
	}
	function get_fh_blood_problems(){
		return $this->fh_blood_problems;
	}
	function set_fh_blood_problems($data){
		if(!empty($data)){
			$this->fh_blood_problems = $data;
		}
	}
	function get_bleeding_problems(){
		return $this->bleeding_problems;
	}
	function set_bleeding_problems($data){
		if(!empty($data)){
			$this->bleeding_problems = $data;
		}
	}
	function get_allergies(){
		return $this->allergies;
	}
	function set_allergies($data){
		if(!empty($data)){
			$this->allergies = $data;
		}
	}
	function get_frequent_illness(){
		return $this->frequent_illness;
	}
	function set_frequent_illness($data){
		if(!empty($data)){
			$this->frequent_illness = $data;
		}
	}
	function get_hiv(){
		return $this->hiv;
	}
	function set_hiv($data){
		if(!empty($data)){
			$this->hiv = $data;
		}
	}
	function get_hai_status(){
		return $this->hai_status;
	}
	function set_hai_status($data){
		if(!empty($data)){
			$this->hai_status = $data;
		}
	}
	function get_hay_fever(){
		return $this->hay_fever;
	}
	function set_hay_fever($data){
		if(!empty($data)){
			$this->hay_fever = $data;
		}
	}
        function get_positive_ppd(){
		return $this->positive_ppd;
	}
	function set_positive_ppd($data){
		if(!empty($data)){
			$this->positive_ppd = $data;
		}
	}
	function get_options(){
		$ret = array("N/A" => xl('N/A'),"YES" => xl('YES'),"NO" => xl('NO'));
		return $ret;
	}
        function get_options_pending(){
		$ret = array("YES" => xl('YES'),"NO" => xl('NO'));
		return $ret;
	}
        function get_options_finalized(){
		$ret = array("YES" => xl('YES'),"NO" => xl('NO'));
		return $ret;
	}
	function get_radio_options(){
		$ret = array("Normal" => xl('Normal'),"Not Examined" => xl('Not Examined'), "Select Details" => xl('Select Details'));
		return $ret;
	}	
//        function get_formstatus_options(){
//                $ret = array("Finalized" => xl('Finalized'),"Pending" => xl('Pending'));
//		return $ret;
//        }
	function get_dysphagia(){
		return $this->dysphagia;
	}
	function set_dysphagia($data){
		if(!empty($data)){
			$this->dysphagia = $data;
		}
	}
	function get_heartburn(){
		return $this->heartburn;
	}
	function set_heartburn($data){
		if(!empty($data)){
			$this->heartburn = $data;
		}
	}
	function get_bloating(){
		return $this->bloating;
	}
	function set_bloating($data){
		if(!empty($data)){
			$this->bloating = $data;
		}
	}
	function get_belching(){
		return $this->belching;
	}
	function set_belching($data){
		if(!empty($data)){
			$this->belching = $data;
		}
	}
	function get_flatulence(){
		return $this->flatulence;
	}
	function set_flatulence($data){
		if(!empty($data)){
			$this->flatulence = $data;
		}
	}
	function get_nausea(){
		return $this->nausea;
	}
	function set_nausea($data){
		if(!empty($data)){
			$this->nausea = $data;
		}
	}
	function get_vomiting(){
		return $this->vomiting;
	}
	function set_vomiting($data){
		if(!empty($data)){
			$this->vomiting = $data;
		}
	}
	function get_hematemesis(){
		return $this->hematemesis;
	}
	function set_hematemesis($data){
		if(!empty($data)){
			$this->hematemesis = $data;
		}
	}
	function get_gastro_pain(){
		return $this->gastro_pain;
	}
	function set_gastro_pain($data){
		if(!empty($data)){
			$this->gastro_pain = $data;
		}
	}
	function get_food_intolerance(){
		return $this->food_intolerance;
	}
	function set_food_intolerance($data){
		if(!empty($data)){
			$this->food_intolerance = $data;
		}
	}
	function get_hepatitis(){
		return $this->hepatitis;
	}
	function set_hepatitis($data){
		if(!empty($data)){
			$this->hepatitis = $data;
		}
	}
	function get_jaundice(){
		return $this->jaundice;
	}
	function set_jaundice($data){
		if(!empty($data)){
			$this->jaundice = $data;
		}
	}
	function get_hematochezia(){
		return $this->hematochezia;
	}
	function set_hematochezia($data){
		if(!empty($data)){
			$this->hematochezia = $data;
		}
	}
	function get_changed_bowel(){
		return $this->changed_bowel;
	}
	function set_changed_bowel($data){
		if(!empty($data)){
			$this->changed_bowel = $data;
		}
	}
	function get_diarrhea(){
		return $this->diarrhea;
	}
	function set_diarrhea($data){
		if(!empty($data)){
			$this->diarrhea = $data;
		}
	}
	function get_constipation(){
		return $this->constipation;
	}
	function set_constipation($data){
		if(!empty($data)){
			$this->constipation = $data;
		}
	}
        function get_blood_in_stool(){
		return $this->blood_in_stool;
	}
	function set_blood_in_stool($data){
		if(!empty($data)){
			$this->blood_in_stool = $data;
		}
	}
        
        function get_genitourinary(){
		return $this->genitourinary;
	}
	function set_genitourinary($data){
		if(!empty($data)){
			$this->genitourinary = $data;
		}
	}
        function get_genitourinary_text(){
		return $this->genitourinary_text;
	}
	function set_genitourinary_text($data){
		if(!empty($data)){
			$this->genitourinary_text = $data;
		}
	}
        function get_genitourinarymale(){
		return $this->genitourinarymale;
	}
	function set_genitourinarymale($data){
		if(!empty($data)){
			$this->genitourinarymale = $data;
		}
	}
        function get_genitourinarymale_text(){
		return $this->genitourinarymale_text;
	}
	function set_genitourinarymale_text($data){
		if(!empty($data)){
			$this->genitourinarymale_text = $data;
		}
	}
        function get_genitourinaryfemale(){
		return $this->genitourinaryfemale;
	}
	function set_genitourinaryfemale($data){
		if(!empty($data)){
			$this->genitourinaryfemale = $data;
		}
	}
        function get_genitourinaryfemale_text(){
		return $this->genitourinaryfemale_text;
	}
	function set_genitourinaryfemale_text($data){
		if(!empty($data)){
			$this->genitourinaryfemale_text = $data;
		}
	}
        function get_musculoskeletal(){
		return $this->musculoskeletal;
	}
	function set_musculoskeletal($data){
		if(!empty($data)){
			$this->musculoskeletal = $data;
		}
	}
        function get_musculoskeletal_text(){
		return $this->musculoskeletal_text;
	}
	function set_musculoskeletal_text($data){
		if(!empty($data)){
			$this->musculoskeletal_text = $data;
		}
	}
        
        function get_extremities(){
		return $this->extremities;
	}
	function set_extremities($data){
		if(!empty($data)){
			$this->extremities = $data;
		}
	}
        function get_extremities_text(){
		return $this->extremities_text;
	}
	function set_extremities_text($data){
		if(!empty($data)){
			$this->extremities_text = $data;
		}
	}
        function get_spasms(){
		return $this->spasms;
	}
	function set_spasms($data){
		if(!empty($data)){
			$this->spasms = $data;
		}
	}
        function get_extreme_tremors(){
		return $this->extreme_tremors;
	}
	function set_extreme_tremors($data){
		if(!empty($data)){
			$this->extreme_tremors = $data;
		}
	}
        function get_neurologic(){
		return $this->neurologic;
	}
	function set_neurologic($data){
		if(!empty($data)){
			$this->neurologic = $data;
		}
	}
        function get_neurologic_text(){
		return $this->neurologic_text;
	}
	function set_neurologic_text($data){
		if(!empty($data)){
			$this->neurologic_text = $data;
		}
	}
        function get_skin(){
		return $this->skin;
	}
	function set_skin($data){
		if(!empty($data)){
			$this->skin = $data;
		}
	}
        function get_skin_text(){
		return $this->skin_text;
	}
	function set_skin_text($data){
		if(!empty($data)){
			$this->skin_text = $data;
		}
	}
        function get_psychiatric(){
		return $this->psychiatric;
	}
	function set_psychiatric($data){
		if(!empty($data)){
			$this->psychiatric = $data;
		}
	}
        function get_psychiatric_text(){
		return $this->psychiatric_text;
	}
	function set_psychiatric_text($data){
		if(!empty($data)){
			$this->psychiatric_text = $data;
		}
	}
        function get_endocrine(){
		return $this->endocrine;
	}
	function set_endocrine($data){
		if(!empty($data)){
			$this->endocrine = $data;
		}
	}
        function get_endocrine_text(){
		return $this->endocrine_text;
	}
	function set_endocrine_text($data){
		if(!empty($data)){
			$this->endocrine_text = $data;
		}
	}
        function get_hai(){
		return $this->hai;
	}
	function set_hai($data){
		if(!empty($data)){
			$this->hai = $data;
		}
	}
        function get_hai_text(){
		return $this->hai_text;
	}
	function set_hai_text($data){
		if(!empty($data)){
			$this->hai_text = $data;
		}
	}
        function get_neck(){
		return $this->neck;
	}
	function set_neck($data){
		if(!empty($data)){
			$this->neck = $data;
		}
	}
        function get_neck_text(){
		return $this->neck_text;
	}
	function set_neck_text($data){
		if(!empty($data)){
			$this->neck_text = $data;
		}
	}
        function get_stiffness(){
		return $this->stiffness;
	}
	function set_stiffness($data){
		if(!empty($data)){
			$this->stiffness = $data;
		}
	}
        function get_neck_pain(){
		return $this->neck_pain;
	}
	function set_neck_pain($data){
		if(!empty($data)){
			$this->neck_pain = $data;
		}
	}
        function get_masses(){
		return $this->masses;
	}
	function set_masses($data){
		if(!empty($data)){
			$this->masses = $data;
		}
	}
        function get_tenderness(){
		return $this->tenderness;
	}
	function set_tenderness($data){
		if(!empty($data)){
			$this->tenderness = $data;
		}
	}
        function get_finalized(){
            if(empty($this->finalized))
               return $this->finalized= 'NO';
            else
                return $this->finalized;
        }
        function set_finalized($data){
                if(!empty($data)){
                      $this->finalized = $data;
			    
                }
	}
        function get_pending(){

            if(empty($this->pending))
               return $this->pending= 'NO';
           else 
               return $this->pending;
        }
        function set_pending($data){
                if(!empty($data)){
                        $this->pending = $data;
			      
                }
	}
        
        
	function toString($html = false) {
		$string .= "\n"
			."ID: " . $this->id . "\n";

		if ($html) {
			return nl2br($string);
		}
		else {
			return $string;
		}
	}
	function persist() {
		parent::persist();
	}
        
        
	
}	// end of Form

?>
