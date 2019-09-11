<?php
/**
 * Author: 24U s.r.o.
 */

namespace fmRESTor;
/**
 * Class fmRESTor
 */
class fmRESTor
{
    /* --- Connection --- */
    private $host;
    private $db;
    private $layout;
    private $user;
    private $password;
    private $fmDateSource;

    /* --- Define another attributes --- */
    private $token;
    private $rowNumber;

    /* --- Default options --- */
    private $tokenExpireTime = 14;
    private $sessionName = "fm-api-token";
    private $logDir = __DIR__ . "/log/";
    private $logType = self::LOG_TYPE_DEBUG;
    private $allowInsecure = false;

    /* --- Define log const --- */
    const LOG_TYPE_DEBUG = "debug";
    const LOG_TYPE_ERRORS = "errors";
    const LOG_TYPE_NONE = "none";

    const LS_ERROR = "error";
    const LS_SUCCESS = "success";
    const LS_INFO = "info";
    const LS_WARNING = "warning";

    const ERROR_RESPONSE_CODE = [400, 401, 403, 404, 405, 415, 500];

    /**
     * fmRESTor constructor.
     * @param string $host
     * @param string $db
     * @param string $layout
     * @param string $user
     * @param string $password
     * @option array $fmDataSource
     * @option array $options
     */
    public function __construct($host, $db, $layout, $user, $password, $options = null, $fmDataSource = null)
    {
        $this->host = $host;
        $this->db = $db;
        $this->layout = $layout;
        $this->user = $user;
        $this->password = $password;
        $this->fmDateSource = $fmDataSource;
        if ($options !== null) {
            $this->setOptions($options);
        }

        $this->setTimezone();
    }

    private function setLogRowNumber()
    {
        $this->rowNumber = rand(1000000, 9999999);
    }

    /**
     * @param array $options
     */
    private function setOptions($options)
    {
        /* --- Log type --- */
        if (isset($options["logType"])) {
            $logType = $options["logType"];

            if (in_array($logType, [self::LOG_TYPE_DEBUG, self::LOG_TYPE_ERRORS, self::LOG_TYPE_NONE]) && !empty($logType)) {
                $this->logType = $logType;
            } else {
                $this->response(-101);
            }
        }

        /* --- Log dir --- */
        if (isset($options["logDir"])) {
            $logDir = $options["logDir"];

            if (is_string($logDir) && !empty($logDir)) {
                $this->logDir = $logDir;
            } else {
                $this->response(-102);
            }
        }

        /* --- Session name --- */
        if (isset($options["sessionName"])) {
            $sessionName = $options["sessionName"];

            if (is_string($sessionName) && !empty($sessionName)) {
                $this->sessionName = $sessionName;
            } else {
                $this->response(-103);
            }
        }

        /* --- Token Expire Time ( In minutes ) --- */
        if (isset($options["tokenExpireTime"])) {
            $tokenExpireTime = $options["tokenExpireTime"];

            if (is_numeric($tokenExpireTime)) {
                $this->tokenExpireTime = $tokenExpireTime;
            } else {
                $this->response(-104);
            }
        }

        /* --- Allow Insecure --- */
        if (isset($options["allowInsecure"])) {
            $allowInsecure = $options["allowInsecure"];

            if (is_bool($allowInsecure)) {
                $this->allowInsecure = $allowInsecure;
            } else {
                $this->response(-105);
            }
        }
    }

    /**
     * Check if is set default timezone in PHP.ini
     */
    private function setTimezone()
    {
        if (ini_get('date.timezone') == "") {
            ini_set('date.timezone', 'Europe/London');
        }
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to logout from database"
        ));

        if ($this->isLogged() === false) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "User is logged out - invalid token"
            ));

            $this->destroySessionToken();
            $this->response(101);
        }

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/sessions/" . rawurlencode($this->token),
            "method" => "DELETE",
        );

        $result = $this->callURL($request);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Logout was successfull",
                "data" => $response
            ));

            $this->destroySessionToken();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Logout was not successfull",
                "data" => $response
            ));
        }

        return $response;

    }

    /**
     * @param string $scriptName
     * @param array $scriptPrameters
     * @return bool|mixed
     */
    public function runScript($scriptName, $scriptPrameters = null)
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to run a script",
            "data" => $scriptPrameters
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $param = "";
        if ($scriptPrameters !== null) {
            $param = $this->convertParametersToString($scriptPrameters);
        }
        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout) . "/script/" . rawurlencode($scriptName) . "?" . $param,
            "method" => "GET",
            "headers" => array(
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request, $param);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Script was successfully called",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Script was not successfully called",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @return bool|mixed
     */
    public function getDatabaseNames()
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to get metadata - database names"
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $request = array(
            "url" => "/fmi/data/vLatest/databases",
            "method" => "GET",
            "headers" => array(
                "Authorization: Basic " . base64_encode($this->user . ":" . $this->password)
            )
        );

        $result = $this->callURL($request);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Information about database names was successfully loaded",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Information about database names was not successfully loaded",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @return bool|mixed
     */
    public function getProductInformation()
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to get metadata - product information"
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $request = array(
            "url" => "/fmi/data/vLatest/productInfo",
            "method" => "GET"
        );

        $result = $this->callURL($request);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Product Information was successfully loaded",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Product Information was not successfully loaded",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @return bool|mixed
     */
    public function getScriptNames()
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to get metadata - script names"
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/scripts",
            "method" => "GET",
            "headers" => array(
                "Authorization: Bearer " . $this->token
            )

        );

        $result = $this->callURL($request);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Information about script names was successfully loaded",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Information about script names was not successfully loaded",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @return bool|mixed
     */
    public function getLayoutNames()
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to get metadata - layout names"
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts",
            "method" => "GET",
            "headers" => array(
                "Authorization: Bearer " . $this->token
            )

        );

        $result = $this->callURL($request);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Information about layout names was successfully loaded",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Information about layout names was not successfully loaded",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @return bool|mixed
     */
    public function getLayoutMetadata()
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to get metadata - layout information"
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout),
            "method" => "GET",
            "headers" => array(
                "Authorization: Bearer " . $this->token
            )

        );

        $result = $this->callURL($request);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Information about layout was successfully loaded",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Information about layout was not successfully loaded",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @param array $parameters
     * @return bool|mixed
     */
    public function createRecord($parameters)
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to create a record",
            "data" => $parameters
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout) . "/records",
            "method" => "POST",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            )
        );

        $param = $this->convertParametersToJson($parameters);
        $result = $this->callURL($request, $param);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Record was successfully created",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Record was not successfully created",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @param integer $id
     * @param array $parameters
     * @return bool|mixed
     */
    public function deleteRecord($id, $parameters = null)
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to delete a record",
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $param = "";
        if ($parameters !== null) {
            $param = $this->convertParametersToString($parameters);
        }
        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout) . "/records/" . $id . "?" . $param,
            "method" => "DELETE",
            "headers" => array(
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Record was successfully deleted",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Record was not successfully deleted",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @param integer $id
     * @param array $parameters
     * @return bool|mixed
     */
    public function duplicateRecord($id, $parameters = null)
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to edit a record",
            "data" => $parameters
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout) . "/records/" . $id,
            "method" => "POST",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            )
        );

        $param = null;
        if ($parameters !== null) {
            $param = $this->convertParametersToJson($parameters);
        }

        $result = $this->callURL($request, $param);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Record was successfully edited",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Record was not successfully edited",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @param integer $id
     * @param array $parameters
     * @return bool|mixed
     */
    public function editRecord($id, $parameters)
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to edit a record",
            "data" => $parameters
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout) . "/records/" . $id,
            "method" => "PATCH",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            )
        );

        $param = $this->convertParametersToJson($parameters);
        $result = $this->callURL($request, $param);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Record was successfully edited",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Record was not successfully edited",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @param integer $id
     * @option array $parameters
     * @return bool|mixed
     */
    public function getRecord($id, $parameters = null)
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to get a record from database",
            "data" => $parameters
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $param = "";
        if ($parameters !== null) {
            $param = $this->convertParametersToString($parameters);
        }
        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout) . "/records/" . $id . "?" . $param,
            "method" => "GET",
            "headers" => array(
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Record was successfully loaded",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Record was not successfully loaded",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @option array $parameters
     * @return bool|mixed
     */
    public function getRecords($parameters = null)
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to get records",
            "data" => $parameters
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $param = "";
        if ($parameters !== null) {
            $param = $this->convertParametersToString($parameters);
        }

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout) . "/records/" . "?" . $param,
            "method" => "GET",
            "headers" => array(
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Records were successfully loaded",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Records were not successfully loaded",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @param integer $id
     * @param string $containerFieldName
     * @param string containerFieldRepetition
     * @param array $file
     * @return bool|mixed
     */
    public function uploadFormDataToContainter($id, $containerFieldName, $containerFieldRepetition, $file)
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to upload a file to a container",
            "data" => $file
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $param = array(
            "upload" => new \CURLFile($file["tmp_name"], $file["type"], $file["name"])
        );

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout) . "/records/" . $id . "/containers/" . $containerFieldName . "/" . $containerFieldRepetition,
            "method" => "POST",
            "headers" => array(
                'Content-Type: multipart/form-data',
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request, $param);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "File was successfully uploaded",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "File was not successfully uploaded",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @param integer $id
     * @param string $containerFieldName
     * @param integer $containerFieldRepetition
     * @param string $path
     * @return bool|mixed
     */
    public function uploadFileToContainter($id, $containerFieldName, $containerFieldRepetition, $path)
    {
        $this->setLogRowNumber();

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        $filename = basename($path);

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to upload a file to a container",
            "data" => $path
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $param = array(
            "upload" => new \CURLFile($path, $mime, $filename)
        );

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout) . "/records/" . $id . "/containers/" . $containerFieldName . "/" . $containerFieldRepetition,
            "method" => "POST",
            "headers" => array(
                'Content-Type: multipart/form-data',
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request, $param);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "File was successfully uploaded",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "File was not successfully uploaded",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @param array $parameters
     * @return bool|mixed
     */
    public function findRecords($parameters)
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to find records",
            "data" => $parameters
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/layouts/" . rawurlencode($this->layout) . "/_find",
            "method" => "POST",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            )
        );

        $param = $this->convertParametersToJson($parameters);
        $result = $this->callURL($request, $param);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Records was successfully found",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Records was not found",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @param array $parameters
     * @return bool|mixed
     */
    public function setGlobalField($parameters)
    {
        $this->setLogRowNumber();

        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to set global fields",
            "data" => $parameters
        ));

        if ($this->isLogged() === false) {
            $login = $this->login();
            if ($login !== true) {
                return $login;
            }
        }

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/globals/",
            "method" => "PATCH",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            )
        );

        $param = $this->convertParametersToJson($parameters);
        $result = $this->callURL($request, $param);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Global fields was successfully set",
                "data" => $response
            ));

            $this->extendTokenExpiration();
        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Global fields was not successfully set",
                "data" => $response
            ));
        }

        return $response;
    }

    /**
     * @param string $layout
     * @return bool
     */
    public function setFilemakerLayout($layout)
    {
        if (is_string($layout)) {
            $this->layout = $layout;
            return true;
        } else {
            return false;
        }
    }


    /**
     * @param array $parameters
     * @return null|string
     */
    private function convertParametersToJson($parameters)
    {
        if (is_array($parameters)) {
            if (!empty($parameters)) {
                return json_encode($parameters);
            } else {
                return null;
            }
        } else {
            $this->response(-106);
        }
        return null;
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function convertParametersToString($parameters)
    {
        if (is_array($parameters)) {
            if (!empty($parameters)) {
                return http_build_query($parameters);
            } else {
                return "";
            }
        } else {
            $this->response(-106);
        }
        return "";
    }

    /**
     * @param array $requestSettings
     * @option array $data
     * @return mixed
     */
    private function callURL($requestSettings, $data = null)
    {
        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "cURL request sending"
        ));

        $headers = (isset($requestSettings["headers"]) ? $requestSettings["headers"] : null);
        $method = $requestSettings["method"];
        $url = $requestSettings["url"];

        /* --- Init CURL --- */
        $ch = curl_init();

        /* --- Allow redirects --- */
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        /* --- Return response --- */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        /* --- Settings control CURL is strict about identify verification --- */
        if ($this->allowInsecure == true) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        /* --- Return the transfer as a string --- */
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);

        /* --- Set headers --- */
        if ($headers !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        /* --- Set post data --- */
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($data === null ? "" : $data));

        /* --- Set request method --- */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        /* --- Set request URL --- */
        curl_setopt($ch, CURLOPT_URL, "https://" . $this->host . $url);

        /* --- Output--- */
        $result = curl_exec($ch);
        $errors = curl_error($ch);

        if (!empty($errors)) {
            return [
                "status" => curl_getinfo($ch),
                "result" => $errors
            ];
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_INFO,
                "message" => "cURL request sent"
            ));

            return [
                "status" => curl_getinfo($ch),
                "result" => json_decode($result, true)
            ];
        }
    }

    /**
     * @return bool|mixed
     */
    private function login()
    {
        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Attempt to create new token"
        ));

        $request = array(
            "url" => "/fmi/data/v1/databases/" . rawurlencode($this->db) . "/sessions",
            "method" => "POST",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode($this->user . ":" . $this->password)
            )
        );

        $param = "";
        if ($this->fmDateSource !== null) {
            $prepareParam = array(
                "fmDataSource" => $this->fmDateSource
            );

            $param = $this->convertParametersToJson($prepareParam);
        }

        $result = $this->callURL($request, $param);
        $response = $result["result"];

        try {
            $this->isResultError($result);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_SUCCESS,
                "message" => "Token was sucessfully created",
                "data" => $response
            ));

            $this->setToken($response["response"]["token"]);
            $this->extendTokenExpiration();

            return true;

        } catch (\Exception $e) {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Token was not sucessfully created",
                "data" => $response
            ));
            return $response;
        }
    }

    /**
     * @return bool
     */
    private function isLogged()
    {
        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Checking if user is logged into the database"
        ));

        if (isset($_SESSION[$this->sessionName]["token"]) && isset($_SESSION[$this->sessionName]["tokenCreated"])) {
            $appSession = $_SESSION[$this->sessionName];

            $currentTime = new \DateTime();
            $tokenExpire = \DateTime::createFromFormat("Y-m-d H:i:s", $appSession["tokenCreated"]);
            if ($tokenExpire === false) {
                return false;
            }

            if ($currentTime >= $tokenExpire) {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_WARNING,
                    "message" => "User is not logged into the database"
                ));
                return false;
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "User is logged into the database"
                ));
                $this->setToken($_SESSION[$this->sessionName]["token"]);
                return true;
            }
        } else {
            return false;
        }
    }

    private function extendTokenExpiration()
    {
        $this->log(array(
            "line" => __LINE__,
            "method" => __METHOD__,
            "type" => self::LS_INFO,
            "message" => "Token expiration time extending"
        ));

        $currentTime = new \DateTime();
        $tokenExpire = $currentTime->modify("+" . $this->tokenExpireTime . "minutes");
        $_SESSION[$this->sessionName]["tokenCreated"] = $tokenExpire->format("Y-m-d H:i:s");
    }

    /**
     * @param $token
     */
    private function setToken($token)
    {
        $_SESSION[$this->sessionName]["token"] = $token;
        $this->token = $token;
    }

    private function destroySessionToken()
    {
        if (isset($_SESSION[$this->sessionName])) {
            unset($_SESSION[$this->sessionName]);
        }
    }

    /**
     * @param array $log
     */
    private function log($log)
    {
        $type = null;
        if (isset($log["type"])) {
            $type = $log["type"];
        }

        $message = null;
        if (isset($log["message"])) {
            $message = $log["message"];
        }

        $section = null;
        if (isset($log["method"])) {
            $section = $log["method"];
        }

        $data = null;
        if (isset($log["data"])) {
            $data = $log["data"];
        }

        if ($this->logType !== self::LOG_TYPE_NONE) {
            if ($this->logType == self::LOG_TYPE_ERRORS && $type === self::LS_ERROR || $this->logType == self::LOG_TYPE_DEBUG) {

                /* --- Define basic variable needed for log function --- */
                $log_message = "";
                $split_string = "\t";

                /* --- Row number --- */
                $log_message .= $this->rowNumber . $split_string;

                /* --- Date & Time --- */
                $log_message .= date("Y-m-d H:i:s") . $split_string;

                /* --- Section name --- */
                if (!empty($section)) {
                    $log_message .= $section . $split_string;
                } else {
                    $log_message .= "" . $split_string;
                }

                /* --- Type--- */
                $log_message .= strtoupper($type) . $split_string;

                /* --- Data --- */
                if (!empty($data)) {
                    if (is_array($data)) {
                        $log_message .= json_encode($data) . $split_string;
                    } else {
                        $log_message .= $data . $split_string;
                    }
                } else {
                    $log_message .= "";
                }

                /* --- Message --- */
                if (!empty($message)) {
                    $log_message .= $message;
                } else {
                    $log_message .= "";
                }

                $log_message .= "\n";

                /* --- Save log to file --- */
                $pathDir = $this->logDir;
                $file = "fm-api-log_" . date("d.m.Y") . ".txt";
                if (is_dir($pathDir) or is_writable($pathDir)) {
                    file_put_contents($pathDir . $file, $log_message, FILE_APPEND);
                }
            }
        }
    }

    /**
     * @param integer $code
     */
    private function response($code)
    {
        echo $code;
        exit();
    }

    // TODO comment
    private function isResultError($result)
    {
        if (isset($result["status"]["http_code"]) && in_array($result["status"]["http_code"], self::ERROR_RESPONSE_CODE)) {
            $errorCode = $result["result"]["messages"][0]["code"];
            if ($errorCode == 1630) {
                // CAUGHT ERROR - IF USER CALL UNSUPPORTED FUNCTION FOR SELECTED FILEMAKER
                $this->response(-107);
            } else {
                throw new \Exception(json_encode($result["result"]), $result["status"]["http_code"]);
            }
        }
    }
}
