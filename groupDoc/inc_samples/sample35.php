<?php

//###<i>This sample will show how to create assembly from document and merge fields </i>
//Set variables and get POST data
F3::set('userId', '');
F3::set('privateKey', '');
F3::set('fileId', '');
F3::set('message', '');
F3::set('basePath', '');
//Check is it first form
if (!empty($_POST['guid'])) {
    //Get all Post data 
    $postData = F3::get('POST');
    $dataForDataSource = array();
    //Get data from post and create array with data for merge
    foreach ($postData as $parameterName => $parameterValue) {
        if ($parameterName == "clientId") {
            $clientId = $parameterValue;
        } elseif ($parameterName == "privateKey") {
            $privateKey = $parameterValue;
        } elseif ($parameterName == "basePath") {
            $basePath = $parameterValue;
        } elseif ($parameterName == "guid") {
            $fileGuId = $parameterValue;
        } else {
            $dataForDataSource[$parameterName] = $parameterValue;
        }
    }
    //###Check clientId and privateKey
    if (empty($clientId) || empty($privateKey)) {
        $error = 'Please enter all required parameters';
        F3::set('error', $error);
    } else {
        //Deleting of tags, slashes and  space from clientId and privateKey
        $clientId = strip_tags(stripslashes(trim($clientId))); //ClientId==UserId
        $apiKey = strip_tags(stripslashes(trim($privateKey))); //ApiKey==PrivateKey
        //###Create Signer, ApiClient and Storage Api objects
        //Create signer object
        $signer = new GroupDocsRequestSigner($apiKey);
        //Create apiClient object
        $apiClient = new APIClient($signer);
        //Create Storage Api object
        $signatureApi = new SignatureApi($apiClient);
        //Create Storage Api object
        $storageApi = new StorageApi($apiClient);
        //Create AsyncApi object
        $asyncApi = new AsyncApi($apiClient);
        $mergApi = new MergeApi($apiClient);
        $docApi = new DocApi($apiClient);
        //Check if user entered base path
        if ($basePath == "") {
            //If base base is empty seting base path to prod server
            $basePath = 'https://api.groupdocs.com/v2.0';
        }
        //Build propper basePath
        if (substr($basePath, -3) != "2.0") {
            if (substr($basePath, -1) != "/") {
                $basePath = $basePath . "/v2.0";
            } else {
                $basePath = $basePath . "v2.0";
            }
        }
        //Set basePath for all Api
        $storageApi->setBasePath($basePath);
        $asyncApi->setBasePath($basePath);
        $mergApi->setBasePath($basePath);
        $signatureApi->setBasePath($basePath);
        $docApi->setBasePath($basePath);
        try {
            //### Get all fields from document
            $fields = $docApi->GetTemplateFields($clientId, $fileGuId);
            //Check status
            if ($fields->status == "Ok") {
                //Create new Datasource object
                $dataSource = new Datasource();
                //Create empty array
                $array = array();
                //Loop for fields creataion
                $paramType = null;
                //Counter for radio buttons
                $counter = -1;
                //Field name
                $fieldName = null;
                //Create Data source field
                for ($i = 0; $i < count($fields->result->fields); $i++) {
                    //Replace all spaces in fields names to "_"
                    //Check is qurent field name equally to field name from document 
                    foreach ($dataForDataSource as $paramName => $paramValue) {
                        //Set field type
                        $list = null;
                        $paramName = str_replace("_", " ", $paramName);
                        if ($paramName == $fields->result->fields[$i]->name) {
                            if ($fields->result->fields[$i]->type == "RadioButton") {
                                $counter = $counter + 1;
                                if ($counter == $paramValue) {
                                    $paramType = "Integer";
                                } else {
                                    continue;
                                }
                            } elseif ($fields->result->fields[$i]->type == "MultiLineText") {
                                $paramType = "text";
                            } elseif ($fields->result->fields[$i]->type == "Checkbox") {
                                $paramType = "boolean";
                                $paramValue = true;
                            } elseif ($fields->result->fields[$i]->type == "Listbox") {
                                $paramType = "Integer";
                                $list = $paramValue;
                            } elseif ($fields->result->fields[$i]->type == "Combobox") {
                                $paramType = "Integer";
                            } else {
                                $paramType = $fields->result->fields[$i]->type;
                            }
                            //Create new DatasourceField object
                            $field = new DatasourceField();
                            //Set DatasourceFiled data
                            $field->name = $paramName;
                            $field->type = $paramType;
                            if ($list) {
                                $field->values = $list;
                            } else {
                                $field->values = array($paramValue);
                            }
                            //Push DatasourceField to array
                            array_push($array, $field);
                        }
                    }
                }
                //Set array feilds array to the Datasourc
                $dataSource->fields = $array;
                //Add Datasource to GroupDocs
                $addDataSource = $mergApi->AddDataSource($clientId, $dataSource);
                //Check status
                if ($addDataSource->status == "Ok") {
                    //If status ok  merge/assemble Datasource to new pdf file
                    $job = $mergApi->MergeDatasource($clientId, $fileGuId, $addDataSource->result->datasource_id, "pdf", null);
                    //Check status
                    if ($job->status == "Ok") {
                        //### Check job status
                        for ($n = 0; $n <= 5; $n++) {
                            //Delay necessary that the inquiry would manage to be processed
                            sleep(2);
                            //Make request to api for get document info by job id
                            $jobInfo = $asyncApi->GetJobDocuments($clientId, $job->result->job_id);
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
                        if ($basePath == "https://api.groupdocs.com/v2.0") {
                            $iframe = 'https://apps.groupdocs.com/document-viewer/embed/' . $guid;
                            //iframe to dev server
                        } elseif ($basePath == "https://dev-api-groupdocs.dynabic.com/v2.0") {
                            $iframe = 'https://dev-apps-groupdocs.dynabic.com/document-viewer/embed/' . $guid;
                            //iframe to test server
                        } elseif ($basePath == "https://stage-api-groupdocs.dynabic.com/v2.0") {
                            $iframe = 'https://stage-apps-groupdocs.dynabic.com/document-viewer/embed/' . $guid;
                        } elseif ($basePath == "http://realtime-api.groupdocs.com") {
                            $iframe = 'http://realtime-apps.groupdocs.com/document-viewer/embed/' . $guid;
                        }
                        $iframe = $signer->signUrl($iframe);
                        F3::set('url', $iframe);
                        F3::set('userId', $clientId);
                        F3::set('privateKey', $privateKey);
                    } else {
                        throw new Exception($job->error_message);
                    }
                } else {
                    throw new Exception($addDataSource->error_message);
                }
            } else {
                throw new Exception($fields->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
} else {
    $clientId = F3::get('POST["clientId"]');
    $privateKey = F3::get('POST["privateKey"]');
    $basePath = F3::get('POST["basePath"]');
    $fileGuId = "";
    $url = F3::get('POST["url"]');
    $fileId = F3::get('POST["guidField"]');
    //###Check clientId and privateKey
    if (empty($clientId) || empty($privateKey)) {
        $error = 'Please enter all required parameters';
        F3::set('error', $error);
    } else {
        //Deleting all tags, slashes and space from clientId and privateKey
        $clientID = strip_tags(stripslashes(trim($clientId))); //ClientId==UserId
        $apiKey = strip_tags(stripslashes(trim($privateKey))); //ApiKey==PrivateKey
        //###Create Signer, ApiClient and Storage Api objects
        //Create signer object
        $signer = new GroupDocsRequestSigner($apiKey);
        //Create apiClient object
        $apiClient = new APIClient($signer);
        //Create docApi object
        $docApi = new DocApi($apiClient);
        //Create Storage Api object
        $storageApi = new StorageApi($apiClient);
        //Check if user entered base path
        if ($basePath == "") {
            //If base base is empty seting base path to prod server
            $basePath = 'https://api.groupdocs.com/v2.0';
        }
        //Build propper basePath
        if (substr($basePath, -3) != "2.0") {
            if (substr($basePath, -1) != "/") {
                $basePath = $basePath . "/v2.0";
            } else {
                $basePath = $basePath . "v2.0";
            }
        }
        //Set base path
        $docApi->setBasePath($basePath);
        $storageApi->setBasePath($basePath);
        try {
            //Check if user choose upload file from URL
            if ($url != "") {
                //Upload file from URL
                $uploadResult = $storageApi->UploadWeb($clientId, $url);
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
                $file = $_FILES['file'];
                //Temp name of the file
                $tmpName = $file['tmp_name'];
                //Original name of the file
                $name = $file['name'];
                //Creat file stream
                $fs = FileStream::fromFile($tmpName);
                //###Make a request to Storage API using clientId
                //Upload file to current user storage
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
            }
            //Check is user choose file GUID
            if ($fileId != "") {
                //Get entered by user file GUID
                $fileGuId = $fileId;
            }
            //###Get all fields from document
            $getFields = $docApi->GetTemplateFields($clientId, $fileGuId);
            if ($getFields->status == "Ok") {
                $fields = $getFields->result->fields;
                //Create support variables and counters
                $optional = "";
                $element = "";
                $countList = 0;
                $countRadio = 0;
                $countCheckBox = 0;
                $countCombo = 0;
                $fieldName = null;
                //Create HTML form from fields
                for ($i = 0; $i < count($fields); $i++) {
                    $fieldName = $fields[$i]->name;
                    if ($fields[$i]->mandatory == false) {
                        $optional = '<span class="optional">(Optional)</span>';
                    } else {
                        $optional = '<span class="optional">(Required)</span>';
                    }
                    if ($fields[$i]->type == "Text") {
                        $element .= '<br /><label for="' . $fieldName . '">' . $fields[$i]->name . " " .
                                $optional . '</label><br /><input type="text" name="' . $fieldName . '" value="" /><br />';
                    } elseif ($fields[$i]->type == "Combobox") {
                        $element .= '<br /><label for="' . $fieldName . '">' . $fields[$i]->name . " " .
                                $optional . '</label><br /><select name="' . $fieldName . '">';
                        $options = $fields[$i]->acceptableValues;
                        foreach ($options as $option) {
                            $element .= '<option value="' . $countList . '">' . $option . '</option>';
                            $countList = $countList + 1;
                        }
                        $element .= '</select><br />';
                    } elseif ($fields[$i]->type == "Listbox") {
                        $element .= '<br /><label for="' . $fieldName . '">' . $fields[$i]->name . " " .
                                $optional . '</label><br /><select multiple name="' . $fieldName . '[]" >';
                        $options = $fields[$i]->acceptableValues;
                        foreach ($options as $option) {
                            $element .= '<option value="' . $countCombo . '">' . $option . '</option>';
                            $countCombo = $countCombo + 1;
                        }
                        $element .= '</select><br />';
                    } elseif ($fields[$i]->type == "Checkbox") {
                        $element .= '<br /><input type="checkbox" name="' . $fieldName . '" value="' . $countCheckBox . '" >' . $fields[$i]->name . $optional . '</input><br />';
                        $countCheckBox = $countCheckBox + 1;
                    } elseif ($fields[$i]->type == "MultiLineText") {
                        $element .= '<br /><label for="' . $fieldName . '">' . $fields[$i]->name . " " .
                                $optional . '</label><br /><textarea name="' . $fieldName . '" value=""></textarea><br />';
                    } elseif ($fields[$i]->type == "Signature") {
                        $element .= '<br /><label for="' . $fieldName . '">' . $fields[$i]->name . " " .
                                $optional . '</label><br /><input type="file" name="' . $fieldName . '" value="" /><br />';
                    } elseif ($fields[$i]->type == "RadioButton") {
                        $element .= '<br /><input type="radio" name="' . $fieldName . '" value="' . $countRadio . '" >' . $fields[$i]->name . $optional . '</input><br />';
                        $countRadio = $countRadio + 1;
                    }
                }
                F3::set('newForm', $element);
                F3::set('userId', $clientId);
                F3::set('privateKey', $privateKey);
                F3::set('fileId', $fileGuId);
                F3::set('basePath', $basePath);
            } else {
                throw new Exception($getFields->error_message);
            }
        } catch (Exception $e) {
            $error = 'ERROR: ' . $e->getMessage() . "\n";
            F3::set('error', $error);
        }
    }
}
//Process template
echo Template::serve('sample35.htm');
