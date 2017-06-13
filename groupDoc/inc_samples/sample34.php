<?php

//###<i>This sample will show how to create folder in the GroupDocs account</i>
//Set variables and get POST data
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$folder = F3::get('POST["folder"]');

//###Check clientId, privateKey and file Id
if (!isset($clientId) || !isset($privateKey) || !isset($folder)) {
    $error = 'Please enter all required parameters';
    F3::set('error', $error);
} else {
    //Clear entered data from tags and spaces
    $clientId = strip_tags(trim($clientId));
    $privateKey = strip_tags(trim($privateKey));
    $folder = strip_tags(trim($folder));
    $basePath = F3::get('POST["basePath"]');
    //###Create Signer, ApiClient and Storage Api objects
    //Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Create Storage Api object
    $storageApi = new StorageApi($apiClient);
    //Check if user entered base path
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    $basePath = strip_tags(trim($basePath));
    //Set base path
    $storageApi->setBasePath($basePath);
    //###Make a request to Storage API using clientId and file id
    try {
        //Check entere path for propper slashes
        if (strpos($folder, '\\') == true) {
            $folder = str_replace('\\', '/', $folder);
        }
        //Create folder
        $createFodler = $storageApi->Create($clientId, $folder);
        //Check status of creating folder action
        if ($createFodler->status == "Ok") {
            //Generate message with successful result
            $message = '<span style="color:green">Folder was created ' . $createFodler->result->path . '</span>';
            F3::set('message', $message);
        } else {
            throw new Exception($docInfo->error_message);
        }
    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
}

//Process template
F3::set('userId', $clientId);
F3::set('privateKey', $privateKey);
F3::set('folder', $folder);

echo Template::serve('sample34.htm');