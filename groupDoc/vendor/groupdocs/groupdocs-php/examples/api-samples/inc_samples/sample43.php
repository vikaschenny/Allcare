<?php

//###<i>This sample will show how to add numeration in the doc file</i>
//Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');

$clientID = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$basePath = F3::get('POST["basePath"]');

try {
    //###Check if user entered all parameters
    if (empty($clientID) || empty($privateKey)) {
        throw new Exception('Please enter FILE ID');
    } else {
        F3::set('userId', $clientID);
        F3::set('privateKey', $privateKey);
        //###Create Signer, ApiClient and Storage Api objects
        //Create signer object
        $signer = new GroupDocsRequestSigner($privateKey);
        //Create apiClient object
        $apiClient = new APIClient($signer);
        //Create Doc Api object
        $docApi = new DocApi($apiClient);
        //Create Storage Api object
        $storageApi = new StorageApi($apiClient);
        //Create AsyncApi object
        $asyncApi = new AsyncApi($apiClient);
        $mergApi = new MergeApi($apiClient);
        //Set url to choose whot server to use
        if ($basePath == "") {
            //If base base is empty seting base path to prod server
            $basePath = 'https://api.groupdocs.com/v2.0';
        }
        //Set base path
        $docApi->setBasePath($basePath);
        $storageApi->setBasePath($basePath);
        $asyncApi->setBasePath($basePath);
        $mergApi->setBasePath($basePath);
        //Get entered by user data
        $url = F3::get('POST["url"]');
        $file = $_FILES['file'];
        $fileId = F3::get('POST["fileId"]');
        //Check if user choose upload file from URL
        if ($url != "") {
            //Upload file from URL
            $uploadResult = $storageApi->UploadWeb($clientID, $url);
            //Check is file uploaded
            if ($uploadResult->status == "Ok") {
                //Get file GUID
                $fileGuId = $uploadResult->result->guid;
                $fileId = "";
                //If it isn't uploaded throw exception to template
            } else {
                throw new Exception($uploadResult->error_message);
            }
        }
        //Check is user choose upload local file
        if ($_FILES['file']["name"] != "") {
            //Temp name of the file
            $tmpName = $file['tmp_name'];
            //Original name of the file
            $name = $file['name'];
            //Create file stream
            $fs = FileStream::fromFile($tmpName);
            //###Make a request to Storage API using clientId
            //Upload file to current user storage
            $uploadResult = $storageApi->Upload($clientID, $name, 'uploaded', "", $fs);

            //###Check if file uploaded successfully
            if ($uploadResult->status == "Ok") {
                //Get file GUID
                $fileGuId = $uploadResult->result->guid;
                $fileId = "";

                //If it isn't uploaded throw exception to template
            } else {
                throw new Exception($uploadResult->error_message);
            }
        }
        //Check is user choose file GUID
        if ($fileId != "") {
            //Get entered by user file GUID
            $fileGuId = $fileId;
        }
        F3::set('fileId', $fileGuId);
         //Create job info object
                $jobInfo = new JobInfo();
                $jobInfo->actions = "8193";
                $jobInfo->out_formats = array('doc');

                //Create new job
                $createJob = $asyncApi->CreateJob($clientID, $jobInfo);

                if ($createJob->status == "Ok") {
                    try {
                        //Add document to job
                            $addJobDocument = $asyncApi->AddJobDocument($clientID, $createJob->result->job_id, $fileGuId, false);
                            if ($addJobDocument->status != "Ok") {
                                throw new Exception($addJobDocument->error_message);
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
                                        $iframe = 'https://apps.groupdocs.com/document-viewer/embed/' . $getJobDocument->result->inputs[0]->outputs[0]->guid;
                                        //iframe to dev server
                                    } elseif ($basePath == "https://dev-api.groupdocs.com/v2.0") {
                                        $iframe = 'https://dev-apps.groupdocs.com/document-viewer/embed/' . $getJobDocument->result->inputs[0]->outputs[0]->guid;
                                        //iframe to test server
                                    } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                                        $iframe = 'https://stage-apps-groupdocs.dynabic.com/document-viewer/embed/' . $getJobDocument->result->inputs[0]->outputs[0]->guid;
                                    } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                                        $iframe = 'https://relatime-apps.groupdocs.com/document-viewer/embed/' . $getJobDocument->result->inputs[0]->outputs[0]->guid;
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
    }
} catch (Exception $e) {
    $error = 'ERROR: ' . $e->getMessage() . "\n";
    F3::set('error', $error);
}
//Process template
echo Template::serve('sample43.htm');
