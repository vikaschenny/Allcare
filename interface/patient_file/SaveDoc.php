<?php
error_reporting(E_ALL);
include_once('globals.php');
include_once('class.COOPSPDF.php');
$objPDF = new COOPS_PDF();
if(isset($_REQUEST)) {	
	$page = 1;
	if(isset($_REQUEST['page']))	
		$page = $_REQUEST['page'];
	$arr_input = array(
					'textData' => $_REQUEST['textData'],
					'textX' => $_REQUEST['textX'], 
					'textY' => $_REQUEST['textY'],
					'x_cordinate' => $_REQUEST['x_cordinate'],
					'y_cordinate' => $_REQUEST['y_cordinate'],
					'imageData' => $_REQUEST['imageData'], 
					'v_width' => $_REQUEST['v_width'], 
					'v_height' => $_REQUEST['v_height'],
					'page'	=> $page,
					'imageWidth'  => $_REQUEST['imageWidth'],
					'imageHeight' => $_REQUEST['imageHeight'],
					'signdate' => $_REQUEST['date']
	);
	echo $objPDF->saveSource('drive2357.pdf', $arr_input, $_REQUEST['fileName']);
	/*$arr_input = array('textData' => $_REQUEST['textData'],'textX' => $_REQUEST['textY'], 'textY' => $_REQUEST['textX'],
						'x_cordinate' => $_REQUEST['x_cordinate'],'y_cordinate' => $_REQUEST['y_cordinate'],'imageData' => $_REQUEST['imageData'], 'v_width' => $_REQUEST['v_width'], 'v_height' => $_REQUEST['v_height']
						);*/
	//$objPDF->saveSource($_SESSION['savedDoc'], $arr_input);
	//echo $_REQUEST['fileName'].'is the filename';	
	//$objPDF->saveSource($_REQUEST['driveFilePath'], $arr_input);
	/*if($objPDF->saveSource('http://localhost:83/openemr/interface/patient_file/31241.pdf'))
		echo 'Saved successfully';
	else
		echo 'Could not save file';*/
}
/*else
	echo 'Could not save';*/
?>