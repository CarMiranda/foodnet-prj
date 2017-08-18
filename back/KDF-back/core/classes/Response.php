<?php
    class Response {
        private $data;
        private $status;

        public function __construct() {
            $this->status = "";
            $this->data = [];
        }

        public function success() {
            $this->status = "success";
        }

        public function setData($_data) {
            if (is_array($_data)) {
                foreach ($_data as $key => $value) {
                    $this->data[$key] = $value;
                }
            } else if (is_object($_data) || is_string($_data)) {
                $this->data[] = $_data;
            } else {
                return false;
            }
        }

        public function setException($exception) {
            $this->_status = "exception";
            $my_exception = new stdClass();
            $my_exception->message = $exception->getMessage();
            $my_exception->trace = $exception->getTraceAsString();
            $this->_data["information"] = $my_exception;
        }

        public function json() {
            return json_encode($this);
        }
    }
?>