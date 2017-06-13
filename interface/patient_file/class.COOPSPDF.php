<?php
/*
* Author: Mahesh Ravva
* Desc: Class file to perform pdf operations using FPDF and FPDI Libraries
* Date: 2nd August, 2013
*/
require_once('fpdf/fpdf.php');
require_once('fpdf/fpdi.php');
class COOPS_PDF {
	public  $pdf_template = 'template.doc';
	public  $pdf_template_path = 'tempfiles/';
	public  $pdf;

	function __construct() {
		$this->pdf = new FPDI();
		//$this->pdf->AddPage();
		$this->pdf->SetFont('Arial');
		$this->pdf->SetTextColor(255,0,0);				
	}
	
	//Function to save a file into server which is at a particular path on the web 
	function writeFile($url = null, $destFilename = null) {
		if($destFilename == null)	$destFilename = rand();
		if($url == null)
			return false;
		else {				
			ob_start();
			readfile($url);
			$result = file_put_contents($this->pdf_template_path.$destFilename, ob_get_contents());
			ob_end_clean();
			return true;
		}
	}
	/*Obselete 2nd August, 2013 
	//Function to read the provided file
	function saveSource($file = null, $input = null, $srcFileName = 'editedDoc.pdf') {
		$destFilename = rand();
		if($file == null)
		{
			//return false;
			return 'Source file information not provided';
		}
		else {
			/*if(file_exists($file)) {				
				$this->pdf->setSourceFile($file);
				// import page 1
				$tplIdx = $this->pdf->importPage(1, '/MediaBox');
				// use the imported page as the template
				$this->pdf->useTemplate($tplIdx, 0, 0);	
				//$this->pdf->Cell($input['textX'], $input['textY'], $input['textData']);			
				$this->pdf->Cell(1, 1, $input['textData']);
				//$data = base64_decode('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');
				$data = base64_decode($input['imageData']);
				$file = uniqid() . '.png';
				$success = file_put_contents($file, $data);							
				$arr_size = array(100,50);
				$arr_size = $this->pdf->getTemplateSize($tplidx);
				$this->pdf->Image($file, $input['textX'], $input['textY'],60, 60);				
				$this->pdf->Output($srcFileName,'F');
				unlink($file);	//Remove the signature file saved in png format immediately after saving the pdf file
				//return 'Manipulated document successfully';
				//return realpath($srcFileName);
				return 'http://emrsb.risecorp.com/interface/patient_file/'.$srcFileName;
			}
			else
			{
				return 'Source file could not be found';
			}* /
			if(file_exists($file)) {
				$this->pdf->setSourceFile($file);
				$this->pdf->SetFont('Arial','B',16);
				$this->pdf->SetTextColor(10,20,30);
				// import page 1
				$tplIdx = $this->pdf->importPage(1, '/MediaBox');
				// use the imported page as the template
				$this->pdf->useTemplate($tplIdx, 0, 0);
				//Initialize coordinate positions of text and signature
				$imgPosX = $input['x_cordinate'];
				$imgPosY = $input['y_cordinate'];
				$txtPosX = $input['textX'];
				$txtPosY = $input['textY'];
				//Scale image position
				$imgPosX = $imgPosX*($arr_size['w']/$input['v_width']);
				$imgPosY = $imgPosY*($arr_size['h']/$input['v_height']);
				//Scale text position
				$txtPosX = $txtPosX*($arr_size['w']/$input['v_width']);
				$txtPosY = $txtPosY*($arr_size['h']/$input['v_height']);
				$data = base64_decode($input['imageData']);
				$signature = uniqid() . '.png';
				$success = file_put_contents($signature, $data);
				$arr_size = array(100,50);	//initialize with dummy values
				$arr_size = $this->pdf->getTemplateSize($tplidx);
				$this->pdf->Image($signature, $imgPosX, $imgPosY,60, 60);
				$this->pdf->Cell($txtPosX,$txtPosY, $input['textData']);				
				$this->pdf->Output($srcFileName,'F');
				//unlink($signature);	//Remove the signature file saved in png format immediately after saving the pdf file
				//return 'Manipulated document successfully';
				//return realpath($srcFileName);
				return 'http://emrsb.risecorp.com/interface/patient_file/'.$srcFileName;
			}
			else
			{
				return 'Source file could not be found';
			}			
		}	//end of else
	}*/
	//Function to add image at a specific location
	
	//Function to write image and text over the provided source file
	//Called by: SaveDoc.php 
	function saveSource($file = null, $input = null, $srcFileName = 'editedDoc') {
		$destFilename = rand();
		//$srcFileName .= '.pdf';
		if($file == null)
		{
			//return false;
			echo 'Source file information not provided';
			return 'Source file information not provided';
		}
		else {
			$output = null;
			if(file_exists($file)) {
				$pages = $this->pdf->setSourceFile($file);
				$this->pdf->SetFont('Arial','B',16);
				$this->pdf->SetTextColor(10,20,30);
				$setPageTplIdx = null;
				$output .= 'Source Found; ';
				//$signature_size = array(30,30,'width="180" height="60"', 10, 'image/png');	//Initialize value of width, height, image_type, height&width, filesize, MIME
				$data = base64_decode($input['imageData']);
				$signature = uniqid() . '.png';								
				$success = file_put_contents($signature, $data);
				//$signature_size = getimagesize($signature);
				$arr_size = array(100,50);	//initialize with dummy values
				$output .= 'Pages: '.$pages.' Sign on: '.$input['page'];
				for($i = 1; $i <= $pages; $i++)
				{
					$this->pdf->addPage();
					$tplIdx = $this->pdf->importPage($i, '/MediaBox');
					$this->pdf->useTemplate($tplIdx);
					if(intval($i) === intval($input['page']))
					{
						$arr_size = $this->pdf->getTemplateSize($i);
						//Initialize coordinate positions of text and signature
						$imgPosX = $input['x_cordinate'];
						$imgPosY = $input['y_cordinate'];
						$txtPosX = $input['textX'];
						$txtPosY = $input['textY'];
						/*Scale image position to make the resolution proportionate to the canvas and doc						 * 
						 * Get ImagePostion-x(from input param) multiplied by (template_width/canvas_width)	
						 * */
						$imgPosX = $imgPosX*($arr_size['w']/$input['v_width']);						
						$imgPosY = $imgPosY*($arr_size['h']/$input['v_height']);
						//Add Signature Date
						$this->pdf->SetXY( $imgPosX, $imgPosY+4);
						$this->pdf->Cell(20, 30, $input['signdate']);
						//Scale text position
						$txtPosX = $txtPosX*($arr_size['w']/$input['v_width']);
						$txtPosY = $txtPosY*($arr_size['h']/$input['v_height']);												
						$tplIdx = $this->pdf->importPage($i, '/MediaBox');						
						$this->pdf->Image($signature, $imgPosX, $imgPosY);
						$this->pdf->SetFont('Arial', '', 15);	//TODO Get the font size requirement												
						$this->pdf->SetXY(intval($txtPosX),intval($txtPosY));
						$this->pdf->Cell(145, 30,$input['textData']);	//Width and height are the dimensions of the text panel
						$output .= 'iteration within if: '.$i.'; ';
					}//end of selected page condition					
					$output .= 'iteration in forloop: '.$i.'; ';
				}	//End of for loop
			}	//end of file exists					
				
			//$output .= 'Tpl W:'.$arr_size['w'].' Tpl H:'.$arr_size['h'].' ; Img X:'.$imgPosX.' ; Img Y:'.$imgPosY.' ; Txt X:'.intval($txtPosX).' ; Txt Y:'.intval($txtPosY).' ; CellPosX: '.$input['textX'].'; CellPosY: '.$input['textY'];
			//$output .= 'Signature Height: '.$signature_size[1].'Width: '.$signature_size[0];
			$destFilename = 'Output.pdf';
			$this->pdf->Output('signed-files/'.$srcFileName,'F');
			unlink($signature);	//Remove the signature file saved in png format immediately after saving the pdf file
			//return 'Manipulated document successfully';
			//return realpath($srcFileName);
			//$output = 'page: '.$input['page'].'; tplIdx: '.$tplIdx.'; sizeof($arr_size): '.sizeof($arr_size).'Tpl W:'.$arr_size['w'].' Tpl H:'.$arr_size['h'].' ; Img X:'.$imgPosX.' ; Img Y:'.$imgPosY.' ; Txt X:'.$txtPosX.' ; Txt Y:'.$txtPosY.' ; CellPosX: '.$input['textX'].'; CellPosY: '.$input['textY'];
                        $this->writeFile('http://emrsb.risecorp.com/interface/patient_file/signed-files/'.$srcFileName,$srcFileName);
			return 'http://emrsb.risecorp.com/interface/patient_file/signed-files/'.$srcFileName;
			}	//end of else
		}
	
	function addImage($image, $posX, $posY) {
		$this->pdf->Image($_REQUEST['imagePath'],10,$_REQUEST['imagePosX'],$_REQUEST['imagePosY']);
	}
	//Function to add Text at a specific location
	function addText($txt, $x = 100, $y = 100) {
		$this->pdf->SetFont('Arial');
		$this->pdf->SetTextColor(255,0,0);
		$this->pdf->Cell($x, $y, $txt);				
	}
	
	function __destruct() {		
		$this->pdf->SetFont('Arial');
		$this->pdf->SetTextColor(255,0,0);
		$this->pdf->Cell('100','200','Hey there!!');	
		//$this->pdf->Image($_REQUEST['imagePath'],10,$_REQUEST['imagePosX'],$_REQUEST['imagePosY']);
		//$this->writeFile('http://178.239.16.28/fzs/sites/default/files/dokumenti-vijesti/sample.pdf', 'sample');
		//$this->pdf->Output('newpdf.pdf', 'D');
		//return $this->pdf->output();		
	}
}