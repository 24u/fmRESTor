<?php

use fmRESTor\fmRESTor;
session_start();
require_once dirname(__DIR__) . '/src/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456", array("allowInsecure" => true));

// These steps are for preparation only
$newRecord = array(
    "fieldData" => array(
        "surname" => "King",
        "email" => "king@tempor.net",
        "birthday" => "02.09.2020",
        "personal_identification_number" => "235",
        "address" => "7182 Morbi Road, Hisar 5230"
    ),
);

$response = $fm->createRecord($newRecord);

// This is ID the record that was made and the file be set there
$id = $response["response"]["recordId"];

// Upload the file
$response2 = $fm->uploadFileToContainter($id, "photo", 1, __DIR__ . "/24uSoftware.jpg");
var_dump($response, $response2);
exit();