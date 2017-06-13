<?php

//<i>This sample will show how to set callback for signature form and re-direct when it was signed using PHP SDK</i>
//###Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$basePath = F3::get('POST["basePath"]');
$callbackUrl = F3::get('POST["callbackUrl"]');
$formGuid = F3::get("POST['formGuid']");
$error = null;
//Check if all requered data is entered
if (empty($clientId) || empty($privateKey) || empty($formGuid)) {
    $error = 'Please enter all parameters';
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
    //Set variables for template
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    //###Create Signer, ApiClient and Storage Api objects
    //Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    //Create apiClient object
    $apiClient = new APIClient($signer);
    //Create Signature API object
    $signatureApi = new SignatureApi($apiClient);
    //Set url to choose whot server to use
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //Set base path
    $signatureApi->setBasePath($basePath);
    //Check if callback URL is empty
    if (empty($callbackUrl)) {
        $callbackUrl = "";
    }
    //Set variables for template       
    F3::set("callbackUrl", $callbackUrl);
    //Create WebHook object
    $webHook = new WebhookInfo();
    //Set callbackUrl url of webhook which will be triggered when form is signed.
    $webHook->callbackUrl = $callbackUrl;
    F3::set("formGuid", $formGuid);
    try {
        //Create signature form (it will be copy of original form if formGUID parameter is set)
        $createForm = $signatureApi->CreateSignatureForm($clientId, "sampleForm" . rand(0, 500), null, null, $formGuid);
        if ($createForm->status == "Ok") {
            //Published new form that users can sign it and set callback URL to it
            $postForm = $signatureApi->PublishSignatureForm($clientId, $createForm->result->form->id, $webHook);
            //Check status
            if ($postForm->status == "Ok") {
                $result = "Form is published successfully";
                F3::set("message", $result);
                //Generate iframe url
                if ($basePath == "https://api.groupdocs.com/v2.0") {
                    $iframe = 'https://apps.groupdocs.com/signature2/forms/signembed/' . $createForm->result->form->id;
                    //iframe to dev server
                } elseif ($basePath == "https://dev-api-groupdocs.dynabic.com/v2.0") {
                    $iframe = 'https://dev-apps-groupdocs.dynabic.com/signature2/forms/signembed/' . $createForm->result->form->id;
                    //iframe to test server
                } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                    $iframe = 'https://stage-apps-groupdocs.dynabic.com/signature2/forms/signembed/' . $createForm->result->form->id;
                } elseif ($basePath == "http://realtime-api-groupdocs.dynabic.com") {
                    $iframe = 'https://relatime-apps-groupdocs.dynabic.com/signature2/forms/signembed/' . $createForm->result->form->id;
                }
                $iframe = $signer->signUrl($iframe);
                F3::set('url', $iframe);
            } else {
                throw new Exception($postForm->error_message);
            }
        } else {
            throw new Exception($createForm->error_message);
        }
    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
}
//Process template
echo Template::serve('sample40.htm');
