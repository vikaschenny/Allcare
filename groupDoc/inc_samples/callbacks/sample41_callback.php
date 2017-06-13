<?php

//Local path to the text file with user data
$userInfo = file(__DIR__ . '/../../user_info.txt');
//Get user data from text file
$clientId = trim($userInfo[0]);
$privateKey = trim($userInfo[1]);
$guid = null;
//Get raw data
$json = file_get_contents("php://input");

//path to settings file - temporary save userId and apiKey like to property file
//Decode json with raw data to array
$callBack_data = json_decode($json, true);
$serializedData = json_decode($callBack_data['SerializedData'], true);

$documentGuid = $serializedData['DocumentGuid'];
$collaboratorGuid = $serializedData['UserGuid'];

if ($documentGuid != "" && $collaboratorGuid != '') {
    //Create signer object
    $signer = new GroupDocsRequestSigner(trim($privateKey));
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Create AsyncApi object
    $antApi = new AntApi($apiClient);
    //Get all collaborators for current document
    $getCollaborators = $antApi->GetAnnotationCollaborators($clientId, $documentGuid);   
	if ($getCollaborators->status == "Ok") {
        //Create ReviewerInfo array
        $reviewer = new ReviewerInfo ();
        //Loop for checking all collaborators        
		for ($n = 0; $n < count($getCollaborators->result->collaborators); $n++) {
			//Set reviewer rights to view only
			if ($getCollaborators->result->collaborators[$n]->guid == $collaboratorGuid) {
                //Add riviewer to ReviewerInfo array
                $reviewer ->id = $getCollaborators->result->collaborators[$n]->id;
                $reviewer ->access_rights = 1;
                $reviewer = array($reviewer);
            }
		}
		
        $setReviewer = $antApi->SetReviewerRights($clientId, $documentGuid, $reviewer);
		
    }
    //path to settings file - temporary save signed document GUID like to property file
    $infoFile = fopen(__DIR__ . '/../../callback_info.txt', 'w');
    fwrite($infoFile, "User rights was set to view only");
    fclose($infoFile);
}
 