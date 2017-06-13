<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../globals.php");
require_once("../../library/formdata.inc.php");
require_once("../../library/globals.inc.php");
?>
<body class="body_top" style="background-color:#FFFFCC;">
<?php
    if (isset($_POST['submit'])):
    $patients = implode(",",$_POST['patientname']);
    $queryStr =  "SELECT * FROM lists WHERE diagnosis like 'ICD9:%' AND enddate IS NULL AND pid IN ($patients) AND begdate < '2015-10-01' LIMIT 1000"; // Get all codes with ICD9 and Active
    $rows = sqlStatement($queryStr);
    $logTxt = "";
        while($row = sqlFetchArray($rows)):
            $icdNine = explode(";",$row['diagnosis']);
            foreach($icdNine as $nine):
                sqlStatement("UPDATE lists SET enddate = '2015-09-30', modifydate = DATE_FORMAT(NOW(),'%Y-%m-%d %T') WHERE id = ".$row['id']);
                $logTxt .= "Updated Enddate as 2015-09-30 for diagnosis = ".$row['diagnosis']." AND pid = ". $row['pid']."\n";
                $queryStr2 = "SELECT codes FROM list_options WHERE list_id = 'icd9toicd10Mapping' AND option_id = '".$nine."'";
                $rows2 = sqlStatement($queryStr2);
                $lastlistID = "";
                while($row2 = sqlFetchArray($rows2)):
                    $queryStr3 = "INSERT INTO `lists` (`date`,`type`,`title`,`begdate`,`enddate`,`returndate`,`occurrence`,`classification`,`referredby`,`extrainfo`,`diagnosis`,`activity`,`comments`,`pid`,`user`,`groupname`,`outcome`,`destination`,`reinjury_id`,`injury_part`,`injury_type`,`injury_grade`,`reaction`,
                                  `external_allergyid`,`erx_source`,`erx_uploaded`,`modifydate`)
                                  SELECT `date`,`type`,`title`,'2015-10-01',NULL,`returndate`,`occurrence`,`classification`,`referredby`,`extrainfo`,'".$row2['codes']."',`activity`,`comments`,`pid`,`user`,`groupname`,`outcome`,`destination`,`reinjury_id`,`injury_part`,`injury_type`,`injury_grade`,`reaction`,
                                  `external_allergyid`,`erx_source`,`erx_uploaded`,DATE_FORMAT(NOW(),'%Y-%m-%d %T') FROM lists  WHERE id = ".$row['id'];
                    $lastlistID = sqlInsert($queryStr3);
                    $logTxt .= "Added diagnosis = ".$row2['codes']." and BeginDate a 2015-10-01 to pid = ".$row['pid']."\n";
                endwhile;
                if($lastlistID != ""):
                    //Find the link betweeb encounters and this listid
                    $queryStr5 = "SELECT * FROM issue_encounter WHERE list_id = ".$row['id'];
                    $rows5 = sqlStatement($queryStr5);
                    while($row5 = sqlFetchArray($rows5)):
                        // link newly inserted listid with encounter
                        $query6 = "INSERT IGNORE INTO issue_encounter (pid,list_id,encounter,resolved) 
                                   SELECT pid,". $lastlistID . ", encounter,resolved FROM issue_encounter WHERE list_id = " . $row['id']; 
                        sqlStatement($query6);           
                    endwhile;
                endif;
            endforeach;
        endwhile;
    endif;
    file_put_contents('../../../../logs/addingICD10issues'.date('Y-m-d').'.log', $logTxt, FILE_APPEND);
    echo nl2br($logTxt);
?>
    <form action="addICD10issues.php" method="POST">
        <?php
            $queryStr4 = "SELECT pid, fname, mname, lname from patient_data";
            $prows = sqlStatement($queryStr4)
        ?>
        Patients <select name="patientname[]" multiple>
            <?php
            while($prow = sqlFetchArray($prows)):
            ?>
                <option value="<?php echo $prow['pid']; ?>" <?php if(in_array($prow['pid'],$_POST['patientname'])): ?> selected <?php endif; ?>><?php echo $prow['fname'] . " " .$prow['mname'] . " " . $prow['lname']; ?></option>
            <?php
            endwhile;
            ?>
        </select><br />
        Click submit to add ICD10 codes to active ICD9 <br> 
        Note: Please select few patients and click submit. If you see no messages being displaying then go for another set of patient selection.
        <input type='submit' name='submit' value='Submit'>
    </form>
</body>
