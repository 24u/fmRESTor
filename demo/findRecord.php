<?php

// Find record with all optional parameters
session_start();
require_once dirname(__DIR__) . '/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456",array("allowInsecure" => true));

// Create new record with mandatory parameters
$newRecord = array(
    "fieldData" => array(
        "surname" => "Find Name",
        "email" => "email@email.com",
        "birthday" => "1.1.2001",
        "personal_identification_number" => "99",
        "address" => "Street 24, City"
    )
);

$response = $fm->createRecord($newRecord);

//This response is just for our control
$id = $response["response"]["recordId"];

// Creating more records
$newRecord["fieldData"]["surname"] = "Find Name 2";
$response2 = $fm->createRecord($newRecord);
$id2 = $response2["response"]["recordId"];

$newRecord["fieldData"]["surname"] = "Find Name 3";
$response3 = $fm->createRecord($newRecord);
$id3 = $response3["response"]["recordId"];

$newRecord["fieldData"]["surname"] = "Find Name 4";
$response4 = $fm->createRecord($newRecord);
$id4 = $response4["response"]["recordId"];

$findRecords = array(
    // Setting up the mandatory options
    "query" => array(
        array(
            //"surname" => "",
            "email" => "email@email.com",
            "birthday" => "1.1.2001",
            "personal_identification_number" => "99",
            "address" => "Street 24, City",
            "omit" => "true"
        )
    )
);

$response5 = $fm->findRecords($findRecords);
var_dump($response, $response2, $response3, $response4, $response5);
exit();