<?php

// Delete record with all optional parameters
session_start();
require_once dirname(__DIR__) . '/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456",array("allowInsecure" => true));

// Create new record
$newRecord = array(
    "fieldData" => array(
        "surname" => "Delete Name",
        "email" => "email@email.com",
        "birthday" => "1.1.2001",
        "personal_identification_number" => "99",
        "address" => "Street 24, City"
    ),
    "portalData" => array(
        "USER_licence" => array(
            array(
                "USER_licence::product_name" => "product01",
                "USER_licence::key" => "key01",
                "USER_licence::version" => "ver01",
                "USER_licence::date_of_expiration" => "1.1.2024"
            )
        )
    )
);

$response = $fm->createRecord($newRecord);

// This is ID the record that was made and this record will be deleted
$id = $response["response"]["recordId"];

// Creating the second record just for check that we know the first one was successful created and deleted
$newRecord["fieldData"]["surname"] = "Delete Name - check";

$response2 = $fm->createRecord($newRecord);

$id2 = $response2["response"]["recordId"];

// Setting up the optional parameters
$deleteRecord = array(
    "script" => "PHP_LOG_REQUEST",
    "script.param" => "script.param",
    "script.prerequest" => "PHP_LOG_REQUEST",
    "script.prerequest.param" => "script.prerequest.param",
    "script.presort" => "PHP_LOG_REQUEST",
    "script.presort.param" => "script.presort.param"
);

$response3 = $fm->deleteRecord($id, $deleteRecord);
var_dump($response, $response2, $response3);
exit();
