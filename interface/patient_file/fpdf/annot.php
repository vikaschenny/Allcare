<?php
require('fpdf.php');

class FPDF_Annot extends FPDF
{
var $annots=array();

function Annot($x,$y,$txt)
{
	$this->annots[$this->page][]=array($x*$this->k,$this->hPt-$y*$this->k,$txt);
}

function _putpages()
{
	//Text annotations
	foreach($this->annots as $annots)
	{
		foreach($annots as $annot)
		{
			$this->_newobj();
			$x=$annot[0];
			$y=$annot[1];
			$this->_out(sprintf('<</Type /Annot /Subtype /Text /Rect [%.2f %.2f %.2f %.2f] /Contents %s>>',$x,$y,$x,$y,$this->_textstring($annot[2])));
			$this->_out('endobj');
		}
	}
	$nbannot=0;
	$nb=$this->page;
	if(!empty($this->AliasNbPages))
	{
		//Replace number of pages
		for($n=1;$n<=$nb;$n++)
			$this->pages[$n]=str_replace($this->AliasNbPages,$nb,$this->pages[$n]);
	}
	if($this->DefOrientation=='P')
	{
		$wPt=$this->fwPt;
		$hPt=$this->fhPt;
	}
	else
	{
		$wPt=$this->fhPt;
		$hPt=$this->fwPt;
	}
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	for($n=1;$n<=$nb;$n++)
	{
		//Page
		$this->_newobj();
		$this->_out('<</Type /Page');
		$this->_out('/Parent 1 0 R');
		if(isset($this->OrientationChanges[$n]))
			$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$hPt,$wPt));
		$this->_out('/Resources 2 0 R');
		$annots='/Annots [';
		if(isset($this->PageLinks[$n]))
		{
			//Links
			foreach($this->PageLinks[$n] as $pl)
			{
				$rect=sprintf('%.2f %.2f %.2f %.2f',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
				$annots.='<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
				if(is_string($pl[4]))
					$annots.='/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
				else
				{
					$l=$this->links[$pl[4]];
					$h=isset($this->OrientationChanges[$l[0]]) ? $wPt : $hPt;
					$annots.=sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]>>',1+2*$l[0],$h-$l[1]*$this->k);
				}
			}
		}
		if(isset($this->annots[$n]))
		{
			//Text annotations
			foreach($this->annots[$n] as $annot)
			{
				$nbannot++;
				$annots.=(2+$nbannot).' 0 R ';
			}
		}
		$this->_out($annots.']');
		$this->_out('/Contents '.($this->n+1).' 0 R>>');
		$this->_out('endobj');
		//Page content
		$p=($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
		$this->_newobj();
		$this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
		$this->_putstream($p);
		$this->_out('endobj');
	}
	//Pages root
	$this->offsets[1]=strlen($this->buffer);
	$this->_out('1 0 obj');
	$this->_out('<</Type /Pages');
	$kids='/Kids [';
	for($i=0;$i<$nb;$i++)
		$kids.=(3+$nbannot+2*$i).' 0 R ';
	$this->_out($kids.']');
	$this->_out('/Count '.$nb);
	$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$wPt,$hPt));
	$this->_out('>>');
	$this->_out('endobj');
}
}
?>