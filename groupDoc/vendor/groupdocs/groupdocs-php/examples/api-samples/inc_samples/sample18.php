<?php

//### This sample will show how to convert Doc to Docx, Docx to Doc, Docx to PDF and PPT to PDF using PHP SDK
// Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
F3::set('fileId', '');
F3::set('convertType', '');
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$convertType = F3::get('POST["convertType"]');
$callbackUrl = F3::get('POST["callbackUrl"]');
if (empty($clientId) || empty($privateKey) || empty($convertType)) {
    $error = 'Please enter all required parameters';
    F3::set('error', $error);
} else {
    //path to settings file - temporary save userId and apiKey like to property file
    $infoFile = fopen(__DIR__ . '/../user_info.txt', 'w');
    fwrite($infoFile, $clientId . "\r\n" . $privateKey);
    fclose($infoFile);
    //check if Downloads folder exists and remove it to clean all old files
    if ($callbackUrl != "") {
        if (file_exists(__DIR__ . '/../downloads')) {
            delFolder(__DIR__ . '/../downloads/');
        }
    }
    //Get base path
    $basePath = F3::get('POST["basePath"]');
    //Set variables for Viewer
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    //Get entered by user data
    $url = F3::get('POST["url"]');
    $file = $_FILES['file'];
    $fileId = F3::get('POST["fileId"]');
    //###Create Signer, ApiClient and Storage Api objects
    //Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Create AsyncApi object
    $asyncApi = new AsyncApi($apiClient);
    //Create Storage Api object
    $storageApi = new StorageApi($apiClient);
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //Set base path
    $asyncApi->setBasePath($basePath);
    $storageApi->setBasePath($basePath);
    //Check if user choose upload file from URL
    if ($url != "") {
        //Upload file from URL
        $uploadResult = $storageApi->UploadWeb($clientId, $url);
        //Check is file uploaded
        try {
            if ($uploadResult->status == "Ok") {
                //Get file GUID
                $fileGuId = $uploadResult->result->guid;
                $fileId = "";
                //If it isn't uploaded throw exception to template
            } else {
                throw new Exception($uploadResult->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
    //Check is user choose upload local file
    if ($_FILES['file']["name"] != "") {
        //Temp name of the file
        $tmpName = $file['tmp_name'];
        //Original name of the file
        $name = $file['name'];
        //Creat file stream
        $fs = FileStream::fromFile($tmpName);
        //###Make a request to Storage API using clientId
        //Upload file to current user storage
        try {
            $uploadResult = $storageApi->Upload($clientId, $name, 'uploaded', "", $fs);
            //###Check if file uploaded successfully
            if ($uploadResult->status == "Ok") {
                //Get file GUID
                $fileGuId = $uploadResult->result->guid;
                $fileId = "";
                //If it isn't uploaded throw exception to template
            } else {
                throw new Exception($uploadResult->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
    //Check is user choose file GUID
    if ($fileId != "") {
        //Get entered by user file GUID
        $fileGuId = $fileId;
    }
    
    F3::set("callbackUrl", $callbackUrl);
    //Make request to api for convert file
    try {
        $convert = $asyncApi->Convert($clientId, $fileGuId, null, null, null, $callbackUrl, $convertType);
        //Check request status
        if ($convert->status == "Ok") {
            //Delay necessary that the inquiry would manage to be processed
            for ($i = 0; $i < 5; $i++) {
                sleep(5);
                try {
                    //Make request to api for get document info by job id
                    $jobInfo = $asyncApi->GetJobDocuments($clientId, $convert->result->job_id, "");
                    $guid = '';
                    if ($jobInfo->result->job_status == "Archived") {
                        if ($jobInfo->result->inputs[0]->outputs[0]->guid != "") {
                            //Get file guid
                            $guid = $jobInfo->result->inputs[0]->outputs[0]->guid;
                            break;
                        } else {
                            throw new Exception($jobInfo->error_message);
                        }
                    } else {
                        continue;
                    }
                } catch (Exception $e) {
                    $error = 'ERROR: ' . $e->getMessage() . "\n";
                    F3::set('error', $error);
                }
            }
            //Generation of iframe URL using fileGuId
            if ($basePath == "https://api.groupdocs.com/v2.0") {
                $iframe = 'http://apps.groupdocs.com/document-viewer/embed/' .
                        $guid . '" frameborder="0" width="100%" height="600"';
                //iframe to dev server
            } elseif ($basePath == "https://dev-api.groupdocs.com/v2.0") {
                $iframe = 'http://dev-apps.groupdocs.com/document-viewer/embed/' .
                        $guid . '" frameborder="0" width="100%" height="600"';
                //iframe to test server
            } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                $iframe = 'https://stage-apps-groupdocs.dynabic.com/document-viewer/embed/' .
                        $guid . '" frameborder="0" width="100%" height="600"';
                //Iframe to realtime server
            } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                $iframe = 'http://realtime-apps.groupdocs.com/document-viewer/embed/' .
                        $guid . '" frameborder="0" width="100%" height="600"';
            }
            $iframe = $signer->signUrl($iframe);
        } else {
            throw new Exception($convert->error_message);
        }
    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
    //If request was successfull - set url variable for template
    F3::set('fileId', $fileId);
    F3::set('iframe', $iframe);
}

//### Delete downloads folder and all files in this folder
function delFolder($path) {
    $next = null;
    $item = array();
    //Get all items fron folder
    $item = scandir($path);
    //Remove from array "." and ".."
    $item = array_slice($item, 2);
    //Check is there was files
    if (count($item) > 0) {
        //Delete files from folder
        for ($i = 0; $i < count($item); $i++) {
            $next = $path . "/" . $item[$i];
            if (file_exists($next)) {
                unlink($next);
            }
        }
    }
    //Delete folder
    rmdir($path);
}

F3::set('userId', $clientId);
F3::set('privateKey', $privateKey);
F3::set('convertType', $convertType);
// Process template
echo Template::serve('sample18.htm');