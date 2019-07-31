<?php

use fmRESTor\fmRESTor;
session_start();
require_once dirname(__DIR__) . '/src/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456", array("allowInsecure" => true));

// Run script
$response = $fm->runScript("Log request", array("script.param"=>"Parameter from fmRESTor - run script"));

var_dump($response);
exit();
