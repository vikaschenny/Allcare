<?php

//###<i>This sample will show how to download document after sign envelope using PHP SDK</i>
//Set variables and get POST data
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$envelopeGuid = F3::get('POST["envelopeGuid"]');

//###Check clientId, privateKey and file Id
if (!isset($clientId) || !isset($privateKey) || !isset($envelopeGuid)) {
    $error = 'Please enter all required parameters';
    F3::set('error', $error);
} else {
    //Clear entered data from tags and spaces
    $clientId = strip_tags(trim($clientId));
    $privateKey = strip_tags(trim($privateKey));
    $envelopeGuid = strip_tags(trim($envelopeGuid));
    $basePath = F3::get('POST["basePath"]');
    //###Create Signer, ApiClient and Storage Api objects
    //Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Create Storage Api object
    $signatureApi = new SignatureApi($apiClient);
    //Check if user entered base path
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    $basePath = strip_tags(trim($basePath));
    //Set base path
    $signatureApi->setBasePath($basePath);

    //###Make a request to Storage API using clientId and file id
    try {
        //Local path to the downloads folder
        $downloadFolder = dirname(__FILE__) . '/../downloads';
        //Check is folder exist
        if (!file_exists($downloadFolder)) {
            //If folder don't exist create it
            mkdir($downloadFolder);
        }
        //Get info from envelope
        $envelopeInfo = $signatureApi->GetSignatureEnvelopeDocuments($clientId, $envelopeGuid);

        //Envelope name
        $name = $envelopeInfo->result->documents[0]->name;

        //Obtaining file stream of downloading file and definition of folder where to download file
        $outFileStream = FileStream::fromHttp($downloadFolder, $name);

        //Download document from envelope
       $downloadDocument = $signatureApi->GetSignedEnvelopeDocuments($clientId, $envelopeGuid, $outFileStream);
        F3::set("message", "<span style=\"color:green\">Files from the envelope were downloaded to server's local folder. You can check them <a href=\"/downloads/{$name}\">here</a></span>");

    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
}

//Process template
F3::set('userId', $clientId);
F3::set('privateKey', $privateKey);
F3::set('envelopeGuid', $envelopeGuid);

echo Template::serve('sample36.htm');