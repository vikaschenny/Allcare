<?php
require_once("ORDataObject.class.php");
/**
 * class FolderCreation
 *
 */
class InsuranceCompanyCentralDB extends ORDataObject{
    function processInsurance($id){
        require_once("{$GLOBALS['srcdir']}/sqlCentralDB.inc"); // This is to connect central db to insert/update patient data in central db
        global $sqlconfCentralDB; // This is declared in central db connection
        
        // Get practice ID
        $practiceId = '';
        $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practiceID'");
        while($row = sqlFetchArray($query)){
            $practiceId = $row['title'];
        }
        //Data sync flag
        $datasync = 1; // 1 = practice could be in sync with central db; 0 = This is a standalone practice which should not be in sync with central db
        $query = sqlStatement("SELECT title FROM list_options WHERE list_id= 'allcareConfig' AND option_id='practicetocentral'");
        while($row = sqlFetchArray($query)){
            $datasync = $row['title'];
        }
        $str3 = "SELECT inc.id,inc.name,ad.line1,ad.line2,ad.city,ad.state,ad.zip,ad.country
                 FROM insurance_companies inc LEFT JOIN addresses ad ON inc.id = ad.foreign_id where inc.id=".$id;
        $query3 = sqlStatement($str3);

        while($row3 = sqlFetchArray($query3)):
        $obj = 'Insurance';

        $sql2 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".$row3['id']." AND objecttype = '".$obj."'";
        $r = sqlStatement($sql2);
        $objref = "";
        while($row2 = sqlFetchArray($r)):
           $objref = $row2['objectref'];
        endwhile;
        $count = sqlNumRows($r);
        if($datasync == 1):
            if($count):
                $sql = "UPDATE allcareobjects SET insurance_comp_id='".$row3['id']."',
                                                  insurance_comp='".$row3['name']."',    
                                                  street='".$row3['line1']."',
                                                  streetb='".$row3['line2']."',
                                                  city='".$row3['city']."',
                                                  state='".$row3['state']."',
                                                  zip='".$row3['zip']."',
                                                  country='".$row3['country']."',
                                                  practiceId='".$practiceId."'  
                                                  WHERE id=".$objref;
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute(); 
            else:
                $sql = "INSERT INTO allcareobjects (practiceId,insurance_comp_id,insurance_comp,street,streetb,city,state,zip,country,objecttype) 
                    VALUES('".$practiceId."',".$row3['id'].",'".$row3['name']."','".$row3['line1']."','".$row3['line2']."','".$row3['city']."','".$row3['state']."','".$row3['zip']."',
                        '".$row3['country']."',  '".$obj."')";
                $stmt = $sqlconfCentralDB->prepare($sql) ;
                $stmt->execute();
                $id = $sqlconfCentralDB->lastInsertId();
            endif;
            if($count == 0):
               sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".$row3['id'].",".$id.",'Insurance')");
            endif;
         endif;  
        endwhile;
    }
}