<?php

//###This sample will show how to download document with annotations using PHP SDK
// Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
F3::set('fileId', '');
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$fileId = F3::get('POST["fileId"]');
if (empty($clientId) || empty($privateKey) || empty($fileId)) {
    $error = 'Please enter all required parameters';
    F3::set('error', $error);
} else {
    //Get base path
    $basePath = F3::get('POST["basePath"]');
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    F3::set('fileId', $fileId);

    #### Create Signer, ApiClient and Annotation Api objects
    # Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    # Create apiClient object
    $apiClient = new ApiClient($signer);
    # Create Annotation object
    $antApi = new AntApi($apiClient);
    //Create AsyncApi object
    $asyncApi = new AsyncApi($apiClient);
    //Create SharedApi object
    $storageApi = new StorageApi($apiClient);
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //Set base path
    $antApi->setBasePath($basePath);
    $asyncApi->setBasePath($basePath);
    $storageApi->setBasePath($basePath);
    # Make a request to Annotation API using clientId and fileId
    try {
        $list = $antApi->ListAnnotations($clientId, $fileId);
        if ($list->status == "Ok") {
            // Check the result of the request
            if (isset($list->result) and !empty($list->result->annotations) ) {
                //Create job info object
                $jobInfo = new JobInfo();
                $jobInfo->actions = "512";
                $jobInfo->out_formats = array("pdf");

                //Create new job
                $createJob = $asyncApi->CreateJob($clientId, $jobInfo);

                if ($createJob->status == "Ok") {
                    try {
                        //Add document to job
                        $addJobDocument = $asyncApi->AddJobDocument($clientId, $createJob->result->job_id, $fileId, false);
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
                        $updateJob = $asyncApi->UpdateJob($clientId, $createJob->result->job_id, $jobInfo);
                        if ($updateJob->status == "Ok") {
                            try {
                                //Delay for server proccesing
                                sleep(8);
                                //Get result document guid from job
                                $getJobDocument = $asyncApi->GetJobDocuments($clientId, $createJob->result->job_id);
                                if ($getJobDocument->status == "Ok") {
                                    // Get guid output file
                                    $fileGuid = $getJobDocument->result->inputs[0]->outputs[0]->guid;
                                    // Get name output file
                                    $fileName = $getJobDocument->result->inputs[0]->outputs[0]->document_path;
                                    //Generate iframe url
                                    if ($basePath == "https://api.groupdocs.com/v2.0") {
                                        $iframe = 'https://apps.groupdocs.com/document-annotation/embed/' . $getJobDocument->result->inputs[0]->guid;
                                        //iframe to dev server
                                    } elseif ($basePath == "https://dev-api.groupdocs.com/v2.0") {
                                        $iframe = 'https://dev-apps.groupdocs.com/document-annotation/embed/' . $getJobDocument->result->inputs[0]->guid;
                                        //iframe to test server
                                    } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                                        $iframe = 'https://stage-apps-groupdocs.dynabic.com/document-annotation/embed/' . $getJobDocument->result->inputs[0]->guid;
                                    } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                                        $iframe = 'https://relatime-apps.groupdocs.com/document-annotation/embed/' . $getJobDocument->result->inputs[0]->guid;
                                    }
                                    $iframe = $signer->signUrl($iframe);
                                    F3::set('url', $iframe);
                                    //Local path to the downloads folder
                                    $downloadFolder = dirname(__FILE__) . '/../downloads';
                                    //Check is folder exist
                                    if (!file_exists($downloadFolder)) {
                                        //If folder don't exist create it
                                        mkdir($downloadFolder);
                                    }
                                    //Obtaining file stream of downloading file and definition of folder where to download file
                                    $outFileStream = FileStream::fromHttp($downloadFolder, $fileName);

                                    try {
                                        $file = $storageApi->GetFile($clientId, $fileGuid, $outFileStream);

                                        if ($file->downloadDirectory != "" && isset($file)) {
                                            F3::set("message", "<span style=\"color:green\">File with annotations was downloaded to server's local folder. You can check them <a href=\"/downloads/{$fileName}\" type=\"application/file\" download >here</a></span>");
                                        } else {
                                            throw new Exception("Something wrong with entered data");
                                        }
                                    } catch (Exception $e) {
                                        $error = 'ERROR: ' . $e->getMessage() . "\n";
                                        F3::set('error', $error);
                                    }

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

            } else {
                throw new Exception("Your file has no annotations");
            }

        } else {
            throw new Exception($list->error_message);
        }
    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
}
// Process template
echo Template::serve('sample42.htm');