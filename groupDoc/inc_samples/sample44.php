<?php

//###<i>This sample will show how to assemble document and add multiple Signatures and Signers to a document</i>
//Set variables and get POST data
F3::set('userId',F3::get('POST["clientId"]'));
F3::set('privateKey', F3::get('POST["privateKey"]'));
F3::set('fileId', '');
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$firstEmail = F3::get('POST["firstEmail"]');
$firstName = F3::get('POST["firstName"]');
$lastName = F3::get('POST["lastName"]');
$secondEmail = F3::get('POST["secondEmail"]');
//Set second signer name. Can be obtained in the same manner as first signer name.
$secondName = $firstName + "2";
$gender = F3::get('POST["gender"]');
$uploadedGuid = "";
$iframe = "";
$iframe2 = "";
$basePath = F3::get('POST["basePath"]');

//Check if all requered data is entered
if (empty($clientId) || empty($privateKey) || empty($firstEmail) || empty($firstName) || empty($secondEmail)) {
    $error = 'Please enter all required parameters';
    F3::set('error', $error);
} else {
    if (empty($lastName)) {
        $lastName = "Empty Last name";
    }
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //###Create GroupDocs Api objects
    //Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Create Doc Api object
    $docApi = new DocApi($apiClient);
    //Create Storage Api object
    $storageApi = new StorageApi($apiClient);
    //Create AsyncApi object
    $AsyncApi = new AsyncApi($apiClient);
    //Create MergeApi object
    $mergApi = new MergeApi($apiClient);
    //Create SignatureApi object
    $signature = new SignatureApi($apiClient);
    //Set base path for all Apis
    $docApi->setBasePath($basePath);
    $storageApi->setBasePath($basePath);
    $AsyncApi->setBasePath($basePath);
    $mergApi->setBasePath($basePath);
    try {
        //File for upload
        $file = $_FILES['file'];
        //Temp name of the file
        $tmpName = $file['tmp_name'];
        //Original name of the file
        $name = $file['name'];
        //Create file stream
        $fs = FileStream::fromFile($tmpName);
        //###Make a request to Storage API using clientId
        //Upload file to current user storage
        $uploadResult = $storageApi->Upload($clientId, $name, 'uploaded', "", $fs);
        //###Check if file uploaded successfully
        if ($uploadResult->status == "Ok") {
            //Get file GUID
            $fileGuId = $uploadResult->result->guid;
            //Create array with data for metging
            $enteredData = array("gender" => $gender, "name" => $firstName);
            //Create new Datasource object
            $dataSource = new Datasource();
            //Create empty array
            $array = array();
            //Loop for fields creataion
            foreach ($enteredData as $fieldName => $data) {
                //Create new DatasourceField object
                $field = new DatasourceField();
                //Set DatasourceFiled data
                $field->name = $fieldName;
                $field->type = "text";
                $field->values = array($data);
                //Push DatasourceField to array
                array_push($array, $field);
            }
            //Set fields array to the Datasourc
            $dataSource->fields = $array;
            //Add Datasource to GroupDocs
            $addDataSource = $mergApi->AddDataSource($clientId, $dataSource);
            //Check status
            if ($addDataSource->status == "Ok") {
                //If status ok merge Datasource to new pdf file
                $job = $mergApi->MergeDatasource($clientId, $fileGuId, $addDataSource->result->datasource_id, "pdf", null);
                //Check status
                if ($job->status == "Ok") {
                    //### Check job status
                    for ($n = 0; $n <= 5; $n++) {
                        //Delay necessary that the inquiry would manage to be processed
                        sleep(2);
                        //Make request to api for get document info by job id
                        $jobInfo = $AsyncApi->GetJobDocuments($clientId, $job->result->job_id);
                        //Check job status, if status is Completed or Archived exit from cycle
                        if ($jobInfo->result->job_status == "Completed" || $jobInfo->result->job_status == "Archived") {
                            break;
                            //If job status Postponed throw exception with error
                        } elseif ($jobInfo->result->job_status == "Postponed") {
                            throw new Exception("Job is failed");
                        }
                    }
                    if ($jobInfo->result->job_status == "Pending") {
                        throw new Exception("Job is pending");
                    }
                    //Get file guid
                    $guid = $jobInfo->result->inputs[0]->outputs[0]->guid;
                    //Get file name
                    $name = $jobInfo->result->inputs[0]->outputs[0]->name;
                    //Create envilope using user id and entered by user name
                    $envelop = $signature->CreateSignatureEnvelope($clientId, $name, null, null, $guid);
                    if ($envelop->status == "Ok") {
                        try {
                            //Get role list for current user
                            $recipient = $signature->GetRolesList($clientId);
                            if ($recipient->status == "Ok") {
                                //Get id of role which can sign
                                for ($i = 0; $i < count($recipient->result->roles); $i++) {
                                    if ($recipient->result->roles[$i]->name == "Signer") {
                                        $roleId = $recipient->result->roles[$i]->id;
                                    }
                                }
                                try {
                                    //Add recipient to envelope
                                    $addRecipient = $signature->AddSignatureEnvelopeRecipient($clientId, $envelop->result->envelope->id, $firstEmail, $firstName, $lastName, $roleId, null);
                                    if ($addRecipient->status == "Ok") {
                                        //Add second recipient to envelope
                                        $addSecondRecipient = $signature->AddSignatureEnvelopeRecipient($clientId, $envelop->result->envelope->id, $secondEmail, $secondName, $lastName . "2", $roleId, null);
                                        if ($addSecondRecipient->status == "Ok") {
                                            try {
                                                //Get document from envelop
                                                $getDocuments = $signature->GetSignatureEnvelopeDocuments($clientId, $envelop->result->envelope->id);
                                                if ($getDocuments->status == "Ok") {
                                                    try {
                                                        //Create signature field
                                                        $signFieldEnvelopSettings = new SignatureEnvelopeFieldSettingsInfo();
                                                        $signFieldEnvelopSettings->locationX = "0.15";
                                                        $signFieldEnvelopSettings->locationY = "0.23";
                                                        $signFieldEnvelopSettings->locationWidth = "150";
                                                        $signFieldEnvelopSettings->locationHeight = "50";
                                                        $signFieldEnvelopSettings->name = "test" . rand(0, 500);
                                                        $signFieldEnvelopSettings->forceNewField = true;
                                                        $signFieldEnvelopSettings->page = "1";
                                                        //Add signature field to document
                                                        $addEnvelopField = $signature->AddSignatureEnvelopeField($clientId, $envelop->result->envelope->id, $getDocuments->result->documents[0]->documentId, $addRecipient->result->recipient->id, "0545e589fb3e27c9bb7a1f59d0e3fcb9", $signFieldEnvelopSettings);
                                                        if ($addEnvelopField->status == "Ok") {
                                                            //Update signature field settings for second signature field
                                                            $signFieldEnvelopSettings->locationX = "0.35";
                                                            $signFieldEnvelopSettings->locationY = "0.23";
                                                            $signFieldEnvelopSettings->locationWidth = "150";
                                                            $signFieldEnvelopSettings->locationHeight = "50";
                                                            $signFieldEnvelopSettings->name = "test" . rand(0, 500);
                                                            $signFieldEnvelopSettings->forceNewField = true;
                                                            $signFieldEnvelopSettings->page = "1";
                                                            //Add second signature field to document
                                                            $addEnvelopSecondField = $signature->AddSignatureEnvelopeField($clientId, $envelop->result->envelope->id, $getDocuments->result->documents[0]->documentId, $addSecondRecipient->result->recipient->id, "0545e589fb3e27c9bb7a1f59d0e3fcb9", $signFieldEnvelopSettings);
                                                            if ($addEnvelopSecondField->status == "Ok") {
                                                                try {
                                                                    //Create WebHook object (URL which will be trigered by callback)
                                                                    $webHook = new WebhookInfo();
                                                                    $webHook->callbackUrl = "";
                                                                    //Send envelop for signing
                                                                    $send = $signature->SignatureEnvelopeSend($clientId, $envelop->result->envelope->id, $webHook);
                                                                    if ($send->status == "Ok") {
                                                                        //Create URL for iframe
                                                                        $iframe = "https://apps.groupdocs.com/signature2/signembed/" . $envelop->result->envelope->id . '/' . $addRecipient->result->recipient->id;
                                                                        //Sign URL
                                                                        $iframe = $signer->signUrl($iframe);
                                                                        F3::set('url1', $iframe);
                                                                        //Create URL for second iframe
                                                                        $iframe2 = "https://apps.groupdocs.com/signature2/signembed/" . $envelop->result->envelope->id . '/' . $addSecondRecipient->result->recipient->id;
                                                                        //Sign URL
                                                                        $iframe2 = $signer->signUrl($iframe2);
                                                                        F3::set('url2', $iframe2);
                                                                    } else {
                                                                        throw new Exception($send->error_message);
                                                                    }
                                                                } catch (Exception $e) {
                                                                    $error = 'ERROR: ' . $e->getMessage() . "\n";
                                                                    F3::set('error', $error);
                                                                }
                                                            } else {
                                                                throw new Exception($addEnvelopSecondFields->error_message);
                                                            }
                                                        } else {
                                                            throw new Exception($addEnvelopField->error_message);
                                                        }
                                                    } catch (Exception $e) {
                                                        $error = 'ERROR: ' . $e->getMessage() . "\n";
                                                        F3::set('error', $error);
                                                    }
                                                } else {
                                                    throw new Exception($getDocuments->error_message);
                                                }
                                            } catch (Exception $e) {
                                                $error = 'ERROR: ' . $e->getMessage() . "\n";
                                                F3::set('error', $error);
                                            }
                                        } else {
                                            throw new Exception($addSecondRecipient->error_message);
                                        }
                                    } else {
                                        throw new Exception($addRecipient->error_message);
                                    }
                                } catch (Exception $e) {
                                    $error = 'ERROR: ' . $e->getMessage() . "\n";
                                    F3::set('error', $error);
                                }
                            } else {
                                throw new Exception($recipient->error_message);
                            }
                        } catch (Exception $e) {
                            $error = 'ERROR: ' . $e->getMessage() . "\n";
                            F3::set('error', $error);
                        }
                    } else {
                        throw new Exception($envelop->error_message);
                    }
                } else {
                    throw new Exception($job->error_message);
                }
            } else {
                throw new Exception($addDataSource->error_message);
            }
            //If it isn't uploaded throw exception to template
        } else {
            throw new Exception($uploadResult->error_message);
        }
    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
}

//Process template
echo Template::serve('sample44.htm');
