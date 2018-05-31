Leverage the FileMaker® 17 Data API with ease!
----

fmRESTor is an object-based PHP library developed to seamlessly interact with databases and custom apps hosted on a FileMaker Server via the new powerful FileMaker Data API from within a PHP code. Forget about learning FileMaker Data API in detail, just create a new object, passing it necessary parameters to connect to the database, and use our easy to understand methods to access or modify your data. fmRESTor will take care of authentication, exceptions, logging, and even session preservation in order for your code to be a well-behaving client for the FileMaker Data API without you having to worry about these technical details.

We have created fmRESTor for ourselves to make it easier and faster to interact with FileMaker databases from within custom web apps we create, without having to waste our valuable time on repeating writing the same patterns over and over again, hunting for unnecessary bugs, and to be able to produce clean, easy to read and easy to maintain code.

We at 24U believe that the whole FileMaker developers community will benefit from the FileMaker Platform not only having new powerful RESTful API, but also developers using the API nicely and efficiently, therefore we decided to make our library available as Open Source, under the GNU LGPLv3 license.

We will greatly appreciate your contributions, although we cannot provide free support for the library. You may, however, hire us to help you with your projects for money, either by purchasing developer-level support from us at [https://www.24uSoftware.com/DevSupport](https://www.24uSoftware.com/DevSupport) or by utilizing our custom development services, available at [https://www.24uSoftware.com/CustomDevelopment](https://www.24uSoftware.com/CustomDevelopment).

Features
-

* fmRESTor contains debug logging

Requirements
-

* PHP >= 5.5
* cURL
* FileMaker 17 database

Usage
-


Include downloaded library file to your project and create new class instance. 

~~~php
session_start();
require_once __DIR__ . '/fmRESTor.php';

$fm = new FilemakerAPI($host, $database, $layout, $user, $password, $options);
~~~

### _Instance parameters_:


 Parameter  | Type  | Mandatory  |Description
------------- | ------------- | ------------- | -------------
$host  | string  | yes | Hostname or IP address where FileMaker database is hosted
$database  | string  | yes | FileMaker database name
$layout  | string  | yes | Database layout name to work on
$user | string  | yes | User login to database
$password | string  | yes | User password to database
$options  | array  | optional | Additional library configuration

### _Options parameters:_

Name | Type | Mandatory | Default value | Description
------------- | ------------- | ------------- | ------------- | -------------
logType | string | optional | LOG\_TYPE\_NONE | **LOG\_TYPE\_DEBUG** - Debug logging level<br/>**LOG\_TYPE\_ERRORS** - Log errors only<br/>**LOG\_TYPE\_NONE** - Disable logging
logDir | string | optional | "log" (same path as library file) | Custom default folder for log output
sessionName | string | optional | "fm-api-token" | Custom session name, available in "$\_SESSION['custom\_session\_name']"
tokenExpireTime | number | optional | 14 | Expiration time in minutes. fmRESTor automatically handles database login and saves token with its expiration time (into $_SESSION var) during first request. If expired, fmRESTor automatically reconnects to database on next request.
allowInsecure | boolean | optional | false | Valid SSL certificate required unless set to **true**
### _Example:_

~~~php
session_start();
require_once __DIR__ . '/fmRESTor.php';

$options = array(
	"logType" => "all",
	"logDir" => dirname(__DIR__) . "/my-logs", 
	"sessionName" => "fm-api-token",
	"tokenExpireTime" => 14,
	"allowInsecure" => true 
);

$fm = new FilemakerAPI($host, $database, $layout, $user, $password, $options);
~~~

Complete list of optional parameters is available at [http://fmhelp.filemaker.com/docs/17/en/dataapi/](http://fmhelp.filemaker.com/docs/17/en/dataapi/)

Methods
-

### _logout_
Close current session in the FileMaker database.

~~~php
/**
 * @return mixed
 */
public function logout()
~~~

**Usage:**

~~~php
$fm->logout();
~~~

___

### _createRecord:_

Create a record in the contextual table of the layout specified as an instance parameter.

~~~php
/**
 * @param array $parameters
 * @return bool|mixed
 */
public function createRecord($parameters)
~~~

**Usage:**

~~~php
$parameters = array(
    "fieldData" => array(
        "surname" => "Create Name",
        "email" => "email@email.com",
        "birthday" => "1.1.2001",
        "personal_identification_number" => "99",
        "address" => "Street 24, City"
    )
);

$recordID = 4;

$fm->createRecord($recordID, $parameters);
~~~

___

### _deleteRecord:_

Delete a record of given ID from the contextual table of the layout specified as an instance parameter.

~~~php
/**
 * @param integer $id
 * @param array $parameters
 * @return bool|mixed
 */
public function deleteRecord($id, $parameters)
~~~

**Usage:**

~~~php
$parameters = array(
    "script" => "PHP_LOG_REQUEST",
    "script.param" => "script.param",
    "script.prerequest" => "PHP_LOG_REQUEST",
    "script.prerequest.param" => "script.prerequest.param",
    "script.presort" => "PHP_LOG_REQUEST",
    "script.presort.param" => "script.presort.param"
);

$recordID = 4;

$fm->deleteRecord($recordID, $parameters);
~~~

___

### _editRecord:_

Update a record of given ID from the contextual table of the layout specified as an instance parameter.

~~~php
/**
 * @param integer $id
 * @param array $parameters
 * @return bool|mixed
 */
public function editRecord($id, $parameters)
~~~

**Usage:**

~~~php
$parameters = array(
	"fieldData" => array(
        "surname" => "Name was edited",
        "email" => "emailwasedited@email.com",
        "personal_identification_number" => "1",
    )
);

$recordID = 4;

$fm->editRecord($recordID, $parameters);
~~~

___

### _getRecord:_

Get a record of given ID from the contextual table of the layout specified as an instance parameter.

~~~php
/**
 * @param integer $id
 * @option array $parameters
 * @return bool|mixed
 */
public function getRecord($id, $parameters = null)
~~~

**Usage:**
    
~~~php
$parameters = array(
	"script" => "PHP_LOG_REQUEST",
    "script.param" => "script.param",
    "script.prerequest" => "PHP_LOG_REQUEST",
    "script.prerequest.param" => "script.prerequest.param",
    "script.presort" => "PHP_LOG_REQUEST",
    "script.presort.param" => "script.presort.param",
    "layout.response"=> "php_user",
    "_limit.USER_licence"=> 5,
    "_offset.USER_licence"=> 10
);

$recordID = 4;

$fm->getRecord($recordID, $parameters);
~~~

___

### _getRecords:_

Get multiple records from the contextual table of the layout specified as an instance parameter. The function returns all records if called with no parameter or those fitting the criteria specified in its parameter.

~~~php
/**
 * @option array $parameters
 * @return bool|mixed
 */
public function getRecords($parameters = null)
~~~

**Usage:**
    
~~~php
$parameters = array(
	"limit"=>30
);

$fm->getRecords($parameters);
~~~

___

### _uploadFormDataToContainter:_

Upload form data and store into container field.
    
~~~php
/**
 * @param integer $id
 * @param string $containerFieldName
 * @param string containerFieldRepetition
 * @param array $file
 * @return bool|mixed
 */
public function uploadFormDataToContainter($id, $containerFieldName, $containerFieldRepetition, $file)
~~~

**Usage:**
    
~~~php
$recordID = 4;
$containerFieldName = "photo";
$containerFieldRepetition = 1;
$file = $_FILES["photo"];

$fm->uploadFormDataToContainter($recordID, $containerFieldName, $containerFieldRepetition, $file);
~~~

___

### _uploadFileToContainter:_

Upload file and store into container field. 
    
~~~php
/**
 * @param integer $id
 * @param string $containerFieldName
 * @param integer $containerFieldRepetition
 * @param string $path
 * @return bool|mixed
 */
public function uploadFileToContainter($id, $containerFieldName, $containerFieldRepetition, $path)
~~~

**Usage:**
    
~~~php
$recordID = 4;
$containerFieldName = "photo";
$containerFieldRepetition = 1;
$file = __DIR__ . "/myImage.png";

$fm->uploadFileToContainter($recordID, $containerFieldName, $containerFieldRepetition, $path);
~~~

___

### _findRecord:_

Returns a set of records from the contextual table of the layout specified as an instance parameter, fitting the find criteria specified in its parameter.

~~~php
/**
  * @param array $parameters
  * @return bool|mixed
  */
public function findRecords($parameters)
~~~

**Usage:**
    
~~~php
$parameters = array(
	"query" => array(
        array(
            "email" => "email@email.com",
            "birthday" => "1.1.2001",
            "personal_identification_number" => "99",
            "address" => "Street 24, City",
            "omit" => "true"
        )
    )
);

$fm->findRecords($parameters);
~~~

___

### _setGlobalField:_

Sets the values for global fields specified in its parameter.

~~~php
/**
 * @param array $parameters
 * @return bool|mixed
 */
public function setGlobalField($parameters)
~~~

**Usage:**

~~~php
$parameters = array(
	"globalFields" => array(
        "USER::g_one" => "Global g_one is set up",
        "USER::g_text" => "Global g_text is set up"
    )
);

$fm->setGlobalField($parameters);
~~~

___

### _setFileMakerLayout_
    
Navigates to a database layout specified by its name.

**Example:** An user needs the data from multiple tables within a single class instance, which is created with only one layout specified. Therefore he requests the data from the contextual table, switches to a different layout using **setFileMakerLayout** method and requests the data from the contextual table of new layout.
    
~~~php
/**
 * @param string $layout
 * @return bool
 */
public function setFilemakerLayout($layout)
~~~     
  
**Usage:**   
    
~~~php
$fm->setFilemakerLayout("Layout_name");
~~~    

Response
-

If the request is valid, an user receives a direct response from FileMaker. Otherwise, the response may contain an error (or success) code. The codes are described below:

 Error Code  | Description
------------- | ------------- 
-101  | Options - Invalid value for parameter "logType"
-102 | Options - Invalid value for parameter "logDir"
-103 | Options - Invalid value for parameter "sessionName"
-104 | Options - Invalid value for parameter "tokenExpireTime"
-105 | Options - Invalid value for parameter "AllowInsecure"

 Success Code  | Description
------------- | ------------- 
101  | User was succesfully logged out

Examples
-
The examples for each method are available inside the folder "demo".

TODO
-
External Data Source

License
-
fmRESTor is licensed under the "GNU LGPLv3" License.








