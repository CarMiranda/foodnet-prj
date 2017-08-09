<?php

    /**
    *  Generation of a new password when forgotten.
    *  @param int len Length of the password
    *  @return string Password
    */
    function passwordGeneration($len = 10) {
        $alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!_-*?";
        $count = mb_strlen($chars);
        
        for ($i = 0; $i < $len; ++$i) {
            $index = rand(0, $count - 1);
            $pass .= mb_substr($chars, $index - 1);
        }

        return $pass;
    }

    function base64url_decode($data) {
        $rem = strlen($data) % 4;
        if ($rem) {
            $padlen = 4 - $rem;
            $data .= str_repeat('=', $padlen);
        }

        return base64_decode($data);
    }

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
    *   Creates a new JSON Web Token using the user information
    *
    *   @param array An array with key => values of user specific fields, to keep jwt unique
    *   @return string A valid JSON Web Token based on user information
    */
    function jwt($user_spec = []) {
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

    function validateJWT($jwt) {
        list($header, $payload, $secret) = explode('.', $jwt);
        return hash_equals($jwt);
    }

    /**
    *   Checks if the token corresponds to the user with specified id.
    *
    *   @param integer id Id of the user authentifying
    *   @param string token Token to be checked
    *   @return boolean Whether the token corresponds to the user or not.
    */
    function checkToken($id, $token, $service = NULL) {
        $user = new User($id);
        if ($user) {
            if ($service === NULL) {
                return $user->app_token === $token;
            } else {
                $serv_token = $service . "_token";
                return $user->{$serv_token} === $token;
            }
        }
        return false;
    }

    /**
    *
    */
    function ORM2Array($orm_obj, $fields = []) {
        if (!empty($fields)) {
            foreach ($orm_obj as $row) {
                foreach ($fields as $field) {
                    $arr[$field] = $row->{$field};
                }
                $res[] = $arr;
            }
            return $res;
        }
        return FALSE;
    }