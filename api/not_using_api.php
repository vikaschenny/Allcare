<?php

// method to get face to face configuration notes
$app->get('/facetofacenotes','getFacetoFaceNotes');
// method to get data from the table based on passed note_id
$app->get('/facetofacenoteid/:noteid', 'getFacetoFaceNoteData');
// method to get lab request config data
$app->get('/labrequestconfig','getLabRequestConfig');



// method to get lab requisition form config data
function getLabRequestConfig()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "SELECT * FROM tbl_form_lab_requisition_configuration";        
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql);           
            $stmt->execute();
            $config_data = $stmt->fetchAll(PDO::FETCH_OBJ);            
            
            if($config_data)
            {
                //returns facetofaceform default data
                $congigDatares = json_encode($config_data); 
                echo $congigDataresult = GibberishAES::enc($congigDatares, $key);
            }
            else
            {    
                //echo 'No data available';
                $congigDatares = '[{"id":"0"}]';
                echo $congigDataresult = GibberishAES::enc($congigDatares, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $congigDataresult = GibberishAES::enc($error, $key);
            
        }
}
// method to get the face to face configuration notes 
function getFacetoFaceNotes()
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
    $sql = "SELECT option_id,title FROM list_options WHERE list_id='FaceToFace_Configuration_Notes'";
    try 
    {
        $db = getConnection();
        $stmt = $db->prepare($sql) ;        	
        $stmt->execute();
        $notes = $stmt->fetchAll(PDO::FETCH_OBJ);

        if($notes)
        {
            //returns facetofaceform default data
            $notesres = json_encode($notes); 
            echo $notesresult = GibberishAES::enc($notesres, $key);
        }
        else
        {    
            //echo 'No data available';
            $notesres = '[{"id":"0"}]';
            echo $patientresult = GibberishAES::enc($error, $key);
        }
    } 
    catch(PDOException $e) 
    {
        $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
        echo $patientresult = GibberishAES::enc($error, $key);
    }
}

// method to get list of facetoface form default data for a given note_id
function getFacetoFaceNoteData($noteid)
{
    $key = 'rotcoderaclla';
    // The default key size is 256 bits.
    $old_key_size = GibberishAES::size();
    GibberishAES::size(256);
	$sql = "SELECT * FROM tbl_form_facetoface_configuration WHERE note_id=:noteid";        
    
        try 
        {
            $db = getConnection();
            $stmt = $db->prepare($sql) ;
            $stmt->bindParam("noteid", $noteid); 		
            $stmt->execute();
            $note_id = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            
            if($note_id)
            {
                //returns facetofaceform default data
                $noteIdres = json_encode($note_id); 
                echo $notesresult = GibberishAES::enc($noteIdres, $key);
            }
            else
            {    
                //echo 'No data available';
                $noteIdres = '[{"id":"0"}]';
                echo $notesresult = GibberishAES::enc($noteIdres, $key);
            }
        } 
        catch(PDOException $e) 
        {
            
            $error = '{"error":{"text":'. $e->getMessage() .'}}'; 
            echo $notesresult = GibberishAES::enc($error, $key);
        }
}


?>