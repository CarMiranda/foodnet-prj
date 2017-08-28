<?php
    class Response {
        public $status;
        public $data;

        public function __construct() {
            $this->status = "";
        }

        public function success($data) {
            $this->status = "success";
            if (is_array($data)) {
                $_data = [];
                foreach ($data as $row) {
                    $_data[] = (object)$row->as_array();
                }
            } else {
                $_data = $data;
            }
            $this->data = $_data;
        }

        public function exception($exception) {
            $this->status = "exception";
            $my_exception["message"] = $exception->getMessage();
            $my_exception{"trace"} = $exception->getTraceAsString();
            $this->data = (object)$my_exception;
        }

        public function json() {
            return json_encode($this);
        }
    }
?>