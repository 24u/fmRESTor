<?php

use fmRESTor\fmRESTor;
session_start();
require_once dirname(__DIR__) . '/src/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456", array("allowInsecure" => true));

// Setting the parameters for the new record
$newRecord = array(
    "fieldData" => array(
        "surname" => "Lawrence",
        "email" => "lawrence@lectus.ca",
        "birthday" => "03.12.2020",
        "personal_identification_number" => "398",
        "address" => "7399 Lobortis Rd., Görlitz 38197"
    ),
    "portalData" => array(
        "USER_licence" => array(
            array(
                "USER_licence::product_name" => "Windows 10 OEM Home",
                "USER_licence::key" => "NKJFK-GPHP7-G8C3J-P6JXR-HQRJR ",
                "USER_licence::version" => "10",
                "USER_licence::date_of_expiration" => "05.12.2020"
            ),
            array(
                "USER_licence::product_name" => "Windows 7 Ultimate 32 bit",
                "USER_licence::key" => "RCGX7-P3XWP-PPPCV-Q2H7C-FCGFR",
                "USER_licence::version" => "7.3",
                "USER_licence::date_of_expiration" => "03.04.2018"
            ),
        )
    ),
    // Setting the optional parameters for the new record. That can be checked in example database in layout data_log after the created record.
    "script" => "Log request",
    "script.param" => "Parameter from fmRESTor - create record"
);

// Creating new record
$response = $fm->createRecord($newRecord);
var_dump($response);
exit();