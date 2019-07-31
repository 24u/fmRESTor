Leverage the FileMaker® 17 & 18 Data API with ease!
----

fmRESTor is an object-based PHP library developed to seamlessly interact with databases and custom apps hosted on a FileMaker Server via the new powerful FileMaker Data API from within a PHP code. Forget about learning FileMaker Data API in detail, just create a new object, passing it necessary parameters to connect to the database, and use our easy to understand methods to access or modify your data. fmRESTor will take care of authentication, exceptions, logging, and even session preservation in order for your code to be a well-behaving client for the FileMaker Data API without you having to worry about these technical details.

We have created fmRESTor for ourselves to make it easier and faster to interact with FileMaker databases from within custom web apps we create, without having to waste our valuable time on repeating writing the same patterns over and over again, hunting for unnecessary bugs, and to be able to produce clean, easy to read and easy to maintain code.

We at 24U believe that the whole FileMaker developers community will benefit from the FileMaker Platform not only having new powerful RESTful API, but also developers using the API nicely and efficiently, therefore we decided to make our library available as Open Source, under the GNU LGPLv3 license.

We will greatly appreciate your contributions, although we cannot provide free support for the library. You may, however, hire us to help you with your projects for money by purchasing our services at [https://www.24uSoftware.com/fmRESTor](https://www.24uSoftware.com/fmRESTor#buy) or by utilizing our custom development services, available at [https://www.24uSoftware.com/custom-apps](https://www.24uSoftware.com/custom-apps).

Features
-

* One object class conveniently handles everything
* Automatically generates authentication token
* Re-uses existing token to avoid unnecessary additional connections
* Automatically re-generates expired token
* Handles exceptions and provides meaningful error results
* Can handle raw form data for easier container uploads
* Provides customizable debug logging

Requirements
-

* PHP >= 5.6
* PHP cURL
* FileMaker Server 17 or 18

Usage (with use composer)
-


Install the latest version with:

~~~
composer require fmrestor/fmrestor
~~~

Usage:

~~~php
session_start();

require __DIR__ . "/vendor/autoload.php";

use fmRESTor\fmRESTor;

$fm = new fmRESTor($host, $database, $layout, $user, $password, $options, $fmExternalSource);
~~~

Usage (without use composer)
-


Include downloaded library file to your project and create new class instance. 

~~~php
session_start();

require_once __DIR__ . '/src/fmRESTor.php';

use fmRESTor\fmRESTor;

$fm = new fmRESTor($host, $database, $layout, $user, $password, $options, $fmExternalSource);
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
$fmExternalSource | array  | optional | Providing additional data sources, i.e. if you use a separation model and the current layout needs to access data from external data sources.
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

require_once __DIR__ . '/src/fmRESTor.php';

use fmRESTor\fmRESTor;

$options = array(
	"logType" => "all",
	"logDir" => dirname(__DIR__) . "/my-logs/", 
	"sessionName" => "fm-api-token",
	"tokenExpireTime" => 14,
	"allowInsecure" => true 
);

$fmExternalSource = array(
    array(
        "database" => "fmRESTorEXTERNAL",
        "username" => "external",
        "password" => "external123456"
    )
);

$fm = new fmRESTor("127.0.0.1","fmRESTor", "php_user", "api", "api123456", $options, $fmExternalSource);
~~~

Methods
-

### _logout_

**Supported FileMaker Server version:** 17, 18

Close current session in the FileMaker database.

~~~php
/**
 * @return mixed
 */
public function logout()
~~~

<details><summary>Usage</summary>

~~~php
$fm->logout();
~~~

</details>

___

### _getProductInformation:_

**Supported FileMaker Server version:** 18

Returns useful information about the FileMaker Server you're connecting to, such as version or data & time formats.

~~~php
/**
 * @return bool|mixed
 */
public function getProductInformation()
~~~


<details><summary>Usage</summary>

~~~php
$fm->getProductInformation();
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(1) {
    ["productInfo"]=>
    array(6) {
      ["name"]=>
      string(25) "FileMaker Data API Engine"
      ["buildDate"]=>
      string(10) "04/29/2019"
      ["version"]=>
      string(10) "18.0.1.123"
      ["dateFormat"]=>
      string(10) "MM/dd/yyyy"
      ["timeFormat"]=>
      string(8) "HH:mm:ss"
      ["timeStampFormat"]=>
      string(19) "MM/dd/yyyy HH:mm:ss"
    }
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

[FileMaker 18 Data API Guide - Get Product Information
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#get-metadata_get-product-info) 
___

### _getDatabaseNames:_

**Supported FileMaker Server version:** 18

Returns array of names of all databases hosted and enabled for access via FileMaker Data API.

~~~php
/**
 * @return bool|mixed
 */
public function getDatabaseNames()
~~~

<details><summary>Usage</summary>

~~~php
$fm->getDatabaseNames();
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(1) {
    ["databases"]=>
    array(1) {
      [0]=>
      array(1) {
        ["name"]=>
        string(8) "fmRESTor"
      }
    }
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

[FileMaker 18 Data API Guide - Get Database Names
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#get-metadata_get-database-names) 
___

### _getScriptNames:_

**Supported FileMaker Server version:** 18

Returns array of names of all available scripts for given database.

~~~php
/**
 * @return bool|mixed
 */
public function getScriptNames()
~~~

<details><summary>Usage</summary>

~~~php
$fm->getScriptNames();
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(1) {
    ["scripts"]=>
    array(1) {
      [0]=>
      array(2) {
        ["name"]=>
        string(15) "Log request"
        ["isFolder"]=>
        bool(false)
      }
    }
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

[FileMaker 18 Data API Guide - Get Script Names
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#get-metadata_get-script-names) 
___

### _getLayoutNames:_

**Supported FileMaker Server version:** 18

Returns array of names of all available layouts for given database.

~~~php
/**
 * @return bool|mixed
 */
public function getLayoutNames()
~~~

<details><summary>Usage</summary>

~~~php
$fm->getLayoutNames();
~~~
</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(1) {
    ["layouts"]=>
    array(3) {
      [0]=>
      array(3) {
        ["name"]=>
        string(3) "php"
        ["isFolder"]=>
        bool(true)
        ["folderLayoutNames"]=>
        array(2) {
          [0]=>
          array(1) {
            ["name"]=>
            string(8) "php_user"
          }
          [1]=>
          array(1) {
            ["name"]=>
            string(11) "php_licence"
          }
        }
      }
      [1]=>
      array(3) {
        ["name"]=>
        string(4) "scpt"
        ["isFolder"]=>
        bool(true)
        ["folderLayoutNames"]=>
        array(1) {
          [0]=>
          array(1) {
            ["name"]=>
            string(8) "scpt_log"
          }
        }
      }
      [2]=>
      array(3) {
        ["name"]=>
        string(4) "data"
        ["isFolder"]=>
        bool(true)
        ["folderLayoutNames"]=>
        array(1) {
          [0]=>
          array(1) {
            ["name"]=>
            string(8) "data_log"
          }
        }
      }
    }
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

[FileMaker 18 Data API Guide - Get Layout Names
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#get-metadata_get-layout-names) 
___

### _getLayoutMetadata:_

**Supported FileMaker Server version:** 18

Returns useful information about specific layout, including fields on the layout, portals, and value list data for each field set to use a value list for data entry.

~~~php
/**
 * @return bool|mixed
 */
public function getLayoutMetadata()
~~~

<details><summary>Usage</summary>

~~~php
$fm->getLayoutMetadata();
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(2) {
    ["fieldMetaData"]=>
    array(17) {
      [0]=>
      array(14) {
        ["name"]=>
        string(2) "id"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(6) "number"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(true)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(true)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [1]=>
      array(14) {
        ["name"]=>
        string(7) "created"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(9) "timeStamp"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(true)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [2]=>
      array(14) {
        ["name"]=>
        string(10) "created_by"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(4) "text"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(true)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [3]=>
      array(14) {
        ["name"]=>
        string(8) "modified"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(9) "timeStamp"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(true)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [4]=>
      array(14) {
        ["name"]=>
        string(11) "modified_by"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(4) "text"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(true)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [5]=>
      array(14) {
        ["name"]=>
        string(7) "surname"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(4) "text"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [6]=>
      array(14) {
        ["name"]=>
        string(5) "email"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(4) "text"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [7]=>
      array(14) {
        ["name"]=>
        string(8) "birthday"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(4) "date"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [8]=>
      array(14) {
        ["name"]=>
        string(30) "personal_identification_number"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(6) "number"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [9]=>
      array(14) {
        ["name"]=>
        string(7) "address"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(4) "text"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [10]=>
      array(14) {
        ["name"]=>
        string(11) "c_record_id"
        ["type"]=>
        string(11) "calculation"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(6) "number"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [11]=>
      array(14) {
        ["name"]=>
        string(5) "photo"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(9) "container"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [12]=>
      array(14) {
        ["name"]=>
        string(5) "g_one"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(6) "number"
        ["global"]=>
        bool(true)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [13]=>
      array(14) {
        ["name"]=>
        string(6) "g_text"
        ["type"]=>
        string(6) "normal"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(4) "text"
        ["global"]=>
        bool(true)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [14]=>
      array(14) {
        ["name"]=>
        string(11) "<No Access>"
        ["type"]=>
        string(7) "invalid"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(7) "invalid"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [15]=>
      array(14) {
        ["name"]=>
        string(11) "<No Access>"
        ["type"]=>
        string(7) "invalid"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(7) "invalid"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
      [16]=>
      array(14) {
        ["name"]=>
        string(11) "<No Access>"
        ["type"]=>
        string(7) "invalid"
        ["displayType"]=>
        string(8) "editText"
        ["result"]=>
        string(7) "invalid"
        ["global"]=>
        bool(false)
        ["autoEnter"]=>
        bool(false)
        ["fourDigitYear"]=>
        bool(false)
        ["maxRepeat"]=>
        int(1)
        ["maxCharacters"]=>
        int(0)
        ["notEmpty"]=>
        bool(false)
        ["numeric"]=>
        bool(false)
        ["timeOfDay"]=>
        bool(false)
        ["repetitionStart"]=>
        int(1)
        ["repetitionEnd"]=>
        int(1)
      }
    }
    ["portalMetaData"]=>
    array(1) {
      ["portal_licence"]=>
      array(4) {
        [0]=>
        array(14) {
          ["name"]=>
          string(26) "USER_licence::product_name"
          ["type"]=>
          string(6) "normal"
          ["displayType"]=>
          string(8) "editText"
          ["result"]=>
          string(4) "text"
          ["global"]=>
          bool(false)
          ["autoEnter"]=>
          bool(false)
          ["fourDigitYear"]=>
          bool(false)
          ["maxRepeat"]=>
          int(1)
          ["maxCharacters"]=>
          int(0)
          ["notEmpty"]=>
          bool(false)
          ["numeric"]=>
          bool(false)
          ["timeOfDay"]=>
          bool(false)
          ["repetitionStart"]=>
          int(1)
          ["repetitionEnd"]=>
          int(1)
        }
        [1]=>
        array(14) {
          ["name"]=>
          string(17) "USER_licence::key"
          ["type"]=>
          string(6) "normal"
          ["displayType"]=>
          string(8) "editText"
          ["result"]=>
          string(4) "text"
          ["global"]=>
          bool(false)
          ["autoEnter"]=>
          bool(false)
          ["fourDigitYear"]=>
          bool(false)
          ["maxRepeat"]=>
          int(1)
          ["maxCharacters"]=>
          int(0)
          ["notEmpty"]=>
          bool(false)
          ["numeric"]=>
          bool(false)
          ["timeOfDay"]=>
          bool(false)
          ["repetitionStart"]=>
          int(1)
          ["repetitionEnd"]=>
          int(1)
        }
        [2]=>
        array(14) {
          ["name"]=>
          string(21) "USER_licence::version"
          ["type"]=>
          string(6) "normal"
          ["displayType"]=>
          string(8) "editText"
          ["result"]=>
          string(4) "text"
          ["global"]=>
          bool(false)
          ["autoEnter"]=>
          bool(false)
          ["fourDigitYear"]=>
          bool(false)
          ["maxRepeat"]=>
          int(1)
          ["maxCharacters"]=>
          int(0)
          ["notEmpty"]=>
          bool(false)
          ["numeric"]=>
          bool(false)
          ["timeOfDay"]=>
          bool(false)
          ["repetitionStart"]=>
          int(1)
          ["repetitionEnd"]=>
          int(1)
        }
        [3]=>
        array(14) {
          ["name"]=>
          string(32) "USER_licence::date_of_expiration"
          ["type"]=>
          string(6) "normal"
          ["displayType"]=>
          string(8) "editText"
          ["result"]=>
          string(4) "date"
          ["global"]=>
          bool(false)
          ["autoEnter"]=>
          bool(false)
          ["fourDigitYear"]=>
          bool(false)
          ["maxRepeat"]=>
          int(1)
          ["maxCharacters"]=>
          int(0)
          ["notEmpty"]=>
          bool(false)
          ["numeric"]=>
          bool(false)
          ["timeOfDay"]=>
          bool(false)
          ["repetitionStart"]=>
          int(1)
          ["repetitionEnd"]=>
          int(1)
        }
      }
    }
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

[FileMaker 18 Data API Guide - Get Layout Metadata
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#get-metadata_get-layout-metadata) 
___

### _createRecord:_

**Supported FileMaker Server version:** 17, 18

Create a record in the primary table of the current fmRESTor instance context.

~~~php
/**
 * @param array $parameters
 * @return bool|mixed
 */
public function createRecord($parameters)
~~~

<details><summary>Usage</summary>

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

$fm->createRecord($parameters);
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(2) {
    ["recordId"]=>
    string(2) "11"
    ["modId"]=>
    string(1) "0"
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

Complete list of optional parameters is available at [FileMaker 18 Data API Guide
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#work-with-records_create-record)
___

### _deleteRecord:_

**Supported FileMaker Server version:** 17, 18

Delete a record of given ID from the primary table of the current fmRESTor instance context.

~~~php
/**
 * @param integer $id
 * @param array $parameters
 * @return bool|mixed
 */
public function deleteRecord($id, $parameters = null)
~~~


<details><summary>Usage</summary>

~~~php
$parameters = array(
    "script" => "Log request",
    "script.param" => "MyScriptParameters",
    "script.prerequest" => "Log request",
    "script.prerequest.param" => "MyScriptPreRequestParameters",
    "script.presort" => "Log request",
    "script.presort.param" => "MyScriptPreSortParameters"
);

$recordID = 4;

$fm->deleteRecord($recordID, $parameters);
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(0) {
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

Complete list of optional parameters is available at [FileMaker 18 Data API Guide
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#work-with-records_delete-record)
___

### _duplicateRecord:_

**Supported FileMaker Server version:** 18

Duplicate a record, specified by ID, found in the primary table of the current fmRESTor instance context.

~~~php
/**
 * @param integer $id
 * @param array $parameters
 * @return bool|mixed
 */
public function duplicateRecord($id, $parameters = null)
~~~

<details><summary>Usage</summary>

~~~php
$parameters = array(
    "script" => "Log request",
    "script.param" => "MyScriptParameters",
    "script.prerequest" => "Log request",
    "script.prerequest.param" => "MyScriptPreRequestParameters",
    "script.presort" => "Log request",
    "script.presort.param" => "MyScriptPreSortParameters"
);

$recordId = 3;

$response = $fm->duplicateRecord($recordId, $parameters);
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(8) {
    ["scriptResult.prerequest"]=>
    string(28) "MyScriptPreRequestParameters"
    ["scriptError.prerequest"]=>
    string(1) "0"
    ["scriptResult.presort"]=>
    string(25) "MyScriptPreSortParameters"
    ["scriptError.presort"]=>
    string(1) "0"
    ["scriptResult"]=>
    string(18) "MyScriptParameters"
    ["scriptError"]=>
    string(1) "0"
    ["recordId"]=>
    string(3) "141"
    ["modId"]=>
    string(1) "0"
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

Complete list of optional parameters is available at [FileMaker 18 Data API Guide
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#work-with-records_duplicate-record) 
___

### _editRecord:_

**Supported FileMaker Server version:** 17, 18

Update a record of given ID from the primary table of the current fmRESTor instance context.

~~~php
/**
 * @param integer $id
 * @param array $parameters
 * @return bool|mixed
 */
public function editRecord($id, $parameters)
~~~

<details><summary>Usage</summary>

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

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(2) {
    ["recordId"]=>
    string(3) "142"
    ["modId"]=>
    string(1) "0"
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

Complete list of optional parameters is available at [FileMaker 18 Data API Guide
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#work-with-records_edit-record)
___

### _getRecord:_

**Supported FileMaker Server version:** 17, 18

Get a record of given ID from the primary table of the current fmRESTor instance context.

~~~php
/**
 * @param integer $id
 * @option array $parameters
 * @return bool|mixed
 */
public function getRecord($id, $parameters = null)
~~~

<details><summary>Usage</summary>

~~~php
$parameters = array(
	"script" => "Log request",
    "script.param" => "MyScriptParameters",
    "script.prerequest" => "Log request",
    "script.prerequest.param" => "MyScriptPreRequestParameters",
    "script.presort" => "Log request",
    "script.presort.param" => "MyScriptPreSortParameters",
    "layout.response"=> "php_user",
    "_limit.USER_licence"=> 5,
    "_offset.USER_licence"=> 10
);

$recordID = 4;

$fm->getRecord($recordID, $parameters);
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(8) {
    ["scriptResult.prerequest"]=>
    string(28) "MyScriptPreRequestParameters"
    ["scriptError.prerequest"]=>
    string(1) "0"
    ["scriptResult.presort"]=>
    string(25) "MyScriptPreSortParameters"
    ["scriptError.presort"]=>
    string(1) "0"
    ["scriptResult"]=>
    string(18) "MyScriptParameters"
    ["scriptError"]=>
    string(1) "0"
    ["dataInfo"]=>
    array(6) {
      ["database"]=>
      string(8) "fmRESTor"
      ["layout"]=>
      string(8) "php_user"
      ["table"]=>
      string(4) "USER"
      ["totalRecordCount"]=>
      int(24)
      ["foundCount"]=>
      int(1)
      ["returnedCount"]=>
      int(1)
    }
    ["data"]=>
    array(1) {
      [0]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(196)
          ["created"]=>
          string(19) "07/30/2019 10:08:10"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:08:10"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(6) "Sutton"
          ["email"]=>
          string(12) "sutton@a.edu"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(421)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(122)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "122"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(2)
            ["returnedCount"]=>
            int(1)
          }
        }
      }
    }
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

Complete list of optional parameters is available at [FileMaker 18 Data API Guide
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#work-with-records_get-record)
___

### _getRecords:_

**Supported FileMaker Server version:** 17, 18

Get multiple records from the primary table of the current fmRESTor instance context. The function returns all records if called with no parameter or those fitting the criteria specified in its parameter.

~~~php
/**
 * @option array $parameters
 * @return bool|mixed
 */
public function getRecords($parameters = null)
~~~

<details><summary>Usage</summary>

~~~php
$parameters = array(
	"_limit"=>5
);

$fm->getRecords($parameters);
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(2) {
    ["dataInfo"]=>
    array(6) {
      ["database"]=>
      string(8) "fmRESTor"
      ["layout"]=>
      string(8) "php_user"
      ["table"]=>
      string(4) "USER"
      ["totalRecordCount"]=>
      int(24)
      ["foundCount"]=>
      int(24)
      ["returnedCount"]=>
      int(5)
    }
    ["data"]=>
    array(5) {
      [0]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(193)
          ["created"]=>
          string(19) "07/30/2019 09:56:38"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 09:56:38"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(4) "King"
          ["email"]=>
          string(15) "king@tempor.net"
          ["birthday"]=>
          string(10) "02/09/2020"
          ["personal_identification_number"]=>
          int(235)
          ["address"]=>
          string(27) "7182 Morbi Road, Hisar 5230"
          ["c_record_id"]=>
          int(119)
          ["photo"]=>
          string(142) "https://myhost.com/Streaming_SSL/MainDB/7C68B3099223C7D861245A59C22561015810109821641B470AA56B5947CEDCC3.jpg?RCType=EmbeddedRCFileProcessor"
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "119"
        ["modId"]=>
        string(1) "1"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(0)
            ["returnedCount"]=>
            int(0)
          }
        }
      }
      [1]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(194)
          ["created"]=>
          string(19) "07/30/2019 09:57:25"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 09:57:25"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(6) "Phelps"
          ["email"]=>
          string(20) "phelps@Aliquam.co.uk"
          ["birthday"]=>
          string(10) "02/24/2020"
          ["personal_identification_number"]=>
          int(96)
          ["address"]=>
          string(27) "9309 In St., Gressan 916926"
          ["c_record_id"]=>
          int(120)
          ["photo"]=>
          string(142) "https://myhost.com/Streaming_SSL/MainDB/27EDF2F544A12AB35CD7E7CEBC5C815AAAC8AD488A525E044EB23C457C08E238.png?RCType=EmbeddedRCFileProcessor"
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(1) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "61"
              ["USER_licence::product_name"]=>
              string(24) "Microsoft Windows Server"
              ["USER_licence::key"]=>
              string(29) "N2434-X9D7W-8PF6X-8DV9T-8TYMD"
              ["USER_licence::version"]=>
              string(17) "Standard 2019 x64"
              ["USER_licence::date_of_expiration"]=>
              string(10) "01/01/2024"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "120"
        ["modId"]=>
        string(1) "1"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(1)
            ["returnedCount"]=>
            int(1)
          }
        }
      }
      [2]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(195)
          ["created"]=>
          string(19) "07/30/2019 10:05:04"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:05:05"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(10) "Pan tester"
          ["email"]=>
          string(17) "testeri@gmail.com"
          ["birthday"]=>
          string(10) "01/01/2001"
          ["personal_identification_number"]=>
          int(99)
          ["address"]=>
          string(15) "Street 24, City"
          ["c_record_id"]=>
          int(121)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(2) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "62"
              ["USER_licence::product_name"]=>
              string(9) "product01"
              ["USER_licence::key"]=>
              string(5) "key01"
              ["USER_licence::version"]=>
              string(5) "ver01"
              ["USER_licence::date_of_expiration"]=>
              string(10) "01/01/2024"
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "63"
              ["USER_licence::product_name"]=>
              string(9) "product02"
              ["USER_licence::key"]=>
              string(5) "key02"
              ["USER_licence::version"]=>
              string(5) "ver02"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/02/2024"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "121"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(2)
            ["returnedCount"]=>
            int(2)
          }
        }
      }
      [3]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(196)
          ["created"]=>
          string(19) "07/30/2019 10:08:10"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:08:10"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(6) "Sutton"
          ["email"]=>
          string(12) "sutton@a.edu"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(421)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(122)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(2) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "64"
              ["USER_licence::product_name"]=>
              string(24) "Adobe Photoshop Elements"
              ["USER_licence::key"]=>
              string(29) "VK7JG-NPHTM-C97JM-9MPGT-3V66T"
              ["USER_licence::version"]=>
              string(15) "2019 MP ENG BOX"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/08/2024"
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "65"
              ["USER_licence::product_name"]=>
              string(20) "Microsoft Office 365"
              ["USER_licence::key"]=>
              string(29) "KTNPV-KTRK4-3RRR8-39X6W-W44T3"
              ["USER_licence::version"]=>
              string(20) "Business Premium OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "06/04/2021"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "122"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(2)
            ["returnedCount"]=>
            int(2)
          }
        }
      }
      [4]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(197)
          ["created"]=>
          string(19) "07/30/2019 10:09:21"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 11:36:48"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(15) "Name was edited"
          ["email"]=>
          string(24) "emailwasedited@email.com"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(1)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(123)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(2) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "66"
              ["USER_licence::product_name"]=>
              string(24) "Adobe Photoshop Elements"
              ["USER_licence::key"]=>
              string(29) "VK7JG-NPHTM-C97JM-9MPGT-3V66T"
              ["USER_licence::version"]=>
              string(15) "2019 MP ENG BOX"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/08/2024"
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "67"
              ["USER_licence::product_name"]=>
              string(20) "Microsoft Office 365"
              ["USER_licence::key"]=>
              string(29) "KTNPV-KTRK4-3RRR8-39X6W-W44T3"
              ["USER_licence::version"]=>
              string(20) "Business Premium OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "06/04/2021"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "123"
        ["modId"]=>
        string(1) "1"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(2)
            ["returnedCount"]=>
            int(2)
          }
        }
      }
    }
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

Complete list of optional parameters is available at [FileMaker 18 Data API Guide
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#work-with-records_get-records)
___

### _uploadFormDataToContainter:_

**Supported FileMaker Server version:** 17, 18

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

<details><summary>Usage</summary>

~~~php
$recordID = 4;
$containerFieldName = "photo";
$containerFieldRepetition = 1;
$file = $_FILES["photo"];

$fm->uploadFormDataToContainter($recordID, $containerFieldName, $containerFieldRepetition, $file);
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(2) {
    ["recordId"]=>
    string(3) "4"
    ["modId"]=>
    string(1) "0"
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
array(2) {
  ["response"]=>
  array(1) {
    ["modId"]=>
    string(1) "1"
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

___

### _uploadFileToContainter:_

**Supported FileMaker Server version:** 17, 18

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

<details><summary>Usage</summary>

~~~php
$recordID = 4;
$containerFieldName = "photo";
$containerFieldRepetition = 1;
$file = __DIR__ . "/24uSoftware.jpg";

$fm->uploadFileToContainter($recordID, $containerFieldName, $containerFieldRepetition, $file);
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(1) {
    ["modId"]=>
    string(1) "1"
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

___

### _findRecord:_

**Supported FileMaker Server version:** 17, 18

Returns a set of records from the primary table of the current fmRESTor instance context, fitting the find criteria specified in its parameter.

~~~php
/**
  * @param array $parameters
  * @return bool|mixed
  */
public function findRecords($parameters)
~~~

<details><summary>Usage</summary>

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

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(2) {
    ["dataInfo"]=>
    array(6) {
      ["database"]=>
      string(8) "fmRESTor"
      ["layout"]=>
      string(8) "php_user"
      ["table"]=>
      string(4) "USER"
      ["totalRecordCount"]=>
      int(24)
      ["foundCount"]=>
      int(24)
      ["returnedCount"]=>
      int(24)
    }
    ["data"]=>
    array(24) {
      [0]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(193)
          ["created"]=>
          string(19) "07/30/2019 09:56:38"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 09:56:38"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(4) "King"
          ["email"]=>
          string(15) "king@tempor.net"
          ["birthday"]=>
          string(10) "02/09/2020"
          ["personal_identification_number"]=>
          int(235)
          ["address"]=>
          string(27) "7182 Morbi Road, Hisar 5230"
          ["c_record_id"]=>
          int(119)
          ["photo"]=>
          string(142) "https://myhost.com/Streaming_SSL/MainDB/550DE9234BFAF60FEFC1D8CDC6E97049CC7FF04854381661A4F0233BDC35E8FE.jpg?RCType=EmbeddedRCFileProcessor"
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "119"
        ["modId"]=>
        string(1) "1"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(0)
            ["returnedCount"]=>
            int(0)
          }
        }
      }
      [1]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(194)
          ["created"]=>
          string(19) "07/30/2019 09:57:25"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 09:57:25"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(6) "Phelps"
          ["email"]=>
          string(20) "phelps@Aliquam.co.uk"
          ["birthday"]=>
          string(10) "02/24/2020"
          ["personal_identification_number"]=>
          int(96)
          ["address"]=>
          string(27) "9309 In St., Gressan 916926"
          ["c_record_id"]=>
          int(120)
          ["photo"]=>
          string(142) "https://myhost.com/Streaming_SSL/MainDB/B6D577DBAB3D60DBFCA99C771B5E7D06A6A73F1800F82935B62ED942214D2670.png?RCType=EmbeddedRCFileProcessor"
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(1) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "61"
              ["USER_licence::product_name"]=>
              string(24) "Microsoft Windows Server"
              ["USER_licence::key"]=>
              string(29) "N2434-X9D7W-8PF6X-8DV9T-8TYMD"
              ["USER_licence::version"]=>
              string(17) "Standard 2019 x64"
              ["USER_licence::date_of_expiration"]=>
              string(10) "01/01/2024"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "120"
        ["modId"]=>
        string(1) "1"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(1)
            ["returnedCount"]=>
            int(1)
          }
        }
      }
      [2]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(195)
          ["created"]=>
          string(19) "07/30/2019 10:05:04"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:05:05"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(10) "Pan tester"
          ["email"]=>
          string(17) "testeri@gmail.com"
          ["birthday"]=>
          string(10) "01/01/2001"
          ["personal_identification_number"]=>
          int(99)
          ["address"]=>
          string(15) "Street 24, City"
          ["c_record_id"]=>
          int(121)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(2) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "62"
              ["USER_licence::product_name"]=>
              string(9) "product01"
              ["USER_licence::key"]=>
              string(5) "key01"
              ["USER_licence::version"]=>
              string(5) "ver01"
              ["USER_licence::date_of_expiration"]=>
              string(10) "01/01/2024"
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "63"
              ["USER_licence::product_name"]=>
              string(9) "product02"
              ["USER_licence::key"]=>
              string(5) "key02"
              ["USER_licence::version"]=>
              string(5) "ver02"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/02/2024"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "121"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(2)
            ["returnedCount"]=>
            int(2)
          }
        }
      }
      [3]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(196)
          ["created"]=>
          string(19) "07/30/2019 10:08:10"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:08:10"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(6) "Sutton"
          ["email"]=>
          string(12) "sutton@a.edu"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(421)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(122)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(2) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "64"
              ["USER_licence::product_name"]=>
              string(24) "Adobe Photoshop Elements"
              ["USER_licence::key"]=>
              string(29) "VK7JG-NPHTM-C97JM-9MPGT-3V66T"
              ["USER_licence::version"]=>
              string(15) "2019 MP ENG BOX"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/08/2024"
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "65"
              ["USER_licence::product_name"]=>
              string(20) "Microsoft Office 365"
              ["USER_licence::key"]=>
              string(29) "KTNPV-KTRK4-3RRR8-39X6W-W44T3"
              ["USER_licence::version"]=>
              string(20) "Business Premium OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "06/04/2021"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "122"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(2)
            ["returnedCount"]=>
            int(2)
          }
        }
      }
      [4]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(197)
          ["created"]=>
          string(19) "07/30/2019 10:09:21"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 11:36:48"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(15) "Name was edited"
          ["email"]=>
          string(24) "emailwasedited@email.com"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(1)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(123)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(2) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "66"
              ["USER_licence::product_name"]=>
              string(24) "Adobe Photoshop Elements"
              ["USER_licence::key"]=>
              string(29) "VK7JG-NPHTM-C97JM-9MPGT-3V66T"
              ["USER_licence::version"]=>
              string(15) "2019 MP ENG BOX"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/08/2024"
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "67"
              ["USER_licence::product_name"]=>
              string(20) "Microsoft Office 365"
              ["USER_licence::key"]=>
              string(29) "KTNPV-KTRK4-3RRR8-39X6W-W44T3"
              ["USER_licence::version"]=>
              string(20) "Business Premium OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "06/04/2021"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "123"
        ["modId"]=>
        string(1) "1"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(2)
            ["returnedCount"]=>
            int(2)
          }
        }
      }
      [5]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(198)
          ["created"]=>
          string(19) "07/30/2019 10:09:22"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:09:22"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(6) "Sutton"
          ["email"]=>
          string(12) "sutton@a.edu"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(421)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(124)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(2) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "68"
              ["USER_licence::product_name"]=>
              string(24) "Adobe Photoshop Elements"
              ["USER_licence::key"]=>
              string(29) "VK7JG-NPHTM-C97JM-9MPGT-3V66T"
              ["USER_licence::version"]=>
              string(15) "2019 MP ENG BOX"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/08/2024"
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "69"
              ["USER_licence::product_name"]=>
              string(20) "Microsoft Office 365"
              ["USER_licence::key"]=>
              string(29) "KTNPV-KTRK4-3RRR8-39X6W-W44T3"
              ["USER_licence::version"]=>
              string(20) "Business Premium OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "06/04/2021"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "124"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(2)
            ["returnedCount"]=>
            int(2)
          }
        }
      }
      [6]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(199)
          ["created"]=>
          string(19) "07/30/2019 10:10:02"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:10:02"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(9) "Sutton G."
          ["email"]=>
          string(20) "sutton.gabriel@a.edu"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(111)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(125)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(4) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "72"
              ["USER_licence::product_name"]=>
              string(0) ""
              ["USER_licence::key"]=>
              string(11) "VK7JG-NPHTM"
              ["USER_licence::version"]=>
              string(0) ""
              ["USER_licence::date_of_expiration"]=>
              string(0) ""
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "73"
              ["USER_licence::product_name"]=>
              string(0) ""
              ["USER_licence::key"]=>
              string(0) ""
              ["USER_licence::version"]=>
              string(12) "Business OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "09/01/2023"
              ["modId"]=>
              string(1) "0"
            }
            [2]=>
            array(6) {
              ["recordId"]=>
              string(2) "70"
              ["USER_licence::product_name"]=>
              string(24) "Adobe Photoshop Elements"
              ["USER_licence::key"]=>
              string(29) "VK7JG-NPHTM-C97JM-9MPGT-3V66T"
              ["USER_licence::version"]=>
              string(15) "2019 MP ENG BOX"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/08/2024"
              ["modId"]=>
              string(1) "0"
            }
            [3]=>
            array(6) {
              ["recordId"]=>
              string(2) "71"
              ["USER_licence::product_name"]=>
              string(20) "Microsoft Office 365"
              ["USER_licence::key"]=>
              string(29) "KTNPV-KTRK4-3RRR8-39X6W-W44T3"
              ["USER_licence::version"]=>
              string(20) "Business Premium OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "06/04/2021"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "125"
        ["modId"]=>
        string(1) "1"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(4)
            ["returnedCount"]=>
            int(4)
          }
        }
      }
      [7]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(200)
          ["created"]=>
          string(19) "07/30/2019 10:13:54"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:13:54"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(6) "Sutton"
          ["email"]=>
          string(12) "sutton@a.edu"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(421)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(126)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(2) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "74"
              ["USER_licence::product_name"]=>
              string(24) "Adobe Photoshop Elements"
              ["USER_licence::key"]=>
              string(29) "VK7JG-NPHTM-C97JM-9MPGT-3V66T"
              ["USER_licence::version"]=>
              string(15) "2019 MP ENG BOX"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/08/2024"
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "75"
              ["USER_licence::product_name"]=>
              string(20) "Microsoft Office 365"
              ["USER_licence::key"]=>
              string(29) "KTNPV-KTRK4-3RRR8-39X6W-W44T3"
              ["USER_licence::version"]=>
              string(20) "Business Premium OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "06/04/2021"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "126"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(2)
            ["returnedCount"]=>
            int(2)
          }
        }
      }
      [8]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(201)
          ["created"]=>
          string(19) "07/30/2019 10:32:59"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:32:59"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(9) "Sutton G."
          ["email"]=>
          string(20) "sutton.gabriel@a.edu"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(111)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(127)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(4) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "78"
              ["USER_licence::product_name"]=>
              string(0) ""
              ["USER_licence::key"]=>
              string(11) "VK7JG-NPHTM"
              ["USER_licence::version"]=>
              string(0) ""
              ["USER_licence::date_of_expiration"]=>
              string(0) ""
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "79"
              ["USER_licence::product_name"]=>
              string(0) ""
              ["USER_licence::key"]=>
              string(0) ""
              ["USER_licence::version"]=>
              string(12) "Business OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "09/01/2023"
              ["modId"]=>
              string(1) "0"
            }
            [2]=>
            array(6) {
              ["recordId"]=>
              string(2) "76"
              ["USER_licence::product_name"]=>
              string(24) "Adobe Photoshop Elements"
              ["USER_licence::key"]=>
              string(29) "VK7JG-NPHTM-C97JM-9MPGT-3V66T"
              ["USER_licence::version"]=>
              string(15) "2019 MP ENG BOX"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/08/2024"
              ["modId"]=>
              string(1) "0"
            }
            [3]=>
            array(6) {
              ["recordId"]=>
              string(2) "77"
              ["USER_licence::product_name"]=>
              string(20) "Microsoft Office 365"
              ["USER_licence::key"]=>
              string(29) "KTNPV-KTRK4-3RRR8-39X6W-W44T3"
              ["USER_licence::version"]=>
              string(20) "Business Premium OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "06/04/2021"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "127"
        ["modId"]=>
        string(1) "1"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(4)
            ["returnedCount"]=>
            int(4)
          }
        }
      }
      [9]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(202)
          ["created"]=>
          string(19) "07/30/2019 10:33:51"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:33:51"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(9) "Sutton G."
          ["email"]=>
          string(20) "sutton.gabriel@a.edu"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(111)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(128)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(4) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "82"
              ["USER_licence::product_name"]=>
              string(0) ""
              ["USER_licence::key"]=>
              string(11) "VK7JG-NPHTM"
              ["USER_licence::version"]=>
              string(0) ""
              ["USER_licence::date_of_expiration"]=>
              string(0) ""
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "83"
              ["USER_licence::product_name"]=>
              string(0) ""
              ["USER_licence::key"]=>
              string(0) ""
              ["USER_licence::version"]=>
              string(12) "Business OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "09/01/2023"
              ["modId"]=>
              string(1) "0"
            }
            [2]=>
            array(6) {
              ["recordId"]=>
              string(2) "80"
              ["USER_licence::product_name"]=>
              string(24) "Adobe Photoshop Elements"
              ["USER_licence::key"]=>
              string(29) "VK7JG-NPHTM-C97JM-9MPGT-3V66T"
              ["USER_licence::version"]=>
              string(15) "2019 MP ENG BOX"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/08/2024"
              ["modId"]=>
              string(1) "0"
            }
            [3]=>
            array(6) {
              ["recordId"]=>
              string(2) "81"
              ["USER_licence::product_name"]=>
              string(20) "Microsoft Office 365"
              ["USER_licence::key"]=>
              string(29) "KTNPV-KTRK4-3RRR8-39X6W-W44T3"
              ["USER_licence::version"]=>
              string(20) "Business Premium OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "06/04/2021"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "128"
        ["modId"]=>
        string(1) "1"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(4)
            ["returnedCount"]=>
            int(4)
          }
        }
      }
      [10]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(203)
          ["created"]=>
          string(19) "07/30/2019 10:33:52"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:33:52"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(9) "Sutton G."
          ["email"]=>
          string(20) "sutton.gabriel@a.edu"
          ["birthday"]=>
          string(10) "12/11/2020"
          ["personal_identification_number"]=>
          int(111)
          ["address"]=>
          string(29) "5776 Nisi Road, Gorlitz 38197"
          ["c_record_id"]=>
          int(129)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(4) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "86"
              ["USER_licence::product_name"]=>
              string(0) ""
              ["USER_licence::key"]=>
              string(11) "VK7JG-NPHTM"
              ["USER_licence::version"]=>
              string(0) ""
              ["USER_licence::date_of_expiration"]=>
              string(0) ""
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "87"
              ["USER_licence::product_name"]=>
              string(0) ""
              ["USER_licence::key"]=>
              string(0) ""
              ["USER_licence::version"]=>
              string(12) "Business OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "09/01/2023"
              ["modId"]=>
              string(1) "0"
            }
            [2]=>
            array(6) {
              ["recordId"]=>
              string(2) "84"
              ["USER_licence::product_name"]=>
              string(24) "Adobe Photoshop Elements"
              ["USER_licence::key"]=>
              string(29) "VK7JG-NPHTM-C97JM-9MPGT-3V66T"
              ["USER_licence::version"]=>
              string(15) "2019 MP ENG BOX"
              ["USER_licence::date_of_expiration"]=>
              string(10) "02/08/2024"
              ["modId"]=>
              string(1) "0"
            }
            [3]=>
            array(6) {
              ["recordId"]=>
              string(2) "85"
              ["USER_licence::product_name"]=>
              string(20) "Microsoft Office 365"
              ["USER_licence::key"]=>
              string(29) "KTNPV-KTRK4-3RRR8-39X6W-W44T3"
              ["USER_licence::version"]=>
              string(20) "Business Premium OLP"
              ["USER_licence::date_of_expiration"]=>
              string(10) "06/04/2021"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "129"
        ["modId"]=>
        string(1) "1"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(4)
            ["returnedCount"]=>
            int(4)
          }
        }
      }
      [11]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(204)
          ["created"]=>
          string(19) "07/30/2019 10:38:13"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:38:13"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(10) "Harrington"
          ["email"]=>
          string(25) "diam.Harrington@leoin.net"
          ["birthday"]=>
          string(10) "02/14/2020"
          ["personal_identification_number"]=>
          int(136)
          ["address"]=>
          string(41) "4262 Pharetra Street, Tournefeuille 77731"
          ["c_record_id"]=>
          int(130)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(1) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "88"
              ["USER_licence::product_name"]=>
              string(9) "product01"
              ["USER_licence::key"]=>
              string(5) "key01"
              ["USER_licence::version"]=>
              string(5) "ver01"
              ["USER_licence::date_of_expiration"]=>
              string(10) "01/01/2024"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "130"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(1)
            ["returnedCount"]=>
            int(1)
          }
        }
      }
      [12]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(205)
          ["created"]=>
          string(19) "07/30/2019 10:38:13"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:38:13"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(7) "Maxwell"
          ["email"]=>
          string(25) "diam.Harrington@leoin.net"
          ["birthday"]=>
          string(10) "02/14/2020"
          ["personal_identification_number"]=>
          int(136)
          ["address"]=>
          string(41) "4262 Pharetra Street, Tournefeuille 77731"
          ["c_record_id"]=>
          int(131)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(1) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "89"
              ["USER_licence::product_name"]=>
              string(9) "product01"
              ["USER_licence::key"]=>
              string(5) "key01"
              ["USER_licence::version"]=>
              string(5) "ver01"
              ["USER_licence::date_of_expiration"]=>
              string(10) "01/01/2024"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "131"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(1)
            ["returnedCount"]=>
            int(1)
          }
        }
      }
      [13]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(206)
          ["created"]=>
          string(19) "07/30/2019 10:38:13"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:38:13"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(8) "Ferguson"
          ["email"]=>
          string(25) "diam.Harrington@leoin.net"
          ["birthday"]=>
          string(10) "02/14/2020"
          ["personal_identification_number"]=>
          int(136)
          ["address"]=>
          string(41) "4262 Pharetra Street, Tournefeuille 77731"
          ["c_record_id"]=>
          int(132)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(1) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "90"
              ["USER_licence::product_name"]=>
              string(9) "product01"
              ["USER_licence::key"]=>
              string(5) "key01"
              ["USER_licence::version"]=>
              string(5) "ver01"
              ["USER_licence::date_of_expiration"]=>
              string(10) "01/01/2024"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "132"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(1)
            ["returnedCount"]=>
            int(1)
          }
        }
      }
      [14]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(207)
          ["created"]=>
          string(19) "07/30/2019 10:38:13"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:38:13"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(6) "Bender"
          ["email"]=>
          string(25) "diam.Harrington@leoin.net"
          ["birthday"]=>
          string(10) "02/14/2020"
          ["personal_identification_number"]=>
          int(136)
          ["address"]=>
          string(41) "4262 Pharetra Street, Tournefeuille 77731"
          ["c_record_id"]=>
          int(133)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(1) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "91"
              ["USER_licence::product_name"]=>
              string(9) "product01"
              ["USER_licence::key"]=>
              string(5) "key01"
              ["USER_licence::version"]=>
              string(5) "ver01"
              ["USER_licence::date_of_expiration"]=>
              string(10) "01/01/2024"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "133"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(1)
            ["returnedCount"]=>
            int(1)
          }
        }
      }
      [15]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(208)
          ["created"]=>
          string(19) "07/30/2019 10:38:32"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:38:32"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(4) "Neal"
          ["email"]=>
          string(13) "neal@at.co.uk"
          ["birthday"]=>
          string(10) "09/22/2020"
          ["personal_identification_number"]=>
          int(219)
          ["address"]=>
          string(36) "1608 Maecenas St., Rijkevorsel 88937"
          ["c_record_id"]=>
          int(134)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "134"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(0)
            ["returnedCount"]=>
            int(0)
          }
        }
      }
      [16]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(209)
          ["created"]=>
          string(19) "07/30/2019 10:38:32"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:38:32"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(9) "Mccormick"
          ["email"]=>
          string(13) "neal@at.co.uk"
          ["birthday"]=>
          string(10) "09/22/2020"
          ["personal_identification_number"]=>
          int(219)
          ["address"]=>
          string(36) "1608 Maecenas St., Rijkevorsel 88937"
          ["c_record_id"]=>
          int(135)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "135"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(0)
            ["returnedCount"]=>
            int(0)
          }
        }
      }
      [17]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(210)
          ["created"]=>
          string(19) "07/30/2019 10:38:32"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:38:32"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(6) "Levine"
          ["email"]=>
          string(13) "neal@at.co.uk"
          ["birthday"]=>
          string(10) "09/22/2020"
          ["personal_identification_number"]=>
          int(219)
          ["address"]=>
          string(36) "1608 Maecenas St., Rijkevorsel 88937"
          ["c_record_id"]=>
          int(136)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "136"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(0)
            ["returnedCount"]=>
            int(0)
          }
        }
      }
      [18]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(211)
          ["created"]=>
          string(19) "07/30/2019 10:38:32"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:38:32"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(5) "Gibbs"
          ["email"]=>
          string(13) "neal@at.co.uk"
          ["birthday"]=>
          string(10) "09/22/2020"
          ["personal_identification_number"]=>
          int(219)
          ["address"]=>
          string(36) "1608 Maecenas St., Rijkevorsel 88937"
          ["c_record_id"]=>
          int(137)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "137"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(0)
            ["returnedCount"]=>
            int(0)
          }
        }
      }
      [19]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(212)
          ["created"]=>
          string(19) "07/30/2019 10:41:47"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 10:41:47"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(6) "Adkins"
          ["email"]=>
          string(27) "adkins@tempusmauriserat.org"
          ["birthday"]=>
          string(10) "01/09/2020"
          ["personal_identification_number"]=>
          int(355)
          ["address"]=>
          string(48) "9523 Nulla. Road, Portico e San Benedetto 378300"
          ["c_record_id"]=>
          int(138)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "138"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(0)
            ["returnedCount"]=>
            int(0)
          }
        }
      }
      [20]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(213)
          ["created"]=>
          string(19) "07/30/2019 11:29:27"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 11:29:27"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(8) "Lawrence"
          ["email"]=>
          string(18) "lawrence@lectus.ca"
          ["birthday"]=>
          string(10) "03/12/2020"
          ["personal_identification_number"]=>
          int(398)
          ["address"]=>
          string(33) "7399 Lobortis Rd., Görlitz 38197"
          ["c_record_id"]=>
          int(139)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(2) {
            [0]=>
            array(6) {
              ["recordId"]=>
              string(2) "92"
              ["USER_licence::product_name"]=>
              string(19) "Windows 10 OEM Home"
              ["USER_licence::key"]=>
              string(30) "NKJFK-GPHP7-G8C3J-P6JXR-HQRJR "
              ["USER_licence::version"]=>
              string(2) "10"
              ["USER_licence::date_of_expiration"]=>
              string(10) "05/12/2020"
              ["modId"]=>
              string(1) "0"
            }
            [1]=>
            array(6) {
              ["recordId"]=>
              string(2) "93"
              ["USER_licence::product_name"]=>
              string(25) "Windows 7 Ultimate 32 bit"
              ["USER_licence::key"]=>
              string(29) "RCGX7-P3XWP-PPPCV-Q2H7C-FCGFR"
              ["USER_licence::version"]=>
              string(3) "7.3"
              ["USER_licence::date_of_expiration"]=>
              string(10) "03/04/2018"
              ["modId"]=>
              string(1) "0"
            }
          }
        }
        ["recordId"]=>
        string(3) "139"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(2)
            ["returnedCount"]=>
            int(2)
          }
        }
      }
      [21]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(214)
          ["created"]=>
          string(19) "07/30/2019 11:31:44"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 11:31:44"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(8) "Lawrence"
          ["email"]=>
          string(18) "lawrence@lectus.ca"
          ["birthday"]=>
          string(10) "03/12/2020"
          ["personal_identification_number"]=>
          int(398)
          ["address"]=>
          string(33) "7399 Lobortis Rd., Görlitz 38197"
          ["c_record_id"]=>
          int(140)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "140"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(0)
            ["returnedCount"]=>
            int(0)
          }
        }
      }
      [22]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(215)
          ["created"]=>
          string(19) "07/30/2019 11:31:46"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 11:31:46"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(8) "Lawrence"
          ["email"]=>
          string(18) "lawrence@lectus.ca"
          ["birthday"]=>
          string(10) "03/12/2020"
          ["personal_identification_number"]=>
          int(398)
          ["address"]=>
          string(33) "7399 Lobortis Rd., Görlitz 38197"
          ["c_record_id"]=>
          int(141)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "141"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(0)
            ["returnedCount"]=>
            int(0)
          }
        }
      }
      [23]=>
      array(5) {
        ["fieldData"]=>
        array(15) {
          ["id"]=>
          int(216)
          ["created"]=>
          string(19) "07/30/2019 11:33:05"
          ["created_by"]=>
          string(3) "api"
          ["modified"]=>
          string(19) "07/30/2019 11:33:05"
          ["modified_by"]=>
          string(3) "api"
          ["surname"]=>
          string(15) "Name was edited"
          ["email"]=>
          string(24) "emailwasedited@email.com"
          ["birthday"]=>
          string(0) ""
          ["personal_identification_number"]=>
          int(1)
          ["address"]=>
          string(0) ""
          ["c_record_id"]=>
          int(142)
          ["photo"]=>
          string(0) ""
          ["g_one"]=>
          string(0) ""
          ["g_text"]=>
          string(0) ""
          [""]=>
          string(11) "<No Access>"
        }
        ["portalData"]=>
        array(1) {
          ["portal_licence"]=>
          array(0) {
          }
        }
        ["recordId"]=>
        string(3) "142"
        ["modId"]=>
        string(1) "0"
        ["portalDataInfo"]=>
        array(1) {
          [0]=>
          array(5) {
            ["portalObjectName"]=>
            string(14) "portal_licence"
            ["database"]=>
            string(8) "fmRESTor"
            ["table"]=>
            string(12) "USER_licence"
            ["foundCount"]=>
            int(0)
            ["returnedCount"]=>
            int(0)
          }
        }
      }
    }
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

Complete list of optional parameters is available at [FileMaker 18 Data API Guide
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#perform-a-find-request)
___

### _setGlobalField:_

**Supported FileMaker Server version:** 17, 18

Sets the values for global fields specified in its parameter.

~~~php
/**
 * @param array $parameters
 * @return bool|mixed
 */
public function setGlobalField($parameters)
~~~

<details><summary>Usage</summary>

~~~php
$parameters = array(
	"globalFields" => array(
        "USER::g_one" => "Global g_one is set up",
        "USER::g_text" => "Global g_text is set up"
    )
);

$fm->setGlobalField($parameters);
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(0) {
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

Complete list of optional parameters is available at [FileMaker 18 Data API Guide
](https://fmhelp.filemaker.com/docs/18/en/dataapi/#set-global-fields)
___

### _runScript:_

**Supported FileMaker Server version:** 18

Simply run a script in the given database without performing any other actions.

**Important:** This methods waits for the script to finish and **is** able to return script result in the response.

~~~php
/**
 * @param string $scriptName
 * @param array $scriptPrameters
 * @return bool|mixed
 */
public function runScript($scriptName, $scriptPrameters = null)
~~~

<details><summary>Usage</summary>

~~~php
$scriptParameters = [
	"script.param" => "MyScriptParameters"
];

$scriptName = "Log request";

$response = $fm->runScript($scriptName, $scriptParameters);
~~~

</details>

<details><summary>Sample Response</summary>

~~~php
array(2) {
  ["response"]=>
  array(2) {
    ["scriptResult"]=>
    string(18) "MyScriptParameters"
    ["scriptError"]=>
    string(1) "0"
  }
  ["messages"]=>
  array(1) {
    [0]=>
    array(2) {
      ["code"]=>
      string(1) "0"
      ["message"]=>
      string(2) "OK"
    }
  }
}
~~~

</details>

___

### _setFileMakerLayout_
    
**Supported FileMaker Server version:** 17, 18    
    
Navigates to a database layout specified by its name.

**Example:** An user needs the data from multiple tables within a single class instance, which is created with only one layout specified. Therefore he requests the data from the contextual table, switches to a different layout using **setFileMakerLayout** method and requests the data from the contextual table of new layout.
    
~~~php
/**
 * @param string $layout
 * @return bool
 */
public function setFilemakerLayout($layout)
~~~     

<details><summary>Usage</summary>

~~~php
$fm->setFilemakerLayout("Layout_name");
~~~

</details>

___
 
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
-106 | Options - Function expected an array but received something else as a parameter
-107 | The method you have tried to use is not supported by this version of FileMaker Server

 Success Code  | Description
------------- | ------------- 
101  | User was succesfully logged out

Examples
-
The examples for each method are available inside the folder "demo".



License
-
fmRESTor is licensed under the "GNU LGPLv3" License.








