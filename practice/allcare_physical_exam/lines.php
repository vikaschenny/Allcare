<?php

// The hash is overkill, but easy to traverse for presenting the form.
// The first level key is the displayed category name, and the second
// level is the line_id for the database.  Be careful not to duplicate
// these IDs!

$pelines = array(
	'GEN' => array(
		'GENWELL'  => xl('Appearance'),
                'GENAWAKE' => xl('Awake, Alert, Oriented, in No Acute Distress')),
       'HEAD' =>array(
		'HEADNORM'  => xl('Normocephalic, Autramatic'),
                'HEADLESI'  => xl('Lesions' )),
	'EYE' => array(
		'EYECP'    => xl('Conjuntiva, Pupils'),
                'EYECON'   => xl('Conjuctive Clear, Tms Intact,Discharge, Wax, Oral Lesions, Gums pink, Bilateral Nasal Turbinates'),
                'EYEPER'  => xl('PERRLA, EOMI')),
	'ENT' => array(
		'ENTTM'    => xl('TMs/EAMs/EE, Ext Nose'),
		'ENTNASAL' => xl('Nasal Mucosa Pink, Septum Midline'),
		'ENTORAL'  => xl('Oral Mucosa Pink, Throat Clear'),
                'ENTSEPT'  => xl('Septum Midline')),
        'NECK'=> array(
		'NECKSUP'    => xl('Supple,Thyromegaly, Carotid of the Nasal Septum,  JVD,  lymphadenopathy')),
        'BACK'=> array(
		'BACKCUR'    => xl('Normal Curvature, Tenderness')),
	'CV' => array(
		'CVRRR'    => xl('RRR'),
		'CVNTOH'   => xl('Thrills or Heaves'),
		'CVCP'     => xl('Cartoid Pulsations, Pedal Pulses'),
		'CVNPE'    => xl('Peripheral Edema'),
                'CVNMU'    => xl('Murmur, Rubs,Gallops'),
                'CVS1S2'    => xl('S1, S2')),
	'CHEST' => array(
		'CHNSD'    => xl('Skin Dimpling or Breast Nodules')),
	'RESP' => array(
		'RECTAB'   => xl('Lungs CTAB'),
		'REEFF'    => xl('Respirator Effort Unlabored'),
                'RELUN'    => xl('Lungs Clear,Rales,Rhonchi,Wheezes'),
                'READV'    => xl('Adventious sounds noted')),
	'GI' => array(
		'GIOG'     => xl('Ogrganomegoly'),
		'GIHERN'   => xl('Hernia'),
		'GIRECT'   => xl('Anus, Rectal Tenderness/Mass'),
                'GISOFT'   => xl('Soft, Non Tender, Non Distended, Masses'),
                'GIBOW'   => xl('Bowel Sounds present in all four quadrants')),
	'GU' => array(
		'GUTEST'   => xl('Testicular Tenderness, Masses'),
		'GUPROS'   => xl('Prostate w/o Enlrgmt, Nodules, Tender'),
		'GUEG'     => xl('Ext Genitalia, Vag Mucosa, Cervix'),
		'GUAD'     => xl('Adnexal Tenderness/Masses'),
                'GULES'    => xl('Normal. Lesions, Discharge, Hernias Noted, Deferred'),
                'GUDEF'    => xl('Deferred')),
   
    'EXTREMITIES'=> array(
		'EXTREMIT'  => xl('Edema, Cyanosis or Clubbing'),
                'EXTREDEF'  => xl('Deformities'),
                'EXTREPED'  => xl('Pedal pulses 2+, radial pulses 2+')),
	'LYMPH' => array(
		'LYAD'     => xl('Adenopathy (2 areas required)')),
	'MUSC' => array(
		'MUSTR'    => xl('Strength'),
		'MUROM'    => xl('ROM'),
		'MUSTAB'   => xl('Stability'),
		'MUINSP'   => xl('Inspection'),
                'MUTEND'   => xl('Tenderness')),
	'NEURO' => array(
		'NEUCN2'   => xl('CN2-12 Intact'),
		'NEUREF'   => xl('Reflexes Normal'),
		'NEUSENS'  => xl('Sensory Exam Normal'),
                'NEULOCAL'  => xl('Physiological, Localizing Findings'),
                'NEUGROSS'  => xl('Grossly intact')),
	'PSYCH' => array(
		'PSYAFF'   => xl('Affect Normal'),
                'PSYJUD'   => xl('Normal Affect, Judgement and Mood, Alert and Oriented X3'),
                'PSYDEP'   => xl('Depressive Symptoms'),
                'PSYSLE'   => xl('Change In Sleeping Habit'),
                'PSYTHO'   => xl('Change In Thought Content'),
                'PSYAPP'   => xl('Patient Appears To Be In Good Mood'),
                'PSYABL'   => xl('Able To Answer Questions Appropriately')),
	'SKIN' => array(
		'SKRASH'   => xl('Rash or Abnormal Lesions'),
                'SKCLEAN'   => xl('Clean & Intact with Good Skin Turgor'),
                'SKNAIL'   => xl('Nails are intact')),
	'OTHER' => array(
		'OTHER'    => xl('Other')),

	// These generate the Treatment lines:
	/*'*' => array(
		'TRTLABS' => xl('Labs'),
		'TRTXRAY' => xl('X-ray'),
		'TRTRET'  => xl('Return Visit'))*/
);
?>
