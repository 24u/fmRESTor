<?php

// Get records with all optional parameters
session_start();
require_once dirname(__DIR__) . '/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456",array("allowInsecure" => true));

// Create new record
$newRecord = array(
    "fieldData" => array(
        "surname" => "Get Records Name",
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

// This is ID the record that was made and this record will be get
$id = $response["response"]["recordId"];

// Creating more records for getting
$newRecord["fieldData"]["surname"] = "Get Records Name 2";
$response2 = $fm->createRecord($newRecord);
$id2 = $response2["response"]["recordId"];

$newRecord["fieldData"]["surname"] = "Get Records Name 3";
$response3 = $fm->createRecord($newRecord);
$id3 = $response3["response"]["recordId"];

$newRecord["fieldData"]["surname"] = "Get Records Name 4";
$response4 = $fm->createRecord($newRecord);
$id4 = $response4["response"]["recordId"];


// Setting up the optional parameters
$GetRecords= array(
    //"limit"=>101,
    //"_offset"=>"",
    //"_sort" =>"",
    //"portal"=>"",
    //"_limit.<portalId>"=>"",
    //"_offset.<portalId>"=>"",
    "script"=>"PHP_LOG_REQUEST",
    "script.param"=>"script.param",
    "script.prerequest"=>"PHP_LOG_REQUEST",
    "script.prerequest.param"=> "script.prerequest.param",
    "script.presort"=> "PHP_LOG_REQUEST",
    "script.presort.param"=> "script.presort.param",
    //"layout.response"=> "php_licence",
);

$response5 = $fm->getRecords($GetRecords); 
var_dump($response, $response2, $response3, $response4, $response5);
exit();
