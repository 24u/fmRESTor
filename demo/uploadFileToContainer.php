<?php

// Upload File to Container with all parameters
session_start();
require_once dirname(__DIR__) . '/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456",array("allowInsecure" => true));

// Create new record
$newRecord = array(
    "fieldData" => array(
        "surname" => "Upload File Name",
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

// This is ID the record that was made and the file be set there
$id = $response["response"]["recordId"];

$response2 = $fm->uploadFileToContainter($id, "photo", 1, __DIR__ . "/24uSoftware.jpg");
var_dump($response, $response2);
exit();