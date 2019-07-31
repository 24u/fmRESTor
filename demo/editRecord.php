<?php

use fmRESTor\fmRESTor;
session_start();
require_once dirname(__DIR__) . '/src/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456", array("allowInsecure" => true));

// These steps are for preparation only
$newRecord = array(
    "fieldData" => array(
        "surname" => "Sutton",
        "email" => "sutton@a.edu",
        "birthday" => "12.11.2020",
        "personal_identification_number" => "421",
        "address" => "5776 Nisi Road, Gorlitz 38197"
    ),
    "portalData" => array(
        "USER_licence" => array(
            array(
                "USER_licence::product_name" => "Adobe Photoshop Elements",
                "USER_licence::key" => "VK7JG-NPHTM-C97JM-9MPGT-3V66T",
                "USER_licence::version" => "2019 MP ENG BOX",
                "USER_licence::date_of_expiration" => "02.08.2024"
            ),
            array(
                "USER_licence::product_name" => "Microsoft Office 365",
                "USER_licence::key" => "KTNPV-KTRK4-3RRR8-39X6W-W44T3",
                "USER_licence::version" => "Business Premium OLP",
                "USER_licence::date_of_expiration" => "06.04.2021"
            )
        )
    )
);

$response = $fm->createRecord($newRecord);

// This is ID the record that was made and this record will be edited
$id = $response["response"]["recordId"];

// Setting parameters for editing - surname, email, personal identification number, product key for first row, version and date of expiration for second row will be edited
$editRecord = array(
    "fieldData" => array(
        "surname" => "Sutton G.",
        "email" => "sutton.gabriel@a.edu",
        "personal_identification_number" => "111",
    ),
    "portalData" => array(
        "USER_licence" => array(
            array(
                "USER_licence::key" => "VK7JG-NPHTM",
            ),
            array(
                "USER_licence::version" => "Business OLP",
                "USER_licence::date_of_expiration" => "09.01.2023"
            )
        )
    ),
    //"modId" => ,
    "script" => "Log request",
    "script.param" => "Parameter from fmRESTor - edit record"
);

// Edit the record
$response2 = $fm->editRecord($id, $editRecord);
var_dump($response, $response2);
exit();
