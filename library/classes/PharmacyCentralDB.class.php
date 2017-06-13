<?php
require_once("ORDataObject.class.php");

/**
 * class Central DB for Pharmacy
 *
 */
class PharmacyCentralDB extends ORDataObject{ 
    function processPharmacy($id) {
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
        $str5 = "SELECT ph.id,ph.name,ph.email,ad.line1,ad.line2,ad.city,ad.state,ad.zip,ad.country 
                FROM pharmacies ph LEFT JOIN addresses ad ON ph.id = ad.foreign_id WHERE ph.id=".$id;
        $query5 = sqlStatement($str5);

        while($row5 = sqlFetchArray($query5)):
            $obj = 'Pharmacy';

            $sql2 = "SELECT objectid,objectref FROM tbl_centralobjectref WHERE objectid=".$row5['id']." AND objecttype = '".$obj."'";
            $r = sqlStatement($sql2);
            $objref = "";
            while($row2 = sqlFetchArray($r)):
               $objref = $row2['objectref'];
            endwhile;
            $count = sqlNumRows($r);
            if($datasync == 1):
                if($count):
                    $sql = "UPDATE allcareobjects SET pharmacy_id='".$row5['id']."',
                                                      pharmacy_name='".$row5['name']."',    
                                                      street='".$row5['line1']."',
                                                      streetb='".$row5['line2']."',
                                                      city='".$row5['city']."',
                                                      state='".$row5['state']."',
                                                      zip='".$row5['zip']."',
                                                      country='".$row5['country']."',
                                                      uemail='".$row5['email']."',
                                                      practiceId='".$practiceId."'      
                                                      WHERE id=".$objref;
                    $stmt = $sqlconfCentralDB->prepare($sql) ;
                    $stmt->execute(); 
                else:
                    $sql = "INSERT INTO allcareobjects (practiceId,pharmacy_id,pharmacy_name,street,streetb,city,state,zip,country,uemail,objecttype) 
                        VALUES('".$practiceId."',".$row5['id'].",'".$row5['name']."','".$row5['line1']."','".$row5['line2']."','".$row5['city']."','".$row5['state']."','".$row5['zip']."',
                            '".$row5['country']."', '".$row5['email']."', '".$obj."')";
                    $stmt = $sqlconfCentralDB->prepare($sql) ;
                    $stmt->execute();
                    $id = $sqlconfCentralDB->lastInsertId();
                endif;
                if($count == 0):
                   sqlStatement("INSERT INTO tbl_centralobjectref (objectid,objectref,objecttype) VALUES(".$row5['id'].",".$id.",'".$obj."')");
                endif;
           endif; 
        endwhile;
        
    }
}

?>