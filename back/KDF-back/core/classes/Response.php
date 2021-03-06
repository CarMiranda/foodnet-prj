<?php
    class Response {
        public $status;
        public $data;

        public function __construct() {
            $this->status = "";
        }

        public function success($data) {
            $this->status = "success";
            $this->data = results($data);
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