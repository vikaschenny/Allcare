<?php

//### This sample will show how programmatically create and post an annotation into document. How to delete the annotation
// Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
F3::set('fileId', '');
$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');

if (empty($clientId) || empty($privateKey)) {
    $error = 'Please enter all required parameters';
    F3::set('error', $error);
} else {
    //Get base path
    $basePath = F3::get('POST["basePath"]');
    //Get entered by user data
    $fileGuId = F3::get('POST["fileId"]');
    $url = F3::get('POST["url"]');
    $file = $_FILES['file'];
    //### Create Signer, ApiClient and Annotation Api objects
    // Create signer object
    $signer = new GroupDocsRequestSigner($privateKey);
    // Create apiClient object
    $apiClient = new ApiClient($signer);
    // Create Annotation object
    $antApi = new AntApi($apiClient);
    //Create Storage Api object
    $storageApi = new StorageApi($apiClient);
    if ($basePath == "") {
        //If base base is empty seting base path to prod server
        $basePath = 'https://api.groupdocs.com/v2.0';
    }
    //Set base path
    $antApi->setBasePath($basePath);
    $storageApi->setBasePath($basePath);
    //Check if user choose upload file from URL
    if ($url != "") {
        $fileGuId = "";
        //Upload file from URL
        $uploadResult = $storageApi->UploadWeb($clientId, $url);
        //Check is file uploaded
        try {
            if ($uploadResult->status == "Ok") {
                //Get file GUID
                $fileId = $uploadResult->result->guid;
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
        $fileGuId = "";
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
                $fileId = $uploadResult->result->guid;
                //If it isn't uploaded throw exception to template
            } else {
                throw new Exception($uploadResult->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
    if ($fileGuId != "") {
        $fileId = $fileGuId;
    }
    $annotationType = F3::get('POST["annotationType"]');
    $replyText = F3::get('POST["text"]');

    // Delete annotation if Delete Button clicked
    if (F3::get('POST["deleteAnnotation"]') == "1") {
        $antApi->DeleteAnnotation($clientId, F3::get('POST["annotationId"]'));
    }
    // Required parameters
    $allParams = array('annotationType', 'boxX', 'boxY', 'text');
    // Added required parameters depends on  annotation type ['type' or 'area']
    if ($annotationType == "text")
        $allParams = array_merge($allParams, array('boxWidth', 'boxHeight', 'annotationPositionX',
            'annotationPositionY', 'rangePosition', 'rangeLength'));
    elseif ($annotationType == "area")
        $allParams = array_merge($allParams, array('boxWidth', 'boxHeight'));
    // Checking required parameters
    foreach ($allParams as $param) {
        $needParam = F3::get('POST["' . $param . '"]');
        if (!isset($needParam) or empty($needParam)) {
            throw new Exception('Please enter all required parameters');
        }
    }
    $types = array('text' => "0", "area" => "1", "point" => "2");
    // reply text
    $reply = new AnnotationReplyInfo();
    $reply->text = $replyText;
    // Annotation Info
    $annotationInfo = new AnnotationInfo();
    $annotationInfo->replies = array($reply);
    $annotationInfo->type = $types[$annotationType];
    // construct annotation info depends on annotation type
    // text annotation
    if ($annotationType == "text") {
        $range = new Range();
        $range->position = F3::get('POST["rangePosition"]');
        $range->length = F3::get('POST["rangeLength"]');
        $box = new Rectangle();
        $box->x = F3::get('POST["boxX"]');
        $box->y = F3::get('POST["boxY"]');
        $box->width = F3::get('POST["boxWidth"]');
        $box->height = F3::get('POST["boxHeight"]');
        $point = new Point();
        $point->x = F3::get('POST["annotationPositionX"]');
        $point->y = F3::get('POST["annotationPositionY"]');
        $annotationInfo->box = $box;
        $annotationInfo->annotationPosition = $point;
        $annotationInfo->range = $range;
        // area annotation
    } elseif ($annotationType == "area") {
        $box = new Rectangle();
        $box->x = F3::get('POST["boxX"]');
        $box->y = F3::get('POST["boxY"]');
        $box->width = F3::get('POST["boxWidth"]');
        $box->height = F3::get('POST["boxHeight"]');
        $point = new Point();
        $point->x = 0;
        $point->y = 0;
        $annotationInfo->box = $box;
        $annotationInfo->annotationPosition = $point;
        // point annotation
    } elseif ($annotationType == "point") {
        $box = new Rectangle();
        $box->x = F3::get('POST["boxX"]');
        $box->y = F3::get('POST["boxY"]');
        $box->width = 0;
        $box->height = 0;
        $point = new Point();
        $point->x = 0;
        $point->y = 0;
        $annotationInfo->box = $box;
        $annotationInfo->annotationPosition = $point;
    }
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    F3::set('fileId', $fileId);
    try {
        $createAnnotation = $antApi->CreateAnnotation($clientId, $fileId, $annotationInfo);
        if ($createAnnotation->status == "Ok") {
            if ($createAnnotation->result) {
                //Generation of iframe URL using fileGuId
                if ($basePath == "https://api.groupdocs.com/v2.0") {
                    $iframe = 'http://apps.groupdocs.com/document-annotation2/embed/' .
                            $createAnnotation->result->documentGuid . '?frameborder="0" width="720" height="600"';
                    //iframe to dev server
                } elseif ($basePath == "https://dev-api.groupdocs.com/v2.0") {
                    $iframe = 'http://dev-apps.groupdocs.com/document-annotation2/embed/' .
                            $createAnnotation->result->documentGuid . '?frameborder="0" width="720" height="600"';
                    //iframe to test server
                } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                            $iframe = 'https://stage-apps-groupdocs.dynabic.com/document-annotation2/Embed/' .
                            $createAnnotation->result->documentGuid . '?frameborder="0" width="720" height="600"';
                    //Iframe to realtime server
                } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                    $iframe = 'http://realtime-apps.groupdocs.com/document-annotation2/embed/' .
                            $createAnnotation->result->documentGuid . '?frameborder="0" width="720" height="600"';
                }
                $iframe = $signer->signUrl($iframe);
                F3::set('annotationId', $createAnnotation->result->annotationGuid);
                F3::set('annotationType', $annotationType);
                F3::set('annotationText', $replyText);
                F3::set('url', $iframe);
            }
        } else {
            throw new Exception($createAnnotation->error_message);
        }
    } catch (Exception $e) {
        $error = 'ERROR: ' . $e->getMessage() . "\n";
        F3::set('error', $error);
    }
}
// Process template
echo Template::serve('sample11.htm');