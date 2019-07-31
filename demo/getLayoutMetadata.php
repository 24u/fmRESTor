<?php

use fmRESTor\fmRESTor;
session_start();
require_once dirname(__DIR__) . '/src/fmRESTor.php';

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456", array("allowInsecure" => true));

// Get useful information about specific layout, including fields on the layout, portals,...
$response = $fm->getLayoutMetadata();
var_dump($response);
exit();