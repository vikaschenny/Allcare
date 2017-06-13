<?php

//###<i>This sample will show how to convert several HTML documents to PDF and merge them to one document </i>
//Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
F3::set('fileId', '');
F3::set('message', '');
F3::set('iframe', '');
F3::set('basePath', '');
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$basePath = F3::get('POST["basePath"]');
$firstUrl = F3::get('POST["url1"]');
$secondUrl = F3::get("POST['url2']");
$thirdUrl = F3::get("POST['url3']");

//###Check clientId and privateKey
if (empty($clientId) || empty($privateKey) || empty($firstUrl) || empty($secondUrl) || empty($thirdUrl)) {
    $error = 'Please enter all required parameters';
    F3::set('error', $error);
} else {
    //Deleting of tags, slashes and  space from clientId and privateKey
    $clientID = strip_tags(stripslashes(trim($clientId))); //ClientId==UserId
    $apiKey = strip_tags(stripslashes(trim($privateKey))); //ApiKey==PrivateKey
    //###Create Signer, ApiClient and Storage Api objects
    //Create signer object
    $signer = new GroupDocsRequestSigner($apiKey);
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Create Storage Api object
    $storageApi = new StorageApi($apiClient);
    $asyncApi = new AsyncApi($apiClient);
    //Check if user entered base path
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //Set base path
    $storageApi->setBasePath($basePath);
    $asyncApi->setBasePath($basePath);
    $iframe = null;
    //Check URL entered
    $urlArray = array($firstUrl, $secondUrl, $thirdUrl);
    F3::set('userId', $clientID);
    F3::set('privateKey', $privateKey);
    F3::set("url1", $firstUrl);
    F3::set("url2", $secondUrl);
    F3::set("url3", $thirdUrl);
    $guidArray = array();
    //Upload file from URL
    for ($i = 0; $i < count($urlArray); $i++) {
        try {
            $uploadResult = $storageApi->UploadWeb($clientID, $urlArray[$i]);
            //Check upload status
            if ($uploadResult->status == "Ok") {
                //Add GUID's of uploaded documents to array
                array_push($guidArray, $uploadResult->result->guid);
            } else {
                throw new Exception($uploadResult->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
    try {
        //Create job info object
        $jobInfo = new JobInfo();
        $jobInfo->actions = "convert, combine";
        $jobInfo->out_formats = array("pdf");
        $jobInfo->status = -1;
        $jobInfo->email_results = true;
        $jobInfo->name = "test" . rand(0, 500);
        //Create new job
        $createJob = $asyncApi->CreateJob($clientID, $jobInfo);
        if ($createJob->status == "Ok") {
            try {
                //Add uploaded documents to job
                for ($n = 0; $n < count($guidArray); $n++) {
                    $addJobDocument = $asyncApi->AddJobDocument($clientID, $createJob->result->job_id, $guidArray[$n], false);
                    if ($addJobDocument->status != "Ok") {
                        throw new Exception($addJobDocument->error_message);
                    }
                }
            } catch (Exception $e) {
                $error = 'ERROR: ' . $e->getMessage() . "\n";
                F3::set('error', $error);
            }
            try {
                //Change job status
                $jobInfo->status = 0;
                //Update job with new status
                $updateJob = $asyncApi->UpdateJob($clientID, $createJob->result->job_id, $jobInfo);
                if ($updateJob->status == "Ok") {
                    try {
                        //Delay for server proccesing
                        sleep(8);
                        //Get result document guid from job
                        $getJobDocument = $asyncApi->GetJobDocuments($clientID, $createJob->result->job_id);
                        if ($getJobDocument->status == "Ok") {
                            //Generate iframe url
                            if ($basePath == "https://api.groupdocs.com/v2.0") {
                                $iframe = 'https://apps.groupdocs.com/document-viewer/embed/' . $getJobDocument->result->outputs[0]->guid;
                                //iframe to dev server
                            } elseif ($basePath == "https://dev-api.groupdocs.com/v2.0") {
                                $iframe = 'https://dev-apps.groupdocs.com/document-viewer/embed/' . $getJobDocument->result->outputs[0]->guid;
                                //iframe to test server
                            } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                                $iframe = 'https://stage-apps-groupdocs.dynabic.com/document-viewer/embed/' . $getJobDocument->result->outputs[0]->guid;
                            } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                                $iframe = 'https://relatime-apps.groupdocs.com/document-viewer/embed/' . $getJobDocument->result->outputs[0]->guid;
                            }
                            $iframe = $signer->signUrl($iframe);
                            F3::set('url', $iframe);
                        } else {
                            throw new Exception($getJobDocument->error_message);
                        }
                    } catch (Exception $e) {
                        $error = 'ERROR: ' . $e->getMessage() . "\n";
                        F3::set('error', $error);
                    }
                } else {
                    throw new Exception($updateJob->error_message);
                }
            } catch (Exception $e) {
                $error = 'ERROR: ' . $e->getMessage() . "\n";
                F3::set('error', $error);
            }
        } else {
            throw new Exception($createJob->error_message);
        }
    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
}
//Process template
echo Template::serve('sample33.htm');
