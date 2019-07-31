<?php

use fmRESTor\fmRESTor;
session_start();
require_once dirname(__DIR__) . '/src/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456", array("allowInsecure" => true));

// Setting the optional parameters for the deleting record. That can be checked in example database in layout data_log after the created record.
$deleteRecord = array(
    "script" => "Log request",
    "script.param" => "Parameter from fmRESTor - delete record"
);

// Deleting record with ID 5 (field in the database - c_record_id)
$id = 5;
$response = $fm->deleteRecord($id, $deleteRecord);
var_dump($response);
exit();
