<?php

// Create new record with all optional parameters
session_start();
require_once dirname(__DIR__) . '/fmRESTor.php';

// Options parameters for login in to the databeses
$options = array(
    "logType" => fmRESTor::LOG_TYPE_DEBUG, 
    //"logDir" => dirname(dirname(__DIR__)) . '/log',
    "sessionName" => "session_name",
    "tokenExpireTime" => 15,
    "allowInsecure" => true
);

$fm = new fmRESTor("127.0.0.1", "fmRESTor", "php_user", "api", "api123456", $options);

// Setting the parameters for the new record
$newRecord = array(
    "fieldData" => array(
        "surname" => "Create Name",
        "email" => "email@email.com",
        "birthday" => "1.1.2001",
        "personal_identification_number" => "99",
        "address" => "Street 24, City"
    ),
    //Setting the optional parameters for the new record
    "portalData" => array(
        "USER_licence" => array(
            array(
                "USER_licence::product_name" => "product01",
                "USER_licence::key" => "key01",
                "USER_licence::version" => "ver01",
                "USER_licence::date_of_expiration" => "1.1.2024"
            ),
            array(
                "USER_licence::product_name" => "product02",
                "USER_licence::key" => "key02",
                "USER_licence::version" => "ver02",
                "USER_licence::date_of_expiration" => "2.2.2024"
            ),
            array(
                "USER_licence::product_name" => "product03",
                "USER_licence::key" => "key03",
                "USER_licence::version" => "ver03",
                "USER_licence::date_of_expiration" => "3.3.2024"
            )
        )
    ),
    "script" => "PHP_LOG_REQUEST",
    "script.param" => "script.param",
    "script.prerequest" => "PHP_LOG_REQUEST",
    "script.prerequest.param" => "script.prerequest.param",
    "script.presort" => "PHP_LOG_REQUEST",
    "script.presort.param" => "script.presort.param"
);

$response = $fm->createRecord($newRecord);
var_dump($response);
exit();