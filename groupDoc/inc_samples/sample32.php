<?php

//<i>This sample will show how to create signature form, publish it and configure notification when it was signed using PHP SDK</i>
//###Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$basePath = F3::get('POST["basePath"]');
$callbackUrl = F3::get('POST["callbackUrl"]');
$templateGuid = F3::get('POST["templateGuid"]');
$formGuid = F3::get("POST['formGuid']");
$email = F3::get('POST["email"]');
$error = null;
if (empty($clientId) || empty($privateKey)) {
    $error = 'Please enter all parameters';
    F3::set('error', $error);
} else {
    //path to settings file - temporary save userId and apiKey like to property file
    $infoFile = fopen(__DIR__ . '/../user_info.txt', 'w');
    fwrite($infoFile, $clientId . "\r\n" . $privateKey . "\r\n" . $email);
    fclose($infoFile);
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    F3::set('email', $email);
    //###Create Signer, ApiClient and Storage Api objects
    //Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    //Create apiClient object
    $apiClient = new APIClient($signer);
    $signatureApi = new SignatureApi($apiClient);
    //Set url to choose whot server to use
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //Set base path
    $signatureApi->setBasePath($basePath);
    if (empty($callbackUrl)) {
        $callbackUrl = "";
    }
    //Set variables for template       
    F3::set("callbackUrl", $callbackUrl);
    //Create WebHook object
    $webHook = new WebhookInfo();
    //Set callbackUrl url of webhook which will be triggered when form is signed.
    $webHook->callbackUrl = $callbackUrl;
    if (!empty($formGuid)) {
        F3::set("formGuid", $formGuid);
        try {
            $postForm = $signatureApi->PublishSignatureForm($clientId, $formGuid, $webHook);
            //Check status
            if ($postForm->status == "Ok") {
                $result = "Form is published successfully";
                F3::set("message", $result);
                //Generate iframe url
                if ($basePath == "https://api.groupdocs.com/v2.0") {
                    $iframe = 'https://apps.groupdocs.com/signature2/forms/signembed/' . $formGuid;
                    //iframe to dev server
                } elseif ($basePath == "https://dev-api.groupdocs.com/v2.0") {
                    $iframe = 'https://dev-apps.groupdocs.com/signature2/forms/signembed/' . $formGuid;
                    //iframe to test server
                } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                    $iframe = 'https://stage-apps-groupdocs.dynabic.com/signature2/forms/signembed/' . $formGuid;
                } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                    $iframe = 'https://relatime-apps.groupdocs.com/signature2/forms/signembed/' . $formGuid;
                }
                $iframe = $signer->signUrl($iframe);
                F3::set('url', $iframe);
            } else {
                throw new Exception($postForm->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    } else {
        //Create Signature form settings object
        $formSettings = new SignatureFormSettingsInfo();
        //To send notification email to owner when form is signed set notifyOwnerOnSign property to true
        $formSettings->notifyOwnerOnSign = true;
        //Generate rendon form name
        $formName = "test form" . rand(0, 500);
        try {
            //Create signature form
            $createForm = $signatureApi->CreateSignatureForm($clientId, $formName, $templateGuid, null, null, $formSettings);
            //Check status
            if ($createForm->status == "Ok") {
                //Set variable for template
                F3::set("tempalteGuid", $templateGuid);
                try {
                    //Publish form
                    $postForm = $signatureApi->PublishSignatureForm($clientId, $createForm->result->form->id, $webHook);
                    //Check status
                    if ($postForm->status == "Ok") {
                        $result = "Form is posted successfully";
                        F3::set("message", $result);
                        //Generate iframe url
                        if ($basePath == "https://api.groupdocs.com/v2.0") {
                            $iframe = 'https://apps.groupdocs.com/signature2/forms/signembed/' . $createForm->result->form->id;
                            //iframe to dev server
                        } elseif ($basePath == "https://dev-api.groupdocs.com/v2.0") {
                            $iframe = 'https://dev-apps.groupdocs.com/signature2/forms/signembed/' . $createForm->result->form->id;
                            //iframe to test server
                        } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                            $iframe = 'https://stage-apps-groupdocs.dynabic.com/signature2/forms/signembed/' . $createForm->result->form->id;
                        } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                            $iframe = 'https://relatime-apps.groupdocs.com/signature2/forms/signembed/' . $createForm->result->form->id;
                        }
                        F3::set('url', $iframe);
                        F3::set("formGuid", $createForm->result->form->id);
                    } else {
                        throw new Exception($postForm->error_message);
                    }
                } catch (Exception $e) {
                    $error = 'ERROR: ' . $e->getMessage() . "\n";
                    F3::set('error', $error);
                }
            } else {
                throw new Exception($createForm->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
}
//Process template
echo Template::serve('sample32.htm');