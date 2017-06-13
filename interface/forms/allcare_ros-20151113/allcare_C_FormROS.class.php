<?php

require_once ($GLOBALS['fileroot'] . "/library/classes/Controller.class.php");
require_once ($GLOBALS['fileroot'] . "/library/forms.inc");
require_once("allcare_FormROS.class.php");

class allcare_C_FormROS extends Controller {

	var $template_dir;
	
        function allcare_C_FormROS($template_mod = "general") {
            parent::Controller();
            $returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
            $this->template_mod = $template_mod;
            $this->template_dir = dirname(__FILE__) . "/templates/allcare_ros/";
            $this->assign("FORM_ACTION", $GLOBALS['web_root']);
            $this->assign("DONT_SAVE_LINK",$GLOBALS['webroot'] . "/interface/patient_file/encounter/$returnurl");
            $this->assign("STYLE", $GLOBALS['style']);
            //$this->assign("DETAILS_LINK", $GLOBALS['webroot'] . "/interface/forms/allcare_ros/details_page.php");
            
        }
    
        function default_action() {
            $ros = new allcare_FormROS();
            $this->assign("form",$ros); 
            //$this->assign("form_status",$ros->_form_layout());
            return $this->fetch($this->template_dir . $this->template_mod . "_new.html");
	}
	
	function view_action($form_id) {
		
		if (is_numeric($form_id)) {
    		$ros = new allcare_FormROS($form_id);
    	}
    	else {
    		$ros = new allcare_FormROS();
    	}
    	
    	$this->assign("form",$ros);
        //$this->assign("form_status",$ros->_form_layout());
    	return $this->fetch($this->template_dir . $this->template_mod . "_new.html");

	}
	
	function default_action_process() {
		if ($_POST['process'] != "true"){
			return;
		}
		$this->allcareros = new allcare_FormROS($_POST['id']);//print_r($_POST);
		 //print_r($this->allcareros);
		parent::populate_object($this->allcareros);
		$this->allcareros->persist();
               
		//exit();
		if ($GLOBALS['encounter'] == "") {
			$GLOBALS['encounter'] = date("Ymd");
		}
		if(empty($_POST['id']))
		{
			addForm($GLOBALS['encounter'], "Allcare Review Of Systems", $this->allcareros->id, "allcare_ros", $GLOBALS['pid'], $_SESSION['userauthorized']);
			$_POST['process'] = "";
                       
		}
		return;
	}
    
}
?>
