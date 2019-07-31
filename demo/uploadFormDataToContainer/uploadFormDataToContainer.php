<?php

use fmRESTor\fmRESTor;
session_start();
require_once dirname(dirname(__DIR__)) . '/src/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456", array("allowInsecure" => true));

// This steps will just prepera data for the function upload form data to container
$newRecord = array(
    "fieldData" => array(
        "surname" => "Phelps",
        "email" => "phelps@Aliquam.co.uk",
        "birthday" => "02.24.2020",
        "personal_identification_number" => "96",
        "address" => "9309 In St., Gressan 916926"
    ),
    "portalData" => array(
        "USER_licence" => array(
            array(
                "USER_licence::product_name" => "Microsoft Windows Server",
                "USER_licence::key" => "N2434-X9D7W-8PF6X-8DV9T-8TYMD",
                "USER_licence::version" => "Standard 2019 x64",
                "USER_licence::date_of_expiration" => "01.01.2024"
            )
        )
    )
);

$response = $fm->createRecord($newRecord);

// This is ID the record that was made and the file be set there
$id = $response["response"]["recordId"];

// Setting up the parameter for upload
$uploadformdatatocontainer = $_FILES["image"];

// File upload
$response2 = $fm->uploadFormDataToContainter($id, "photo", 1, $uploadformdatatocontainer);
var_dump($response, $response2);
exit();