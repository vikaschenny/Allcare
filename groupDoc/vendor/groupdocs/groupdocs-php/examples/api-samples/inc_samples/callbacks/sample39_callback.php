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
//Get job id from array
$envelopeId = $callBack_data["SourceId"];
$jobStatus = $callBack_data["EventType"];
if ($jobStatus == "JobCompleted") {
    //Create signer object
    $signer = new GroupDocsRequestSigner(trim($privateKey));
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Create AsyncApi object
    $signatureApi = new SignatureApi($apiClient);
    //Create Storage Api object
    $storageApi = new StorageApi($apiClient);
    $getDocInfo = $signatureApi->GetSignatureEnvelopeDocuments($clientId, $envelopeId);
    //path to settings file - temporary save userId and apiKey like to property file
    if ($getDocInfo->status == "Ok") {
        $guid = $getDocInfo->result->documents[0]->documentId;
    }
    //path to settings file - temporary save userId and apiKey like to property file
    if (file_exists(__DIR__ . '/../../callback_info.txt')) {
        unlink(__DIR__ . '/../../callback_info.txt');
    }
    $infoFile = fopen(__DIR__ . '/../../callback_info.txt', 'w');
    fwrite($infoFile, $guid);
    fclose($infoFile);
}