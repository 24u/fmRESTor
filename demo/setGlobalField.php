<?php

use fmRESTor\fmRESTor;
session_start();
require_once dirname(__DIR__) . '/src/fmRESTor.php';

$options = array(
    "sessionName" => "Session_for_GlobalField",
    "allowInsecure" => true
);

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456", $options);

// These steps are for preparation only
$newRecord = array(
    "fieldData" => array(
        "surname" => "Adkins",
        "email" => "adkins@tempusmauriserat.org",
        "birthday" => "01.09.2020",
        "personal_identification_number" => "355",
        "address" => "9523 Nulla. Road, Portico e San Benedetto 378300"
    ),
);

$response = $fm->createRecord($newRecord);

$id = $response["response"]["recordId"];

// Setting up the mandatory parameters for set global field
$setGlobalField = array(
    "globalFields" => array(
        "USER::g_one" => "123456789",
        "USER::g_text" => "Global g_text is set up"
        
    )
);

// Sets the values into the global fields
$response2 = $fm->setGlobalField($setGlobalField);

// The value from global fields is set up just for this session. We can check the global fields with get record
$response3 = $fm->getRecord($id); 
var_dump($response, $response2, $response3);
exit();