<?php

// Edit record with all optional parameters
session_start();
require_once dirname(__DIR__) . '/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456",array("allowInsecure" => true));

// Create new record
$newRecord = array(
    "fieldData" => array(
        "surname" => "Edit Name",
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
            ),
            array(
                "USER_licence::product_name" => "product02",
                "USER_licence::key" => "key02",
                "USER_licence::version" => "ver02",
                "USER_licence::date_of_expiration" => "2.2.2024"
            )
        )
    )
);

$response = $fm->createRecord($newRecord);

// This is ID the record that was made and this record will be edited
$id = $response["response"]["recordId"];

// Setting up the optional parameters
// This parameters will be edited - surname, email, personal identification number, product name and key for first row, version and date of expiration for second row
$editRecord = array(
    "fieldData" => array(
        "surname" => "Name was edited",
        "email" => "emailwasedited@email.com",
        "personal_identification_number" => "1",
    ),
    "portalData" => array(
        "USER_licence" => array(
            array(
                "USER_licence::product_name" => "edited - product01",
                "USER_licence::key" => "edited - key01",
            ),
            array(
                "USER_licence::version" => "edited - ver02",
                "USER_licence::date_of_expiration" => "9.9.2099"
            )
        )
    ),
    "modId" => 0,
    "script" => "PHP_LOG_REQUEST",
    "script.param" => "script.param",
    "script.prerequest" => "PHP_LOG_REQUEST",
    "script.prerequest.param" => "script.prerequest.param",
    "script.presort" => "PHP_LOG_REQUEST",
    "script.presort.param" => "script.presort.param"
);


$response2 = $fm->editRecord($id, $editRecord);
var_dump($response, $response2);
exit();
