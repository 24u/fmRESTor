<?php

// Set global field with all parameters
session_start();
require_once dirname(__DIR__) . '/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456",array("allowInsecure" => true));

// Setting up the mandatory parameters
$setGlobalField = array(
    "globalFields" => array(
        "USER::g_one" => "Global g_one is set up",
        "USER::g_text" => "Global g_text is set up"
        
    )
);

$response = $fm->setGlobalField($setGlobalField);
var_dump($response);
exit();
