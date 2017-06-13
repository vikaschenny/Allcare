<?php

//###<i>This sample will show how to use <b>GetFile</b> method from Storage Api to download a file from GroupDocs Storage</i>
//Set variables and get POST data
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$fileId = F3::get('POST["fileId"]');

//###Check clientId, privateKey and file Id
if (!isset($clientId) || !isset($privateKey) || !isset($fileId)) {
    $error = 'Please enter all required parameters';
    F3::set('error', $error);
} else {
    $basePath = F3::get('POST["basePath"]');
    //###Create Signer, ApiClient and Storage Api objects
    //Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Create Storage Api object
    $storageApi = new StorageApi($apiClient);
    $docApi = new DocApi($apiClient);
    //Check if user entered base path
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //Set base path
    $storageApi->setBasePath($basePath);
    $docApi->setBasePath($basePath);
    //###Make a request to Doc API using clientId and file id
    //Obtaining all Metadata for file
    try {
        $docInfo = $docApi->GetDocumentMetadata($clientId, $fileId);
        //Selecting file names
        if ($docInfo->status == "Ok") {
            //Obtaining file name for entered file Id
            $name = $docInfo->result->last_view->document->name;
        } else {
            throw new Exception($docInfo->error_message);
        }
        //###Make a request to Storage Api for dowloading file
        //Obtaining file stream of downloading file and definition of folder where to download file
        $outFileStream = FileStream::fromHttp(dirname(__FILE__) . '/../temp', $name);
        //Downlaoding of file
        try {
            $file = $storageApi->GetFile($clientId, $fileId, $outFileStream);
            if ($file->downloadDirectory != "" && isset($file)) {
                //If request was successfull - set message variable for template
                $message = '<font color="green">File was downloaded to the <font color="blue">' .
                        $outFileStream->downloadDirectory . '</font> folder</font> <br />';
                F3::set('message', $message);
            } else {
                throw new Exception("Something wrong with entered data");
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
}

//Process template
F3::set('userId', $clientId);
F3::set('privateKey', $privateKey);
F3::set('fileId', $fileId);

echo Template::serve('sample04.htm');