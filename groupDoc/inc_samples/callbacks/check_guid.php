<?php

$result = "";
//counter to not wait forever
$counter = 0;
//Check folder for downloaded file
    do {
        //Set max. iterations
        if ($counter >= 10) {
            $result = "Error";
            break;
        }
        sleep(5);
        //Check is downloads folder exist
        if (file_exists(__DIR__ . '/../../callback_info.txt')) {
            //If folder don't exist create it
            $callbackInfo = __DIR__ . '/../../callback_info.txt';
            //Local path to the text file with user data
            $info = file($callbackInfo);
            //Get user data from text file
            $result = trim($info[0]);
            break;
        } else {
            $counter++;
        }
    } while (true);
//Check result
    if ($result == "Error") {
        echo "File was not found. Looks like something went wrong.";
    } else {
        //Return file name to the template for ajax
        echo $result;
    }