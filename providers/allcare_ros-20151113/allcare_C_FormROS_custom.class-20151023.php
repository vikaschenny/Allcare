<?php
require_once ($GLOBALS['fileroot'] . "/library/classes/Controller.class.php");
require_once ($GLOBALS['fileroot'] . "/library/forms.inc");
require_once("allcare_FormROS_custom.class.php");
class allcare_C_FormROS1 extends Controller {

	var $template_dir;
	
        function allcare_C_FormROS1($template_mod = "general") {
            parent::Controller();
           // $returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
            $this->template_mod = $template_mod;
            $this->template_dir = dirname(__FILE__) . "/templates/allcare_ros/";
            $this->assign("FORM_ACTION", $GLOBALS['web_root']);
            //$this->assign("DONT_SAVE_LINK",$GLOBALS['webroot'] . "/interface/reports/incomplete_charts.php");
            //$this->assign("DONT_SAVE_LINK1","window.close();");
            $this->assign("STYLE", $GLOBALS['style']);
            //$this->assign("DETAILS_LINK", $GLOBALS['webroot'] . "/interface/forms/allcare_ros/details_page.php");
            
        }
    
        function default_action1($enc,$pid1,$location,$id1,$provider) {
           $ros = new allcare_FormROS(0,$pid1,$location,$provider);
          
           $this->assign("form",$ros);
            $this->assign("encounter",$enc);
            $this->assign("pid",$pid1);
 //           $this->assign("id1",$id1);
            $this->assign("location",$location);
            $this->assign("provider",$provider);
            //$this->assign("form_status",$ros->_form_layout());
            return $this->fetch($this->template_dir . $this->template_mod . "_new_custom.html");
	}
	
	function view_action1($form_id,$pid1,$location,$provider) {
		
		if (is_numeric($form_id)) {
    		$ros = new allcare_FormROS($form_id,$pid1,$location,$provider);
    	}
    	else {
    		$ros = new allcare_FormROS(0,$pid1,$location,$provider);
    	}
    	
    	$this->assign("form",$ros);
        //$this->assign("form_status",$ros->_form_layout());
    	return $this->fetch($this->template_dir . $this->template_mod . "_new_custom.html");

	}
	
	function default_action_process1() {
		if ($_POST['process'] != "true"){
			return;
		}
		$this->allcareros = new allcare_FormROS($_POST['id1'],$_POST['pid']);
               // print_r($_POST);
                
		//print_r($this->allcareros);
               
		parent::populate_object($this->allcareros);
		$this->allcareros->persist();
               
		//exit();
//		if ($GLOBALS['encounter'] == "") {
//			$GLOBALS['encounter'] = date("Ymd");
//		}
//		if(empty($_POST['id']))
//		{
//			addForm($GLOBALS['encounter'], "Allcare Review Of Systems", $this->allcareros->id, "allcare_ros", $GLOBALS['pid'], $_SESSION['userauthorized']);
//			$_POST['process'] = "";
//                       
//		}
                $userauthorized=1;
                if ($_POST['encounter'] == "") {
			$_POST['encounter'] = date("Ymd");
		}
		if(empty($_POST['id1']))
		{      $_SESSION['authUser']=$_POST['provider'];
                       $_SESSION['authProvider']='Default';
			addForm($_POST['encounter'], "Allcare Review Of Systems", $this->allcareros->id, "allcare_ros", $_POST['pid'], $userauthorized);
			$_POST['process'] = "";
                       
		}
		return;
	}
    
}
?>
