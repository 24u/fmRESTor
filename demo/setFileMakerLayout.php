<?php

// Set FileMaker Layout with all parameters
session_start();
require_once dirname(__DIR__) . '/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456",array("allowInsecure" => true));

// Setting up the mandatory parameters
$response = $fm->setFilemakerLayout("php_licence");
var_dump($response);
exit();
