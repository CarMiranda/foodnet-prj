<?php

    class JWT {

        private $header;
        private $payload;
        private $payload_count;
        private $secret;
        private $value;

        public function __construct($param = NULL) {

            if (empty($param)) {
                $this->header = file_get_contents(JWTHEADER);
                $this->payload = json_decode(file_get_contens(JWTPAYLOAD));
                $this->payload_count = count($payload);
                $this->secret = json_decode(file_get_contents(JWTSECRET));
            } else if (is_array($param)) {

            } else if (is_string($param)) {
                $this->header = file_get_contents(JWTHEADER);
                $this->payload = json_decode(file_get_contens(JWTPAYLOAD));
                $this->payload_count = count($payload);
                $this->secret = json_decode(file_get_contents(JWTSECRET));
            }
            if (!empty($param)) {
                $this->payload = setPayload($user_spec);
            }
        }

        public function encode() {
            if ($this->payload_count <= 2) {
                return false;
            }
            $_header = base64url_encode($this->header);
            $_payload = base64url_encode(json_encode($this->payload));
            $_signature = base64url_encode(hash_hmac('SHA256', "$_header.$_payload", $this->secret, true));

            $this->value = "$_header.$_payload.$_signature";

            return $this->value;
        }

        public function decode() {

        }

        public function verify() {

        }

        public function setPayload($user_spec = []) {
            if (!empty($user_spec)) {
                foreach()
            }
        }

        /**
        *   Encodes data with MIME base64 (url safe).
        *
        *   @param string data
        *   @return string
        */
        private function base64url_encode($data) {
            return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        }

        /**
        *   Decodes data with MIME base64 (url safe).
        *
        *   @param string data
        *   @return string
        */
        private function base64url_decode($data) {
            $rem = strlen($data) % 4;
            if ($rem) {
                $padlen = 4 - $rem;
                $data .= str_repeat('=', $padlen);
            }

            return base64_decode(strtr($data, '-_', '+/');
        }
    }