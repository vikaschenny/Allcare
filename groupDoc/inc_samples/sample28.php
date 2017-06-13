<?php

//###This sample will show how to list all annotations from document
//### Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
F3::set('fileId', '');

$clientId = F3::get('POST["clientId"]');
$privateKey = F3::get('POST["privateKey"]');
$fileId = F3::get('POST["fileId"]');

try {
    if (empty($clientId) || empty($privateKey) || empty($fileId)) {
        throw new Exception('Please enter all required parameters');
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
        $ant = new AntApi($apiClient);
        if ($basePath == "") {
            //If base base is empty seting base path to prod server
            $basePath = 'https://api.groupdocs.com/v2.0';
        }
        //Set base path
        $ant->setBasePath($basePath);
        # Make a request to Annotation API using clientId and fileId
        $list = $ant->ListAnnotations($clientId, $fileId);

        $message = "";
        // Check the result of the request
        if ($list->status == "Ok") {
            if (!empty($list->result->annotations)) {
                for ($i = 0; $i < count($list->result->annotations); $i++) {
                    $del = $ant->DeleteAnnotation($clientId, $list->result->annotations[$i]->guid);
                    if ($del->status == "Ok") {
                        $message = '<span style="color: green">All annotation were deleted successfully</span>';
                        //### If request was successfull
                        //Generation of iframe URL using $pageImage->result->guid
                        //iframe to prodaction server
                        if ($basePath == "https://api.groupdocs.com/v2.0") {
                            $iframe = 'https://apps.groupdocs.com/document-viewer/embed/' . $fileId;
                            //iframe to dev server
                        } elseif ($basePath == "https://dev-api.groupdocs.com/v2.0") {
                            $iframe = 'https://dev-apps.groupdocs.com/document-viewer/embed/' . $fileId;
                            //iframe to test server
                        } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                            $iframe = 'https://stage-apps-groupdocs.dynabic.com/document-viewer/embed/' . $fileId;
                        } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                            $iframe = 'http://realtime-apps.groupdocs.com/document-viewer/embed/' . $fileId;
                        }
                        $iframe = $signer->signUrl($iframe);
                        F3::set("message", $message);
                        F3::set("url", $iframe);
                    } else {
                        throw new Exception($del->error_message);
                    }
                }
            } else {
                throw new Exception('<span style="color: red">There are no annotations</span>');
            }
        } else {
            throw new Exception($list->error_message);
        }
    }
} catch (Exception $e) {
    $error = 'ERROR: ' . $e->getMessage() . "\n";
    F3::set('error', $error);
}

// Process template
echo Template::serve('sample28.htm');
