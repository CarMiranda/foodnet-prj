<?php

    /**
    *  Generation of a new password when forgotten.
    *  @param int len Length of the password
    *  @return string Password
    */
    function passwordGeneration($len = 10) {
        $alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!_-*?";
        $count = mb_strlen($alpha);
        $pass = "";
        
        for ($i = 0; $i < $len; ++$i) {
            $index = rand(0, $count - 1);
            $pass .= mb_substr($alpha, $index - 1, 1);
        }

        return $pass;
    }

    /**
    *   Check app token from Authorization header in request
    *   @return StdObject jwt A parsed Json Web Token
    */
    function checkAuthToken() {

        // First get all headers from request
        $headers = getallheaders();

        if (empty($headers['Authorization'])) {
            return false;
        }
        if (!preg_match('/Bearer\s.+/', $headers['Authorization'])) {
            throw new Exception("Invalid authorization header.");
        }
        $auth = explode(' ', $headers['Authorization']);
        if (count($auth) != 2 || !validateJWT($auth[1])) {
            throw new Exception("Invalid authentication token.");
        }
        try {
            $jwt = decodeJWT($auth[1]);
        } catch (Exception $e) {
            throw new Exception("Error decoding JWT.", 0, $e);
        }
        if (empty($jwt[1]->id)) {
            throw new Exception("No identifier in authentication token.");
        }
        
        return $jwt;
    }

    /**
    *   Check service token from Authorization
    */

    /**
    *   Encodes data with MIME base64.
    *
    *   @param string data
    *   @return string
    */
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
    *   Decodes data with MIME base64 (url safe).
    *
    *   @param string data
    *   @return string
    */
    function base64url_decode($data) {
        $rem = strlen($data) % 4;
        $_data = $data;
        if ($rem) {
            $pad = 4 - $rem;
            $_data .= str_repeat('=', $pad);
        }
        return base64_decode(strtr($_data, '-_', '+/'));
    }

    /**
    *   Creates a new JSON Web Token using the user information
    *
    *   @param array An array with key => values of user specific fields, to keep jwt unique
    *   @return string A valid JSON Web Token based on user information
    */
    function encodeJWT($user_spec = []) {
        if (!empty($user_spec)) {
            // Get configuration from files
            $header = base64url_encode(file_get_contents(JWTHEADER)); // Contains fields and info, so we can encode it right away
            $payload = json_decode(file_get_contents(JWTPAYLOAD)); // Contains some fields and info, but it will also contain user info to keep it unique
            $secret = json_decode(file_get_contents(JWTSECRET)); // Secret pass phrase

            // Add variable fields
            foreach ($user_spec as $key => $value) {
                $payload->$key = $value;
            }

            $payload = base64url_encode(json_encode($payload)); // Encode the payload

            // Sign the JWT
            $signature = base64url_encode(hash_hmac('SHA256', "$header.$payload", $secret, true));

            $token = "$header.$payload.$signature"; // JWT
            return $token;
        }

        return false;
    }

    /**
    *   
    *   @param string jwt A string representing and encoded JWT
    *   @return StdObject An object representing the JWT 
    */
    function decodeJWT($jwt) {
        $_jwt = explode('.', $jwt);
        if (count($_jwt) !== 3) {
            throw new Exception("Invalid authentication token.");
        }
        return array_map('json_decode', array_map("base64url_decode", $_jwt));
    }

    /**
    *   Validates a JWT with the secret pass phrase
    *
    *   @param string jwt The JWT to validate
    *   @return boolean
    */
    function validateJWT($jwt = '', $service = NULL) {
        if (!empty($jwt)) {
            if ($service === NULL) {
                $secret = json_decode(file_get_contents(JWTSECRET)); // Secret pass phrase
            } else {
                switch ($service) {
                    case "facebook" :
                        $secret = json_decode(file_get_contents(JWTSECRETFB));
                    break;
                    case "googleplus" :
                        $secret = json_decode(file_get_contents(JWTSECRETGP));
                    break;
                    case "instagram" :
                        $secret = json_decode(file_get_contents(JWTSECRETIG));
                    break;
                    case "linkedin" :
                        $secret = json_decode(file_get_contents(JWTSECRETLI));
                    break;
                }
            }
            list($header, $payload, $signature) = explode('.', $jwt);
            return hash_equals(base64url_decode($signature), hash_hmac('SHA256', "$header.$payload", $secret, true));
        }
        return FALSE;
    }

    /**
    *
    *   Transforms an ORM instance into an array as field_name => value
    *   
    *   @param ORM orm_obj
    *   @param array fields Fields to extract
    *   @return array
    */
    function ORM2Array($orm_obj, $fields = []) {
        if (!empty($fields)) {
            foreach ($orm_obj as $row) {
                foreach ($fields as $field) {
                    $arr[$field] = $row->{$field};
                }
                $res[] = (object)$arr;
            }
            return $res;
        }
        return FALSE;
    }

    /**
    *
    *
    */
    function parsePHP($param) {
        if (is_string($param)) {
            if ($param[0] == '[' && $param[strlen($param) - 1] == ']') {
                return array_map('urldecode', explode(',', trim($param, '[]')));
            } else if (preg_match("/^[^0].[0-9]+/")) {
                return (int)$param;
            } else {
                return urldecode($param);
            }
        }
    }

    /**
    *
    *
    */
    function parseRequestBody() {
        $request_body = json_decode(file_get_contents("php://input"));
        /*$db = DB::factory('app');
        recursive_db_escape($db, $request_body->data);*/
        return $request_body;
    }

    function recursive_db_escape($db, &$data) {
        if (is_object($data) || is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $data->{$key} = recursive_db_escape($db, $data->{$key});
                } else {
                    $data->{$key} = $db->quote($value);
                }
            }
        } else {
            $data = $db->quote($data);
        }
    }

    function results($data) {
        if (is_array($data)) {
            $_data = [];
            foreach ($data as $key => $value) {
                if (get_class($value) == "ORM") {
                    $_data[] = results($value);
                } else {
                    $_data[$key] = $value;
                }
            }
        } else if (get_class($data) == "ORM") {
            $_data = $data->as_array();
        } else if (is_object($data)) {
            $_data = [];
            $_data_array = (array)$data;
            foreach ($_data_array as $key => $value) {
                if (is_array($value)) {
                    $_data[$key] = results($value);
                } else {
                    $_data[$key] = $value;
                }
            }
        } else {
            $_data = $data;
        }
        return $_data;
    }

    function extractKey($result_set, $key) {
        $res = [];
        foreach ($result_set as $row) {
            $res[] = $row->{$key};
        }
        return $res;
    }