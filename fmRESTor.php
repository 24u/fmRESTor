<?php
/**
 * Author: 24U s.r.o.
 */
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

    /**
     * FilemakerAPI constructor.
     * @param string $host
     * @param string $db
     * @param string $layout
     * @param string $user
     * @param string $password
     * @option array $fmDataSource
     * @option array $options
     */
    public function __construct($host, $db, $layout, $user, $password, $options = null)
    {
        $this->host = $host;
        $this->db = $db;
        $this->layout = $layout;
        $this->user = $user;
        $this->password = $password;
        if ($options !== null) {
            $this->setOptions($options);
        }
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
            "url" => "/fmi/data/v1/databases/" . $this->db . "/sessions/" . $this->token,
            "method" => "DELETE",
        );

        $result = $this->callURL($request);

        if (is_array($result)) {
            $response = $result["response"];
            $message = $result["messages"][0]["message"];

            if ($this->verificationResult($message)) {
                $this->destroySessionToken();
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "Logout was successfull",
                    "data" => $response
                ));
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "Logout was not successfull",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Logout was not successfull",
                "data" => $result
            ));
        }

        return $result;
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
            "url" => "/fmi/data/v1/databases/" . $this->db . "/layouts/" . $this->layout . "/records",
            "method" => "POST",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            )
        );

        $param = $this->convertParametersToJson($parameters);
        $result = $this->callURL($request, $param);

        if (is_array($result)) {
            $response = $result["response"];
            $message = $result["messages"][0]["message"];

            if ($this->verificationResult($message)) {
                $this->extendTokenExpiration();
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "Record was successfully created",
                    "data" => $response
                ));
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "Record was not successfully created",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Record was not successfully created",
                "data" => $result
            ));
        }

        return $result;
    }

    /**
     * @param integer $id
     * @param array $parameters
     * @return bool|mixed
     */
    public function deleteRecord($id, $parameters)
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

        $param = $this->convertParametersToString($parameters);
        $request = array(
            "url" => "/fmi/data/v1/databases/" . $this->db . "/layouts/" . $this->layout . "/records/" . $id . "?" . $param,
            "method" => "DELETE",
            "headers" => array(
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request);

        if (is_array($result)) {
            $response = $result["response"];
            $message = $result["messages"][0]["message"];

            if ($this->verificationResult($message)) {
                $this->extendTokenExpiration();
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "Record was successfully deleted",
                    "data" => $response
                ));
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "Record was not successfully deleted",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Record was not successfully deleted",
                "data" => $result
            ));
        }

        return $result;
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
            "url" => "/fmi/data/v1/databases/" . $this->db . "/layouts/" . $this->layout . "/records/" . $id,
            "method" => "PATCH",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            )
        );

        $param = $this->convertParametersToJson($parameters);
        $result = $this->callURL($request, $param);
        $response = $result["response"];
        $message = $result["messages"][0]["message"];

        if (is_array($result)) {
            if ($this->verificationResult($message)) {
                $this->extendTokenExpiration();
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "Record was successfully edited",
                    "data" => $response
                ));
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "Record was not successfully edited",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Record was not successfully edited",
                "data" => $result
            ));
        }

        return $result;
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
            "url" => "/fmi/data/v1/databases/" . $this->db . "/layouts/" . $this->layout . "/records/" . $id . "?" . $param,
            "method" => "GET",
            "headers" => array(
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request);

        if (is_array($result)) {
            $response = $result["response"];
            $message = $result["messages"][0]["message"];

            if ($this->verificationResult($message)) {
                $this->extendTokenExpiration();
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "Record was successfully loaded",
                    "data" => $response
                ));
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "Record was not successfully loaded",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Record was not successfully loaded",
                "data" => $result
            ));
        }

        return $result;
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
            "url" => "/fmi/data/v1/databases/" . $this->db . "/layouts/" . $this->layout . "/records/" . "?" . $param,
            "method" => "GET",
            "headers" => array(
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request);

        if (is_array($result)) {
            $response = $result["response"];
            $message = $result["messages"][0]["message"];

            if ($this->verificationResult($message)) {
                $this->extendTokenExpiration();
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "Records were successfully loaded",
                    "data" => $response
                ));
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "Records were not successfully loaded",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Records were not successfully loaded",
                "data" => $result
            ));
        }

        return $result;
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
            "upload" => new CURLFile($file["tmp_name"], $file["type"], $file["name"])
        );

        $request = array(
            "url" => "/fmi/data/v1/databases/" . $this->db . "/layouts/" . $this->layout . "/records/" . $id . "/containers/" . $containerFieldName . "/" . $containerFieldRepetition,
            "method" => "POST",
            "headers" => array(
                'Content-Type: multipart/form-data',
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request, $param);

        if (is_array($result)) {
            $response = $result["response"];
            $message = $result["messages"][0]["message"];

            if ($this->verificationResult($message)) {
                $this->extendTokenExpiration();
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "File was successfully uploaded",
                    "data" => $response
                ));
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "File was not successfully uploaded",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "File was not successfully uploaded",
                "data" => $result
            ));
        }

        return $result;
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
            "upload" => new CURLFile($path, $mime, $filename)
        );

        $request = array(
            "url" => "/fmi/data/v1/databases/" . $this->db . "/layouts/" . $this->layout . "/records/" . $id . "/containers/" . $containerFieldName . "/" . $containerFieldRepetition,
            "method" => "POST",
            "headers" => array(
                'Content-Type: multipart/form-data',
                "Authorization: Bearer " . $this->token
            )
        );

        $result = $this->callURL($request, $param);

        if (is_array($result)) {
            $response = $result["response"];
            $message = $result["messages"][0]["message"];

            if ($this->verificationResult($message)) {
                $this->extendTokenExpiration();
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "File was successfully uploaded",
                    "data" => $response
                ));
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "File was not successfully uploaded",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "File was not successfully uploaded",
                "data" => $result
            ));
        }
        return $result;
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
            "url" => "/fmi/data/v1/databases/" . $this->db . "/layouts/" . $this->layout . "/_find",
            "method" => "POST",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            )
        );

        $param = $this->convertParametersToJson($parameters);
        $result = $this->callURL($request, $param);

        if (is_array($result)) {
            $response = $result["response"];
            $message = $result["messages"][0]["message"];

            if ($this->verificationResult($message)) {
                $this->extendTokenExpiration();
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "Records was successfully found",
                    "data" => $response
                ));
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "Records was not found",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Records was not found",
                "data" => $result
            ));
        }

        return $result;
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
            "url" => "/fmi/data/v1/databases/" . $this->db . "/globals/",
            "method" => "PATCH",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            )
        );

        $param = $this->convertParametersToJson($parameters);
        $result = $this->callURL($request, $param);

        if (is_array($result)) {
            $response = $result["response"];
            $message = $result["messages"][0]["message"];

            if ($this->verificationResult($message)) {
                $this->extendTokenExpiration();
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "Global fields was successfully set",
                    "data" => $response
                ));
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "Global fields was not successfully set",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Global fields was not successfully set",
                "data" => $result
            ));
        }

        return $result;
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
            return $errors;
        } else {
            $response = json_decode($result, true);

            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_INFO,
                "message" => "cURL request sent"
            ));
            return $response;
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
            "url" => "/fmi/data/v1/databases/" . $this->db . "/sessions",
            "method" => "POST",
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode($this->user . ":" . $this->password)
            )
        );

        $result = $this->callURL($request);

        if (is_array($result)) {
            $response = $result["response"];
            $message = $result["messages"][0]["message"];

            if ($this->verificationResult($message)) {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_SUCCESS,
                    "message" => "Token was sucessfully created",
                    "data" => $result
                ));

                $this->setToken($response["token"]);
                $this->extendTokenExpiration();

                return true;
            } else {
                $this->log(array(
                    "line" => __LINE__,
                    "method" => __METHOD__,
                    "type" => self::LS_ERROR,
                    "message" => "Token was not sucessfully created",
                    "data" => $message
                ));
            }
        } else {
            $this->log(array(
                "line" => __LINE__,
                "method" => __METHOD__,
                "type" => self::LS_ERROR,
                "message" => "Token was not sucessfully created",
                "data" => $result
            ));
        }
        return $result;
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

            $currentTime = new DateTime();
            $tokenExpire = DateTime::createFromFormat("Y-m-d H:i:s", $appSession["tokenCreated"]);
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

        $currentTime = new DateTime();
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
        unset($_SESSION[$this->sessionName]);
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

    /**
     * @param string $message
     * @return bool
     */
    private function verificationResult($message)
    {
        $lowerMessage = strtolower($message);
        if ($lowerMessage === "ok") {
            return true;
        } else {
            return false;
        }
    }
}