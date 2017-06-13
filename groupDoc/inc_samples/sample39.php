<?php

//###<i>This sample will show how to add a Signature to a document and redirect after signing with GroupDocs widget</i>
//Get all data from ajax
$postdata = file_get_contents("php://input");
//### Check if user use Widget for signing
if (!empty($postdata)) {
    $error = null;
    //Decode ajax data
    $json_post_data = json_decode($postdata, true);
    //Get Client ID
    $clientId = $json_post_data['userId'];
    //Get Private Key
    $privateKey = $json_post_data['privateKey'];
    //Get document for sign
    $documents = $json_post_data['documents'];
    //Get signature file
    $signers = $json_post_data['signers'];
    //Inable signature parameter for the signature object
    for ($i = 0; $i < count($signers); $i++) {
        $signers[$i]['placeSignatureOn'] = '';
    }
    //Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    //Create Api Client object
    $apiClient = new APIClient($signer);
    //Create Signature Api object
    $signatureApi = new SignatureApi($apiClient);
    //Create object of sign ssettings
    $settings = new SignatureSignDocumentSettingsInfo();
    //Set document for signing
    $settings->documents = $documents;
    //Set signature
    $settings->signers = $signers;
    //Make request to sign documnet
    $signDocument = $signatureApi->SignDocument($clientId, $settings);
    //Check request status
    if ($signDocument->status == "Ok") {
        //Get signed document GUID
        for ($i = 0; $i < 5; $i++) {
            //Check status of documnet is it signed
            $getSignDocument = $signatureApi->GetSignDocumentStatus($clientId, $signDocument->result->jobId);
            if ($getSignDocument->status == "Ok") {
                if ($getSignDocument->result->documents[0]->status == "Completed") {
                    //Get file GUID
                    $guid = $getSignDocument->result->documents[0]->documentId;
                    break;
                } else {
                    //Wait while server processed data
                    sleep(3);
                }
            } else {
                $error = $getSignDocument->error_message;
            }
        }
    } else {
        $error = $signDocument->error_message;
    }
    //Create array with result data
    $result = array('guid' => $guid,
        'clientId' => $clientId,
        'privateKey' => $privateKey,
        'error' => $error);
    //Decode array to json and return json string to ajax request
    echo json_encode($result);
//### Check if user not use Widget for signing
} elseif (!empty($_POST["clientId"])) {
	//Get all entered data
    $clientId = F3::get('POST["clientId"]');
    $privateKey = F3::get('POST["privateKey"]');
    $email = F3::get('POST["email"]');
    $signName = F3::get('POST["name"]');
    $lastName = F3::get('POST["lastName"]');
    $callbackUrl = F3::get('POST["callbackUrl"]');
    F3::set('email', $email);
    F3::set('name', $signName);
    F3::set('lastName', $lastName);
    //Check clientId and privateKey
    if (empty($clientId) || empty($privateKey)) {
        $error = 'Please enter all required parameters';
        F3::set('error', $error);
    } else {
        //path to settings file - temporary save userId and apiKey like to property file
        $infoFile = fopen(__DIR__ . '/../user_info.txt', 'w');
        fwrite($infoFile, $clientId . "\r\n" . $privateKey);
        fclose($infoFile);
        //Delete temporary file which content callback data
        if (file_exists(__DIR__ . '/../callback_info.txt')) {
            unlink(__DIR__ . '/../callback_info.txt');
        }
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
        //Get entered by user data
        $name = "";
        $fileGuId = "";
        //Get file for upload
        $file = $_FILES['file'];
        if ($file["name"] != "") {
            //Get uploaded file
            $uploadedFile = $_FILES['file'];
            //###Check uploaded file
            if (null === $uploadedFile) {
                return new RedirectResponse("/sample39");
            }
            //Temp name of the file
            $tmpName = $uploadedFile['tmp_name'];
            //Original name of the file
            $name = $uploadedFile['name'];
            //Creat file stream
            $fs = FileStream::fromFile($tmpName);
            //###Make a request to Storage API using clientId
            //Upload file to current user storage
            try {
                $uploadResult = $storageApi->Upload($clientID, $name, 'uploaded', "", $fs);
                //###Check if file uploaded successfully
                if ($uploadResult->status == "Ok") {
                    //Get file GUID
                    $fileGuId = $uploadResult->result->guid;
                    //Get file name
                    $name = $uploadResult->result->adj_name;
                    //Create SignatureApi object
                    $signature = new SignatureApi($apiClient);
                    try {
                        //Create envilope using user id and entered by user name
                        $envelop = $signature->CreateSignatureEnvelope($clientID, $name);
                        if ($envelop->status == "Ok") {
                            sleep(5);
                            //Add uploaded document to envelope
                            $addDocument = $signature->AddSignatureEnvelopeDocument($clientID, $envelop->result->envelope->id, $fileGuId, null, true);
                            try {
                                if ($addDocument->status == "Ok") {
                                    try {
                                        //Get role list for current user
                                        $recipient = $signature->GetRolesList($clientID);
                                        if ($recipient->status == "Ok") {
                                            //Get id of role which can sign
                                            for ($i = 0; $i < count($recipient->result->roles); $i++) {
                                                if ($recipient->result->roles[$i]->name == "Signer") {
                                                    $roleId = $recipient->result->roles[$i]->id;
                                                }
                                            }
                                            try {
                                                //Add recipient to envelope
                                                $addRecipient = $signature->AddSignatureEnvelopeRecipient($clientID, $envelop->result->envelope->id, $email, $signName, $lastName, $roleId, null);
                                                if ($addRecipient->status == "Ok") {
                                                    try {
                                                        //Ger recipient ID
                                                        $getRecipient = $signature->GetSignatureEnvelopeRecipients($clientID, $envelop->result->envelope->id);
                                                        if ($getRecipient->status == "Ok") {
                                                            $recipientId = $getRecipient->result->recipients[0]->id;
                                                            //Get Url for callbackUrl
                                                            F3::set("callbackUrl", $callbackUrl);
                                                            try {
                                                                //Get document from envelop
                                                                $getDocuments = $signature->GetSignatureEnvelopeDocuments($clientID, $envelop->result->envelope->id);
                                                                if ($getDocuments->status == "Ok") {
                                                                    try {
                                                                        //Create sognature field
                                                                        $signFieldEnvelopSettings = new SignatureEnvelopeFieldSettingsInfo();
                                                                        $signFieldEnvelopSettings->locationX = "0.15";
                                                                        $signFieldEnvelopSettings->locationY = "0.73";
                                                                        $signFieldEnvelopSettings->locationWidth = "150";
                                                                        $signFieldEnvelopSettings->locationHeight = "50";
                                                                        $signFieldEnvelopSettings->name = "test" . rand(0, 500);
                                                                        $signFieldEnvelopSettings->forceNewField = true;
                                                                        $signFieldEnvelopSettings->page = "1";
                                                                        //Add signature field to document
                                                                        $addEnvelopField = $signature->AddSignatureEnvelopeField($clientID, $envelop->result->envelope->id, $getDocuments->result->documents[0]->documentId, $recipientId, "0545e589fb3e27c9bb7a1f59d0e3fcb9", $signFieldEnvelopSettings);
                                                                        try {
                                                                            //Create WebHook object (URL which will be trigered by callback)
                                                                            $webHook = new WebhookInfo();
                                                                            if ($callbackUrl != "") {
                                                                                $webHook->callbackUrl = trim(strip_tags($callbackUrl));
                                                                            } else {
                                                                                $webHook->callbackUrl = "";
                                                                            }
                                                                            //Send envelop for signing
                                                                            $send = $signature->SignatureEnvelopeSend($clientID, $envelop->result->envelope->id, $webHook);
                                                                            if ($send->status == "Ok") {
                                                                                //Create URL for iframe
                                                                                $iframe = "https://apps.groupdocs.com/signature2/signembed/" . $envelop->result->envelope->id . '/' . $recipientId;
                                                                                //Sign URL
                                                                                $iframe = $signer->signUrl($iframe);
                                                                                F3::set('iframe', $iframe);
                                                                            } else {
                                                                                throw new Exception($send->error_message);
                                                                            }
                                                                        } catch (Exception $e) {
                                                                            $error = 'ERROR: ' . $e->getMessage() . "\n";
                                                                            F3::set('error', $error);
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
                                                            throw new Exception($getRecipient->error_message);
                                                        }
                                                    } catch (Exception $e) {
                                                        $error = 'ERROR: ' . $e->getMessage() . "\n";
                                                        F3::set('error', $error);
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
                                    throw new Exception($addDocument->error_message);
                                }
                            } catch (Exception $e) {
                                $error = 'ERROR: ' . $e->getMessage() . "\n";
                                F3::set('error', $error);
                            }
                        } else {
                            throw new Exception($envelop->error_message);
                        }
                    } catch (Exception $e) {
                        $error = 'ERROR: ' . $e->getMessage() . "\n";
                        F3::set('error', $error);
                    }
                } else {
                    throw new Exception($uploadResult->error_message);
                }
            } catch (Exception $e) {
                $error = 'ERROR: ' . $e->getMessage() . "\n";
                F3::set('error', $error);
            }
        }
    }
    //Process template
    F3::set('clientId', $clientId);
    F3::set('privateKey', $privateKey);
    echo Template::serve('sample39.htm');
} elseif (empty($postdata) && empty($_POST["clientId"])) {
    echo Template::serve('sample39.htm');
}