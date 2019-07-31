<?php

use fmRESTor\fmRESTor;
session_start();
require_once dirname(__DIR__) . '/src/fmRESTor.php';

// Setting the external source
$fmExternalSource = array(
    array(
        "database" => "fmRESTorEXTERNAL",
        "username" => "external",
        "password" => "external123456"
    )
);

// Log in to the database with external source
$response = $fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456", array("allowInsecure" => true), $fmExternalSource);

var_dump($response);
exit();