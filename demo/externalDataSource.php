<?php

// Find data from external data source
session_start();
require_once dirname(__DIR__) . '/fmRESTor.php';

$fmDataSource = array(
    array(
        "database" => "fmRESTorBUDY",
        "username" => "budy",
        "password" => "budy123456"
    )
);

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456",array("allowInsecure" => true), $fmDataSource);

// Create new record
$newRecord = array(
    "fieldData" => array(
        "surname" => "Get Record Name",
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

// This is ID the record that was made and this record will be get with external source
$id = $response["response"]["recordId"];

// Setting up the optional parameters
$GetRecord= array(
    "script" => "PHP_LOG_REQUEST",
    "script.param" => "script.param",
    "script.prerequest" => "PHP_LOG_REQUEST",
    "script.prerequest.param" => "script.prerequest.param",
    "script.presort" => "PHP_LOG_REQUEST",
    "script.presort.param" => "script.presort.param",
    "layout.response"=> "php_user",
    //"portal"=> "",
    "_limit.USER_licence"=> 5,
    "_offset.USER_licence"=> 10
);

$response2 = $fm->getRecord($id, $GetRecord); 
var_dump($response, $response2);
exit();