<?php

require_once ($GLOBALS['fileroot'] . "/library/classes/Controller.class.php");
require_once ($GLOBALS['fileroot'] . "/library/forms.inc");
require_once ($GLOBALS['fileroot'] . "/library/patient.inc");
require_once("FormVitals_custom.class.php");

class C_FormVitals_custom extends Controller {

	var $template_dir;

    function C_FormVitals_custom($template_mod = "general") {
    	parent::Controller();
    	//$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
    	$this->template_mod = $template_mod;
    	$this->template_dir = dirname(__FILE__) . "/templates/vitals/";
    	$this->assign("FORM_ACTION", $GLOBALS['web_root']);
    	$this->assign("DONT_SAVE_LINK",$GLOBALS['webroot'] . "/interface/reports/incomplete_charts.php");
        $this->assign("STYLE", $GLOBALS['style']);

      // Options for units of measurement and things to omit.
      $this->assign("units_of_measurement",$GLOBALS['units_of_measurement']);
      $this->assign("gbl_vitals_options",$GLOBALS['gbl_vitals_options']);
    }

    function default_action_old() {
    	//$vitals = array();
    	//array_push($vitals, new FormVitals());
    	$vitals = new FormVitals();
    	$this->assign("vitals",$vitals);
    	$this->assign("results", $results);
    	return $this->fetch($this->template_dir . $this->template_mod . "_new.html");
	}

    function default_action_custom($form_id,$pid1,$encounter1,$provider1,$location1,$pending,$finalized,$isSingleView,$isFromCharts) {
        $this->assign("encounter1",$encounter1);
        $this->assign("pid",$pid1);
        $this->assign("id",$form_id);
        $this->assign("provider",$provider1);
        $this->assign("location",$location1);
        $this->assign("isSingleView",$isSingleView);
        $this->assign("isFromCharts",$isFromCharts);
        $this->assign("DONT_SAVE_LINK1", "window.close();");
        if (is_numeric($form_id)) {
    		$vitals = new FormVitals($form_id,$pid1,$encounter1,$pending,$finalized,$isSingleView,$isFromCharts);
    	}
    	else {
    		$vitals = new FormVitals(0,$pid1,$encounter1,$isSingleView,$isFromCharts);
    	}

    	$dbconn = $GLOBALS['adodb']['db'];
    	//Combined query for retrieval of vital information which is not deleted
      $sql = "SELECT fv.*, fe.date AS encdate " .
        "FROM form_vitals AS fv, forms AS f, form_encounter AS fe WHERE " .
        "fv.id != $form_id and fv.pid = " . $pid1 . " AND " .
        "f.formdir = 'vitals' AND f.deleted = 0 AND f.form_id = fv.id AND " .
        "fe.pid = f.pid AND fe.encounter = f.encounter " .
        "ORDER BY encdate DESC, fv.date DESC";
    	$result = $dbconn->Execute($sql);

        // get the patient's current age
    	$patient_data = getPatientData($pid1);
        $patient_dob=$patient_data['DOB'];
        $patient_age = getPatientAge($patient_dob);
    	$this->assign("patient_age", $patient_age);
        $this->assign("patient_dob",$patient_dob);

    	$i = 1;
    	while($result && !$result->EOF)
    	{
    		$results[$i]['id'] = $result->fields['id'];
    		$results[$i]['encdate'] = substr($result->fields['encdate'], 0, 10);
                $results[$i]['date'] = $result->fields['date'];
    		$results[$i]['activity'] = $result->fields['activity'];
    		$results[$i]['bps'] = $result->fields['bps'];
    		$results[$i]['bpd'] = $result->fields['bpd'];
    		$results[$i]['weight'] = $result->fields['weight'];
    		$results[$i]['height'] = $result->fields['height'];
    		$results[$i]['temperature'] = $result->fields['temperature'];
    		$results[$i]['temp_method'] = $result->fields['temp_method'];
    		$results[$i]['pulse'] = $result->fields['pulse'];
    		$results[$i]['respiration'] = $result->fields['respiration'];
    		$results[$i]['BMI'] = $result->fields['BMI'];
    		$results[$i]['BMI_status'] = $result->fields['BMI_status'];
                $results[$i]['note'] = $result->fields['note'];
    		$results[$i]['waist_circ'] = $result->fields['waist_circ'];
    		$results[$i]['head_circ'] = $result->fields['head_circ'];
    		$results[$i]['oxygen_saturation'] = $result->fields['oxygen_saturation'];
                $results[$i]['O2source'] = $result->fields['O2source'];
                $results[$i]['O2_flow_rate'] = $result->fields['O2_flow_rate'];
                $results[$i]['pain_scale'] = $result->fields['pain_scale'];
                $results[$i]['pending'] = $result->fields['pending'];
                $results[$i++]['finalized'] = $result->fields['finalized'];
    		$result->MoveNext();
    	}
        
    	$this->assign("vitals",$vitals);
    	$this->assign("results", $results);

    	$this->assign("VIEW",true);
	return $this->fetch($this->template_dir . $this->template_mod . "_new_custom.html");

    }
	
    function default_action_process_custom() {
          //  echo $_POST['process']."==".$_POST['encounter1']."==".$_POST['id1']."==".$_POST['pid'];
               print_r($_POST); 
		if ($_POST['process'] != "true")
			return;

		$weight = $_POST["weight"];
		$height = $_POST["height"];
		if ($weight > 0 && $height > 0) {
			$_POST["BMI"] = ($weight/$height/$height)*703;
		}
		if     ( $_POST["BMI"] > 42 )   $_POST["BMI_status"] = 'Obesity III';
		elseif ( $_POST["BMI"] > 34 )   $_POST["BMI_status"] = 'Obesity II';
		elseif ( $_POST["BMI"] > 30 )   $_POST["BMI_status"] = 'Obesity I';
		elseif ( $_POST["BMI"] > 27 )   $_POST["BMI_status"] = 'Overweight';
		elseif ( $_POST["BMI"] > 25 )   $_POST["BMI_status"] = 'Normal BL';
		elseif ( $_POST["BMI"] > 18.5 ) $_POST["BMI_status"] = 'Normal';
		elseif ( $_POST["BMI"] > 10 )   $_POST["BMI_status"] = 'Underweight';
		$temperature = $_POST["temperature"];
		if ($temperature == '0' || $temperature == '') {
			$_POST["temp_method"] = "";
		}
                //echo "<pre>"; print_r($_POST); echo "</pre>";
               
		$this->vitals = new FormVitals($_POST['id1'],$_POST['pid'],$_POST['encounter1'],$_REQUEST['isSingleView'],$_REQUEST['isFromCharts']);
		
		parent::populate_object($this->vitals);
		
		$this->vitals->persist();
		if ($_POST['encounter1'] < 1) {
			$_POST['encounter1'] = date("Ymd");
		}
		if(empty($_POST['id']))
		{
			addForm($_POST['encounter1'], "Vitals", $this->vitals->id, "vitals", $_POST['pid'], $_SESSION['userauthorized']);
			$_POST['process'] = "";
                        $sql1=sqlStatement("select form_id  from forms where form_name ='Vitals' AND encounter='".$_POST['encounter1']."' AND pid='".$_POST['pid']."' AND deleted=0 order by id desc");
                        $row1=sqlFetchArray($sql1);
                        if($row1['form_id']!=''){
                            $update=sqlStatement("UPDATE form_vitals SET  `finalized` = ?  ,  pending= ? WHERE pid=? AND id=?",array($_POST['finalized'][0],$_POST['pending'][0],$_POST['pid'],$row1['form_id']));
                        }
                        
		}else {
                    $update=sqlStatement("UPDATE form_vitals SET  `finalized` = ?  ,  pending= ? WHERE pid=? AND id=?",array($_POST['finalized'][0],$_POST['pending'][0],$_POST['pid'],$_POST['id']));
                }
		return;
    }

}

?>
